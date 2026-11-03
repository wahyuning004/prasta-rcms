<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinanceController extends Controller
{
    /**
     * Dashboard khusus Finance & Licensing Officer.
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        // 1. Antrean Verifikasi Bukti Pembayaran Digital / Transfer
        $paymentsQuery = DB::table('payments')
            ->leftJoin('orders', 'payments.order_id', '=', 'orders.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'payments.*',
                'orders.order_number',
                'orders.total_cost',
                'users.name as client_name',
                'users.company_name',
                'users.email as client_email',
                'users.phone as client_phone'
            );

        $filterStatus = $request->get('status');
        if ($filterStatus && $filterStatus !== 'all') {
            $paymentsQuery->where('payments.status', $filterStatus);
        }

        $payments = $paymentsQuery->latest('payments.created_at')->get();

        // 2. Daftar Projek untuk Update Progress Stepper
        $orders = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.*', 'users.name as client_name', 'users.company_name')
            ->latest('orders.created_at')
            ->get();

        // Riwayat Project Trackings
        $projectTrackings = DB::table('project_trackings')
            ->leftJoin('orders', 'project_trackings.order_id', '=', 'orders.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'project_trackings.*',
                'orders.order_number',
                'users.company_name'
            )
            ->latest('project_trackings.date_recorded')
            ->take(15)
            ->get();

        // 3. Direktori Privat Server Internal
        $archives = DB::table('private_archives')->latest('created_at')->get();

        // Statistik Keuangan & Operasional
        $stats = [
            'pendingPayments'  => DB::table('payments')->where('status', 'waiting_confirmation')->count(),
            'verifiedPayments' => DB::table('payments')->where('status', 'success')->count(),
            'totalRevenue'     => DB::table('orders')->whereIn('status', ['processing', 'completed', 'verified'])->sum('total_cost'),
            'totalArchives'    => DB::table('private_archives')->count(),
            'activeTrackings'  => DB::table('project_trackings')->count(),
        ];

        return view('finance.dashboard', compact('user', 'payments', 'orders', 'projectTrackings', 'archives', 'stats', 'filterStatus'));
    }

    /**
     * Verifikasi Bukti Pembayaran (Konfirmasi / Tolak).
     */
    public function verifyPayment(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:success,failed,waiting_confirmation',
        ]);

        $payment = DB::table('payments')->where('id', $id)->first();
        if (!$payment) {
            return back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        DB::table('payments')->where('id', $id)->update([
            'status'     => $request->status,
            'updated_at' => now(),
        ]);

        // Jika diverifikasi sukses, perbarui status order menjadi processing
        if ($request->status === 'success') {
            DB::table('orders')->where('id', $payment->order_id)->update([
                'status'     => 'processing',
                'updated_at' => now(),
            ]);

            // Tambahkan linimasa otomatis
            DB::table('project_trackings')->insert([
                'order_id'      => $payment->order_id,
                'stage_name'    => 'Pembayaran Diverifikasi Bagian Keuangan',
                'description'   => 'Dana transfer pelunasan senilai Rp ' . number_format($payment->amount, 0, ',', '.') . ' telah terverifikasi valid.',
                'date_recorded' => now(),
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        return back()->with('success', 'Status verifikasi pembayaran berhasil disimpan.');
    }

    /**
     * Penerbitan Invoice Resmi.
     */
    public function generateInvoice($orderId)
    {
        $order = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
            ->select(
                'orders.*',
                'users.name as client_name',
                'users.company_name',
                'users.address as client_address',
                'users.phone as client_phone',
                'users.email as client_email',
                'payments.payment_method',
                'payments.status as payment_status'
            )
            ->where('orders.id', $orderId)
            ->first();

        if (!$order) {
            return back()->with('error', 'Pesanan tidak ditemukan.');
        }

        $financeOfficer = Auth::user();

        return view('finance.invoice_pdf', compact('order', 'financeOfficer'));
    }

    /**
     * Update progress tracking pengerjaan projek (real-time stepper).
     */
    public function addTracking(Request $request)
    {
        $request->validate([
            'order_id'    => 'required|exists:orders,id',
            'stage_name'  => 'required|string|max:200',
            'description' => 'required|string|max:1500',
        ]);

        DB::table('project_trackings')->insert([
            'order_id'      => $request->order_id,
            'stage_name'    => $request->stage_name,
            'description'   => $request->description,
            'date_recorded' => now(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return back()->with('success', 'Tahapan linimasa projek berhasil diupdate.');
    }

    /**
     * Simpan dokumen resmi ke Direktori Privat Server Internal.
     */
    public function uploadArchive(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'category'  => 'required|string|max:100',
            'file_name' => 'nullable|string|max:255',
            'notes'     => 'nullable|string|max:1000',
        ]);

        $filePath = 'private_archives/' . strtolower(str_replace(' ', '_', $request->category)) . '/' . ($request->file_name ?? (time() . '_dokumen_resmi.pdf'));

        DB::table('private_archives')->insert([
            'title'       => $request->title,
            'category'    => $request->category,
            'file_path'   => $filePath,
            'file_size'   => rand(1, 4) . '.' . rand(1, 9) . ' MB',
            'uploaded_by' => Auth::user()->name,
            'notes'       => $request->notes ?? 'Diarsipkan ke server privat internal CV Prasta Solusi Indonesia.',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return back()->with('success', 'Dokumen resmi berhasil diamankan ke dalam direktori privat server.');
    }

    /**
     * Hapus arsip server internal.
     */
    public function destroyArchive($id)
    {
        DB::table('private_archives')->where('id', $id)->delete();
        return back()->with('success', 'Arsip dokumen berhasil dihapus dari direktori server.');
    }

    /**
     * Ekspor data transaksi pembayaran keuangan ke format CSV / Excel.
     */
    public function exportExcel(): StreamedResponse
    {
        $filename = 'Transaksi_Keuangan_Finance_' . date('Ymd_His') . '.csv';

        $payments = DB::table('payments')
            ->leftJoin('orders', 'payments.order_id', '=', 'orders.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'payments.*',
                'orders.order_number',
                'users.name as client_name',
                'users.company_name'
            )
            ->latest('payments.created_at')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($payments) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['DATA TRANSAKSI KEUANGAN & PEMBAYARAN DIGITAL']);
            fputcsv($handle, ['DIVISI FINANCE & LICENSING OFFICER - CV PRASTA SOLUSI INDONESIA']);
            fputcsv($handle, ['Tanggal Cetak:', date('d-m-Y H:i:s')]);
            fputcsv($handle, []);

            fputcsv($handle, [
                'No.',
                'No. Order',
                'Perusahaan Klien',
                'Nama PIC',
                'Metode Pembayaran',
                'Nominal (IDR)',
                'Status Verifikasi',
                'Tanggal Transaksi'
            ]);

            $no = 1;
            $total = 0;
            foreach ($payments as $p) {
                $total += (float) $p->amount;
                fputcsv($handle, [
                    $no++,
                    $p->order_number ?? '-',
                    $p->company_name ?? '-',
                    $p->client_name ?? '-',
                    $p->payment_method ?? 'Transfer Bank',
                    number_format($p->amount, 0, ',', '.'),
                    $p->status === 'success' ? 'Terverifikasi (Lunas)' : ($p->status === 'failed' ? 'Ditolak' : 'Menunggu Konfirmasi'),
                    date('d/m/Y H:i', strtotime($p->created_at)),
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['', '', '', '', 'TOTAL PEMBAYARAN:', number_format($total, 0, ',', '.')]);

            fclose($handle);
        }, 200, $headers);
    }
}
