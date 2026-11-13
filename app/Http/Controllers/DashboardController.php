<?php

namespace App\Http\Controllers;

use App\Models\ClientDocument;
use App\Models\FreeConsultation;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderProgressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Redirect general /dashboard to specific role dashboard.
     */
    public function index(): RedirectResponse
    {
        $user = Auth::user();

        return redirect()->route($user->getDashboardRoute());
    }

    /**
     * Dashboard untuk Administrator.
     */
    public function adminDashboard(): View
    {
        $user = Auth::user();

        $stats = [
            'totalClients' => User::where('role', 'klien')->count(),
            'totalConsultants' => User::where('role', 'konsultan')->count(),
            'totalServices' => DB::table('services')->count(),
            'totalOrders' => DB::table('orders')->count(),
            'pendingDocs' => DB::table('client_documents')->where('status', 'uploaded')->count(),
            'pendingConsultations' => DB::table('free_consultations')->where('status', 'pending')->count(),
            'totalRevenue' => DB::table('orders')->whereIn('status', ['processing', 'completed', 'verified'])->sum('total_cost'),
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
     * Dashboard untuk Klien.
     */
    public function klienDashboard(Request $request, OrderProgressService $progressService): View
    {
        $user = Auth::user();

        $myOrders = Order::with(['service.category', 'invoice', 'latestPayment', 'trackings', 'documents.requirement', 'review'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // Saring pesanan untuk Modul Pelacakan Progres Proyek & Sertifikasi:
        // Pesanan/proyek yang dibatalkan atau status pembayarannya "Dibatalkan"
        // (seperti Surat Keterangan Impor BPOM yang sudah dibatalkan) otomatis disaring
        // dan TIDAK DITAMPILKAN di data pelacakan progres maupun dropdown pilihan proyek.
        $trackableOrders = $myOrders->reject(function (Order $order): bool {
            if ($order->status === 'cancelled') {
                return true;
            }

            $paymentMeta = $order->paymentStatusMeta();
            if (($paymentMeta['code'] ?? '') === 'cancelled' || strtolower((string) ($paymentMeta['label'] ?? '')) === 'dibatalkan') {
                return true;
            }

            if ($order->invoice && strtolower((string) $order->invoice->payment_status) === 'cancelled') {
                return true;
            }

            return false;
        })->values();

        $selectedOrderId = $request->query('order_id');
        $activeOrder = $selectedOrderId
            ? ($trackableOrders->firstWhere('id', (int) $selectedOrderId) ?: $trackableOrders->first())
            : $trackableOrders->first();

        // Pastikan activeOrder hanya merujuk pada pesanan yang valid untuk dilacak
        if ($activeOrder && ! $trackableOrders->contains('id', $activeOrder->id)) {
            $activeOrder = $trackableOrders->first();
        }

        $myTrackings = $activeOrder ? $activeOrder->trackings : collect();
        $timeline = $activeOrder ? $progressService->buildTimeline($activeOrder) : [];

        $myDocuments = ClientDocument::with(['order', 'requirement', 'versions'])
            ->whereHas('order', fn ($q) => $q->where('user_id', $user->id))
            ->latest()
            ->get();

        $myConsultations = FreeConsultation::where('user_id', $user->id)
            ->latest()
            ->get();

        $stats = [
            'ordersCount' => $myOrders->count(),
            'approvedDocsCount' => $myDocuments->where('status', 'approved')->count(),
            'pendingDocsCount' => $myDocuments->where('status', 'uploaded')->count(),
            'revisionDocsCount' => $myDocuments->where('status', 'rejected')->count(),
            'consultationsCount' => $myConsultations->count(),
            'consultationsPendingCount' => $myConsultations->where('status', 'pending')->count(),
            'consultationsRespondedCount' => $myConsultations->where('status', 'responded')->count(),
        ];

        return view('klien.dashboard', compact(
            'user',
            'stats',
            'myOrders',
            'trackableOrders',
            'activeOrder',
            'myTrackings',
            'timeline',
            'myDocuments'
        ));
    }

    /**
     * Halaman terpisah Konsultasi Perizinan Klien (pengajuan baru, riwayat, status, & tanggapan tim).
     */
    public function klienConsultations(): View
    {
        $user = Auth::user();
        $myConsultations = FreeConsultation::where('user_id', $user->id)
            ->latest()
            ->get();

        $consultationStats = [
            'total' => $myConsultations->count(),
            'pending' => $myConsultations->where('status', 'pending')->count(),
            'responded' => $myConsultations->where('status', 'responded')->count(),
        ];

        return view('klien.consultations', compact('user', 'myConsultations', 'consultationStats'));
    }

    /**
     * Klien mengajukan konsultasi baru.
     */
    public function submitConsultation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:2000',
        ]);

        $user = Auth::user();

        FreeConsultation::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'subject' => trim($validated['subject']),
            'message' => trim($validated['message']),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Pertanyaan konsultasi berhasil diajukan. Tim konsultan kami akan segera merespons.');
    }
}
