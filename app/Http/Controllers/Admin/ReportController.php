<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Tampilkan halaman pusat ekspor laporan.
     */
    public function index()
    {
        $transactions = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
            ->select(
                'orders.id',
                'orders.order_number',
                'orders.total_cost',
                'orders.status as order_status',
                'orders.created_at',
                'users.name as client_name',
                'users.company_name',
                'payments.payment_method',
                'payments.status as payment_status'
            )
            ->latest('orders.created_at')
            ->get();

        $stats = [
            'totalRevenue' => DB::table('orders')->whereIn('status', ['processing', 'completed', 'verified'])->sum('total_cost'),
            'totalOrders' => DB::table('orders')->count(),
            'completedOrders' => DB::table('orders')->where('status', 'completed')->count(),
            'approvedDocs' => DB::table('client_documents')->where('status', 'approved')->count(),
            'totalDocs' => DB::table('client_documents')->count(),
            'avgRating' => round(DB::table('reviews')->avg('rating') ?? 4.9, 1),
        ];

        return view('admin.reports.index', compact('transactions', 'stats'));
    }

    /**
     * Ekspor Laporan Rekapitulasi Mutasi Keuangan (PDF / Printable).
     */
    public function exportFinancePdf()
    {
        $orders = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
            ->select(
                'orders.*',
                'users.name as client_name',
                'users.company_name',
                'payments.payment_method',
                'payments.status as payment_status'
            )
            ->latest('orders.created_at')
            ->get();

        $totalRevenue = $orders->whereIn('status', ['processing', 'completed', 'verified'])->sum('total_cost');

        return view('admin.reports.finance_pdf', compact('orders', 'totalRevenue'));
    }

    /**
     * Ekspor Laporan Rekapitulasi Mutasi Keuangan (Excel / CSV).
     */
    public function exportFinanceExcel(): StreamedResponse
    {
        $filename = 'Laporan_Mutasi_Keuangan_IZINYOU_'.date('Ymd_His').'.csv';

        $orders = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
            ->select(
                'orders.order_number',
                'orders.created_at',
                'users.name as client_name',
                'users.company_name',
                'payments.payment_method',
                'orders.total_cost',
                'orders.status as order_status',
                'payments.status as payment_status'
            )
            ->latest('orders.created_at')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($orders) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['REKAPITULASI DATA MUTASI KEUANGAN & TRANSAKSI LAYANAN']);
            fputcsv($handle, ['SISTEM IZINYOU - CV PRASTA SOLUSI INDONESIA']);
            fputcsv($handle, ['Tanggal Unduh:', date('d-m-Y H:i:s')]);
            fputcsv($handle, []);

            // Table headers
            fputcsv($handle, [
                'No.',
                'No. Pesanan',
                'Tanggal Pengajuan',
                'Nama Klien',
                'Perusahaan',
                'Metode Pembayaran',
                'Nominal (IDR)',
                'Status Projek',
                'Status Pembayaran',
            ]);

            $no = 1;
            $grandTotal = 0;
            foreach ($orders as $o) {
                $grandTotal += (float) $o->total_cost;
                fputcsv($handle, [
                    $no++,
                    $o->order_number,
                    date('d/m/Y H:i', strtotime($o->created_at)),
                    $o->client_name,
                    $o->company_name ?? '-',
                    $o->payment_method ?? 'Transfer Bank',
                    number_format($o->total_cost, 0, ',', '.'),
                    ucfirst($o->order_status),
                    $o->payment_status === 'success' ? 'Lunas / Berhasil' : 'Menunggu / Proses',
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['', '', '', '', '', 'TOTAL AKUMULASI (IDR):', number_format($grandTotal, 0, ',', '.')]);

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Ekspor Laporan Mutu Layanan (PDF / Printable).
     */
    public function exportQualityPdf()
    {
        $stats = [
            'totalDocs' => DB::table('client_documents')->count(),
            'approvedDocs' => DB::table('client_documents')->where('status', 'approved')->count(),
            'pendingDocs' => DB::table('client_documents')->where('status', 'uploaded')->count(),
            'rejectedDocs' => DB::table('client_documents')->where('status', 'rejected')->count(),
            'totalOrders' => DB::table('orders')->count(),
            'completedOrders' => DB::table('orders')->where('status', 'completed')->count(),
            'avgRating' => round(DB::table('reviews')->avg('rating') ?? 4.9, 1),
            'reviewsCount' => DB::table('reviews')->count(),
        ];

        $documents = DB::table('client_documents')
            ->leftJoin('orders', 'client_documents.order_id', '=', 'orders.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'client_documents.*',
                'orders.order_number',
                'users.name as client_name',
                'users.company_name'
            )
            ->latest('client_documents.created_at')
            ->get();

        return view('admin.reports.quality_pdf', compact('stats', 'documents'));
    }

    /**
     * Ekspor Laporan Mutu Layanan (Excel / CSV).
     */
    public function exportQualityExcel(): StreamedResponse
    {
        $filename = 'Laporan_Mutu_Layanan_IZINYOU_'.date('Ymd_His').'.csv';

        $documents = DB::table('client_documents')
            ->leftJoin('orders', 'client_documents.order_id', '=', 'orders.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'client_documents.id',
                'orders.order_number',
                'users.name as client_name',
                'users.company_name',
                'client_documents.file_path',
                'client_documents.status',
                'client_documents.notes',
                'client_documents.created_at'
            )
            ->latest('client_documents.created_at')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($documents) {
            $handle = fopen('php://output', 'w');

            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['LAPORAN MUTU & KAJIAN KEPATUHAN STANDAR REGULASI']);
            fputcsv($handle, ['SISTEM IZINYOU - CV PRASTA SOLUSI INDONESIA']);
            fputcsv($handle, ['Tanggal Cetak:', date('d-m-Y H:i:s')]);
            fputcsv($handle, []);

            fputcsv($handle, [
                'No.',
                'No. Order Referensi',
                'Perusahaan Mitra',
                'Nama PIC',
                'Berkas Teknis Diuji',
                'Status Verifikasi Mutu',
                'Catatan Rekomendasi Legal/Teknis',
                'Tanggal Unggah',
            ]);

            $no = 1;
            foreach ($documents as $doc) {
                fputcsv($handle, [
                    $no++,
                    $doc->order_number ?? '-',
                    $doc->company_name ?? '-',
                    $doc->client_name ?? '-',
                    basename($doc->file_path),
                    $doc->status === 'approved' ? 'Valid / Disetujui' : ($doc->status === 'rejected' ? 'Perlu Revisi' : 'Menunggu Review'),
                    $doc->notes ?? 'Sesuai ketentuan SOP',
                    date('d/m/Y H:i', strtotime($doc->created_at)),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
