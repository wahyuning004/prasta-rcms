<?php

namespace App\Http\Controllers\Legal;

use App\Http\Controllers\Controller;
use App\Models\ClientDocument;
use App\Models\ClientDocumentVersion;
use App\Models\Order;
use App\Models\ServiceRequirement;
use App\Services\OrderProgressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LegalController extends Controller
{
    /**
     * Dashboard khusus Legal & Regulatory Specialist.
     */
    public function dashboard(Request $request): View
    {
        $user = Auth::user();

        // 1. Antrean Kajian Berkas Teknis / Legalitas
        $documentsQuery = ClientDocument::with(['order.user', 'requirement', 'versions'])
            ->leftJoin('orders', 'client_documents.order_id', '=', 'orders.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->leftJoin('service_requirements', 'client_documents.service_requirement_id', '=', 'service_requirements.id')
            ->select(
                'client_documents.*',
                'orders.order_number',
                'users.name as client_name',
                'users.company_name',
                'service_requirements.document_name as req_document_name',
                'service_requirements.document_type as req_document_type',
                'service_requirements.is_mandatory'
            );

        $filterStatus = $request->get('status');
        if ($filterStatus && $filterStatus !== 'all') {
            $documentsQuery->where('client_documents.status', $filterStatus);
        }

        $documents = $documentsQuery->latest('client_documents.created_at')->get();

        // Riwayat Projek Aktif
        $orders = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.status', '!=', 'cancelled')
            ->select('orders.*', 'users.name as client_name', 'users.company_name')
            ->latest('orders.created_at')
            ->get();

        // Statistik Legal & Regulatory
        $stats = [
            'pendingDocs' => DB::table('client_documents')->where('status', 'uploaded')->count(),
            'approvedDocs' => DB::table('client_documents')->where('status', 'approved')->count(),
            'rejectedDocs' => DB::table('client_documents')->where('status', 'rejected')->count(),
            'totalDocs' => DB::table('client_documents')->count(),
        ];

        $stages = OrderProgressService::stages();

        return view('legal.dashboard', compact('user', 'documents', 'orders', 'stats', 'filterStatus', 'stages'));
    }

    /**
     * Verifikasi berkas teknis (Diterima vs Perlu Revisi Dokumen) beserta pembaruan riwayat versi & progres.
     */
    public function verifyDocument(Request $request, int $id, OrderProgressService $progressService): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected,uploaded'],
            'notes' => ['required_if:status,rejected', 'nullable', 'string', 'max:1000'],
        ], [
            'notes.required_if' => 'Alasan atau catatan revisi wajib diisi saat dokumen berstatus Perlu Revisi.',
        ]);

        $document = ClientDocument::with('order')->findOrFail($id);
        $newStatus = $validated['status'];
        $defaultNotes = $newStatus === 'approved'
            ? 'Dokumen teknis telah memenuhi seluruh kepatuhan standar regulasi instansi terkait (Diterima).'
            : ($newStatus === 'rejected'
                ? 'Dokumen teknis belum lengkap / memerlukan perbaikan sesuai ketentuan SOP regulasi.'
                : 'Dokumen menunggu pemeriksaan teknis lanjutan.');

        $finalNotes = ! empty($validated['notes']) ? trim($validated['notes']) : $defaultNotes;
        $actorId = Auth::id();

        DB::transaction(function () use ($document, $newStatus, $finalNotes, $actorId, $progressService): void {
            $document->update([
                'status' => $newStatus,
                'notes' => $finalNotes,
                'verified_by' => $actorId,
                'verified_at' => now(),
            ]);

            // Sinkronkan status review pada versi dokumen terbaru
            $latestVersion = ClientDocumentVersion::where('client_document_id', $document->id)
                ->orderByDesc('version_number')
                ->first();

            if ($latestVersion) {
                $latestVersion->update([
                    'review_status' => $newStatus,
                    'reviewer_notes' => $finalNotes,
                ]);
            }

            $order = $document->order;
            if ($order) {
                if ($newStatus === 'rejected') {
                    $progressService->recordStage(
                        $order,
                        'document_review',
                        'in_progress',
                        'Dokumen "'.($document->document_name ?: 'Persyaratan').'" perlu revisi: '.$finalNotes,
                        $actorId
                    );
                } elseif ($newStatus === 'approved') {
                    $service = $order->resolvedService();
                    $mandatoryIds = $service
                        ? ServiceRequirement::where('service_id', $service->id)->where('is_mandatory', true)->pluck('id')
                        : collect();

                    if ($mandatoryIds->isNotEmpty()) {
                        $approvedCount = ClientDocument::where('order_id', $order->id)
                            ->whereIn('service_requirement_id', $mandatoryIds)
                            ->where('status', 'approved')
                            ->count();

                        if ($approvedCount >= $mandatoryIds->count()) {
                            $progressService->recordStage(
                                $order,
                                'document_review',
                                'completed',
                                'Seluruh dokumen persyaratan wajib telah diperiksa dan dinyatakan lengkap/valid oleh tim Legal.',
                                $actorId
                            );
                        }
                    }
                }
            }
        });

        return back()->with('success', 'Status verifikasi dokumen berhasil diperbarui.');
    }

    /**
     * Penerbitan Berita Acara Kajian Teknis (BAK) Resmi.
     */
    public function generateBak(int $id): View|RedirectResponse
    {
        $document = DB::table('client_documents')
            ->leftJoin('orders', 'client_documents.order_id', '=', 'orders.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'client_documents.*',
                'orders.order_number',
                'orders.total_cost',
                'users.name as client_name',
                'users.company_name',
                'users.address as client_address',
                'users.phone as client_phone'
            )
            ->where('client_documents.id', $id)
            ->first();

        if (! $document) {
            return back()->with('error', 'Dokumen tidak ditemukan.');
        }

        $legalSpecialist = Auth::user();

        return view('legal.bak_pdf', compact('document', 'legalSpecialist'));
    }

    /**
     * Simpan hasil Checksheet Kepatuhan Regulasi Standar.
     */
    public function saveChecksheet(Request $request): RedirectResponse
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'standard_type' => 'required|string',
            'notes' => 'nullable|string|max:1000',
        ]);

        return back()->with('success', 'Hasil penilaian Checksheet Kepatuhan '.$request->standard_type.' berhasil disimpan dan diverifikasi.');
    }

    /**
     * Perbarui tahapan progres proyek dari dashboard Legal.
     */
    public function updateTracking(Request $request, OrderProgressService $progressService): RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'stage_name' => ['required', 'string', 'max:200'],
            'stage_status' => ['required', 'in:pending,in_progress,completed'],
            'description' => ['required', 'string', 'max:1500'],
        ]);

        $order = Order::findOrFail($validated['order_id']);
        if ($order->isCancelled()) {
            return back()->with('error', 'Pesanan telah dibatalkan dan linimasa tidak dapat diperbarui.');
        }
        $progressService->recordStage(
            $order,
            $validated['stage_name'],
            $validated['stage_status'],
            trim($validated['description']),
            Auth::id()
        );

        return back()->with('success', 'Progres proyek berhasil diperbarui.');
    }
}
