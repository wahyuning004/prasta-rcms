<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ClientDocument;
use App\Models\ClientDocumentVersion;
use App\Models\FreeConsultation;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Promo;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceRequirement;
use App\Models\User;
use App\Services\ServiceRequirementSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuditFeaturesAToLTest extends TestCase
{
    use RefreshDatabase;

    private User $client;

    private User $otherClient;

    private User $financeStaff;

    private User $legalStaff;

    private User $marketingStaff;

    private User $adminUser;

    private Category $category;

    private Category $secondCategory;

    private Service $service;

    private Service $secondService;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true], 200),
        ]);

        $this->client = User::factory()->create([
            'name' => 'Andi Pratama',
            'email' => 'andi@prasta-audit.co.id',
            'phone' => '081111222333',
            'company_name' => 'PT Prasta Audit Mitra',
            'address' => 'Jl. Raya Bekasi Km. 20',
            'password' => Hash::make('AuditPass123'),
            'role' => 'klien',
            'email_verified_at' => now(),
        ]);

        $this->otherClient = User::factory()->create([
            'name' => 'Rina Kusuma',
            'email' => 'rina@other-company.co.id',
            'phone' => '081999888777',
            'company_name' => 'CV Kompetitor Lain',
            'address' => 'Jl. Sudirman No. 99',
            'password' => Hash::make('OtherPass123'),
            'role' => 'klien',
            'email_verified_at' => now(),
        ]);

        $this->financeStaff = User::factory()->create([
            'name' => 'Petugas Finance Audit',
            'email' => 'finance.audit@izinyou.com',
            'phone' => '081200001001',
            'role' => 'finance',
            'email_verified_at' => now(),
        ]);

        $this->legalStaff = User::factory()->create([
            'name' => 'Konsultan Legal Audit',
            'email' => 'legal.audit@izinyou.com',
            'phone' => '081200001002',
            'role' => 'legal',
            'email_verified_at' => now(),
        ]);

        $this->marketingStaff = User::factory()->create([
            'name' => 'Staff Marketing Audit',
            'email' => 'marketing.audit@izinyou.com',
            'phone' => '081200001003',
            'role' => 'marketing',
            'email_verified_at' => now(),
        ]);

        $this->adminUser = User::factory()->create([
            'name' => 'Admin Audit',
            'email' => 'admin.audit@izinyou.com',
            'phone' => '081200001004',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->category = Category::create([
            'name' => 'Sertifikasi Halal & SNI',
            'slug' => 'halal-sni',
            'description' => 'Layanan sertifikasi mutu dan kehalalan produk',
        ]);

        $this->secondCategory = Category::create([
            'name' => 'Alat Kesehatan & BPOM',
            'slug' => 'alkes-bpom',
            'description' => 'Perizinan alat kesehatan dan kosmetika',
        ]);

        $this->service = Service::create([
            'category_id' => $this->category->id,
            'name' => 'Sertifikasi Produk SNI Industri',
            'slug' => 'sertifikasi-produk-sni-industri',
            'description' => 'Pendampingan lengkap SNI wajib maupun sukarela hingga LSPro terbit.',
            'price' => 10000000,
            'estimated_price' => 10000000,
            'estimated_days' => 45,
            'timeline' => '45 Hari Kerja',
            'requirements_info' => "Akta Pendirian & SK Kemenkumham\nNomor Induk Berusaha (NIB) & KBLI Sesuai\nNPWP Badan Usaha\n[Opsional] Katalog Spesifikasi Teknis Produk",
        ]);

        $this->secondService = Service::create([
            'category_id' => $this->secondCategory->id,
            'name' => 'Izin Edar Alat Kesehatan AKL',
            'slug' => 'izin-edar-akl',
            'description' => 'Registrasi izin edar AKL Kementerian Kesehatan.',
            'price' => 6000000,
            'estimated_price' => 6000000,
            'estimated_days' => 30,
            'timeline' => '30 Hari Kerja',
            'requirements_info' => "Letter of Authorization (LoA)\nCertificate of Free Sale (CFS)",
        ]);

        $sync = app(ServiceRequirementSyncService::class);
        $sync->ensureForService($this->service);
        $sync->ensureForService($this->secondService);
    }

    public function test_feature_a_homepage_renders_db_data_and_has_no_dead_layanan_anchor(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee($this->service->name);
        $response->assertSee(route('catalog.index'), false);
        $response->assertDontSee('href="#layanan"', false);
    }

    public function test_feature_b_free_consultation_positive_and_negative_validation(): void
    {
        // Negative: guest sends empty payload
        $invalidGuest = $this->post(route('services.consultation'), [
            'name' => '',
            'phone' => '',
            'service_name' => $this->service->name,
            'missing_doc_category' => '',
            'message' => '',
        ]);

        $invalidGuest->assertSessionHasErrors(['name', 'phone', 'missing_doc_category', 'message']);
        $this->assertSame(0, FreeConsultation::count());

        // Positive: guest submits complete consultation on service detail page
        $validGuest = $this->from(route('services.show', $this->service->slug))
            ->post(route('services.consultation'), [
                'name' => 'Bapak Hendra',
                'phone' => '081234500999',
                'email' => 'hendra@industri.co.id',
                'company' => 'PT Industri Maju',
                'service_name' => $this->service->name,
                'missing_doc_category' => 'Belum Memiliki SOP Mutu & Pedoman Kerja Tertulis',
                'message' => 'Kami butuh konsultasi penyusunan SOP sebelum audit SNI.',
            ]);

        $validGuest->assertRedirect(route('services.show', $this->service->slug));
        $validGuest->assertSessionHas('success');

        $this->assertDatabaseHas('free_consultations', [
            'name' => 'Bapak Hendra',
            'phone' => '081234500999',
            'status' => 'pending',
        ]);

        // Positive & Negative: authenticated client dashboard consultation
        $this->actingAs($this->client)
            ->post(route('klien.consultation.submit'), ['subject' => '', 'message' => ''])
            ->assertSessionHasErrors(['subject', 'message']);

        $this->actingAs($this->client)
            ->post(route('klien.consultation.submit'), [
                'subject' => 'Tanya Estimasi Uji Lab SNI',
                'message' => 'Berapa lama antrian laboratorium saat ini?',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('free_consultations', [
            'user_id' => $this->client->id,
            'subject' => 'Tanya Estimasi Uji Lab SNI',
        ]);
    }

    public function test_feature_c_contact_hub_channels_and_links_are_valid(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('id="kontak"', false);
        $response->assertSee('https://wa.me/6282128765874', false);
        $response->assertSee('mailto:prastasolusi.indonesia@gmail.com', false);
        $response->assertSee('Babelan, Kab. Bekasi');
    }

    public function test_feature_d_registration_and_otp_positive_and_negative_security(): void
    {
        // Negative: missing reCAPTCHA fails
        $this->post(route('register.post'), [
            'name' => 'Calon Klien Audit',
            'company' => 'PT Uji Captcha',
            'email' => 'calon@captcha.co.id',
            'phone' => '081355556666',
            'password' => 'AmanPass123',
            'password_confirmation' => 'AmanPass123',
            'terms' => 'on',
            'g-recaptcha-response' => '',
        ])->assertSessionHasErrors(['g-recaptcha-response']);

        // Negative: duplicate email & duplicate phone rejected
        $this->post(route('register.post'), [
            'name' => 'Duplikat Email',
            'company' => 'PT Duplikat',
            'email' => $this->client->email,
            'phone' => '081377778888',
            'password' => 'AmanPass123',
            'password_confirmation' => 'AmanPass123',
            'terms' => 'on',
            'g-recaptcha-response' => 'ok',
        ])->assertSessionHasErrors(['email']);

        $this->post(route('register.post'), [
            'name' => 'Duplikat HP',
            'company' => 'PT Duplikat',
            'email' => 'unik@baru.co.id',
            'phone' => $this->client->phone,
            'password' => 'AmanPass123',
            'password_confirmation' => 'AmanPass123',
            'terms' => 'on',
            'g-recaptcha-response' => 'ok',
        ])->assertSessionHasErrors(['phone']);

        // Positive registration
        $regResponse = $this->post(route('register.post'), [
            'name' => 'Surya Permana',
            'company' => 'PT Surya Regulasi Indonesia',
            'email' => 'surya@regulasi.co.id',
            'phone' => '081344445555',
            'password' => 'SuryaPass123',
            'password_confirmation' => 'SuryaPass123',
            'terms' => 'on',
            'g-recaptcha-response' => 'ok',
        ]);

        $regResponse->assertRedirect(route('otp.verify'));
        $registered = User::where('email', 'surya@regulasi.co.id')->firstOrFail();
        $this->assertNull($registered->email_verified_at);
        $this->assertNotNull($registered->otp_code);

        // Render verify-otp page
        $this->get(route('otp.verify'))->assertOk()->assertSee('081344445555');

        // Negative: resend OTP within 60-second cooldown is blocked
        $this->post(route('otp.resend'))->assertSessionHasErrors(['otp']);

        // Travel 61 seconds & resend succeeds
        $this->travel(61)->seconds();
        $this->post(route('otp.resend'))->assertSessionHas('success');
        $registered->refresh();

        // Negative: expired OTP rejected
        $registered->forceFill(['otp_expires_at' => now()->subMinutes(1)])->save();
        $this->post(route('otp.verify.post'), ['otp' => $registered->otp_code])
            ->assertSessionHasErrors(['otp']);

        // Reset expiry, test 5 wrong OTP attempts & brute-force lock
        $registered->forceFill(['otp_expires_at' => now()->addMinutes(10)])->save();
        $wrongCode = $registered->otp_code === '000000' ? '111111' : '000000';
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('otp.verify.post'), ['otp' => $wrongCode])
                ->assertSessionHasErrors(['otp']);
        }

        // 6th attempt even with old valid code is blocked and OTP is wiped
        $this->post(route('otp.verify.post'), ['otp' => $registered->otp_code])
            ->assertSessionHasErrors(['otp']);
        $registered->refresh();
        $this->assertNull($registered->otp_code);

        // Travel 61s, resend fresh OTP, and verify with per-digit array payload
        $this->travel(61)->seconds();
        $this->post(route('otp.resend'))->assertSessionHas('success');
        $registered->refresh();
        $digits = str_split((string) $registered->otp_code);

        $verifyOk = $this->post(route('otp.verify.post'), ['otp' => $digits]);
        $verifyOk->assertRedirect(route('klien.dashboard'));
        $this->assertAuthenticatedAs($registered);
        $this->assertNotNull($registered->fresh()->email_verified_at);
    }

    public function test_feature_e_login_positive_negative_and_unverified_client_otp_gate(): void
    {
        // Negative: missing CAPTCHA
        $this->post(route('login.post'), [
            'email' => $this->client->email,
            'password' => 'AuditPass123',
            'g-recaptcha-response' => '',
        ])->assertSessionHasErrors(['g-recaptcha-response']);
        $this->assertGuest();

        // Negative: wrong password
        $this->post(route('login.post'), [
            'email' => $this->client->email,
            'password' => 'WrongPass999',
            'g-recaptcha-response' => 'ok',
        ])->assertSessionHasErrors(['email']);
        $this->assertGuest();

        // Unverified client logging in is redirected to 2FA WhatsApp OTP verification
        $unverified = User::factory()->create([
            'email' => 'belum.aktif@mitra.co.id',
            'phone' => '081566667777',
            'password' => Hash::make('Pass2FA123'),
            'role' => 'klien',
            'email_verified_at' => null,
        ]);

        $loginUnverified = $this->post(route('login.post'), [
            'email' => 'belum.aktif@mitra.co.id',
            'password' => 'Pass2FA123',
            'g-recaptcha-response' => 'ok',
        ]);

        $loginUnverified->assertRedirect(route('otp.verify'));
        $this->assertGuest();
        $unverified->refresh();
        $this->assertNotNull($unverified->otp_code);

        // Completing 2FA OTP logs client in
        $this->post(route('otp.verify.post'), ['otp' => $unverified->otp_code])
            ->assertRedirect(route('klien.dashboard'));
        $this->assertAuthenticatedAs($unverified);

        // Logout clears session cleanly
        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_feature_f_profile_validation_isolation_and_password_update(): void
    {
        // Guest blocked
        $this->get(route('klien.profile.edit'))->assertRedirect(route('login'));

        // Non-client role blocked by CheckRole middleware
        $this->actingAs($this->financeStaff)
            ->get(route('klien.profile.edit'))
            ->assertRedirect(route('finance.dashboard'))
            ->assertSessionHas('error');

        // Invalid NIB & NPWP rejected
        $this->actingAs($this->client)
            ->put(route('klien.profile.update'), [
                'name' => $this->client->name,
                'email' => $this->client->email,
                'phone' => $this->client->phone,
                'company_name' => $this->client->company_name,
                'address' => $this->client->address,
                'nib' => '12345',
                'npwp' => 'abc',
            ])
            ->assertSessionHasErrors(['nib', 'npwp']);

        // Wrong current password rejected
        $this->actingAs($this->client)
            ->put(route('klien.profile.password'), [
                'current_password' => 'SalahSandi1',
                'password' => 'SandiBaru99',
                'password_confirmation' => 'SandiBaru99',
            ])
            ->assertSessionHasErrors(['current_password']);

        // Valid password update succeeds and preserves other client untouched
        $this->actingAs($this->client)
            ->put(route('klien.profile.password'), [
                'current_password' => 'AuditPass123',
                'password' => 'SandiBaru99',
                'password_confirmation' => 'SandiBaru99',
            ])
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('SandiBaru99', $this->client->fresh()->password));
        $this->assertSame('rina@other-company.co.id', $this->otherClient->fresh()->email);
    }

    public function test_feature_g_catalog_rating_filter_unlimited_promo_and_404_slug(): void
    {
        // Create published 5-star review on service 1
        $completedOrder = Order::create([
            'order_number' => 'ORD-REV-CAT-01',
            'user_id' => $this->client->id,
            'service_id' => $this->service->id,
            'base_price' => 10000000,
            'discount_amount' => 0,
            'total_cost' => 10000000,
            'status' => 'completed',
            'current_stage' => 'Selesai',
            'progress_percentage' => 100,
        ]);

        Review::create([
            'order_id' => $completedOrder->id,
            'user_id' => $this->client->id,
            'service_id' => $this->service->id,
            'rating' => 5,
            'comment' => 'Sangat membantu hingga sertifikat SNI terbit!',
            'status' => 'published',
        ]);

        // Unlimited quota promo (quota = 0) should be visible on catalog & checkout
        Promo::create([
            'code' => 'UNLIMITED500',
            'title' => 'Diskon Tanpa Batas Kuota',
            'discount_amount' => 500000,
            'min_transaction' => 1000000,
            'quota' => 0,
            'used_count' => 0,
            'is_active' => true,
        ]);

        $catRating5 = $this->get(route('catalog.index', ['rating' => '5']));
        $catRating5->assertOk();
        $catRating5->assertSee($this->service->name);
        $catRating5->assertDontSee($this->secondService->name);
        $catRating5->assertSee('UNLIMITED500');

        // Service detail page has no dead #layanan links and renders requirements
        $detail = $this->get(route('services.show', $this->service->slug));
        $detail->assertOk();
        $detail->assertSee($this->service->name);
        $detail->assertDontSee('#layanan', false);

        // Non-existent service returns 404
        $this->get('/layanan/slug-layanan-tidak-ada-xyz')->assertNotFound();
    }

    public function test_feature_h_checkout_ignores_tampered_client_amounts_and_handles_no_promo(): void
    {
        // Percentage voucher with minimum transaction
        $promoPct = Promo::create([
            'code' => 'PERSEN15',
            'title' => 'Diskon 15%',
            'discount_percentage' => 15,
            'discount_amount' => 0,
            'min_transaction' => 8000000,
            'quota' => 10,
            'used_count' => 0,
            'is_active' => true,
            'expired_date' => now()->addDays(10),
        ]);

        // Negative AJAX voucher on secondService (6jt < min 8jt)
        $this->actingAs($this->client)
            ->postJson(route('klien.checkout.validate-voucher'), [
                'service_id' => $this->secondService->id,
                'promo_code' => 'PERSEN15',
            ])
            ->assertStatus(422)
            ->assertJson(['valid' => false]);

        // Negative: tampered amounts on POST checkout ignored; server calculates real total (10jt - 15% = 8.5jt)
        $tamperResp = $this->actingAs($this->client)
            ->post(route('klien.order.checkout'), [
                'service_id' => $this->service->id,
                'promo_code' => 'persen15',
                'base_price' => 1,
                'discount_amount' => 9999999,
                'total_cost' => 1,
                'checkout_token' => 'tok-tamper-audit-01',
            ]);

        $order = Order::where('checkout_token', 'tok-tamper-audit-01')->firstOrFail();
        $tamperResp->assertRedirect(route('klien.orders.confirmation', $order->order_number));
        $this->assertEquals(10000000.0, (float) $order->base_price);
        $this->assertEquals(1500000.0, (float) $order->discount_amount);
        $this->assertEquals(8500000.0, (float) $order->total_cost);
        $this->assertSame(1, $promoPct->fresh()->used_count);

        // Positive: checkout without promo
        $this->actingAs($this->client)
            ->post(route('klien.order.checkout'), [
                'service_id' => $this->secondService->id,
                'checkout_token' => 'tok-nopromo-audit-02',
            ]);

        $plainOrder = Order::where('checkout_token', 'tok-nopromo-audit-02')->firstOrFail();
        $this->assertEquals(6000000.0, (float) $plainOrder->total_cost);
        $this->assertEquals(0.0, (float) $plainOrder->discount_amount);
    }

    public function test_feature_i_payment_underpayment_reject_without_reason_and_isolation(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-PAY-AUD-01',
            'user_id' => $this->client->id,
            'service_id' => $this->service->id,
            'base_price' => 10000000,
            'discount_amount' => 1000000,
            'total_cost' => 9000000,
            'status' => 'pending',
            'current_stage' => 'Pesanan Diterima',
            'progress_percentage' => 15,
        ]);

        // Ensure invoice exists by opening confirmation
        $this->actingAs($this->client)
            ->get(route('klien.orders.confirmation', $order->order_number))
            ->assertOk();

        // Isolation: other client cannot view invoice or download PDF
        $this->actingAs($this->otherClient)->get(route('klien.orders.invoice', $order->id))->assertForbidden();
        $this->actingAs($this->otherClient)->get(route('klien.orders.invoice.pdf', $order->id))->assertForbidden();

        // Negative: underpayment & invalid exe proof rejected
        $this->actingAs($this->client)
            ->post(route('klien.orders.payment.store', $order->id), [
                'payment_method' => 'Transfer Bank Mandiri',
                'bank_name' => 'Mandiri',
                'sender_name' => 'Andi Pratama',
                'amount' => 1000000,
                'payment_proof' => UploadedFile::fake()->create('virus.exe', 100),
            ])
            ->assertSessionHasErrors(['amount', 'payment_proof']);

        // Positive: valid payment proof upload
        $this->actingAs($this->client)
            ->post(route('klien.orders.payment.store', $order->id), [
                'payment_method' => 'Transfer Bank Mandiri',
                'bank_name' => 'Mandiri',
                'sender_name' => 'Andi Pratama',
                'amount' => 9000000,
                'payment_proof' => UploadedFile::fake()->create('transfer.pdf', 300, 'application/pdf'),
            ])
            ->assertRedirect(route('klien.orders.show', $order->id));

        $payment = Payment::where('order_id', $order->id)->firstOrFail();

        // Negative: duplicate upload while waiting_confirmation blocked
        $this->actingAs($this->client)
            ->post(route('klien.orders.payment.store', $order->id), [
                'payment_method' => 'Transfer Bank BCA',
                'bank_name' => 'BCA',
                'sender_name' => 'Andi Pratama',
                'amount' => 9000000,
                'payment_proof' => UploadedFile::fake()->create('transfer2.pdf', 300, 'application/pdf'),
            ])
            ->assertSessionHas('error');

        // Negative: finance cannot reject without rejection reason
        $this->actingAs($this->financeStaff)
            ->post(route('finance.payment.verify', $payment->id), [
                'status' => 'failed',
                'rejection_reason' => '',
            ])
            ->assertSessionHasErrors(['rejection_reason']);

        // Positive: finance rejects with explicit reason, client sees reason & can re-upload
        $this->actingAs($this->financeStaff)
            ->post(route('finance.payment.verify', $payment->id), [
                'status' => 'failed',
                'rejection_reason' => 'Nominal pada struk transfer buram dan tidak terbaca.',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('invoices', [
            'order_id' => $order->id,
            'payment_status' => 'rejected',
        ]);

        // Isolation on payment proof file
        $this->actingAs($this->otherClient)
            ->get(route('payments.proof', $payment->id))
            ->assertForbidden();
    }

    public function test_feature_j_document_mismatched_requirement_missing_revision_note_and_rbac(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-DOC-AUD-01',
            'user_id' => $this->client->id,
            'service_id' => $this->service->id,
            'base_price' => 10000000,
            'discount_amount' => 0,
            'total_cost' => 10000000,
            'status' => 'processing',
            'current_stage' => 'Pemeriksaan Dokumen',
            'progress_percentage' => 50,
        ]);

        $wrongServiceReq = ServiceRequirement::where('service_id', $this->secondService->id)->firstOrFail();
        $validReq = ServiceRequirement::where('service_id', $this->service->id)->firstOrFail();

        // Negative: requirement from another service is rejected
        $this->actingAs($this->client)
            ->post(route('klien.orders.documents.upload', $order->id), [
                'service_requirement_id' => $wrongServiceReq->id,
                'document_file' => UploadedFile::fake()->create('wrong.pdf', 150, 'application/pdf'),
            ])
            ->assertSessionHas('error');
        $this->assertSame(0, ClientDocument::count());

        // Negative: disallowed file extension rejected
        $this->actingAs($this->client)
            ->post(route('klien.orders.documents.upload', $order->id), [
                'service_requirement_id' => $validReq->id,
                'document_file' => UploadedFile::fake()->create('shell.php', 50),
            ])
            ->assertSessionHasErrors(['document_file']);

        // Positive upload v1
        $this->actingAs($this->client)
            ->post(route('klien.orders.documents.upload', $order->id), [
                'service_requirement_id' => $validReq->id,
                'document_file' => UploadedFile::fake()->create('akta_v1.pdf', 200, 'application/pdf'),
            ])
            ->assertRedirect(route('klien.orders.show', $order->id));

        $doc = ClientDocument::where('order_id', $order->id)->firstOrFail();
        $v1 = ClientDocumentVersion::where('client_document_id', $doc->id)->where('version_number', 1)->firstOrFail();

        // Legal rejects v1 requesting revision
        $this->actingAs($this->legalStaff)
            ->post(route('legal.document.verify', $doc->id), [
                'status' => 'rejected',
                'notes' => 'Halaman SK Kemenkumham terpotong.',
            ])
            ->assertSessionHas('success');

        // Negative: client re-uploading rejected document without revision_note fails
        $this->actingAs($this->client)
            ->post(route('klien.orders.documents.upload', $order->id), [
                'service_requirement_id' => $validReq->id,
                'document_file' => UploadedFile::fake()->create('akta_v2.pdf', 220, 'application/pdf'),
                'revision_note' => '',
            ])
            ->assertSessionHasErrors(['revision_note']);

        // Positive upload v2 preserving v1 on disk
        $this->actingAs($this->client)
            ->post(route('klien.orders.documents.upload', $order->id), [
                'service_requirement_id' => $validReq->id,
                'document_file' => UploadedFile::fake()->create('akta_v2.pdf', 220, 'application/pdf'),
                'revision_note' => 'Sudah lengkap beserta SK Kemenkumham utuh',
            ])
            ->assertRedirect(route('klien.orders.show', $order->id));

        Storage::disk('local')->assertExists($v1->file_path);
        Storage::disk('local')->assertExists($doc->fresh()->file_path);

        // RBAC check on downloads: owner, legal, admin allowed; otherClient and marketing forbidden
        $this->actingAs($this->client)->get(route('documents.version.download', $v1->id))->assertOk();
        $this->actingAs($this->legalStaff)->get(route('documents.current.download', $doc->id))->assertOk();
        $this->actingAs($this->adminUser)->get(route('documents.current.download', $doc->id))->assertOk();
        $this->actingAs($this->otherClient)->get(route('documents.version.download', $v1->id))->assertForbidden();
        $this->actingAs($this->marketingStaff)->get(route('documents.current.download', $doc->id))->assertForbidden();
    }

    public function test_feature_k_tracking_dashboard_order_switcher_and_history_consistency(): void
    {
        $orderA = Order::create([
            'order_number' => 'ORD-TRK-A',
            'user_id' => $this->client->id,
            'service_id' => $this->service->id,
            'base_price' => 10000000,
            'discount_amount' => 0,
            'total_cost' => 10000000,
            'status' => 'pending',
            'current_stage' => 'Pesanan Diterima',
            'progress_percentage' => 15,
        ]);

        $orderB = Order::create([
            'order_number' => 'ORD-TRK-B',
            'user_id' => $this->client->id,
            'service_id' => $this->secondService->id,
            'base_price' => 6000000,
            'discount_amount' => 0,
            'total_cost' => 6000000,
            'status' => 'processing',
            'current_stage' => 'Proses Pengerjaan',
            'progress_percentage' => 75,
        ]);

        // Dashboard defaults to latest or selected order_id
        $this->actingAs($this->client)
            ->get(route('klien.dashboard', ['order_id' => $orderB->id]))
            ->assertOk()
            ->assertSee('ORD-TRK-B')
            ->assertSee('75%');

        $this->actingAs($this->client)
            ->get(route('klien.dashboard', ['order_id' => $orderA->id]))
            ->assertOk()
            ->assertSee('ORD-TRK-A')
            ->assertSee('15%');

        // Legal advances orderA to awaiting_approval (in_progress = 78%) then completed (100%)
        $this->actingAs($this->legalStaff)
            ->post(route('legal.tracking.update'), [
                'order_id' => $orderA->id,
                'stage_name' => 'awaiting_approval',
                'stage_status' => 'in_progress',
                'description' => 'Menunggu tanda tangan LSPro.',
            ])
            ->assertSessionHas('success');

        $orderA->refresh();
        $this->assertSame('Menunggu Persetujuan', $orderA->current_stage);
        $this->assertSame(78, (int) $orderA->progress_percentage);
        $this->assertDatabaseHas('project_trackings', [
            'order_id' => $orderA->id,
            'stage_name' => 'Menunggu Persetujuan',
            'status' => 'in_progress',
        ]);

        $this->actingAs($this->legalStaff)
            ->post(route('legal.tracking.update'), [
                'order_id' => $orderA->id,
                'stage_name' => 'completed',
                'stage_status' => 'completed',
                'description' => 'Sertifikat resmi terbit.',
            ])
            ->assertSessionHas('success');

        $orderA->refresh();
        $this->assertSame('completed', $orderA->status);
        $this->assertSame('Selesai', $orderA->current_stage);
        $this->assertSame(100, (int) $orderA->progress_percentage);

        // Isolation: other client cannot view orderA details
        $this->actingAs($this->otherClient)
            ->get(route('klien.orders.show', $orderA->id))
            ->assertForbidden();
    }

    public function test_feature_l_rating_negative_boundaries_and_non_completed_orders(): void
    {
        $processingOrder = Order::create([
            'order_number' => 'ORD-REV-PROC',
            'user_id' => $this->client->id,
            'service_id' => $this->service->id,
            'base_price' => 10000000,
            'discount_amount' => 0,
            'total_cost' => 10000000,
            'status' => 'processing',
            'current_stage' => 'Proses Pengerjaan',
            'progress_percentage' => 75,
        ]);

        $completedOrder = Order::create([
            'order_number' => 'ORD-REV-DONE',
            'user_id' => $this->client->id,
            'service_id' => $this->service->id,
            'base_price' => 10000000,
            'discount_amount' => 0,
            'total_cost' => 10000000,
            'status' => 'completed',
            'current_stage' => 'Selesai',
            'progress_percentage' => 100,
        ]);

        // Negative: processing order cannot be reviewed
        $this->actingAs($this->client)
            ->post(route('klien.orders.review.store', $processingOrder->id), [
                'rating' => 5,
                'comment' => 'Belum selesai kok dinilai.',
            ])
            ->assertSessionHas('error');
        $this->assertSame(0, Review::count());

        // Negative: out-of-range rating (0 and 6) and short comment (<5) rejected
        $this->actingAs($this->client)
            ->post(route('klien.orders.review.store', $completedOrder->id), [
                'rating' => 0,
                'comment' => 'Ok',
            ])
            ->assertSessionHasErrors(['rating', 'comment']);

        $this->actingAs($this->client)
            ->post(route('klien.orders.review.store', $completedOrder->id), [
                'rating' => 6,
                'comment' => 'Komentar cukup panjang tetapi bintang 6.',
            ])
            ->assertSessionHasErrors(['rating']);

        // Negative: other client blocked
        $this->actingAs($this->otherClient)
            ->post(route('klien.orders.review.store', $completedOrder->id), [
                'rating' => 1,
                'comment' => 'Ulasan palsu dari akun lain.',
            ])
            ->assertForbidden();

        // Positive: valid 4-star review published and visible publicly on homepage & service detail
        $this->actingAs($this->client)
            ->post(route('klien.orders.review.store', $completedOrder->id), [
                'rating' => 4,
                'comment' => 'Pendampingan SNI berjalan lancar dan responsif.',
            ])
            ->assertRedirect(route('klien.orders.show', $completedOrder->id));

        $this->get(route('services.show', $this->service->slug))
            ->assertOk()
            ->assertSee('Pendampingan SNI berjalan lancar dan responsif.');
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Pendampingan SNI berjalan lancar dan responsif.');
    }

    public function test_client_dashboard_order_flow_and_sidebar_deduplication(): void
    {
        $clientCatalogUrl = route('klien.services.index');
        $internalDetailUrl = route('klien.services.show', $this->service->id);
        $publicCatalogUrl = route('catalog.index');
        $consultationsUrl = route('klien.consultations.index');

        Promo::create([
            'code' => 'KATALOG10',
            'title' => 'Promo Khusus Katalog',
            'discount_percentage' => 10,
            'quota' => 25,
            'used_count' => 0,
            'is_active' => true,
            'expired_date' => now()->addMonth(),
        ]);

        // RBAC: Guest dan staf non-klien ditolak mengakses katalog & detail layanan internal klien
        $this->get($clientCatalogUrl)->assertRedirect(route('login'));
        $this->get($internalDetailUrl)->assertRedirect(route('login'));
        $this->actingAs($this->marketingStaff)->get($clientCatalogUrl)->assertRedirect(route('marketing.dashboard'));
        $this->actingAs($this->marketingStaff)->get($internalDetailUrl)->assertRedirect(route('marketing.dashboard'));

        $dashboard = $this->actingAs($this->client)->get(route('klien.dashboard'));
        $dashboard->assertOk();
        $dashboard->assertSee('<span>Katalog Layanan</span>', false);
        $dashboard->assertSee('href="'.$clientCatalogUrl.'"', false);
        $dashboard->assertDontSee('href="'.$publicCatalogUrl.'"', false);
        $dashboard->assertDontSee('<span>Ajukan Pertanyaan</span>', false);
        $dashboard->assertDontSee('<span>Konsultasi Perizinan</span>', false);
        $dashboard->assertDontSee('Tanya Konsultan');
        $dashboard->assertSee('<span class="hidden sm:inline">Ajukan Konsultasi</span>', false);
        $dashboard->assertSee('href="'.$consultationsUrl.'"', false);
        $dashboard->assertDontSee('id="konsultasi"', false);
        $dashboard->assertDontSee('Ajukan Pertanyaan Perizinan &amp; Sertifikasi', false);
        $dashboard->assertDontSee(route('klien.consultation.submit'), false);
        $dashboard->assertDontSee('id="layanan"', false);
        $dashboard->assertDontSee('Katalog Layanan &amp; Pemesanan Perizinan', false);
        $dashboard->assertDontSee('Voucher Promo Diskon Tersedia!');
        $dashboard->assertDontSee('dashboardServicesGrid');
        $dashboard->assertDontSee('<span>Profil Perusahaan &amp; Akun</span>', false);
        $dashboard->assertDontSee('<span>Kelola Profil &amp; Akun</span>', false);
        $dashboard->assertSee('<span class="hidden sm:inline">Profil</span>', false);
        $this->assertSame(1, substr_count($dashboard->getContent(), route('klien.profile.edit')));

        $ordersIndex = $this->actingAs($this->client)->get(route('klien.orders.index'));
        $ordersIndex->assertOk();
        $ordersIndex->assertSee('<span>Katalog Layanan</span>', false);
        $ordersIndex->assertSee('href="'.$clientCatalogUrl.'"', false);
        $ordersIndex->assertDontSee('href="'.$publicCatalogUrl.'"', false);
        $ordersIndex->assertSee('Pesan Layanan Baru');
        $ordersIndex->assertDontSee('#layanan', false);

        // Katalog INTERNAL Portal Klien (/klien/layanan) menggunakan sidebar & layout Portal Klien tanpa navbar/footer homepage
        $internalCatalog = $this->actingAs($this->client)->get($clientCatalogUrl);
        $internalCatalog->assertOk()
            ->assertSee('id="klienSidebar"', false)
            ->assertSee('Kembali ke Dashboard')
            ->assertDontSee('Tentang Kami')
            ->assertDontSee('Login Sistem')
            ->assertSee($this->service->name)
            ->assertSee('id="catalogSearch"', false)
            ->assertSee('id="categoryFilter"', false)
            ->assertSee('Persyaratan')
            ->assertSee('KATALOG10')
            ->assertSee(route('klien.services.show', $this->service->id), false)
            ->assertDontSee(route('services.show', $this->service->slug), false)
            ->assertSee(route('klien.checkout.show', $this->service->slug), false);

        // Pencarian & filter kategori di katalog internal bekerja sesuai data DB
        $this->actingAs($this->client)
            ->get(route('klien.services.index', ['search' => $this->service->name, 'category' => $this->category->slug]))
            ->assertOk()
            ->assertSee($this->service->name);

        // Detail Layanan INTERNAL Portal Klien (/klien/layanan/{id}) dengan data lengkap DB, Kembali ke Katalog, & Checkout
        $this->actingAs($this->client)->get('/klien/layanan/999999')->assertNotFound();

        $detail = $this->actingAs($this->client)->get($internalDetailUrl);
        $detail->assertOk()
            ->assertSee('id="klienSidebar"', false)
            ->assertDontSee('Tentang Kami')
            ->assertDontSee('Login Sistem')
            ->assertDontSee($publicCatalogUrl, false)
            ->assertSee($this->service->name)
            ->assertSee($this->category->name)
            ->assertSee('Rp 10.000.000')
            ->assertSee('Persyaratan Dokumen')
            ->assertSee('Kembali ke Katalog')
            ->assertSee('href="'.$clientCatalogUrl.'"', false)
            ->assertSee('Checkout / Pesan Layanan')
            ->assertSee(route('klien.checkout.show', $this->service->slug), false);

        // Tombol Kembali di Checkout mengarah ke detail layanan internal Portal Klien
        $this->actingAs($this->client)
            ->get(route('klien.checkout.show', $this->service->slug))
            ->assertOk()
            ->assertSee('href="'.$internalDetailUrl.'"', false)
            ->assertDontSee(route('services.show', $this->service->slug), false);

        // Katalog & detail publik untuk pengunjung non-klien tetap berjalan normal
        $this->get($publicCatalogUrl)->assertOk()->assertSee($this->service->name);
        $this->get(route('services.show', $this->service->slug))->assertOk()->assertSee($this->service->name);
    }

    public function test_client_help_center_moved_to_sidebar_with_complete_guides(): void
    {
        $dashboard = $this->actingAs($this->client)->get(route('klien.dashboard'));
        $dashboard->assertOk();
        $dashboard->assertDontSee('<span class="hidden md:inline">Pusat Bantuan</span>', false);
        $dashboard->assertSeeInOrder([
            'Dashboard Saya',
            'Pesanan &amp; Pembayaran',
            'Katalog Layanan',
            'Pusat Bantuan',
        ], false);
        $dashboard->assertSee('id="helpCenterModal"', false);
        $dashboard->assertSee('Panduan Pemesanan Layanan Baru');
        $dashboard->assertSee('Panduan Penggunaan Kode Voucher Promo');
        $dashboard->assertSee('Panduan Pembayaran, Invoice PDF &amp; Unggah Ulang', false);
        $dashboard->assertSee('Panduan Upload Dokumen &amp; Riwayat Revisi (Versioning)', false);
        $dashboard->assertSee('Panduan Melihat Progres Perizinan (Tracking Proyek)');
        $dashboard->assertSee('Panduan Mengajukan Konsultasi &amp; Pertanyaan Teknis', false);
        $dashboard->assertSee('FAQ Umum: Keamanan Akun, Profil &amp; Ulasan Layanan', false);
        $dashboard->assertSee('Bantuan Pelanggan Resmi CV Prasta Solusi Indonesia (IzinYou)');

        // Pusat Bantuan juga tersedia dari sidebar pada halaman profil & pesanan
        $this->actingAs($this->client)
            ->get(route('klien.profile.edit'))
            ->assertOk()
            ->assertSee('id="helpCenterModal"', false)
            ->assertSee('<span>Pusat Bantuan</span>', false);

        $this->actingAs($this->client)
            ->get(route('klien.orders.index'))
            ->assertOk()
            ->assertSee('id="helpCenterModal"', false)
            ->assertSee('<span>Pusat Bantuan</span>', false);
    }

    public function test_client_consultation_page_is_separated_with_rbac_submission_history_and_responses(): void
    {
        // RBAC: Guest & staf non-klien ditolak
        $this->get(route('klien.consultations.index'))->assertRedirect(route('login'));
        $this->actingAs($this->marketingStaff)
            ->get(route('klien.consultations.index'))
            ->assertRedirect(route('marketing.dashboard'));

        FreeConsultation::create([
            'user_id' => $this->client->id,
            'name' => $this->client->name,
            'email' => $this->client->email,
            'phone' => $this->client->phone,
            'subject' => 'Tanya Syarat CDAKB',
            'message' => 'Apakah gudang harus memiliki kalibrasi suhu terakreditasi?',
            'status' => 'responded',
            'response' => 'Ya, bukti kalibrasi thermo-hygrometer wajib dilampirkan saat pengajuan CDAKB.',
        ]);

        FreeConsultation::create([
            'user_id' => $this->client->id,
            'name' => $this->client->name,
            'email' => $this->client->email,
            'phone' => $this->client->phone,
            'subject' => 'Estimasi Biaya Uji Lab Pangan',
            'message' => 'Berapa estimasi waktu penerbitan LHU?',
            'status' => 'pending',
        ]);

        FreeConsultation::create([
            'user_id' => $this->otherClient->id,
            'name' => $this->otherClient->name,
            'email' => $this->otherClient->email,
            'phone' => $this->otherClient->phone,
            'subject' => 'Konsultasi Rahasia Klien Lain',
            'message' => 'Pertanyaan milik perusahaan lain.',
            'status' => 'pending',
        ]);

        $page = $this->actingAs($this->client)->get(route('klien.consultations.index'));
        $page->assertOk()
            ->assertSee('Ajukan Pertanyaan Perizinan &amp; Sertifikasi', false)
            ->assertSee(route('klien.consultation.submit'), false)
            ->assertSee('Riwayat Tanya-Jawab Konsultasi')
            ->assertSee('Tanya Syarat CDAKB')
            ->assertSee('Ya, bukti kalibrasi thermo-hygrometer wajib dilampirkan saat pengajuan CDAKB.')
            ->assertSee('Dijawab')
            ->assertSee('Estimasi Biaya Uji Lab Pangan')
            ->assertSee('Menunggu')
            ->assertDontSee('Konsultasi Rahasia Klien Lain')
            ->assertDontSee('<span>Konsultasi Perizinan</span>', false)
            ->assertDontSee('Tanya Konsultan');

        // Ringkasan pada Dashboard memperlihatkan angka akurat & tidak menampilkan form konsultasi
        $dash = $this->actingAs($this->client)->get(route('klien.dashboard'));
        $dash->assertOk()
            ->assertSee('Sesi Konsultasi')
            ->assertSee('1 menunggu')
            ->assertSee('1 dijawab')
            ->assertDontSee('Ya, bukti kalibrasi thermo-hygrometer wajib dilampirkan saat pengajuan CDAKB.')
            ->assertDontSee('Estimasi Biaya Uji Lab Pangan');

        // Pengajuan konsultasi baru dari halaman konsultasi tersimpan dengan benar
        $this->actingAs($this->client)
            ->from(route('klien.consultations.index'))
            ->post(route('klien.consultation.submit'), [
                'subject' => 'Tanya Perizinan NIB Cabang',
                'message' => 'Bagaimana prosedur penambahan KBLI cabang baru?',
            ])
            ->assertRedirect(route('klien.consultations.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('free_consultations', [
            'user_id' => $this->client->id,
            'subject' => 'Tanya Perizinan NIB Cabang',
            'status' => 'pending',
        ]);
    }

    public function test_client_order_cancellation_flow_constraints_and_history_preservation(): void
    {
        // Buat 2 pesanan: Order A (pending belum dibayar -> boleh batal) dan Order B (sudah proses -> dilarang batal)
        $this->actingAs($this->client)
            ->post(route('klien.order.checkout'), [
                'service_id' => $this->service->id,
                'checkout_token' => 'tok-cancel-a',
            ])
            ->assertRedirect();

        $orderA = Order::with('invoice')->where('checkout_token', 'tok-cancel-a')->firstOrFail();

        $this->actingAs($this->client)
            ->post(route('klien.order.checkout'), [
                'service_id' => $this->service->id,
                'checkout_token' => 'tok-cancel-b',
            ])
            ->assertRedirect();

        $orderB = Order::where('checkout_token', 'tok-cancel-b')->firstOrFail();
        $orderB->update(['status' => 'processing', 'current_stage' => 'Pemeriksaan Dokumen', 'progress_percentage' => 50]);

        // Halaman daftar & detail pesanan menampilkan tombol Batalkan Pesanan untuk Order A
        $this->actingAs($this->client)
            ->get(route('klien.orders.index'))
            ->assertOk()
            ->assertSee('Batalkan Pesanan')
            ->assertSee('cancelOrderModal');

        $this->actingAs($this->client)
            ->get(route('klien.orders.show', $orderA->id))
            ->assertOk()
            ->assertSee('Batalkan Pesanan')
            ->assertSee('Konfirmasi Pembatalan Pesanan');

        // Isolasi: klien lain dilarang membatalkan Order A (403)
        $this->actingAs($this->otherClient)
            ->post(route('klien.orders.cancel', $orderA->id), ['cancel_reason' => 'Coba batalkan'])
            ->assertForbidden();

        // Order B yang sudah masuk tahap proses ditolak pembatalannya
        $this->actingAs($this->client)
            ->post(route('klien.orders.cancel', $orderB->id), ['cancel_reason' => 'Berubah pikiran'])
            ->assertSessionHas('error');
        $this->assertSame('processing', $orderB->fresh()->status);

        // Order yang sudah memiliki bukti pembayaran waiting_confirmation juga tidak boleh dibatalkan
        $orderC = Order::create([
            'order_number' => 'ORD-WAIT-CANCEL',
            'user_id' => $this->client->id,
            'service_id' => $this->service->id,
            'base_price' => 10000000,
            'discount_amount' => 0,
            'total_cost' => 10000000,
            'status' => 'pending',
            'current_stage' => 'Pesanan Diterima',
            'progress_percentage' => 15,
        ]);
        Payment::create([
            'order_id' => $orderC->id,
            'user_id' => $this->client->id,
            'payment_method' => 'QRIS',
            'bank_name' => 'QRIS',
            'sender_name' => 'Andi',
            'amount' => 10000000,
            'status' => 'waiting_confirmation',
        ]);
        $this->actingAs($this->client)
            ->post(route('klien.orders.cancel', $orderC->id))
            ->assertSessionHas('error');
        $this->assertSame('pending', $orderC->fresh()->status);

        // Positif: batalkan Order A dengan alasan -> status Dibatalkan, riwayat & invoice tetap tersimpan
        $this->actingAs($this->client)
            ->post(route('klien.orders.cancel', $orderA->id), [
                'cancel_reason' => 'Salah memilih jenis layanan',
            ])
            ->assertRedirect(route('klien.orders.show', $orderA->id))
            ->assertSessionHas('success');

        $orderA->refresh();
        $this->assertSame('cancelled', $orderA->status);
        $this->assertSame('Pesanan Dibatalkan', $orderA->current_stage);
        $this->assertDatabaseHas('orders', ['id' => $orderA->id, 'order_number' => $orderA->order_number, 'status' => 'cancelled']);
        $this->assertDatabaseHas('invoices', ['order_id' => $orderA->id, 'invoice_number' => $orderA->invoice->invoice_number]);

        // Order A yang sudah dibatalkan tampil dengan status Dibatalkan dan tidak menerima pembayaran baru
        $this->actingAs($this->client)
            ->get(route('klien.orders.index'))
            ->assertOk()
            ->assertSee($orderA->order_number)
            ->assertSee('Status Proyek: Dibatalkan')
            ->assertSee('Salah memilih jenis layanan');

        $this->actingAs($this->client)
            ->get(route('klien.orders.show', $orderA->id))
            ->assertOk()
            ->assertSee('Pesanan Telah Dibatalkan')
            ->assertDontSee('Kirim Bukti Pembayaran');

        $this->actingAs($this->client)
            ->post(route('klien.orders.payment.store', $orderA->id), [
                'payment_method' => 'QRIS',
                'sender_name' => 'Andi',
                'amount' => 10000000,
                'payment_proof' => UploadedFile::fake()->image('qris.png'),
            ])
            ->assertSessionHas('error');
    }

    public function test_client_payment_methods_exclude_bri_va_bank_name_and_render_distinct_bank_and_qris_details(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-PAY-METODE-1',
            'user_id' => $this->client->id,
            'service_id' => $this->service->id,
            'base_price' => 10000000,
            'discount_amount' => 0,
            'total_cost' => 10000000,
            'status' => 'pending',
            'current_stage' => 'Pesanan Diterima',
            'progress_percentage' => 15,
        ]);

        // Halaman detail pesanan menampilkan hanya Mandiri, BCA, BNI, QRIS dengan nomor rekening berbeda + QRIS visual, tanpa BRI/VA/Nama Bank Pengirim
        $page = $this->actingAs($this->client)->get(route('klien.orders.show', $order->id));
        $page->assertOk()
            ->assertSee('Transfer Bank Mandiri')
            ->assertSee('156-00-1928374-1')
            ->assertSee('Transfer Bank BCA')
            ->assertSee('522-0918273')
            ->assertSee('Transfer Bank BNI')
            ->assertSee('084-9201753')
            ->assertSee('QRIS')
            ->assertSee('qris-izinyou.svg')
            ->assertSee('CV PRASTA SOLUSI INDONESIA (IZINYOU)')
            ->assertSee('ID1026001928374')
            ->assertSee('Lihat QRIS Lebih Jelas')
            ->assertSee('qrisPreviewModal')
            ->assertDontSee('Transfer Bank BRI')
            ->assertDontSee('Virtual Account')
            ->assertDontSee('Nama Bank Pengirim')
            ->assertDontSee('name="bank_name"', false);

        // Negative: BRI dan Virtual Account ditolak oleh validasi backend
        foreach (['Transfer Bank BRI', 'Virtual Account'] as $blockedMethod) {
            $this->actingAs($this->client)
                ->post(route('klien.orders.payment.store', $order->id), [
                    'payment_method' => $blockedMethod,
                    'sender_name' => 'Andi Pratama',
                    'amount' => 10000000,
                    'payment_proof' => UploadedFile::fake()->image('bukti.png'),
                ])
                ->assertSessionHasErrors(['payment_method']);
        }

        // Positive: Pembayaran QRIS tanpa field bank_name berhasil disimpan dan otomatis mengisi bank_name='QRIS'
        $this->actingAs($this->client)
            ->post(route('klien.orders.payment.store', $order->id), [
                'payment_method' => 'QRIS',
                'sender_name' => 'Andi Pratama',
                'amount' => 10000000,
                'payment_proof' => UploadedFile::fake()->image('bukti_qris.png'),
                'notes' => 'Ref QRIS #998811',
            ])
            ->assertRedirect(route('klien.orders.show', $order->id))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'payment_method' => 'QRIS',
            'bank_name' => 'QRIS',
            'sender_name' => 'Andi Pratama',
            'status' => 'waiting_confirmation',
        ]);
    }
}
