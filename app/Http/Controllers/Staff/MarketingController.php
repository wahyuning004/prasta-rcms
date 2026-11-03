<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MarketingController extends Controller
{
    /**
     * Dashboard khusus Marketing & Client Relations.
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        // 1. Antrean Tiket Konsultasi Gratis & Live Chat
        $consultationsQuery = DB::table('free_consultations')
            ->leftJoin('users', 'free_consultations.user_id', '=', 'users.id')
            ->select(
                'free_consultations.*',
                'users.name as client_name',
                'users.company_name',
                'users.phone',
                'users.email'
            );

        $statusFilter = $request->get('status');
        if ($statusFilter && $statusFilter !== 'all') {
            $consultationsQuery->where('free_consultations.status', $statusFilter);
        }

        $consultations = $consultationsQuery->latest('free_consultations.created_at')->get();

        // 2. Manajemen Kode Kupon / Promo Diskon & Batas Kuota Transaksi
        $promos = DB::table('promos')->latest('created_at')->get();

        // 3. Moderasi & Publikasi Ulasan / Rating Bintang Klien
        $reviews = DB::table('reviews')
            ->leftJoin('users', 'reviews.user_id', '=', 'users.id')
            ->leftJoin('orders', 'reviews.order_id', '=', 'orders.id')
            ->select(
                'reviews.*',
                'users.name as client_name',
                'users.company_name',
                'orders.order_number'
            )
            ->latest('reviews.created_at')
            ->get();

        // Statistik Marketing
        $stats = [
            'pendingConsultations' => DB::table('free_consultations')->where('status', 'pending')->count(),
            'totalConsultations'   => DB::table('free_consultations')->count(),
            'activePromos'         => DB::table('promos')->where('is_active', true)->count(),
            'totalPromosUsed'      => DB::table('promos')->sum('used_count'),
            'totalReviews'         => DB::table('reviews')->count(),
            'publishedReviews'     => DB::table('reviews')->where('status', 'published')->count(),
            'avgRating'            => round(DB::table('reviews')->avg('rating') ?? 5.0, 1),
        ];

        return view('marketing.dashboard', compact('user', 'consultations', 'promos', 'reviews', 'stats', 'statusFilter'));
    }

    /**
     * Jawab konsultasi klien & respons tiket.
     */
    public function replyConsultation(Request $request, $id)
    {
        $request->validate([
            'response' => 'required|string|max:3000',
        ]);

        DB::table('free_consultations')->where('id', $id)->update([
            'response'   => $request->response,
            'status'     => 'responded',
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Tanggapan konsultasi berhasil dikirimkan kepada calon klien mitra.');
    }

    /**
     * Tambah kode kupon promo baru dengan kuota transaksi.
     */
    public function storePromo(Request $request)
    {
        $request->validate([
            'code'                => 'required|string|max:50|unique:promos,code',
            'discount_type'       => 'required|in:nominal,percentage',
            'discount_value'      => 'required|numeric|min:1',
            'quota'               => 'required|integer|min:1',
            'expired_date'        => 'nullable|date|after_or_equal:today',
        ], [
            'code.required'       => 'Kode promo wajib diisi.',
            'code.unique'         => 'Kode promo ini sudah ada dalam sistem.',
            'discount_value.min'  => 'Nilai diskon harus lebih besar dari 0.',
            'quota.min'           => 'Batas kuota transaksi minimal 1.',
        ]);

        $isNominal = $request->discount_type === 'nominal';

        DB::table('promos')->insert([
            'code'                => strtoupper(trim($request->code)),
            'discount_amount'     => $isNominal ? $request->discount_value : 0.00,
            'discount_percentage' => !$isNominal ? $request->discount_value : null,
            'quota'               => $request->quota,
            'used_count'          => 0,
            'is_active'           => true,
            'expired_date'        => $request->expired_date,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        return back()->with('success', 'Kode kupon promo ' . strtoupper($request->code) . ' berhasil diterbitkan dengan kuota ' . $request->quota . ' transaksi.');
    }

    /**
     * Perbarui kuota dan status kupon promo.
     */
    public function updatePromo(Request $request, $id)
    {
        $request->validate([
            'quota'     => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ]);

        DB::table('promos')->where('id', $id)->update([
            'quota'      => $request->quota,
            'is_active'  => $request->is_active,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Pengaturan kupon promo berhasil diperbarui.');
    }

    /**
     * Hapus kupon promo.
     */
    public function destroyPromo($id)
    {
        DB::table('promos')->where('id', $id)->delete();
        return back()->with('success', 'Kupon promo berhasil dihapus dari sistem.');
    }

    /**
     * Moderasi ulasan klien (Terbitkan / Batalkan / Unggulkan).
     */
    public function moderateReview(Request $request, $id)
    {
        $request->validate([
            'status'      => 'required|in:published,pending,rejected',
            'is_featured' => 'nullable|boolean',
        ]);

        DB::table('reviews')->where('id', $id)->update([
            'status'      => $request->status,
            'is_featured' => $request->boolean('is_featured'),
            'updated_at'  => now(),
        ]);

        return back()->with('success', 'Status moderasi ulasan berhasil diperbarui.');
    }
}
