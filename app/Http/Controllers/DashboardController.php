<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Redirect general /dashboard to specific role dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        return redirect()->route($user->getDashboardRoute());
    }

    /**
     * Dashboard untuk Administrator.
     */
    public function adminDashboard()
    {
        $user = Auth::user();

        $stats = [
            'totalClients'        => User::where('role', 'klien')->count(),
            'totalConsultants'    => User::where('role', 'konsultan')->count(),
            'totalServices'       => DB::table('services')->count(),
            'totalOrders'         => DB::table('orders')->count(),
            'pendingDocs'         => DB::table('client_documents')->where('status', 'uploaded')->count(),
            'pendingConsultations'=> DB::table('free_consultations')->where('status', 'pending')->count(),
            'totalRevenue'        => DB::table('orders')->whereIn('status', ['processing', 'completed'])->sum('total_cost'),
        ];

        $recentOrders = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.*', 'users.name as client_name', 'users.company_name')
            ->latest('orders.created_at')
            ->take(5)
            ->get();

        $services = DB::table('services')->take(6)->get();

        $consultations = DB::table('free_consultations')
            ->leftJoin('users', 'free_consultations.user_id', '=', 'users.id')
            ->select('free_consultations.*', 'users.name as user_name', 'users.company_name')
            ->latest('free_consultations.created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('user', 'stats', 'recentOrders', 'services', 'consultations'));
    }

    /**
     * Redirect konsultan to their specific role dashboard (marketing, legal, finance).
     */
    public function konsultanDashboard()
    {
        $user = Auth::user();
        return redirect()->route($user->getDashboardRoute());
    }

    /**
     * Dashboard untuk Klien.
     */
    public function klienDashboard()
    {
        $user = Auth::user();

        $myOrders = DB::table('orders')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $activeOrder = $myOrders->first();

        $myTrackings = collect();
        $myDocuments = collect();

        if ($activeOrder) {
            $myTrackings = DB::table('project_trackings')
                ->where('order_id', $activeOrder->id)
                ->orderBy('date_recorded', 'asc')
                ->get();

            $myDocuments = DB::table('client_documents')
                ->where('order_id', $activeOrder->id)
                ->latest()
                ->get();
        }

        $myConsultations = DB::table('free_consultations')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $services = DB::table('services')->get();
        $promos = DB::table('promos')->where('is_active', true)->get();

        $stats = [
            'ordersCount'        => $myOrders->count(),
            'approvedDocsCount'  => $myDocuments->where('status', 'approved')->count(),
            'pendingDocsCount'   => $myDocuments->where('status', 'uploaded')->count(),
            'consultationsCount' => $myConsultations->count(),
        ];

        return view('klien.dashboard', compact('user', 'stats', 'myOrders', 'activeOrder', 'myTrackings', 'myDocuments', 'myConsultations', 'services', 'promos'));
    }

    /**
     * Klien memesan layanan dengan kode kupon diskon.
     */
    public function checkoutOrder(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'promo_code' => 'nullable|string|max:50',
        ]);

        $service = DB::table('services')->where('id', $request->service_id)->first();
        $basePrice = (float) ($service->price ?? $service->cost ?? 0);
        if ($basePrice <= 0) {
            $basePrice = 3500000.00;
        }

        $discount = 0;
        $promoId = null;

        if (!empty($request->promo_code)) {
            $codeUpper = strtoupper(trim($request->promo_code));
            $promo = DB::table('promos')
                ->where('code', $codeUpper)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expired_date')->orWhere('expired_date', '>=', now()->toDateString());
                })
                ->first();

            if ($promo) {
                if ($promo->quota > 0 && $promo->used_count >= $promo->quota) {
                    return back()->with('error', 'Maaf, batas kuota transaksi kupon ' . $promo->code . ' telah habis.');
                }

                if ($promo->discount_amount > 0) {
                    $discount = (float) $promo->discount_amount;
                } elseif ($promo->discount_percentage > 0) {
                    $discount = $basePrice * ((float) $promo->discount_percentage / 100);
                }

                $promoId = $promo->id;
                DB::table('promos')->where('id', $promo->id)->increment('used_count');
            } else {
                return back()->with('error', 'Kode kupon promo "' . $request->promo_code . '" tidak valid atau telah kedaluwarsa.');
            }
        }

        $finalPrice = max(0, $basePrice - $discount);
        $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        $orderId = DB::table('orders')->insertGetId([
            'order_number' => $orderNumber,
            'user_id'      => Auth::id(),
            'promo_id'     => $promoId,
            'total_cost'   => $finalPrice,
            'status'       => 'pending',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // Catat tahapan pelacakan awal
        DB::table('project_trackings')->insert([
            'order_id'      => $orderId,
            'stage_name'    => 'Pemesanan Layanan Dibuat',
            'description'   => 'Pemesanan layanan ' . $service->name . ' telah diterima sistem. Menunggu upload berkas dan konfirmasi pembayaran.',
            'date_recorded' => now(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $successMsg = 'Pemesanan layanan ' . $service->name . ' berhasil diajukan dengan nomor referensi ' . $orderNumber . '.';
        if ($discount > 0) {
            $successMsg .= ' Kupon ' . $request->promo_code . ' berhasil memotong biaya sebesar Rp ' . number_format($discount, 0, ',', '.') . '!';
        }

        return back()->with('success', $successMsg);
    }

    /**
     * Konsultan update verifikasi dokumen (Approve / Reject).
     */
    public function verifyDocument(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,uploaded',
            'notes'  => 'nullable|string|max:500',
        ]);

        DB::table('client_documents')->where('id', $id)->update([
            'status'     => $request->status,
            'notes'      => $request->notes ?? ($request->status === 'approved' ? 'Dokumen valid dan disetujui.' : 'Dokumen perlu diperbaiki.'),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Status verifikasi dokumen berhasil diperbarui.');
    }

    /**
     * Konsultan menambah tahapan pelacakan projek (Project Tracking).
     */
    public function addTracking(Request $request)
    {
        $request->validate([
            'order_id'    => 'required|exists:orders,id',
            'stage_name'  => 'required|string|max:100',
            'description' => 'required|string|max:1000',
        ]);

        DB::table('project_trackings')->insert([
            'order_id'      => $request->order_id,
            'stage_name'    => $request->stage_name,
            'description'   => $request->description,
            'date_recorded' => now(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return back()->with('success', 'Tahapan pelacakan projek berhasil ditambahkan.');
    }

    /**
     * Konsultan menjawab konsultasi gratis.
     */
    public function answerConsultation(Request $request, $id)
    {
        $request->validate([
            'response' => 'required|string|max:2000',
        ]);

        DB::table('free_consultations')->where('id', $id)->update([
            'response'   => $request->response,
            'status'     => 'responded',
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Tanggapan konsultasi berhasil dikirim ke klien.');
    }

    /**
     * Klien mengajukan konsultasi baru.
     */
    public function submitConsultation(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:2000',
        ]);

        DB::table('free_consultations')->insert([
            'user_id'    => Auth::id(),
            'subject'    => $request->subject,
            'message'    => $request->message,
            'attachment' => null,
            'response'   => null,
            'status'     => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Pertanyaan konsultasi berhasil diajukan. Tim konsultan kami akan segera merespons.');
    }
}
