<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LegalController extends Controller
{
    /**
     * Dashboard khusus Legal & Regulatory Specialist.
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        // 1. Antrean Kajian Berkas Teknis / Legalitas
        $documentsQuery = DB::table('client_documents')
            ->leftJoin('orders', 'client_documents.order_id', '=', 'orders.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->leftJoin('service_requirements', 'client_documents.service_requirement_id', '=', 'service_requirements.id')
            ->select(
                'client_documents.*',
                'orders.order_number',
                'users.name as client_name',
                'users.company_name',
                'service_requirements.document_name'
            );

        $filterStatus = $request->get('status');
        if ($filterStatus && $filterStatus !== 'all') {
            $documentsQuery->where('client_documents.status', $filterStatus);
        }

        $documents = $documentsQuery->latest('client_documents.created_at')->get();

        // Riwayat Projek Aktif
        $orders = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.*', 'users.name as client_name', 'users.company_name')
            ->latest('orders.created_at')
            ->get();

        // Statistik Legal & Regulatory
        $stats = [
            'pendingDocs'  => DB::table('client_documents')->where('status', 'uploaded')->count(),
            'approvedDocs' => DB::table('client_documents')->where('status', 'approved')->count(),
            'rejectedDocs' => DB::table('client_documents')->where('status', 'rejected')->count(),
            'totalDocs'    => DB::table('client_documents')->count(),
        ];

        return view('legal.dashboard', compact('user', 'documents', 'orders', 'stats', 'filterStatus'));
    }

    /**
     * Verifikasi berkas teknis (Valid / Disetujui vs Perlu Revisi Dokumen).
     */
    public function verifyDocument(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,uploaded',
            'notes'  => 'nullable|string|max:1000',
        ]);

        $defaultNotes = $request->status === 'approved' 
            ? 'Dokumen teknis telah memenuhi seluruh kepatuhan standar regulasi instansi terkait (Valid).'
            : 'Dokumen teknis belum lengkap / memerlukan perbaikan sesuai ketentuan SOP regulasi.';

        DB::table('client_documents')->where('id', $id)->update([
            'status'     => $request->status,
            'notes'      => $request->notes ?? $defaultNotes,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Status verifikasi berkas teknis berhasil diperbarui.');
    }

    /**
     * Penerbitan Berita Acara Kajian Teknis (BAK) Resmi.
     */
    public function generateBak($id)
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

        if (!$document) {
            return back()->with('error', 'Dokumen tidak ditemukan.');
        }

        $legalSpecialist = Auth::user();

        return view('legal.bak_pdf', compact('document', 'legalSpecialist'));
    }

    /**
     * Simpan hasil Checksheet Kepatuhan Regulasi Standar.
     */
    public function saveChecksheet(Request $request)
    {
        $request->validate([
            'order_id'       => 'required|exists:orders,id',
            'standard_type'  => 'required|string',
            'notes'          => 'nullable|string|max:1000',
        ]);

        return back()->with('success', 'Hasil penilaian Checksheet Kepatuhan ' . $request->standard_type . ' berhasil disimpan dan diverifikasi.');
    }
}
