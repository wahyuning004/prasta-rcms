<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\Models\ProjectTracking;
use App\Models\Promo;
use App\Models\Service;
use App\Services\InvoicePdfService;
use App\Services\OrderProgressService;
use App\Services\PromoValidationService;
use App\Services\ServiceCatalogService;
use App\Services\ServiceRequirementSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrderController extends Controller
{
    /**
     * Daftar metode pembayaran resmi beserta detail rekening / QRIS masing-masing.
     *
     * @var array<string, array<string, string>>
     */
    public const PAYMENT_METHODS = [
        'Transfer Bank Mandiri' => [
            'type' => 'bank',
            'bank_name' => 'Bank Mandiri',
            'branch' => 'KCP Bekasi',
            'account_number' => '156-00-1928374-1',
            'account_holder' => 'CV PRASTA SOLUSI INDONESIA',
        ],
        'Transfer Bank BCA' => [
            'type' => 'bank',
            'bank_name' => 'Bank BCA',
            'branch' => 'Cabang Bekasi',
            'account_number' => '522-0918273',
            'account_holder' => 'CV PRASTA SOLUSI INDONESIA',
        ],
        'Transfer Bank BNI' => [
            'type' => 'bank',
            'bank_name' => 'Bank BNI',
            'branch' => 'Cabang Bekasi',
            'account_number' => '084-9201753',
            'account_holder' => 'CV PRASTA SOLUSI INDONESIA',
        ],
        'QRIS' => [
            'type' => 'qris',
            'bank_name' => 'QRIS',
            'merchant_name' => 'CV PRASTA SOLUSI INDONESIA (IZINYOU)',
            'nmid' => 'ID1026001928374',
            'qr_image' => 'images/qris-izinyou.svg',
        ],
    ];

    /**
     * Halaman internal Katalog Layanan Portal Klien (menggunakan data database & ServiceCatalogService).
     */
    public function services(Request $request, ServiceCatalogService $catalogService): View
    {
        return view('klien.services.index', array_merge(
            ['user' => Auth::user()],
            $catalogService->buildCatalogData($request)
        ));
    }

    /**
     * Halaman detail layanan internal Portal Klien berdasarkan ID layanan dari database.
     */
    public function showService(
        Service $service,
        ServiceCatalogService $catalogService,
        ServiceRequirementSyncService $requirementSync
    ): View {
        return view('klien.services.show', array_merge(
            ['user' => Auth::user()],
            $catalogService->buildServiceDetailData($service, $requirementSync)
        ));
    }

    /**
     * Daftar seluruh pesanan pelanggan yang login.
     */
    public function index(): View
    {
        $orders = Order::with(['service.category', 'invoice', 'latestPayment', 'trackings', 'review'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('klien.orders.index', [
            'user' => Auth::user(),
            'orders' => $orders,
        ]);
    }

    /**
     * Halaman checkout untuk layanan tertentu.
     */
    public function showCheckout(Service $service, ServiceRequirementSyncService $requirementSync): View
    {
        $service->load('category');
        $requirements = $requirementSync->ensureForService($service);

        $promos = Promo::available()
            ->orderByDesc('created_at')
            ->get();

        return view('klien.checkout', [
            'user' => Auth::user(),
            'service' => $service,
            'requirements' => $requirements,
            'promos' => $promos,
            'basePrice' => $service->getEffectivePrice(),
            'checkoutToken' => Str::random(40),
        ]);
    }

    /**
     * Validasi kode voucher secara real-time (AJAX) tanpa mengubah database.
     */
    public function validateVoucher(Request $request, PromoValidationService $promoService): JsonResponse
    {
        $validated = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'promo_code' => ['required', 'string', 'max:50'],
        ]);

        $service = Service::findOrFail($validated['service_id']);
        $subtotal = $service->getEffectivePrice();
        $result = $promoService->validate($validated['promo_code'], $subtotal, false);

        if (! $result['valid']) {
            return response()->json([
                'valid' => false,
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'message' => $result['message'],
            'promo_code' => $result['promo']->code,
            'subtotal' => $subtotal,
            'discount' => $result['discount'],
            'discount_formatted' => 'Rp '.number_format($result['discount'], 0, ',', '.'),
            'final_total' => $result['final_total'],
            'final_total_formatted' => 'Rp '.number_format($result['final_total'], 0, ',', '.'),
        ]);
    }

    /**
     * Proses konfirmasi checkout pesanan di sisi server dengan transaksi database & pencegahan ganda.
     */
    public function storeCheckout(
        Request $request,
        PromoValidationService $promoService,
        OrderProgressService $progressService,
        ServiceRequirementSyncService $requirementSync
    ): RedirectResponse {
        $validated = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'promo_code' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'checkout_token' => ['nullable', 'string', 'max:64'],
        ], [
            'service_id.required' => 'Pilih layanan terlebih dahulu sebelum checkout.',
            'service_id.exists' => 'Layanan yang dipilih tidak ditemukan.',
        ]);

        $user = Auth::user();
        $checkoutToken = ! empty($validated['checkout_token']) ? trim($validated['checkout_token']) : null;

        // Cegah pesanan ganda akibat klik berulang atau refresh form dengan checkout_token sama
        if ($checkoutToken !== null) {
            $existingOrder = Order::where('user_id', $user->id)
                ->where('checkout_token', $checkoutToken)
                ->first();

            if ($existingOrder) {
                return redirect()->route('klien.orders.confirmation', $existingOrder->order_number)
                    ->with('info', 'Pesanan Anda ('.$existingOrder->order_number.') sudah tercatat sebelumnya.');
            }
        }

        $service = Service::findOrFail($validated['service_id']);
        $requirementSync->ensureForService($service);
        $basePrice = $service->getEffectivePrice();

        $order = DB::transaction(function () use (
            $validated,
            $user,
            $service,
            $basePrice,
            $checkoutToken,
            $promoService,
            $progressService
        ): Order {
            // Cek ulang idempotensi di dalam transaksi
            if ($checkoutToken !== null) {
                $existingInTx = Order::where('user_id', $user->id)
                    ->where('checkout_token', $checkoutToken)
                    ->lockForUpdate()
                    ->first();

                if ($existingInTx) {
                    return $existingInTx;
                }
            }

            $discount = 0.0;
            $promoId = null;
            $promoCode = null;

            if (! empty($validated['promo_code'])) {
                $promoCheck = $promoService->validate($validated['promo_code'], $basePrice, true);

                if (! $promoCheck['valid']) {
                    throw ValidationException::withMessages([
                        'promo_code' => $promoCheck['message'],
                    ]);
                }

                /** @var Promo $promo */
                $promo = $promoCheck['promo'];
                $discount = $promoCheck['discount'];
                $promoId = $promo->id;
                $promoCode = $promo->code;

                // Increment kuota terpakai secara atomik
                $updatedRows = Promo::where('id', $promo->id)
                    ->when((int) $promo->quota > 0, fn ($q) => $q->whereColumn('used_count', '<', 'quota'))
                    ->increment('used_count');

                if ((int) $promo->quota > 0 && $updatedRows === 0) {
                    throw ValidationException::withMessages([
                        'promo_code' => 'Maaf, kuota pemakaian voucher "'.$promo->code.'" baru saja habis.',
                    ]);
                }
            }

            $finalTotal = max(0.0, $basePrice - $discount);
            $orderNumber = $this->generateUniqueOrderNumber();

            $createdOrder = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'promo_id' => $promoId,
                'promo_code' => $promoCode,
                'service_id' => $service->id,
                'base_price' => $basePrice,
                'discount_amount' => $discount,
                'total_cost' => $finalTotal,
                'notes' => ! empty($validated['notes']) ? trim($validated['notes']) : null,
                'checkout_token' => $checkoutToken,
                'status' => 'pending',
                'current_stage' => 'Pesanan Diterima',
                'progress_percentage' => 15,
            ]);

            OrderItem::create([
                'order_id' => $createdOrder->id,
                'service_id' => $service->id,
                'price' => $basePrice,
            ]);

            Invoice::create([
                'order_id' => $createdOrder->id,
                'user_id' => $user->id,
                'invoice_number' => $this->generateUniqueInvoiceNumber($createdOrder->order_number),
                'issued_at' => now(),
                'due_date' => now()->addDays(7)->toDateString(),
                'customer_name' => $user->name,
                'customer_company' => $user->company_name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
                'customer_address' => $user->address,
                'service_name' => $service->name,
                'subtotal' => $basePrice,
                'discount_amount' => $discount,
                'total_amount' => $finalTotal,
                'payment_status' => 'unpaid',
            ]);

            $progressService->recordStage(
                $createdOrder,
                'order_received',
                'completed',
                'Pesanan layanan '.$service->name.' berhasil dikonfirmasi dengan kode '.$orderNumber.'.',
                $user->id
            );

            return $createdOrder;
        });

        return redirect()->route('klien.orders.confirmation', $order->order_number)
            ->with('success', 'Pemesanan layanan "'.$service->name.'" berhasil dibuat! Nomor pesanan Anda: '.$order->order_number.'.');
    }

    /**
     * Halaman konfirmasi pemesanan setelah checkout berhasil.
     */
    public function confirmation(string $orderNumber): View
    {
        $order = Order::with(['service.category', 'invoice', 'promo'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        abort_if($order->user_id !== Auth::id(), 403, 'Anda tidak memiliki akses ke pesanan ini.');

        $invoice = $this->ensureInvoiceExists($order);

        return view('klien.orders.confirmation', [
            'user' => Auth::user(),
            'order' => $order,
            'invoice' => $invoice,
        ]);
    }

    /**
     * Halaman detail pesanan: ringkasan, tracking 6 tahap, dokumen persyaratan & versi, pembayaran, dan ulasan.
     */
    public function show(
        Order $order,
        OrderProgressService $progressService,
        ServiceRequirementSyncService $requirementSync
    ): View {
        abort_if($order->user_id !== Auth::id(), 403, 'Anda tidak memiliki akses ke pesanan ini.');

        $order->load([
            'service.category',
            'items.service',
            'promo',
            'invoice',
            'payments',
            'latestPayment',
            'paymentLogs.actor',
            'trackings.updater',
            'documents.versions.uploader',
            'review',
        ]);

        $invoice = $this->ensureInvoiceExists($order);
        $service = $order->resolvedService();

        $requirements = $service ? $requirementSync->ensureForService($service) : collect();
        $docsByRequirement = $order->documents->keyBy('service_requirement_id');

        $mandatoryRequirements = $requirements->where('is_mandatory', true)->values();
        $optionalRequirements = $requirements->where('is_mandatory', false)->values();

        $timeline = $progressService->buildTimeline($order);
        $paymentMeta = $order->paymentStatusMeta();

        return view('klien.orders.show', [
            'user' => Auth::user(),
            'order' => $order,
            'service' => $service,
            'invoice' => $invoice,
            'paymentMeta' => $paymentMeta,
            'paymentMethods' => self::PAYMENT_METHODS,
            'mandatoryRequirements' => $mandatoryRequirements,
            'optionalRequirements' => $optionalRequirements,
            'docsByRequirement' => $docsByRequirement,
            'timeline' => $timeline,
        ]);
    }

    /**
     * Batalkan pesanan oleh pelanggan sebelum masuk tahap proses / verifikasi pembayaran.
     */
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        abort_if($order->user_id !== Auth::id(), 403, 'Anda tidak memiliki akses ke pesanan ini.');

        $validated = $request->validate([
            'cancel_reason' => ['nullable', 'string', 'max:500'],
        ]);

        if (! $order->canBeCancelled()) {
            return back()->with('error', 'Pesanan ini sudah masuk tahap proses atau pembayaran terverifikasi sehingga tidak dapat dibatalkan.');
        }

        $reason = trim((string) ($validated['cancel_reason'] ?? ''));
        $reasonDescription = $reason !== ''
            ? 'Pesanan dibatalkan oleh pelanggan. Alasan: '.$reason
            : 'Pesanan dibatalkan oleh pelanggan sebelum pembayaran diverifikasi.';

        $cancelled = DB::transaction(function () use ($order, $reasonDescription): bool {
            $lockedOrder = Order::with('latestPayment')->lockForUpdate()->findOrFail($order->id);
            if (! $lockedOrder->canBeCancelled()) {
                return false;
            }

            $lockedOrder->update([
                'status' => 'cancelled',
                'current_stage' => 'Pesanan Dibatalkan',
            ]);

            if ($lockedOrder->invoice) {
                $lockedOrder->invoice->update([
                    'payment_status' => 'cancelled',
                ]);
            }

            ProjectTracking::create([
                'order_id' => $lockedOrder->id,
                'stage_code' => 'order_cancelled',
                'stage_order' => 0,
                'stage_name' => 'Pesanan Dibatalkan',
                'description' => $reasonDescription,
                'status' => 'cancelled',
                'progress_percentage' => (int) ($lockedOrder->progress_percentage ?? 15),
                'date_recorded' => now(),
                'updated_by' => Auth::id(),
            ]);

            PaymentLog::create([
                'order_id' => $lockedOrder->id,
                'payment_id' => null,
                'status' => 'cancelled',
                'notes' => $reasonDescription,
                'changed_by' => Auth::id(),
            ]);

            return true;
        });

        if (! $cancelled) {
            return back()->with('error', 'Pesanan ini tidak dapat dibatalkan karena statusnya telah berubah.');
        }

        return redirect()->route('klien.orders.show', $order->id)
            ->with('success', 'Pesanan '.$order->order_number.' berhasil dibatalkan dan tetap tersimpan pada riwayat transaksi Anda.');
    }

    /**
     * Lihat halaman Invoice resmi pelanggan.
     */
    public function showInvoice(Order $order): View
    {
        abort_if($order->user_id !== Auth::id(), 403, 'Anda tidak memiliki akses ke invoice ini.');
        abort_if($order->isCancelled(), 403, 'Akses invoice untuk pesanan yang telah dibatalkan dinonaktifkan.');

        $order->load(['service', 'promo', 'latestPayment']);
        $invoice = $this->ensureInvoiceExists($order);

        return view('klien.orders.invoice', [
            'user' => Auth::user(),
            'order' => $order,
            'invoice' => $invoice,
            'paymentMeta' => $order->paymentStatusMeta(),
        ]);
    }

    /**
     * Unduh Invoice resmi dalam format file PDF.
     */
    public function downloadInvoicePdf(Order $order, InvoicePdfService $pdfService): Response
    {
        abort_if($order->user_id !== Auth::id(), 403, 'Anda tidak memiliki akses ke invoice ini.');
        abort_if($order->isCancelled(), 403, 'Akses unduh PDF untuk pesanan yang telah dibatalkan dinonaktifkan.');

        $order->load(['service', 'promo', 'latestPayment']);
        $invoice = $this->ensureInvoiceExists($order);
        $pdfBinary = $pdfService->generate($invoice);
        $filename = 'Invoice-'.preg_replace('/[^A-Za-z0-9\-_]/', '-', $invoice->invoice_number).'.pdf';

        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Unggah / unggah ulang bukti pembayaran pelanggan.
     */
    public function uploadPayment(Request $request, Order $order, OrderProgressService $progressService): RedirectResponse
    {
        abort_if($order->user_id !== Auth::id(), 403, 'Anda tidak memiliki akses ke pesanan ini.');

        if ($order->isCancelled()) {
            return back()->with('error', 'Pesanan yang telah dibatalkan tidak dapat menerima unggahan pembayaran.');
        }

        $latestPayment = $order->latestPayment()->first();
        if ($latestPayment && $latestPayment->status === 'success') {
            return back()->with('error', 'Pembayaran untuk pesanan ini sudah berhasil diverifikasi.');
        }

        if ($latestPayment && $latestPayment->status === 'waiting_confirmation') {
            return back()->with('error', 'Bukti pembayaran sebelumnya masih menunggu verifikasi petugas Finance.');
        }

        $allowedMethods = array_keys(self::PAYMENT_METHODS);

        $validated = $request->validate([
            'payment_method' => ['required', 'string', Rule::in($allowedMethods)],
            'sender_name' => ['required', 'string', 'max:150'],
            'amount' => [
                'required',
                'numeric',
                'min:1',
                function (string $attribute, mixed $value, \Closure $fail) use ($order): void {
                    if ((float) $value < (float) $order->total_cost) {
                        $fail('Nominal pembayaran minimal sesuai total tagihan Rp '.number_format((float) $order->total_cost, 0, ',', '.').'.');
                    }
                },
            ],
            'payment_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'payment_method.required' => 'Pilih metode pembayaran.',
            'payment_method.in' => 'Metode pembayaran tidak valid. Pilih Mandiri, BCA, BNI, atau QRIS.',
            'sender_name.required' => 'Nama pemilik rekening / pengirim wajib diisi.',
            'amount.required' => 'Nominal transfer wajib diisi.',
            'payment_proof.required' => 'File bukti pembayaran wajib diunggah.',
            'payment_proof.mimes' => 'Format bukti pembayaran harus JPG, JPEG, PNG, atau PDF.',
            'payment_proof.max' => 'Ukuran file bukti pembayaran maksimal 5 MB.',
        ]);

        $methodConfig = self::PAYMENT_METHODS[$validated['payment_method']];
        $derivedBankName = $methodConfig['bank_name'];

        $file = $request->file('payment_proof');
        $storedPath = Storage::disk('local')->putFile('payment_proofs/'.$order->id, $file);
        $user = Auth::user();

        DB::transaction(function () use ($order, $validated, $derivedBankName, $file, $storedPath, $user, $progressService): void {
            $invoice = $this->ensureInvoiceExists($order);

            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'invoice_id' => $invoice->id,
                'payment_method' => $validated['payment_method'],
                'bank_name' => $derivedBankName,
                'sender_name' => trim($validated['sender_name']),
                'amount' => (float) $validated['amount'],
                'payment_proof' => $storedPath,
                'original_filename' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'notes' => ! empty($validated['notes']) ? trim($validated['notes']) : null,
                'status' => 'waiting_confirmation',
            ]);

            $invoice->update([
                'payment_status' => 'waiting_verification',
            ]);

            PaymentLog::create([
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'status' => 'waiting_confirmation',
                'notes' => 'Pelanggan mengunggah bukti pembayaran via '.$validated['payment_method'].'.',
                'changed_by' => $user->id,
            ]);

            if ((int) $order->progress_percentage < 30) {
                $progressService->recordStage(
                    $order,
                    'payment_verification',
                    'in_progress',
                    'Bukti pembayaran telah diunggah oleh pelanggan dan menunggu verifikasi tim Finance.',
                    $user->id
                );
            }
        });

        return redirect()->route('klien.orders.show', $order->id)
            ->with('success', 'Bukti pembayaran berhasil diunggah dan sedang menunggu verifikasi petugas.');
    }

    /**
     * Tampilkan/unduh file bukti pembayaran secara aman (hanya pemilik pesanan & petugas berwenang).
     */
    public function viewPaymentProof(Payment $payment): Response|BinaryFileResponse
    {
        $user = Auth::user();
        $order = $payment->order;

        $isAuthorized = $order && (
            $order->user_id === $user->id
            || $user->isAdmin()
            || $user->isFinance()
        );

        abort_unless($isAuthorized, 403, 'Anda tidak memiliki izin untuk melihat bukti pembayaran ini.');

        $path = $payment->payment_proof;
        abort_if(empty($path), 404, 'File bukti pembayaran tidak ditemukan.');

        if (Storage::disk('local')->exists($path)) {
            $content = Storage::disk('local')->get($path);
            $mime = Storage::disk('local')->mimeType($path) ?: 'image/jpeg';

            return response($content, 200, [
                'Content-Type' => $mime,
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        if (Storage::disk('public')->exists($path)) {
            $content = Storage::disk('public')->get($path);
            $mime = Storage::disk('public')->mimeType($path) ?: 'image/jpeg';

            return response($content, 200, [
                'Content-Type' => $mime,
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        abort(404, 'File bukti pembayaran tidak tersedia di penyimpanan.');
    }

    /**
     * Pastikan pesanan memiliki record invoice di database (termasuk untuk order lama).
     */
    protected function ensureInvoiceExists(Order $order): Invoice
    {
        if ($order->invoice) {
            return $order->invoice;
        }

        $user = $order->user;
        $service = $order->resolvedService();
        $subtotal = (float) ($order->base_price > 0 ? $order->base_price : ($order->total_cost + $order->discount_amount));
        $paymentMeta = $order->paymentStatusMeta();

        return Invoice::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'invoice_number' => $this->generateUniqueInvoiceNumber($order->order_number),
            'issued_at' => $order->created_at ?? now(),
            'due_date' => ($order->created_at ?? now())->copy()->addDays(7)->toDateString(),
            'customer_name' => $user?->name ?? 'Pelanggan',
            'customer_company' => $user?->company_name,
            'customer_email' => $user?->email,
            'customer_phone' => $user?->phone,
            'customer_address' => $user?->address,
            'service_name' => $service?->name ?? 'Layanan Perizinan & Sertifikasi',
            'subtotal' => $subtotal,
            'discount_amount' => (float) $order->discount_amount,
            'total_amount' => (float) $order->total_cost,
            'payment_status' => $paymentMeta['code'],
        ]);
    }

    protected function generateUniqueOrderNumber(): string
    {
        do {
            $candidate = 'ORD-'.date('Ymd').'-'.strtoupper(Str::random(6));
        } while (Order::where('order_number', $candidate)->exists());

        return $candidate;
    }

    protected function generateUniqueInvoiceNumber(string $orderNumber): string
    {
        $suffix = Str::afterLast($orderNumber, '-');
        $candidate = 'INV-'.date('Ymd').'-'.$suffix;

        while (Invoice::where('invoice_number', $candidate)->exists()) {
            $candidate = 'INV-'.date('Ymd').'-'.strtoupper(Str::random(6));
        }

        return $candidate;
    }
}
