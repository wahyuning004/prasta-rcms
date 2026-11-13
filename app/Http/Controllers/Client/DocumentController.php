<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientDocument;
use App\Models\ClientDocumentVersion;
use App\Models\Order;
use App\Models\ServiceRequirement;
use App\Services\OrderProgressService;
use App\Services\SecureDocumentStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentController extends Controller
{
    /**
     * Unggah dokumen persyaratan baru atau unggah versi revisi tanpa menghapus versi sebelumnya.
     */
    public function upload(
        Request $request,
        Order $order,
        OrderProgressService $progressService,
        SecureDocumentStorageService $storageService
    ): RedirectResponse {
        abort_if($order->user_id !== Auth::id(), 403, 'Anda tidak berhak mengunggah dokumen untuk pesanan lain.');
        abort_if($order->isCancelled(), 403, 'Pesanan yang telah dibatalkan tidak dapat menerima unggahan dokumen.');

        $validated = $request->validate([
            'service_requirement_id' => ['required', 'integer', 'exists:service_requirements,id'],
            'document_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx', 'max:10240'],
            'revision_note' => ['nullable', 'string', 'max:500'],
        ], [
            'service_requirement_id.required' => 'Jenis persyaratan dokumen wajib dipilih.',
            'service_requirement_id.exists' => 'Persyaratan dokumen tidak ditemukan.',
            'document_file.required' => 'File dokumen wajib dipilih.',
            'document_file.mimes' => 'Format file harus PDF, JPG, JPEG, PNG, DOC, DOCX, XLS, atau XLSX.',
            'document_file.max' => 'Ukuran dokumen maksimal 10 MB.',
        ]);

        $requirement = ServiceRequirement::findOrFail($validated['service_requirement_id']);
        $resolvedService = $order->resolvedService();

        if ($resolvedService && (int) $requirement->service_id !== (int) $resolvedService->id) {
            return back()->with('error', 'Persyaratan dokumen yang dipilih tidak sesuai dengan layanan pesanan ini.');
        }

        $existing = ClientDocument::where('order_id', $order->id)
            ->where('service_requirement_id', $requirement->id)
            ->first();

        $revisionNote = trim((string) ($validated['revision_note'] ?? ''));
        if ($existing && $existing->status === 'rejected' && $revisionNote === '') {
            return back()->withErrors([
                'revision_note' => 'Mohon isi keterangan revisi saat mengunggah ulang dokumen yang diminta revisi.',
            ])->withInput();
        }

        $file = $request->file('document_file');
        $storageResult = $storageService->storeUploadedFile($file, $order, $requirement);
        $storedPath = $storageResult['stored_path'];
        $formattedSize = $storageResult['file_size'];

        $userId = Auth::id();
        $newVersionNumber = 1;

        DB::transaction(function () use (
            $order,
            $requirement,
            $existing,
            $file,
            $storedPath,
            $formattedSize,
            $revisionNote,
            $userId,
            $progressService,
            &$newVersionNumber
        ): void {
            if ($existing) {
                $newVersionNumber = (int) $existing->version + 1;
                $effectiveNote = $revisionNote !== ''
                    ? $revisionNote
                    : 'Pembaruan dokumen versi v'.$newVersionNumber;

                // Pastikan v1 legacy tersimpan di client_document_versions jika belum ada riwayat
                if ($existing->versions()->count() === 0 && ! empty($existing->file_path)) {
                    ClientDocumentVersion::create([
                        'client_document_id' => $existing->id,
                        'version_number' => (int) $existing->version,
                        'file_path' => $existing->file_path,
                        'original_filename' => $existing->original_filename ?: basename($existing->file_path),
                        'file_size' => $existing->file_size,
                        'mime_type' => $existing->mime_type,
                        'revision_notes' => $existing->revision_note ?: 'Unggahan awal dokumen',
                        'review_status' => $existing->status,
                        'reviewer_notes' => $existing->notes,
                        'uploaded_by' => $existing->uploaded_by ?? $userId,
                    ]);
                }

                $existing->update([
                    'document_name' => $requirement->document_name,
                    'document_type' => $requirement->document_type ?? 'Persyaratan Teknis',
                    'file_path' => $storedPath,
                    'original_filename' => $file->getClientOriginalName(),
                    'version' => $newVersionNumber,
                    'file_size' => $formattedSize,
                    'mime_type' => $file->getMimeType(),
                    'status' => 'uploaded',
                    'revision_note' => $effectiveNote,
                    'notes' => 'Versi v'.$newVersionNumber.' diunggah ('.$effectiveNote.'). Menunggu pemeriksaan petugas.',
                    'uploaded_by' => $userId,
                ]);

                $doc = $existing;
            } else {
                $effectiveNote = $revisionNote !== '' ? $revisionNote : 'Unggahan awal dokumen v1';

                $doc = ClientDocument::create([
                    'order_id' => $order->id,
                    'service_requirement_id' => $requirement->id,
                    'document_name' => $requirement->document_name,
                    'document_type' => $requirement->document_type ?? 'Persyaratan Teknis',
                    'file_path' => $storedPath,
                    'original_filename' => $file->getClientOriginalName(),
                    'version' => 1,
                    'file_size' => $formattedSize,
                    'mime_type' => $file->getMimeType(),
                    'status' => 'uploaded',
                    'revision_note' => $effectiveNote,
                    'notes' => 'Dokumen diunggah. Menunggu pemeriksaan tim Legal.',
                    'uploaded_by' => $userId,
                ]);
            }

            ClientDocumentVersion::create([
                'client_document_id' => $doc->id,
                'version_number' => $newVersionNumber,
                'file_path' => $storedPath,
                'original_filename' => $file->getClientOriginalName(),
                'file_size' => $formattedSize,
                'mime_type' => $file->getMimeType(),
                'revision_notes' => $effectiveNote,
                'review_status' => 'uploaded',
                'reviewer_notes' => null,
                'uploaded_by' => $userId,
            ]);

            if ((int) $order->progress_percentage < 50) {
                $progressService->recordStage(
                    $order,
                    'document_review',
                    'in_progress',
                    'Pelanggan mengunggah berkas "'.$requirement->document_name.'" (v'.$newVersionNumber.') untuk pemeriksaan tim Legal.',
                    $userId
                );
            }
        });

        return redirect()->route('klien.orders.show', $order->id)
            ->with('success', 'Dokumen "'.$requirement->document_name.'" (versi v'.$newVersionNumber.') berhasil diunggah.');
    }

    /**
     * Unduh / lihat versi spesifik dokumen secara aman (pemilik pesanan & petugas berwenang saja).
     */
    public function downloadVersion(ClientDocumentVersion $version, SecureDocumentStorageService $storageService): BinaryFileResponse
    {
        return $storageService->serveVersionDownload($version, Auth::user());
    }

    /**
     * Unduh versi terbaru dokumen secara aman (pemilik pesanan & petugas berwenang saja).
     */
    public function downloadCurrent(ClientDocument $document, SecureDocumentStorageService $storageService): BinaryFileResponse
    {
        return $storageService->serveDocumentDownload($document, Auth::user());
    }
}
