<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\Models\ProjectTracking;
use App\Models\Service;
use App\Models\User;
use App\Services\TwoWayPaymentVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FinanceDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $financeUser;

    private User $clientUser;

    private User $adminUser;

    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->financeUser = User::factory()->create([
            'name' => 'Siti Dania (Finance Officer)',
            'email' => 'dania.finance@izinyou.com',
            'phone' => '081233445566',
            'role' => 'finance',
            'company_name' => 'CV Prasta Solusi Indonesia',
            'pic_position' => 'Finance & Administration Specialist',
        ]);

        $this->clientUser = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@mitra.co.id',
            'phone' => '081299887766',
            'role' => 'klien',
            'company_name' => 'PT Mitra Sejahtera',
        ]);

        $this->adminUser = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@izinyou.com',
            'phone' => '081111222333',
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'Perizinan Usaha',
            'slug' => 'perizinan-usaha',
            'description' => 'Kategori izin usaha',
        ]);

        $this->service = Service::create([
            'category_id' => $category->id,
            'name' => 'Izin Edar Alat Kesehatan',
            'slug' => 'izin-edar-alkes',
            'price' => 5000000,
            'estimated_days' => 14,
            'description' => 'Pengurusan izin edar alkes resmi Kemenkes',
            'is_active' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('finance.dashboard'))->assertRedirect(route('login'));
        $this->get(route('finance.payments'))->assertRedirect(route('login'));
        $this->get(route('finance.transactions'))->assertRedirect(route('login'));
    }

    public function test_client_is_blocked_by_rbac_from_finance_dashboard(): void
    {
        $this->actingAs($this->clientUser)
            ->get(route('finance.dashboard'))
            ->assertRedirect(route('klien.dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($this->clientUser)
            ->get(route('finance.payments'))
            ->assertRedirect(route('klien.dashboard'));

        $this->actingAs($this->clientUser)
            ->get(route('finance.transactions'))
            ->assertRedirect(route('klien.dashboard'));
    }

    public function test_finance_officer_can_view_finance_dashboard_with_metrics_and_queues(): void
    {
        // Buat pesanan dengan pembayaran
        $order = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-2026-FN-001',
            'total_cost' => 5000000,
            'base_price' => 5000000,
            'status' => 'pending',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => 5000000,
            'payment_method' => 'Transfer Bank BCA',
            'status' => 'waiting_confirmation',
            'payment_proof' => 'payments/proof_001.jpg',
        ]);

        $response = $this->actingAs($this->financeUser)->get(route('finance.dashboard'));

        $response->assertOk()
            ->assertSee('Dasbor Finance &amp; Administration', false)
            ->assertSee('Menunggu Verifikasi')
            ->assertSee('Total Mutasi Keuangan')
            ->assertSee('ORD-2026-FN-001')
            ->assertSee('PT Mitra Sejahtera')
            ->assertSee('5.000.000')
            ->assertSee('Verifikasi Lunas');
    }

    public function test_finance_payments_queue_view_and_filtering(): void
    {
        $order1 = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-PAY-PENDING',
            'total_cost' => 3000000,
            'base_price' => 3000000,
            'status' => 'pending',
        ]);

        Payment::create([
            'order_id' => $order1->id,
            'amount' => 3000000,
            'payment_method' => 'Transfer Bank Mandiri',
            'status' => 'waiting_confirmation',
            'payment_proof' => 'proof_pending.jpg',
        ]);

        $order2 = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-PAY-SUCCESS',
            'total_cost' => 4000000,
            'base_price' => 4000000,
            'status' => 'processing',
        ]);

        Payment::create([
            'order_id' => $order2->id,
            'amount' => 4000000,
            'payment_method' => 'QRIS',
            'status' => 'success',
            'paid_at' => now(),
        ]);

        // 1. Tampilkan semua antrean pembayaran
        $this->actingAs($this->financeUser)
            ->get(route('finance.payments'))
            ->assertOk()
            ->assertSee('ORD-PAY-PENDING')
            ->assertSee('ORD-PAY-SUCCESS')
            ->assertSee('Antrean Verifikasi Pembayaran');

        // 2. Filter status: waiting_confirmation
        $this->actingAs($this->financeUser)
            ->get(route('finance.payments', ['status' => 'waiting_confirmation']))
            ->assertOk()
            ->assertSee('ORD-PAY-PENDING')
            ->assertDontSee('ORD-PAY-SUCCESS');

        // 3. Filter status: success
        $this->actingAs($this->financeUser)
            ->get(route('finance.payments', ['status' => 'success']))
            ->assertOk()
            ->assertSee('ORD-PAY-SUCCESS')
            ->assertDontSee('ORD-PAY-PENDING');

        // 4. Pencarian berdasarkan kata kunci
        $this->actingAs($this->financeUser)
            ->get(route('finance.payments', ['search' => 'PENDING']))
            ->assertOk()
            ->assertSee('ORD-PAY-PENDING')
            ->assertDontSee('ORD-PAY-SUCCESS');
    }

    public function test_finance_officer_can_verify_and_approve_payment(): void
    {
        $order = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-VERIFY-OK',
            'total_cost' => 5000000,
            'base_price' => 5000000,
            'status' => 'pending',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => 5000000,
            'payment_method' => 'Transfer Bank BCA',
            'status' => 'waiting_confirmation',
        ]);

        $response = $this->actingAs($this->financeUser)
            ->post(route('finance.payment.verify', $payment->id), [
                'status' => 'success',
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'success',
            'verified_by' => $this->financeUser->id,
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing',
        ]);

        $this->assertDatabaseHas('payment_logs', [
            'payment_id' => $payment->id,
            'status' => 'success',
        ]);
    }

    public function test_finance_officer_rejects_payment_with_mandatory_reason(): void
    {
        $order = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-REJECT-TEST',
            'total_cost' => 5000000,
            'base_price' => 5000000,
            'status' => 'pending',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => 5000000,
            'payment_method' => 'Transfer Bank BRI',
            'status' => 'waiting_confirmation',
        ]);

        // Negatif: Gagal jika alasan penolakan kosong saat status failed
        $this->actingAs($this->financeUser)
            ->post(route('finance.payment.verify', $payment->id), [
                'status' => 'failed',
                'rejection_reason' => '',
            ])
            ->assertSessionHasErrors(['rejection_reason']);

        // Positif: Sukses menolak jika alasan diisi
        $this->actingAs($this->financeUser)
            ->post(route('finance.payment.verify', $payment->id), [
                'status' => 'failed',
                'rejection_reason' => 'Nominal transfer tidak sesuai dengan invoice (kurang Rp 500.000).',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'failed',
            'rejection_reason' => 'Nominal transfer tidak sesuai dengan invoice (kurang Rp 500.000).',
        ]);
    }

    public function test_finance_transactions_monitoring_and_customer_status(): void
    {
        $orderPaid = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-TRANS-PAID',
            'total_cost' => 4500000,
            'base_price' => 5000000,
            'discount_amount' => 500000,
            'promo_code' => 'DISKON10',
            'status' => 'processing',
        ]);

        Payment::create([
            'order_id' => $orderPaid->id,
            'amount' => 4500000,
            'payment_method' => 'Transfer Bank BCA',
            'status' => 'success',
            'paid_at' => now(),
        ]);

        $orderUnpaid = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-TRANS-UNPAID',
            'total_cost' => 5000000,
            'base_price' => 5000000,
            'status' => 'pending',
        ]);

        // 1. Tampilkan halaman transaksi utama
        $this->actingAs($this->financeUser)
            ->get(route('finance.transactions'))
            ->assertOk()
            ->assertSee('Monitoring Transaksi &amp; Status Pelanggan', false)
            ->assertSee('ORD-TRANS-PAID')
            ->assertSee('ORD-TRANS-UNPAID')
            ->assertSee('DISKON10')
            ->assertSee('Lunas')
            ->assertSee('Belum Bayar');

        // 2. Filter status: paid
        $this->actingAs($this->financeUser)
            ->get(route('finance.transactions', ['payment_status' => 'paid']))
            ->assertOk()
            ->assertSee('ORD-TRANS-PAID')
            ->assertDontSee('ORD-TRANS-UNPAID');

        // 3. Filter status: unpaid
        $this->actingAs($this->financeUser)
            ->get(route('finance.transactions', ['payment_status' => 'unpaid']))
            ->assertOk()
            ->assertSee('ORD-TRANS-UNPAID')
            ->assertDontSee('ORD-TRANS-PAID');
    }

    public function test_finance_officer_can_generate_invoice_html(): void
    {
        $order = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-INV-GEN',
            'total_cost' => 5000000,
            'base_price' => 5000000,
            'status' => 'processing',
        ]);

        $this->actingAs($this->financeUser)
            ->get(route('finance.invoice.generate', $order->id))
            ->assertOk()
            ->assertSee('ORD-INV-GEN')
            ->assertSee('PT Mitra Sejahtera')
            ->assertSee('INVOICE');
    }

    public function test_finance_officer_can_export_excel_csv(): void
    {
        $response = $this->actingAs($this->financeUser)
            ->get(route('finance.export.excel'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', (string) $response->headers->get('Content-Disposition'));
    }

    public function test_finance_profile_view_update_and_privilege_escalation_guard(): void
    {
        // 1. Halaman profil staf Finance
        $this->actingAs($this->financeUser)
            ->get(route('finance.profile.edit'))
            ->assertOk()
            ->assertSee('Siti Dania (Finance Officer)')
            ->assertSee('dania.finance@izinyou.com')
            ->assertSee('Profil Staf Finance &amp; Administration', false);

        // 2. Perbarui profil & pastikan role tampering dicegah
        $this->actingAs($this->financeUser)
            ->put(route('finance.profile.update'), [
                'name' => 'Siti Dania Rahayu',
                'email' => 'dania.rahayu@izinyou.com',
                'phone' => '+6281233445566',
                'pic_position' => 'Senior Finance Manager',
                'company_name' => 'CV Prasta Solusi Indonesia',
                'role' => 'admin', // Tampering ditolak / diabaikan
            ])
            ->assertRedirect(route('finance.profile.edit'))
            ->assertSessionHas('success');

        $this->financeUser->refresh();
        $this->assertSame('Siti Dania Rahayu', $this->financeUser->name);
        $this->assertSame('dania.rahayu@izinyou.com', $this->financeUser->email);
        $this->assertSame('081233445566', $this->financeUser->phone);
        $this->assertSame('Senior Finance Manager', $this->financeUser->pic_position);
        $this->assertSame('finance', $this->financeUser->role); // Role tetap finance

        // 3. Ganti kata sandi
        $this->actingAs($this->financeUser)
            ->put(route('finance.profile.password'), [
                'current_password' => 'password',
                'password' => 'BaruAmanFinance2026',
                'password_confirmation' => 'BaruAmanFinance2026',
            ])
            ->assertRedirect(route('finance.profile.edit'))
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('BaruAmanFinance2026', $this->financeUser->fresh()->password));
    }

    public function test_finance_officer_can_fetch_payment_two_way_audit_endpoint_json(): void
    {
        Storage::fake('local');

        $order = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-AUDIT-JSON-01',
            'total_cost' => 5000000,
            'base_price' => 5000000,
            'status' => 'pending',
        ]);

        $filePath = 'payment_proofs/'.$order->id.'/bca_receipt.jpg';
        $jpegContent = "\xFF\xD8\xFF\xE0".'Transfer Berhasil m-BCA Rekening Tujuan PT PRASTA SOLUSI INDONESIA 5415778899'."\xFF\xD9";
        Storage::disk('local')->put($filePath, $jpegContent);

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => 5000000,
            'payment_method' => 'Transfer Bank BCA',
            'status' => 'waiting_confirmation',
            'payment_proof' => $filePath,
        ]);

        $response = $this->actingAs($this->financeUser)
            ->get(route('finance.payment.audit', $payment->id));

        $response->assertOk()
            ->assertJsonStructure([
                'payment_id',
                'overall_score',
                'verdict_code',
                'verdict_label',
                'badge_color',
                'direction_1' => [
                    'amount' => ['status', 'score', 'message', 'expected', 'actual'],
                    'reference' => ['status', 'score', 'message', 'order_number'],
                    'destination' => ['status', 'score', 'message', 'channel'],
                    'timing' => ['status', 'score', 'message'],
                ],
                'direction_2' => [
                    'manipulation' => ['detected', 'score', 'message', 'software'],
                    'bank_signature' => ['matched', 'score', 'message', 'matched_keywords_count'],
                    'integrity' => ['valid', 'score', 'message'],
                    'anti_replay' => ['is_duplicate', 'score', 'message', 'duplicate_order_id'],
                ],
                'recommendation',
            ]);

        $json = $response->json();
        $this->assertSame($payment->id, $json['payment_id']);
        $this->assertSame('valid', $json['direction_1']['amount']['status']);
        $this->assertFalse($json['direction_2']['manipulation']['detected']);
        $this->assertGreaterThanOrEqual(80, $json['overall_score']);
        $this->assertSame('AUTHENTIC', $json['verdict_code']);
    }

    public function test_two_way_verification_detects_amount_mismatch_and_flags_verdict(): void
    {
        Storage::fake('local');

        $order = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-MISMATCH-99',
            'total_cost' => 5000000,
            'base_price' => 5000000,
            'status' => 'pending',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => 3000000,
            'payment_method' => 'Transfer Bank Mandiri',
            'status' => 'waiting_confirmation',
        ]);

        $service = app(TwoWayPaymentVerificationService::class);
        $result = $service->verify($payment);

        $this->assertSame('underpaid', $result['direction_1']['amount']['status']);
        $this->assertSame('AMOUNT_MISMATCH', $result['verdict_code']);
        $this->assertSame('amber', $result['badge_color']);
        $this->assertStringContainsString('Kurang Bayar', $result['verdict_label']);
        $this->assertSame(3000000.0, (float) $result['direction_1']['amount']['actual']);
        $this->assertSame(5000000.0, (float) $result['direction_1']['amount']['expected']);
        $this->assertStringContainsString('kurang Rp 2.000.000', $result['suggested_rejection']);
    }

    public function test_two_way_verification_detects_image_manipulation_signatures(): void
    {
        Storage::fake('local');

        $order = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-FAKE-EDIT-01',
            'total_cost' => 5000000,
            'base_price' => 5000000,
            'status' => 'pending',
        ]);

        $fakeProofPath = 'payment_proofs/'.$order->id.'/edited_photoshop.png';
        $pngWithPhotoshop = "\x89PNG\r\n\x1a\n".'IHDR'.str_repeat("\x00", 20).'Software: Adobe Photoshop CC 2024'."IEND\xaeB`\x82";
        Storage::disk('local')->put($fakeProofPath, $pngWithPhotoshop);

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => 5000000,
            'payment_method' => 'Transfer Bank BCA',
            'status' => 'waiting_confirmation',
            'payment_proof' => $fakeProofPath,
        ]);

        $service = app(TwoWayPaymentVerificationService::class);
        $result = $service->verify($payment);

        $this->assertTrue($result['direction_2']['manipulation']['detected']);
        $this->assertSame('SUSPICIOUS_MANIPULATION', $result['verdict_code']);
        $this->assertSame('rose', $result['badge_color']);
        $this->assertSame('Adobe Photoshop', $result['direction_2']['manipulation']['software']);
        $this->assertStringContainsString('manipulasi digital', $result['suggested_rejection']);
    }

    public function test_two_way_verification_detects_duplicate_proof_replay(): void
    {
        Storage::fake('local');

        $order1 = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-ORIGINAL-01',
            'total_cost' => 5000000,
            'base_price' => 5000000,
            'status' => 'processing',
        ]);

        $fileContent = "\xFF\xD8\xFF\xE0".'Authentic Bank Transfer Receipt Content 2026'."\xFF\xD9";
        $path1 = 'payment_proofs/'.$order1->id.'/proof1.jpg';
        Storage::disk('local')->put($path1, $fileContent);

        Payment::create([
            'order_id' => $order1->id,
            'amount' => 5000000,
            'payment_method' => 'Transfer Bank BCA',
            'status' => 'success',
            'payment_proof' => $path1,
        ]);

        $order2 = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-REPLAY-02',
            'total_cost' => 5000000,
            'base_price' => 5000000,
            'status' => 'pending',
        ]);

        $path2 = 'payment_proofs/'.$order2->id.'/proof2.jpg';
        Storage::disk('local')->put($path2, $fileContent);

        $payment2 = Payment::create([
            'order_id' => $order2->id,
            'amount' => 5000000,
            'payment_method' => 'Transfer Bank BCA',
            'status' => 'waiting_confirmation',
            'payment_proof' => $path2,
        ]);

        $service = app(TwoWayPaymentVerificationService::class);
        $result = $service->verify($payment2);

        $this->assertTrue($result['direction_2']['anti_replay']['is_duplicate']);
        $this->assertSame('DUPLICATE_FLAGGED', $result['verdict_code']);
        $this->assertStringContainsString('identik 100%', $result['direction_2']['anti_replay']['message']);
    }

    public function test_approving_payment_records_two_way_verification_forensics_in_logs(): void
    {
        $order = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-AUDIT-LOG-01',
            'total_cost' => 5000000,
            'base_price' => 5000000,
            'status' => 'pending',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => 5000000,
            'payment_method' => 'Transfer Bank BCA',
            'status' => 'waiting_confirmation',
        ]);

        $this->actingAs($this->financeUser)
            ->post(route('finance.payment.verify', $payment->id), [
                'status' => 'success',
            ]);

        $this->assertDatabaseHas('payment_logs', [
            'payment_id' => $payment->id,
            'status' => 'success',
        ]);

        $log = PaymentLog::where('payment_id', $payment->id)->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString('Audit 2-Arah', $log->notes);
    }

    public function test_finance_officer_can_view_invoices_list_with_metrics_and_filters(): void
    {
        $order = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-INV-TEST-01',
            'total_cost' => 5000000,
            'base_price' => 5000000,
            'status' => 'pending',
        ]);

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'user_id' => $this->clientUser->id,
            'invoice_number' => 'INV-2026-TEST-001',
            'issued_at' => now(),
            'due_date' => now()->addDays(7),
            'customer_name' => $this->clientUser->name,
            'customer_company' => $this->clientUser->company_name,
            'customer_email' => $this->clientUser->email,
            'service_name' => $this->service->name,
            'subtotal' => 5000000,
            'discount_amount' => 0,
            'total_amount' => 5000000,
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->financeUser)
            ->get(route('finance.invoices'));

        $response->assertOk()
            ->assertSee('Manajemen &amp; Penerbitan Invoice Tagihan', false)
            ->assertSee('INV-2026-TEST-001')
            ->assertSee('ORD-INV-TEST-01')
            ->assertSee('PT Mitra Sejahtera')
            ->assertSee('Menunggu Pembayaran');

        // Filter status unpaid
        $this->actingAs($this->financeUser)
            ->get(route('finance.invoices', ['status' => 'unpaid']))
            ->assertOk()
            ->assertSee('INV-2026-TEST-001');

        // Filter status paid (should not show this unpaid invoice)
        $this->actingAs($this->financeUser)
            ->get(route('finance.invoices', ['status' => 'paid']))
            ->assertOk()
            ->assertDontSee('INV-2026-TEST-001');
    }

    public function test_finance_officer_can_issue_new_invoice_for_unbilled_order(): void
    {
        $order = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-UNBILLED-01',
            'total_cost' => 5000000,
            'base_price' => 5000000,
            'status' => 'pending',
        ]);

        $this->assertDatabaseMissing('invoices', [
            'order_id' => $order->id,
        ]);

        $response = $this->actingAs($this->financeUser)
            ->post(route('finance.invoices.issue'), [
                'order_id' => $order->id,
                'due_date' => now()->addDays(14)->toDateString(),
                'invoice_number' => 'INV-MANUAL-2026-001',
                'notes' => 'Tagihan termin awal sebelum pengajuan izin edar alkes.',
            ]);

        $response->assertRedirect(route('finance.invoices'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('invoices', [
            'order_id' => $order->id,
            'invoice_number' => 'INV-MANUAL-2026-001',
            'total_amount' => 5000000,
        ]);
    }

    public function test_finance_officer_can_update_invoice_payment_status(): void
    {
        $order = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-INV-UPDATE-01',
            'total_cost' => 5000000,
            'base_price' => 5000000,
            'status' => 'pending',
        ]);

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'user_id' => $this->clientUser->id,
            'invoice_number' => 'INV-UPDATE-001',
            'issued_at' => now(),
            'due_date' => now()->addDays(7),
            'customer_name' => $this->clientUser->name,
            'customer_company' => $this->clientUser->company_name,
            'service_name' => $this->service->name,
            'subtotal' => 5000000,
            'discount_amount' => 0,
            'total_amount' => 5000000,
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->financeUser)
            ->post(route('finance.invoices.status', $invoice->id), [
                'payment_status' => 'paid',
                'notes' => 'Pembayaran telah disetorkan via setoran kliring tunai bank.',
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'payment_status' => 'paid',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing',
        ]);
    }

    public function test_finance_officer_can_update_invoice_due_date(): void
    {
        $order = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-INV-DUE-01',
            'total_cost' => 5000000,
            'base_price' => 5000000,
            'status' => 'pending',
        ]);

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'user_id' => $this->clientUser->id,
            'invoice_number' => 'INV-DUE-001',
            'issued_at' => now(),
            'due_date' => now()->addDays(7),
            'customer_name' => $this->clientUser->name,
            'service_name' => $this->service->name,
            'subtotal' => 5000000,
            'discount_amount' => 0,
            'total_amount' => 5000000,
            'payment_status' => 'unpaid',
        ]);

        $newDueDate = now()->addDays(30)->toDateString();

        $response = $this->actingAs($this->financeUser)
            ->post(route('finance.invoices.due-date', $invoice->id), [
                'due_date' => $newDueDate,
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame($newDueDate, $invoice->fresh()->due_date?->toDateString());
    }

    public function test_finance_officer_can_view_cash_flow_and_revenue_reports(): void
    {
        $orderPaid = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-REP-PAID',
            'total_cost' => 4500000,
            'base_price' => 5000000,
            'discount_amount' => 500000,
            'promo_code' => 'HEMAT10',
            'status' => 'processing',
        ]);

        Payment::create([
            'order_id' => $orderPaid->id,
            'amount' => 4500000,
            'payment_method' => 'Transfer Bank BCA',
            'status' => 'success',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->financeUser)
            ->get(route('finance.reports', ['period' => 'this_month']));

        $response->assertOk()
            ->assertSee('Laporan Rekapitulasi Pendapatan &amp; Arus Kas', false)
            ->assertSee('Arus Kas Masuk Riil')
            ->assertSee('4.500.000')
            ->assertSee('Transfer Bank BCA')
            ->assertSee('ORD-REP-PAID');
    }

    public function test_finance_officer_can_export_cash_flow_and_revenue_report_csv(): void
    {
        $order = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-EXPORT-CASHFLOW',
            'total_cost' => 5000000,
            'base_price' => 5000000,
            'status' => 'processing',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'amount' => 5000000,
            'payment_method' => 'Transfer Bank Mandiri',
            'status' => 'success',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->financeUser)
            ->get(route('finance.reports.export', ['period' => 'this_month']));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', (string) $response->headers->get('Content-Disposition'));
    }

    public function test_finance_officer_can_filter_transactions_by_date_and_payment_method(): void
    {
        $orderBca = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-FLTR-BCA',
            'total_cost' => 5000000,
            'base_price' => 5000000,
            'status' => 'processing',
            'created_at' => now(),
        ]);

        Payment::create([
            'order_id' => $orderBca->id,
            'amount' => 5000000,
            'payment_method' => 'Transfer Bank BCA',
            'status' => 'success',
            'paid_at' => now(),
        ]);

        $orderQris = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-FLTR-QRIS',
            'total_cost' => 2000000,
            'base_price' => 2000000,
            'status' => 'processing',
            'created_at' => now(),
        ]);

        Payment::create([
            'order_id' => $orderQris->id,
            'amount' => 2000000,
            'payment_method' => 'QRIS',
            'status' => 'success',
            'paid_at' => now(),
        ]);

        // Filter by BCA method
        $responseBca = $this->actingAs($this->financeUser)
            ->get(route('finance.transactions', ['payment_method' => 'bca']));

        $responseBca->assertOk()
            ->assertSee('ORD-FLTR-BCA')
            ->assertDontSee('ORD-FLTR-QRIS');

        // Filter by QRIS method
        $responseQris = $this->actingAs($this->financeUser)
            ->get(route('finance.transactions', ['payment_method' => 'qris']));

        $responseQris->assertOk()
            ->assertSee('ORD-FLTR-QRIS')
            ->assertDontSee('ORD-FLTR-BCA');
    }

    public function test_finance_officer_can_export_transactions_xlsx(): void
    {
        $order = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-EXP-XLSX',
            'total_cost' => 3500000,
            'base_price' => 3500000,
            'status' => 'processing',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'amount' => 3500000,
            'payment_method' => 'Transfer Bank BCA',
            'status' => 'success',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->financeUser)
            ->get(route('finance.export.xlsx'));

        $response->assertOk();
        $this->assertStringContainsString('spreadsheetml.sheet', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment;', (string) $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.xlsx', (string) $response->headers->get('Content-Disposition'));

        $binary = $response->getContent();
        $this->assertStringStartsWith("PK\x03\x04", $binary, 'Exported XLSX file should begin with standard PKZip magic bytes');
    }

    public function test_finance_officer_can_export_transactions_pdf(): void
    {
        $order = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-EXP-PDF',
            'total_cost' => 4500000,
            'base_price' => 4500000,
            'status' => 'processing',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'amount' => 4500000,
            'payment_method' => 'Transfer Bank Mandiri',
            'status' => 'success',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->financeUser)
            ->get(route('finance.export.pdf'));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment;', (string) $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.pdf', (string) $response->headers->get('Content-Disposition'));

        $binary = $response->getContent();
        $this->assertStringStartsWith('%PDF-1.4', $binary, 'Exported PDF file should begin with %PDF-1.4 header');
    }

    public function test_finance_officer_can_export_cash_flow_report_xlsx(): void
    {
        $order = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-REP-XLSX',
            'total_cost' => 7500000,
            'base_price' => 7500000,
            'status' => 'completed',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'amount' => 7500000,
            'payment_method' => 'Transfer Bank BNI',
            'status' => 'success',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->financeUser)
            ->get(route('finance.reports.export.xlsx', ['period' => 'this_month']));

        $response->assertOk();
        $this->assertStringContainsString('spreadsheetml.sheet', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment;', (string) $response->headers->get('Content-Disposition'));
        $this->assertStringContainsStringIgnoringCase('laporan_arus_kas_', (string) $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.xlsx', (string) $response->headers->get('Content-Disposition'));

        $binary = $response->getContent();
        $this->assertStringStartsWith("PK\x03\x04", $binary, 'Exported cash flow report XLSX should begin with PKZip magic bytes');
    }

    public function test_finance_officer_can_export_cash_flow_report_pdf(): void
    {
        $order = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-REP-PDF',
            'total_cost' => 6000000,
            'base_price' => 6000000,
            'status' => 'completed',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'amount' => 6000000,
            'payment_method' => 'Transfer Bank BCA',
            'status' => 'success',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->financeUser)
            ->get(route('finance.reports.export.pdf', ['period' => 'this_month']));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment;', (string) $response->headers->get('Content-Disposition'));
        $this->assertStringContainsStringIgnoringCase('laporan_arus_kas_', (string) $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.pdf', (string) $response->headers->get('Content-Disposition'));

        $binary = $response->getContent();
        $this->assertStringStartsWith('%PDF-1.4', $binary, 'Exported cash flow report PDF should begin with %PDF-1.4 header');
    }

    public function test_unauthorized_users_cannot_access_export_endpoints(): void
    {
        // Guest is redirected to login
        $this->get(route('finance.export.xlsx'))->assertRedirect(route('login'));
        $this->get(route('finance.export.pdf'))->assertRedirect(route('login'));
        $this->get(route('finance.reports.export.xlsx'))->assertRedirect(route('login'));
        $this->get(route('finance.reports.export.pdf'))->assertRedirect(route('login'));

        // Client role is redirected with RBAC protection
        $this->actingAs($this->clientUser)->get(route('finance.export.xlsx'))->assertRedirect(route('klien.dashboard'));
        $this->actingAs($this->clientUser)->get(route('finance.export.pdf'))->assertRedirect(route('klien.dashboard'));
        $this->actingAs($this->clientUser)->get(route('finance.reports.export.xlsx'))->assertRedirect(route('klien.dashboard'));
        $this->actingAs($this->clientUser)->get(route('finance.reports.export.pdf'))->assertRedirect(route('klien.dashboard'));
    }

    public function test_finance_officer_sees_cancelled_order_and_cancellation_reason_on_dashboard_and_transactions(): void
    {
        // 1. Pelanggan membuat pesanan
        $order = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-CANCEL-TEST',
            'total_cost' => 4500000,
            'base_price' => 4500000,
            'status' => 'pending',
            'created_at' => now(),
        ]);

        // Pelanggan membatalkan pesanan dengan alasan tertentu
        $cancelReason = 'Pelanggan salah memilih paket layanan & ingin mengganti ke PT PMA';
        $order->update([
            'status' => 'cancelled',
            'current_stage' => 'Pesanan Dibatalkan',
        ]);

        ProjectTracking::create([
            'order_id' => $order->id,
            'stage_code' => 'order_cancelled',
            'stage_order' => 0,
            'stage_name' => 'Pesanan Dibatalkan',
            'description' => 'Pesanan dibatalkan oleh pelanggan. Alasan: '.$cancelReason,
            'status' => 'cancelled',
            'progress_percentage' => 15,
            'date_recorded' => now(),
            'updated_by' => $this->clientUser->id,
        ]);

        PaymentLog::create([
            'order_id' => $order->id,
            'payment_id' => null,
            'status' => 'cancelled',
            'notes' => 'Pesanan dibatalkan oleh pelanggan. Alasan: '.$cancelReason,
            'changed_by' => $this->clientUser->id,
        ]);

        // Buat satu pesanan aktif lain sebagai pembanding
        $activeOrder = Order::create([
            'user_id' => $this->clientUser->id,
            'service_id' => $this->service->id,
            'order_number' => 'ORD-ACTIVE-TEST',
            'total_cost' => 2500000,
            'base_price' => 2500000,
            'status' => 'processing',
            'created_at' => now(),
        ]);

        // 2. Finance membuka Dasbor Utama Keuangan
        $dashboardResponse = $this->actingAs($this->financeUser)
            ->get(route('finance.dashboard'));

        $dashboardResponse->assertOk()
            ->assertSee('ORD-CANCEL-TEST')
            ->assertSee('Informasi Pembatalan')
            ->assertSee('Dibatalkan')
            ->assertSee('Pelanggan salah memilih paket layanan');

        // 3. Finance membuka Monitoring Transaksi & Status Pelanggan
        $txResponse = $this->actingAs($this->financeUser)
            ->get(route('finance.transactions'));

        $txResponse->assertOk()
            ->assertSee('ORD-CANCEL-TEST')
            ->assertSee('Pesanan Dibatalkan')
            ->assertSee('Keterangan Pembatalan:')
            ->assertSee('Pelanggan salah memilih paket layanan &amp; ingin mengganti ke PT PMA', false)
            ->assertSee('Dibatalkan (1)');

        // 4. Finance menggunakan Filter Tab khusus "Dibatalkan"
        $filterCancelledResponse = $this->actingAs($this->financeUser)
            ->get(route('finance.transactions', ['payment_status' => 'cancelled']));

        $filterCancelledResponse->assertOk()
            ->assertSee('ORD-CANCEL-TEST')
            ->assertDontSee('ORD-ACTIVE-TEST');
    }
}
