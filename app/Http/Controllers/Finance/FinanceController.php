<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Concerns\ManagesUserProfile;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\Services\FinancialReportExcelService;
use App\Services\FinancialReportPdfService;
use App\Services\InvoicePdfService;
use App\Services\OrderProgressService;
use App\Services\TwoWayPaymentVerificationService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinanceController extends Controller
{
    use ManagesUserProfile;

    /**
     * Dashboard Utama Keuangan & Administrasi (Finance & Administration).
     * Fokus pada metrik perputaran kas, antrean verifikasi pembayaran,
     * dan ringkasan status pembayaran pelanggan.
     */
    public function dashboard(Request $request): View
    {
        $user = Auth::user();
        $stats = $this->buildFinanceStats();

        // 1. Antrean Verifikasi Pembayaran Cepat (5 Pembayaran Menunggu Konfirmasi Terkini)
        $verificationService = app(TwoWayPaymentVerificationService::class);
        $pendingPaymentModels = Payment::with(['order.user', 'invoice'])
            ->where('status', 'waiting_confirmation')
            ->latest('created_at')
            ->take(6)
            ->get();

        $pendingPayments = $pendingPaymentModels->map(function (Payment $p) use ($verificationService) {
            $p->verification = $verificationService->verify($p);
            $p->order_number = $p->order?->order_number;
            $p->total_cost = $p->order?->total_cost;
            $p->client_name = $p->order?->user?->name;
            $p->company_name = $p->order?->user?->company_name;
            $p->client_email = $p->order?->user?->email;
            $p->client_phone = $p->order?->user?->phone;

            return $p;
        });

        // 2. Monitoring Transaksi & Status Pembayaran Pelanggan Terkini (8 Transaksi Terakhir)
        $recentTransactions = Order::with(['user', 'service', 'invoice', 'latestPayment', 'trackings', 'paymentLogs'])
            ->latest('created_at')
            ->take(8)
            ->get();

        // 3. Distribusi Metode Pembayaran
        $paymentMethods = DB::table('payments')
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total_amount'))
            ->groupBy('payment_method')
            ->orderByDesc('count')
            ->get();

        // Kompatibilitas mundur variabel view lama
        $filterStatus = $request->get('status', 'all');
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

        if ($filterStatus && $filterStatus !== 'all') {
            $paymentsQuery->where('payments.status', $filterStatus);
        }
        $payments = $paymentsQuery->latest('payments.created_at')->take(15)->get();

        $orders = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.*', 'users.name as client_name', 'users.company_name')
            ->latest('orders.created_at')
            ->take(15)
            ->get();

        $projectTrackings = DB::table('project_trackings')
            ->leftJoin('orders', 'project_trackings.order_id', '=', 'orders.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select('project_trackings.*', 'orders.order_number', 'users.company_name')
            ->latest('project_trackings.date_recorded')
            ->take(10)
            ->get();

        $archives = DB::table('private_archives')->latest('created_at')->take(10)->get();
        $stages = OrderProgressService::stages();

        return view('finance.dashboard', compact(
            'user',
            'stats',
            'pendingPayments',
            'recentTransactions',
            'paymentMethods',
            'payments',
            'orders',
            'projectTrackings',
            'archives',
            'filterStatus',
            'stages'
        ));
    }

    /**
     * Halaman Khusus Antrean Verifikasi Bukti Pembayaran Digital / Transfer.
     */
    public function payments(Request $request, TwoWayPaymentVerificationService $verificationService): View
    {
        $user = Auth::user();
        $stats = $this->buildFinanceStats();

        $filterStatus = $request->get('status', 'all');
        $searchTerm = trim((string) $request->get('search', ''));

        $paymentsQuery = Payment::with(['order.user', 'invoice']);

        if ($filterStatus && $filterStatus !== 'all') {
            $paymentsQuery->where('status', $filterStatus);
        }

        if ($searchTerm !== '') {
            $paymentsQuery->where(function ($q) use ($searchTerm) {
                $q->where('payment_method', 'like', "%{$searchTerm}%")
                    ->orWhere('bank_name', 'like', "%{$searchTerm}%")
                    ->orWhere('sender_name', 'like', "%{$searchTerm}%")
                    ->orWhereHas('order', function ($orderQuery) use ($searchTerm) {
                        $orderQuery->where('order_number', 'like', "%{$searchTerm}%")
                            ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                                $userQuery->where('name', 'like', "%{$searchTerm}%")
                                    ->orWhere('company_name', 'like', "%{$searchTerm}%")
                                    ->orWhere('email', 'like', "%{$searchTerm}%")
                                    ->orWhere('phone', 'like', "%{$searchTerm}%");
                            });
                    });
            });
        }

        $paymentModels = $paymentsQuery->latest('created_at')->get();

        $payments = $paymentModels->map(function (Payment $p) use ($verificationService) {
            $p->verification = $verificationService->verify($p);
            $p->order_number = $p->order?->order_number;
            $p->total_cost = $p->order?->total_cost;
            $p->client_name = $p->order?->user?->name;
            $p->company_name = $p->order?->user?->company_name;
            $p->client_email = $p->order?->user?->email;
            $p->client_phone = $p->order?->user?->phone;

            return $p;
        });

        $orders = DB::table('orders')->latest('created_at')->take(20)->get();
        $archives = DB::table('private_archives')->latest('created_at')->take(10)->get();

        return view('finance.payments', compact(
            'user',
            'payments',
            'stats',
            'filterStatus',
            'searchTerm',
            'orders',
            'archives'
        ));
    }

    /**
     * Endpoint Audit Forensik & Verifikasi Dua Arah Bukti Transfer (JSON).
     */
    public function paymentAudit(int $id, TwoWayPaymentVerificationService $service): JsonResponse
    {
        $payment = Payment::with(['order.user', 'invoice'])->findOrFail($id);
        $result = $service->verify($payment);

        return response()->json($result);
    }

    /**
     * Halaman Khusus Pengelolaan & Penerbitan Invoice Tagihan Transaksi Layanan.
     */
    public function invoices(Request $request): View
    {
        $user = Auth::user();
        $stats = $this->buildFinanceStats();

        $filterStatus = $request->get('status', 'all');
        $searchTerm = trim((string) $request->get('search', ''));
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $invoicesQuery = Invoice::with(['order.user', 'order.service', 'order.trackings', 'order.paymentLogs', 'payments']);

        if ($filterStatus && $filterStatus !== 'all') {
            if ($filterStatus === 'paid') {
                $invoicesQuery->whereIn('payment_status', ['paid', 'success']);
            } elseif ($filterStatus === 'unpaid') {
                $invoicesQuery->where('payment_status', 'unpaid');
            } elseif ($filterStatus === 'overdue') {
                $invoicesQuery->whereNotIn('payment_status', ['paid', 'success'])
                    ->whereNotNull('due_date')
                    ->where('due_date', '<', now()->startOfDay());
            } elseif ($filterStatus === 'cancelled') {
                $invoicesQuery->where(function (Builder $q) {
                    $q->where('payment_status', 'cancelled')
                        ->orWhereHas('order', fn (Builder $oq) => $oq->where('status', 'cancelled'));
                });
            } else {
                $invoicesQuery->where('payment_status', $filterStatus);
            }
        }

        if ($searchTerm !== '') {
            $invoicesQuery->where(function (Builder $q) use ($searchTerm) {
                $q->where('invoice_number', 'like', "%{$searchTerm}%")
                    ->orWhere('customer_name', 'like', "%{$searchTerm}%")
                    ->orWhere('customer_company', 'like', "%{$searchTerm}%")
                    ->orWhere('customer_email', 'like', "%{$searchTerm}%")
                    ->orWhere('customer_phone', 'like', "%{$searchTerm}%")
                    ->orWhere('service_name', 'like', "%{$searchTerm}%")
                    ->orWhereHas('order', function (Builder $oq) use ($searchTerm) {
                        $oq->where('order_number', 'like', "%{$searchTerm}%");
                    });
            });
        }

        if (! empty($startDate)) {
            $invoicesQuery->whereDate('issued_at', '>=', $startDate);
        }
        if (! empty($endDate)) {
            $invoicesQuery->whereDate('issued_at', '<=', $endDate);
        }

        $invoices = $invoicesQuery->latest('issued_at')->paginate(15)->withQueryString();

        // Pesanan yang belum memiliki invoice (untuk modal penerbitan invoice baru)
        $ordersWithoutInvoice = Order::with(['user', 'service'])
            ->whereDoesntHave('invoice')
            ->latest('created_at')
            ->take(30)
            ->get();

        // Ringkasan metrik invoice
        $invoiceMetrics = [
            'totalBilled' => (float) Invoice::sum('total_amount'),
            'totalCollected' => (float) Invoice::whereIn('payment_status', ['paid', 'success'])->sum('total_amount'),
            'totalUnpaid' => (float) Invoice::whereIn('payment_status', ['unpaid', 'waiting_verification'])->sum('total_amount'),
            'overdueCount' => Invoice::whereNotIn('payment_status', ['paid', 'success'])
                ->whereNotNull('due_date')
                ->where('due_date', '<', now()->startOfDay())
                ->count(),
        ];

        return view('finance.invoices', compact(
            'user',
            'stats',
            'invoices',
            'ordersWithoutInvoice',
            'invoiceMetrics',
            'filterStatus',
            'searchTerm',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Menerbitkan Tagihan Invoice Baru untuk Pesanan Layanan.
     */
    public function issueInvoice(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'due_date' => ['nullable', 'date'],
            'invoice_number' => ['nullable', 'string', 'max:50', 'unique:invoices,invoice_number'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'order_id.required' => 'Pilih pesanan layanan yang akan diterbitkan tagihannya.',
            'invoice_number.unique' => 'Nomor invoice tersebut sudah digunakan oleh transaksi lain.',
        ]);

        $order = Order::with(['user', 'service', 'invoice'])->findOrFail($validated['order_id']);

        if ($order->invoice) {
            $order->invoice->update([
                'due_date' => $validated['due_date'] ?? $order->invoice->due_date,
            ]);

            return redirect()->route('finance.invoices')
                ->with('success', "Invoice {$order->invoice->invoice_number} sudah aktif dan data tagihan berhasil diperbarui.");
        }

        $invoice = $this->createInvoiceForOrder(
            $order,
            $validated['due_date'] ?? null,
            $validated['invoice_number'] ?? null
        );

        return redirect()->route('finance.invoices')
            ->with('success', "Invoice tagihan resmi {$invoice->invoice_number} untuk pesanan {$order->order_number} berhasil diterbitkan.");
    }

    /**
     * Perbarui Status Pembayaran pada Invoice Tagihan.
     */
    public function updateInvoiceStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'payment_status' => ['required', 'in:unpaid,paid,waiting_verification,cancelled'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $invoice = Invoice::with(['order', 'order.latestPayment'])->findOrFail($id);
        if ($invoice->payment_status === 'cancelled' || ($invoice->order && $invoice->order->isCancelled())) {
            return back()->with('error', 'Invoice untuk pesanan yang telah dibatalkan tidak dapat diubah.');
        }
        $oldStatus = $invoice->payment_status;
        $newStatus = $validated['payment_status'];

        DB::transaction(function () use ($invoice, $newStatus, $validated, $oldStatus): void {
            $invoice->update([
                'payment_status' => $newStatus,
            ]);

            $order = $invoice->order;
            if ($order) {
                if ($newStatus === 'paid') {
                    if ($order->status === 'pending') {
                        $order->update(['status' => 'processing']);
                    }
                    if ($order->latestPayment && $order->latestPayment->status !== 'success') {
                        $order->latestPayment->update([
                            'status' => 'success',
                            'paid_at' => now(),
                            'verified_by' => Auth::id(),
                            'verified_at' => now(),
                        ]);
                    }
                } elseif ($newStatus === 'cancelled') {
                    $order->update(['status' => 'cancelled']);
                }
            }

            PaymentLog::create([
                'order_id' => $invoice->order_id,
                'payment_id' => $order?->latestPayment?->id,
                'status' => $newStatus,
                'notes' => "Status invoice #{$invoice->invoice_number} diubah dari [{$oldStatus}] menjadi [{$newStatus}] oleh Finance. ".($validated['notes'] ?? ''),
                'changed_by' => Auth::id(),
            ]);
        });

        return back()->with('success', "Status invoice #{$invoice->invoice_number} berhasil diperbarui menjadi {$invoice->statusLabel()}.");
    }

    /**
     * Perbarui Batas Jatuh Tempo Tagihan Invoice.
     */
    public function updateInvoiceDueDate(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'due_date' => ['required', 'date'],
        ]);

        $invoice = Invoice::with('order')->findOrFail($id);
        if ($invoice->payment_status === 'cancelled' || ($invoice->order && $invoice->order->isCancelled())) {
            return back()->with('error', 'Invoice untuk pesanan yang telah dibatalkan tidak dapat diubah.');
        }
        $invoice->update([
            'due_date' => $validated['due_date'],
        ]);

        return back()->with('success', "Batas jatuh tempo invoice #{$invoice->invoice_number} berhasil diubah menjadi ".date('d M Y', strtotime($validated['due_date'])).'.');
    }

    /**
     * Halaman Khusus Monitoring Transaksi & Status Pembayaran Pelanggan.
     */
    public function transactions(Request $request): View
    {
        $user = Auth::user();
        $stats = $this->buildFinanceStats();

        $filterPaymentStatus = $request->get('payment_status', 'all');
        $filterMethod = $request->get('payment_method', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $searchTerm = trim((string) $request->get('search', ''));

        $ordersQuery = Order::with(['user', 'service.category', 'invoice', 'latestPayment', 'payments', 'trackings', 'paymentLogs']);

        if ($searchTerm !== '') {
            $ordersQuery->where(function (Builder $q) use ($searchTerm) {
                $q->where('order_number', 'like', "%{$searchTerm}%")
                    ->orWhere('promo_code', 'like', "%{$searchTerm}%")
                    ->orWhereHas('user', function (Builder $userQuery) use ($searchTerm) {
                        $userQuery->where('name', 'like', "%{$searchTerm}%")
                            ->orWhere('company_name', 'like', "%{$searchTerm}%")
                            ->orWhere('email', 'like', "%{$searchTerm}%")
                            ->orWhere('phone', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('service', function (Builder $serviceQuery) use ($searchTerm) {
                        $serviceQuery->where('name', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('invoice', function (Builder $invoiceQuery) use ($searchTerm) {
                        $invoiceQuery->where('invoice_number', 'like', "%{$searchTerm}%");
                    });
            });
        }

        if (! empty($startDate)) {
            $ordersQuery->whereDate('created_at', '>=', $startDate);
        }
        if (! empty($endDate)) {
            $ordersQuery->whereDate('created_at', '<=', $endDate);
        }

        if ($filterMethod && $filterMethod !== 'all') {
            $ordersQuery->whereHas('payments', function (Builder $pq) use ($filterMethod) {
                $pq->where('payment_method', 'like', "%{$filterMethod}%")
                    ->orWhere('bank_name', 'like', "%{$filterMethod}%");
            });
        }

        $allOrders = $ordersQuery->latest('created_at')->get();

        // Filter berdasarkan status pembayaran pelanggan
        if ($filterPaymentStatus !== 'all') {
            $filteredOrders = $allOrders->filter(function (Order $ord) use ($filterPaymentStatus) {
                $statusMeta = $ord->paymentStatusMeta();
                $statusCode = $statusMeta['code'];

                return match ($filterPaymentStatus) {
                    'paid', 'lunas' => $statusCode === 'paid',
                    'waiting_verification', 'menunggu_verifikasi' => $statusCode === 'waiting_verification',
                    'unpaid', 'belum_bayar' => $statusCode === 'unpaid',
                    'rejected', 'ditolak', 'failed' => $statusCode === 'rejected',
                    'cancelled', 'dibatalkan', 'batal' => $statusCode === 'cancelled' || $ord->isCancelled(),
                    default => true,
                };
            });
        } else {
            $filteredOrders = $allOrders;
        }

        $orders = DB::table('orders')->latest('created_at')->take(20)->get();
        $archives = DB::table('private_archives')->latest('created_at')->take(10)->get();

        $txSummary = [
            'totalCount' => $allOrders->count(),
            'totalVolume' => (float) $allOrders->sum('total_cost'),
            'paidCount' => $allOrders->filter(fn ($o) => $o->paymentStatusMeta()['code'] === 'paid')->count(),
            'paidVolume' => (float) $allOrders->filter(fn ($o) => $o->paymentStatusMeta()['code'] === 'paid')->sum('total_cost'),
            'unpaidCount' => $allOrders->filter(fn ($o) => $o->paymentStatusMeta()['code'] === 'unpaid')->count(),
            'unpaidVolume' => (float) $allOrders->filter(fn ($o) => $o->paymentStatusMeta()['code'] === 'unpaid')->sum('total_cost'),
            'pendingVerificationCount' => $allOrders->filter(fn ($o) => $o->paymentStatusMeta()['code'] === 'waiting_verification')->count(),
            'cancelledCount' => $allOrders->filter(fn ($o) => $o->isCancelled() || $o->paymentStatusMeta()['code'] === 'cancelled')->count(),
            'cancelledVolume' => (float) $allOrders->filter(fn ($o) => $o->isCancelled() || $o->paymentStatusMeta()['code'] === 'cancelled')->sum('total_cost'),
        ];

        return view('finance.transactions', [
            'user' => $user,
            'orders' => $orders,
            'transactions' => $filteredOrders,
            'stats' => $stats,
            'txSummary' => $txSummary,
            'filterPaymentStatus' => $filterPaymentStatus,
            'filterMethod' => $filterMethod,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'searchTerm' => $searchTerm,
            'archives' => $archives,
        ]);
    }

    /**
     * Halaman Laporan Rekapitulasi Pendapatan serta Arus Kas Perusahaan.
     */
    public function reports(Request $request): View
    {
        $user = Auth::user();
        $stats = $this->buildFinanceStats();

        $period = $request->get('period', 'this_month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        [$start, $end, $periodLabel] = $this->resolveReportPeriod($period, $startDate, $endDate);

        // 1. Pesanan dalam periode
        $orders = Order::with(['user', 'service.category', 'invoice', 'latestPayment'])
            ->whereBetween('created_at', [$start, $end])
            ->latest('created_at')
            ->get();

        // 2. Pembayaran kas masuk riil dalam periode
        $payments = Payment::with(['order.user', 'order.service'])
            ->where('status', 'success')
            ->where(function (Builder $q) use ($start, $end) {
                $q->whereBetween('paid_at', [$start, $end])
                    ->orWhere(function (Builder $sq) use ($start, $end) {
                        $sq->whereNull('paid_at')->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->latest('created_at')
            ->get();

        // 3. Metrik Pendapatan & Arus Kas
        $grossRevenue = (float) $orders->sum(fn ($o) => (float) ($o->base_price > 0 ? $o->base_price : ($o->total_cost + $o->discount_amount)));
        $totalDiscounts = (float) $orders->sum('discount_amount');
        $netRevenue = (float) $orders->sum('total_cost');
        $cashInflow = (float) $payments->sum('amount');
        $receivables = (float) $orders->filter(fn ($o) => $o->paymentStatusMeta()['code'] !== 'paid')->sum('total_cost');
        $collectionRate = $netRevenue > 0 ? round(($cashInflow / $netRevenue) * 100, 1) : 0;

        // 4. Buku Kas Harian (Daily Cash Ledger)
        $dailyLedgerRaw = Payment::where('status', 'success')
            ->where(function (Builder $q) use ($start, $end) {
                $q->whereBetween('paid_at', [$start, $end])
                    ->orWhere(function (Builder $sq) use ($start, $end) {
                        $sq->whereNull('paid_at')->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->select(
                DB::raw('DATE(COALESCE(paid_at, created_at)) as tx_date'),
                DB::raw('COUNT(*) as tx_count'),
                DB::raw('SUM(amount) as daily_inflow')
            )
            ->groupBy('tx_date')
            ->orderBy('tx_date', 'asc')
            ->get();

        $runningBalance = 0;
        $dailyLedger = $dailyLedgerRaw->map(function ($item) use (&$runningBalance) {
            $runningBalance += (float) $item->daily_inflow;
            $item->running_balance = $runningBalance;

            return $item;
        });

        // 5. Komposisi per Kanal Bank
        $channelBreakdown = Payment::where('status', 'success')
            ->where(function (Builder $q) use ($start, $end) {
                $q->whereBetween('paid_at', [$start, $end])
                    ->orWhere(function (Builder $sq) use ($start, $end) {
                        $sq->whereNull('paid_at')->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total_amount'))
            ->groupBy('payment_method')
            ->orderByDesc('total_amount')
            ->get();

        // 6. Komposisi per Kategori Layanan
        $serviceBreakdown = DB::table('orders')
            ->leftJoin('services', 'orders.service_id', '=', 'services.id')
            ->leftJoin('categories', 'services.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->select(
                DB::raw("COALESCE(categories.name, 'Perizinan') as category_name"),
                DB::raw('COUNT(orders.id) as order_count'),
                DB::raw('SUM(orders.total_cost) as total_revenue')
            )
            ->groupBy('category_name')
            ->orderByDesc('total_revenue')
            ->get();

        return view('finance.reports', [
            'user' => $user,
            'stats' => $stats,
            'period' => $period,
            'periodLabel' => $periodLabel,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'grossRevenue' => $grossRevenue,
            'totalDiscounts' => $totalDiscounts,
            'netRevenue' => $netRevenue,
            'cashInflow' => $cashInflow,
            'receivables' => $receivables,
            'collectionRate' => $collectionRate,
            'orders' => $orders,
            'payments' => $payments,
            'dailyLedger' => $dailyLedger,
            'channelBreakdown' => $channelBreakdown,
            'serviceBreakdown' => $serviceBreakdown,
        ]);
    }

    /**
     * Ekspor Laporan Rekapitulasi Pendapatan & Arus Kas ke CSV / Excel.
     */
    public function exportReport(Request $request): StreamedResponse
    {
        $period = $request->get('period', 'this_month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        [$start, $end, $periodLabel] = $this->resolveReportPeriod($period, $startDate, $endDate);

        $filename = 'Laporan_Rekapitulasi_Kas_'.date('Ymd_His').'.csv';

        // 1. Ambil data transaksi pesanan dalam periode
        $orders = Order::with(['user', 'service.category', 'invoice', 'latestPayment'])
            ->whereBetween('created_at', [$start, $end])
            ->latest('created_at')
            ->get();

        // 2. Ambil data mutasi kas masuk riil dalam periode
        $payments = Payment::with(['order.user'])
            ->where('status', 'success')
            ->where(function (Builder $q) use ($start, $end) {
                $q->whereBetween('paid_at', [$start, $end])
                    ->orWhere(function (Builder $sq) use ($start, $end) {
                        $sq->whereNull('paid_at')->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->latest('created_at')
            ->get();

        $grossRevenue = (float) $orders->sum(fn ($o) => (float) ($o->base_price > 0 ? $o->base_price : ($o->total_cost + $o->discount_amount)));
        $totalDiscounts = (float) $orders->sum('discount_amount');
        $netRevenue = (float) $orders->sum('total_cost');
        $cashInflow = (float) $payments->sum('amount');
        $receivables = (float) $orders->filter(fn ($o) => $o->paymentStatusMeta()['code'] !== 'paid')->sum('total_cost');
        $collectionRate = $netRevenue > 0 ? round(($cashInflow / $netRevenue) * 100, 1) : 0;

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($periodLabel, $grossRevenue, $totalDiscounts, $netRevenue, $cashInflow, $receivables, $collectionRate, $payments, $orders): void {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['LAPORAN REKAPITULASI PENDAPATAN & ARUS KAS PERUSAHAAN']);
            fputcsv($handle, ['DIVISI FINANCE & ADMINISTRATION - CV PRASTA SOLUSI INDONESIA']);
            fputcsv($handle, ['Periode Laporan:', $periodLabel]);
            fputcsv($handle, ['Tanggal Diterbitkan:', date('d-m-Y H:i:s')]);
            fputcsv($handle, ['Petugas Pengesah:', Auth::user()->name ?? 'Finance Officer']);
            fputcsv($handle, []);

            // Bagian I: Ringkasan Eksekutif Keuangan & Arus Kas
            fputcsv($handle, ['=== 1. RINGKASAN EKSEKUTIF PENDAPATAN & ARUS KAS ===']);
            fputcsv($handle, ['Indikator Keuangan', 'Nilai (IDR)', 'Keterangan']);
            fputcsv($handle, ['Total Pendapatan Kotor (Gross Billed)', number_format($grossRevenue, 0, ',', '.'), 'Total tagihan layanan sebelum diskon']);
            fputcsv($handle, ['Total Potongan Promo (Voucher Diskon)', number_format($totalDiscounts, 0, ',', '.'), 'Subsidi promosi & diskon kupon']);
            fputcsv($handle, ['Pendapatan Bersih Buku (Net Accrual Revenue)', number_format($netRevenue, 0, ',', '.'), 'Tagihan bersih setelah potongan']);
            fputcsv($handle, ['Arus Kas Masuk Riil (Actual Cash Inflow)', number_format($cashInflow, 0, ',', '.'), 'Dana masuk riil terverifikasi di bank']);
            fputcsv($handle, ['Piutang Usaha Berjalan (Outstanding Receivables)', number_format($receivables, 0, ',', '.'), 'Tagihan yang belum lunas']);
            fputcsv($handle, ['Tingkat Realisasi Kas (Collection Rate)', $collectionRate.'%', 'Rasio perputaran kas masuk']);
            fputcsv($handle, []);

            // Bagian II: Rincian Penerimaan Kas Masuk Riil (Cash Inflows)
            fputcsv($handle, ['=== 2. RINCIAN BUKU KAS MASUK RIIL (CASH INFLOW LEDGER) ===']);
            fputcsv($handle, ['No.', 'No. Order', 'Klien / Perusahaan', 'Metode / Bank', 'Tanggal Masuk', 'Kas Masuk (IDR)', 'Status']);
            $no = 1;
            foreach ($payments as $p) {
                fputcsv($handle, [
                    $no++,
                    $p->order?->order_number ?? '-',
                    ($p->order?->user?->company_name ?: $p->order?->user?->name) ?? '-',
                    $p->payment_method ?? 'Transfer Bank',
                    $p->paid_at ? date('d/m/Y H:i', strtotime($p->paid_at)) : date('d/m/Y H:i', strtotime($p->created_at)),
                    number_format((float) $p->amount, 0, ',', '.'),
                    'Terverifikasi (Lunas)',
                ]);
            }
            fputcsv($handle, ['', '', '', '', 'TOTAL ARUS KAS MASUK:', number_format($cashInflow, 0, ',', '.')]);
            fputcsv($handle, []);

            // Bagian III: Rincian Seluruh Transaksi & Tagihan Layanan
            fputcsv($handle, ['=== 3. RINCIAN TRANSAKSI PESANAN & STATUS TAGIHAN INVOICE ===']);
            fputcsv($handle, ['No.', 'No. Order', 'No. Invoice', 'Klien / Perusahaan', 'Layanan Perizinan', 'Subtotal (IDR)', 'Diskon (IDR)', 'Total Tagihan (IDR)', 'Status Pembayaran', 'Tanggal Order']);
            $idx = 1;
            foreach ($orders as $ord) {
                $statusMeta = $ord->paymentStatusMeta();
                fputcsv($handle, [
                    $idx++,
                    $ord->order_number,
                    $ord->invoice?->invoice_number ?? 'Belum Terbit',
                    ($ord->user?->company_name ?: $ord->user?->name) ?? '-',
                    $ord->service?->name ?? 'Layanan Perizinan',
                    number_format((float) ($ord->base_price > 0 ? $ord->base_price : ($ord->total_cost + $ord->discount_amount)), 0, ',', '.'),
                    number_format((float) $ord->discount_amount, 0, ',', '.'),
                    number_format((float) $ord->total_cost, 0, ',', '.'),
                    $statusMeta['label'],
                    date('d/m/Y', strtotime($ord->created_at)),
                ]);
            }
            fputcsv($handle, []);
            fputcsv($handle, ['--- AKHIR LAPORAN KEUANGAN RESMI CV PRASTA SOLUSI INDONESIA ---']);

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Ekspor Laporan Mutasi & Transaksi Keuangan ke format Excel (.xlsx).
     */
    public function exportTransactionsXlsx(FinancialReportExcelService $excelService): Response
    {
        $filename = 'Transaksi_Keuangan_Finance_'.date('Ymd_His').'.xlsx';

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

        $xlsxBinary = $excelService->exportTransactions($payments);

        return response($xlsxBinary, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Ekspor Laporan Mutasi & Transaksi Keuangan ke format PDF (.pdf).
     */
    public function exportTransactionsPdf(FinancialReportPdfService $pdfService): Response
    {
        $filename = 'Transaksi_Keuangan_Finance_'.date('Ymd_His').'.pdf';

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

        $pdfBinary = $pdfService->exportTransactions($payments);

        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Kumpulkan data analitik laporan untuk export XLSX / PDF.
     */
    protected function gatherReportData(Request $request): array
    {
        $period = $request->get('period', 'this_month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        [$start, $end, $periodLabel] = $this->resolveReportPeriod($period, $startDate, $endDate);

        $orders = Order::with(['user', 'service.category', 'invoice', 'latestPayment'])
            ->whereBetween('created_at', [$start, $end])
            ->latest('created_at')
            ->get();

        $payments = Payment::with(['order.user'])
            ->where('status', 'success')
            ->where(function (Builder $q) use ($start, $end) {
                $q->whereBetween('paid_at', [$start, $end])
                    ->orWhere(function (Builder $sq) use ($start, $end) {
                        $sq->whereNull('paid_at')->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->latest('created_at')
            ->get();

        $grossRevenue = (float) $orders->sum(fn ($o) => (float) ($o->base_price > 0 ? $o->base_price : ($o->total_cost + $o->discount_amount)));
        $totalDiscounts = (float) $orders->sum('discount_amount');
        $netRevenue = (float) $orders->sum('total_cost');
        $cashInflow = (float) $payments->sum('amount');
        $receivables = (float) $orders->filter(fn ($o) => $o->paymentStatusMeta()['code'] !== 'paid')->sum('total_cost');
        $collectionRate = $netRevenue > 0 ? round(($cashInflow / $netRevenue) * 100, 1) : 0;

        return [
            'periodLabel' => $periodLabel,
            'start' => $start,
            'end' => $end,
            'orders' => $orders,
            'payments' => $payments,
            'grossRevenue' => $grossRevenue,
            'totalDiscounts' => $totalDiscounts,
            'netRevenue' => $netRevenue,
            'cashInflow' => $cashInflow,
            'receivables' => $receivables,
            'collectionRate' => $collectionRate,
        ];
    }

    /**
     * Ekspor Laporan Rekapitulasi Pendapatan & Arus Kas ke format Excel (.xlsx).
     */
    public function exportReportXlsx(Request $request, FinancialReportExcelService $excelService): Response
    {
        $data = $this->gatherReportData($request);
        $filename = 'Laporan_Arus_Kas_'.date('Ymd_His').'.xlsx';
        $xlsxBinary = $excelService->exportCashFlowReport($data);

        return response($xlsxBinary, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Ekspor Laporan Rekapitulasi Pendapatan & Arus Kas ke format PDF (.pdf).
     */
    public function exportReportPdf(Request $request, FinancialReportPdfService $pdfService): Response
    {
        $data = $this->gatherReportData($request);
        $filename = 'Laporan_Arus_Kas_'.date('Ymd_His').'.pdf';
        $pdfBinary = $pdfService->exportCashFlowReport($data);

        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Parsing dan standardisasi rentang tanggal laporan.
     *
     * @return array{0: Carbon, 1: Carbon, 2: string}
     */
    protected function resolveReportPeriod(string $period, ?string $startDate, ?string $endDate): array
    {
        switch ($period) {
            case 'last_month':
                $start = now()->subMonth()->startOfMonth();
                $end = now()->subMonth()->endOfMonth();
                $label = 'Bulan Lalu ('.$start->translatedFormat('F Y').')';
                break;
            case 'this_quarter':
                $start = now()->startOfQuarter();
                $end = now()->endOfQuarter();
                $label = 'Kuartal Ini ('.$start->format('d/m/Y').' - '.$end->format('d/m/Y').')';
                break;
            case 'this_year':
                $start = now()->startOfYear();
                $end = now()->endOfYear();
                $label = 'Tahun Ini ('.$start->format('Y').')';
                break;
            case 'custom':
                $start = ! empty($startDate) ? Carbon::parse($startDate)->startOfDay() : now()->startOfMonth();
                $end = ! empty($endDate) ? Carbon::parse($endDate)->endOfDay() : now()->endOfDay();
                $label = 'Kustom ('.$start->format('d/m/Y').' s/d '.$end->format('d/m/Y').')';
                break;
            case 'all_time':
                $start = Carbon::parse('2020-01-01')->startOfDay();
                $end = now()->endOfDay();
                $label = 'Semua Riwayat Transaksi';
                break;
            case 'this_month':
            default:
                $start = now()->startOfMonth();
                $end = now()->endOfMonth();
                $label = 'Bulan Ini ('.$start->translatedFormat('F Y').')';
                break;
        }

        return [$start, $end, $label];
    }

    /**
     * Halaman Terpisah: Linimasa Stepper Projek (Operasional Perizinan).
     */
    public function tracking(Request $request): View
    {
        $user = Auth::user();
        $stats = $this->buildFinanceStats();

        $orders = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.status', '!=', 'cancelled')
            ->select('orders.*', 'users.name as client_name', 'users.company_name')
            ->latest('orders.created_at')
            ->get();

        $projectTrackings = DB::table('project_trackings')
            ->leftJoin('orders', 'project_trackings.order_id', '=', 'orders.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.status', '!=', 'cancelled')
            ->select('project_trackings.*', 'orders.order_number', 'users.company_name')
            ->latest('project_trackings.date_recorded')
            ->get();

        $stages = OrderProgressService::stages();
        $archives = DB::table('private_archives')->latest('created_at')->take(10)->get();

        return view('finance.tracking', compact(
            'user',
            'orders',
            'projectTrackings',
            'stages',
            'stats',
            'archives'
        ));
    }

    /**
     * Halaman Terpisah: Direktori Privat Server Internal.
     */
    public function archives(Request $request): View
    {
        $user = Auth::user();
        $stats = $this->buildFinanceStats();

        $archives = DB::table('private_archives')->latest('created_at')->get();
        $orders = DB::table('orders')->latest('created_at')->take(20)->get();

        return view('finance.archives', compact(
            'user',
            'archives',
            'stats',
            'orders'
        ));
    }

    /**
     * Halaman Profil Staf Finance & Administration.
     */
    public function profile(Request $request): View
    {
        $user = Auth::user();
        $stats = $this->buildFinanceStats();
        $orders = DB::table('orders')->latest('created_at')->take(10)->get();
        $archives = DB::table('private_archives')->latest('created_at')->take(10)->get();

        return view('finance.profile', compact('user', 'stats', 'orders', 'archives'));
    }

    /**
     * Perbarui data profil akun staf Finance & Administration.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $this->normalizeBaseProfileInputs($request);
        $user = $request->user();

        $validated = $request->validate([
            'name' => $this->profileNameRules(true),
            'email' => $this->profileEmailRules($user->id),
            'phone' => $this->profilePhoneRules($user->id),
            'pic_position' => ['nullable', 'string', 'max:100'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:500'],
        ], $this->baseProfileMessages());

        // Perlindungan eskalasi peran: pertahankan peran staf saat ini
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $this->normalizePhoneToLocal($validated['phone']),
            'pic_position' => $validated['pic_position'] ?? $user->pic_position,
            'company_name' => $validated['company_name'] ?? $user->company_name,
            'address' => $validated['address'] ?? $user->address,
        ]);

        return redirect()->route('finance.profile.edit')
            ->with('success', 'Profil Finance & Administration berhasil diperbarui.');
    }

    /**
     * Perbarui kata sandi akun staf Finance & Administration.
     */
    public function updateProfilePassword(Request $request): RedirectResponse
    {
        return $this->performPasswordUpdate($request, 'finance.profile.edit');
    }

    /**
     * Verifikasi Bukti Pembayaran (Konfirmasi Lunas atau Tolak beserta alasan).
     */
    public function verifyPayment(Request $request, int $id, OrderProgressService $progressService): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:success,failed,waiting_confirmation'],
            'rejection_reason' => ['required_if:status,failed', 'nullable', 'string', 'max:1000'],
        ], [
            'rejection_reason.required_if' => 'Alasan penolakan wajib diisi jika pembayaran ditolak.',
        ]);

        $payment = Payment::with('order')->find($id);
        if (! $payment) {
            return back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        if ($payment->order && $payment->order->isCancelled()) {
            return back()->with('error', 'Pesanan ini telah dibatalkan dan pembayaran tidak dapat diverifikasi.');
        }

        $actorId = Auth::id();
        $newStatus = $validated['status'];
        $rejectionReason = $newStatus === 'failed'
            ? trim((string) ($validated['rejection_reason'] ?? 'Bukti transfer belum sesuai atau tidak terbaca.'))
            : null;

        $twoWayAudit = app(TwoWayPaymentVerificationService::class)->verify($payment);

        DB::transaction(function () use ($payment, $newStatus, $rejectionReason, $actorId, $progressService, $twoWayAudit): void {
            $payment->update([
                'status' => $newStatus,
                'rejection_reason' => $rejectionReason,
                'verified_by' => $actorId,
                'verified_at' => now(),
                'paid_at' => $newStatus === 'success' ? now() : null,
            ]);

            $invoiceStatus = match ($newStatus) {
                'success' => 'paid',
                'failed' => 'rejected',
                default => 'waiting_verification',
            };

            Invoice::where('order_id', $payment->order_id)->update([
                'payment_status' => $invoiceStatus,
            ]);

            $auditNote = ' [Audit 2-Arah: '.$twoWayAudit['verdict_label'].' (Skor: '.$twoWayAudit['overall_score'].'%)]';

            PaymentLog::create([
                'order_id' => $payment->order_id,
                'payment_id' => $payment->id,
                'status' => $newStatus,
                'notes' => ($newStatus === 'success'
                    ? 'Pembayaran diverifikasi lunas oleh petugas Finance.'
                    : ($newStatus === 'failed'
                        ? 'Pembayaran ditolak: '.$rejectionReason
                        : 'Status pembayaran dikembalikan ke menunggu verifikasi.'))
                    .$auditNote,
                'changed_by' => $actorId,
            ]);

            $order = $payment->order;
            if ($order) {
                if ($newStatus === 'success') {
                    if ($order->status === 'pending') {
                        $order->update(['status' => 'processing']);
                    }

                    $progressService->recordStage(
                        $order,
                        'payment_verification',
                        'completed',
                        'Dana pembayaran senilai Rp '.number_format((float) $payment->amount, 0, ',', '.').' telah terverifikasi valid.',
                        $actorId
                    );
                } elseif ($newStatus === 'failed') {
                    if ($order->status !== 'completed') {
                        $order->update(['status' => 'pending']);
                    }

                    $progressService->recordStage(
                        $order,
                        'payment_verification',
                        'in_progress',
                        'Bukti pembayaran ditolak: '.$rejectionReason.'. Menunggu unggah ulang oleh pelanggan.',
                        $actorId
                    );
                }
            }
        });

        $msg = $newStatus === 'success'
            ? 'Pembayaran berhasil diverifikasi lunas.'
            : ($newStatus === 'failed' ? 'Pembayaran ditolak beserta catatan alasan penolakan.' : 'Status verifikasi pembayaran berhasil diperbarui.');

        return back()->with('success', $msg);
    }

    /**
     * Penerbitan Invoice Resmi (HTML View).
     */
    public function generateInvoice(int $orderId): View|RedirectResponse
    {
        $orderModel = Order::with(['user', 'service', 'invoice', 'latestPayment', 'promo'])->find($orderId);

        if (! $orderModel) {
            return back()->with('error', 'Pesanan tidak ditemukan.');
        }

        if ($orderModel->isCancelled()) {
            return back()->with('error', 'Akses invoice untuk pesanan yang telah dibatalkan dinonaktifkan.');
        }

        $invoice = $orderModel->invoice ?? $this->createInvoiceForOrder($orderModel);
        $financeOfficer = Auth::user();

        // Variabel flat untuk kompatibilitas tampilan invoice
        $order = (object) [
            'id' => $orderModel->id,
            'order_number' => $orderModel->order_number,
            'total_cost' => $orderModel->total_cost,
            'base_price' => $invoice->subtotal,
            'discount_amount' => $invoice->discount_amount,
            'promo_code' => $orderModel->promo_code,
            'service_name' => $invoice->service_name,
            'invoice_number' => $invoice->invoice_number,
            'issued_at' => $invoice->issued_at,
            'client_name' => $invoice->customer_name,
            'company_name' => $invoice->customer_company,
            'client_address' => $invoice->customer_address,
            'client_phone' => $invoice->customer_phone,
            'client_email' => $invoice->customer_email,
            'payment_method' => $orderModel->latestPayment?->payment_method ?? 'Transfer Bank',
            'payment_status' => $orderModel->latestPayment?->status ?? 'unpaid',
            'payment_label' => $orderModel->paymentStatusMeta()['label'],
        ];

        return view('finance.invoice_pdf', compact('order', 'invoice', 'financeOfficer'));
    }

    /**
     * Unduh file PDF Invoice resmi untuk petugas Finance/Admin.
     */
    public function downloadInvoicePdf(int $orderId, InvoicePdfService $pdfService): Response|RedirectResponse
    {
        $orderModel = Order::with(['user', 'service', 'invoice', 'latestPayment'])->find($orderId);

        if (! $orderModel) {
            return back()->with('error', 'Pesanan tidak ditemukan.');
        }

        if ($orderModel->isCancelled()) {
            return back()->with('error', 'Akses invoice untuk pesanan yang telah dibatalkan dinonaktifkan.');
        }

        $invoice = $orderModel->invoice ?? $this->createInvoiceForOrder($orderModel);
        $pdfBinary = $pdfService->generate($invoice);
        $filename = 'Invoice-'.preg_replace('/[^A-Za-z0-9\-_]/', '-', $invoice->invoice_number).'.pdf';

        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Update progress tracking pengerjaan projek (real-time stepper).
     */
    public function addTracking(Request $request, OrderProgressService $progressService): RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'stage_name' => ['required', 'string', 'max:200'],
            'stage_status' => ['nullable', 'in:pending,in_progress,completed'],
            'description' => ['required', 'string', 'max:1500'],
        ]);

        $order = Order::findOrFail($validated['order_id']);
        if ($order->isCancelled()) {
            return back()->with('error', 'Pesanan ini telah dibatalkan dan linimasa tidak dapat diperbarui.');
        }
        $progressService->recordStage(
            $order,
            $validated['stage_name'],
            $validated['stage_status'] ?? 'completed',
            trim($validated['description']),
            Auth::id()
        );

        return back()->with('success', 'Tahapan linimasa & persentase progres projek berhasil diperbarui.');
    }

    /**
     * Simpan dokumen resmi ke Direktori Privat Server Internal.
     */
    public function uploadArchive(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'file_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $filePath = 'private_archives/'.strtolower(str_replace(' ', '_', $request->category)).'/'.($request->file_name ?? (time().'_dokumen_resmi.pdf'));

        DB::table('private_archives')->insert([
            'title' => $request->title,
            'category' => $request->category,
            'file_path' => $filePath,
            'file_size' => rand(1, 4).'.'.rand(1, 9).' MB',
            'uploaded_by' => Auth::user()->name,
            'notes' => $request->notes ?? 'Diarsipkan ke server privat internal CV Prasta Solusi Indonesia.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Dokumen resmi berhasil diamankan ke dalam direktori privat server.');
    }

    /**
     * Hapus arsip server internal.
     */
    public function destroyArchive(int $id): RedirectResponse
    {
        DB::table('private_archives')->where('id', $id)->delete();

        return back()->with('success', 'Arsip dokumen berhasil dihapus dari direktori server.');
    }

    /**
     * Ekspor data transaksi pembayaran keuangan ke format CSV / Excel.
     */
    public function exportExcel(): StreamedResponse
    {
        $filename = 'Transaksi_Keuangan_Finance_'.date('Ymd_His').'.csv';

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
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($payments): void {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['DATA TRANSAKSI KEUANGAN & PEMBAYARAN DIGITAL']);
            fputcsv($handle, ['DIVISI FINANCE & ADMINISTRATION - CV PRASTA SOLUSI INDONESIA']);
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
                'Tanggal Transaksi',
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

    /**
     * Hitung ringkasan statistik komprehensif keuangan & administrasi.
     */
    protected function buildFinanceStats(): array
    {
        $pendingPayments = DB::table('payments')->where('status', 'waiting_confirmation')->count();
        $verifiedPayments = DB::table('payments')->where('status', 'success')->count();
        $failedPayments = DB::table('payments')->where('status', 'failed')->count();
        $totalPayments = DB::table('payments')->count();

        $totalRevenue = (float) DB::table('orders')->whereIn('status', ['processing', 'completed', 'verified'])->sum('total_cost');
        $unpaidRevenue = (float) DB::table('orders')->where('status', 'pending')->sum('total_cost');
        $totalOrders = DB::table('orders')->count();

        $totalArchives = DB::table('private_archives')->count();
        $activeTrackings = DB::table('project_trackings')->count();

        $totalInvoices = DB::table('invoices')->count();
        $unpaidInvoices = DB::table('invoices')->whereIn('payment_status', ['unpaid', 'waiting_verification'])->count();
        $paidInvoices = DB::table('invoices')->whereIn('payment_status', ['paid', 'success'])->count();
        $totalCashInflow = (float) DB::table('payments')->where('status', 'success')->sum('amount');
        $cancelledOrders = DB::table('orders')->where('status', 'cancelled')->count();
        $cancelledOrdersVolume = (float) DB::table('orders')->where('status', 'cancelled')->sum('total_cost');

        return [
            'pendingPayments' => $pendingPayments,
            'verifiedPayments' => $verifiedPayments,
            'failedPayments' => $failedPayments,
            'totalPayments' => $totalPayments,
            'totalRevenue' => $totalRevenue,
            'unpaidRevenue' => $unpaidRevenue,
            'totalOrders' => $totalOrders,
            'totalArchives' => $totalArchives,
            'activeTrackings' => $activeTrackings,
            'totalInvoices' => $totalInvoices,
            'unpaidInvoices' => $unpaidInvoices,
            'paidInvoices' => $paidInvoices,
            'totalCashInflow' => $totalCashInflow,
            'cancelledOrders' => $cancelledOrders,
            'cancelledOrdersVolume' => $cancelledOrdersVolume,
        ];
    }

    /**
     * Buat invoice resmi untuk pesanan jika belum pernah digenerate.
     */
    protected function createInvoiceForOrder(Order $order, ?string $dueDate = null, ?string $customInvoiceNumber = null): Invoice
    {
        $user = $order->user;
        $service = $order->resolvedService();
        $subtotal = (float) ($order->base_price > 0 ? $order->base_price : ($order->total_cost + $order->discount_amount));
        $invoiceNumber = $customInvoiceNumber ?: ('INV-'.date('Ymd').'-'.strtoupper(Str::random(6)));
        $computedDueDate = $dueDate ?: (($order->created_at ?? now())->copy()->addDays(7)->toDateString());

        return Invoice::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'invoice_number' => $invoiceNumber,
            'issued_at' => $order->created_at ?? now(),
            'due_date' => $computedDueDate,
            'customer_name' => $user?->name ?? 'Pelanggan',
            'customer_company' => $user?->company_name,
            'customer_email' => $user?->email,
            'customer_phone' => $user?->phone,
            'customer_address' => $user?->address,
            'service_name' => $service?->name ?? 'Layanan Perizinan & Sertifikasi',
            'subtotal' => $subtotal,
            'discount_amount' => (float) $order->discount_amount,
            'total_amount' => (float) $order->total_cost,
            'payment_status' => $order->paymentStatusMeta()['code'],
        ]);
    }
}
