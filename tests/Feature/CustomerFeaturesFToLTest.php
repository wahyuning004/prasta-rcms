<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ClientDocument;
use App\Models\ClientDocumentVersion;
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
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerFeaturesFToLTest extends TestCase
{
    use RefreshDatabase;

    private User $client;

    private User $otherClient;

    private User $financeStaff;

    private User $legalStaff;

    private User $marketingStaff;

    private User $adminUser;

    private Category $category;

    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->client = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@mitra.co.id',
            'phone' => '081234567890',
            'company_name' => 'PT Mitra Sejahtera',
            'address' => 'Jl. Industri No. 10, Bekasi',
            'role' => 'klien',
            'email_verified_at' => now(),
        ]);

        $this->otherClient = User::factory()->create([
            'name' => 'Siti Aminah',
            'email' => 'siti@lain.co.id',
            'phone' => '081299998888',
            'company_name' => 'CV Lainnya',
            'address' => 'Jl. Merdeka No. 2, Jakarta',
            'role' => 'klien',
            'email_verified_at' => now(),
        ]);

        $this->financeStaff = User::factory()->create([
            'name' => 'Dania Finance',
            'email' => 'finance@izinyou.com',
            'phone' => '082199887766',
            'role' => 'finance',
            'email_verified_at' => now(),
        ]);

        $this->legalStaff = User::factory()->create([
            'name' => 'Ahmad Legal',
            'email' => 'legal@izinyou.com',
            'phone' => '082155667788',
            'role' => 'legal',
            'email_verified_at' => now(),
        ]);

        $this->marketingStaff = User::factory()->create([
            'name' => 'Ari Marketing',
            'email' => 'marketing@izinyou.com',
            'phone' => '082111223344',
            'role' => 'marketing',
            'email_verified_at' => now(),
        ]);

        $this->adminUser = User::factory()->create([
            'name' => 'Admin Prasta',
            'email' => 'admin@izinyou.com',
            'phone' => '081111111111',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->category = Category::create([
            'name' => 'Kemenkes (Alkes & PKRT)',
            'slug' => 'kemenkes',
            'description' => 'Layanan izin alat kesehatan.',
        ]);

        $this->service = Service::create([
            'category_id' => $this->category->id,
            'name' => 'Izin Edar Alat Kesehatan Kemenkes',
            'slug' => 'izin-edar-alkes-kemenkes',
            'description' => 'Pendampingan izin edar alkes lengkap.',
            'requirements_info' => "• NIB & NPWP\n• Sertifikat IDAK\n• Hasil Uji Lab",
            'timeline_estimation' => '1-3 Bulan',
            'price' => 10000000,
        ]);

        app(ServiceRequirementSyncService::class)->ensureForService($this->service);
    }

    // =========================================================================
    // FITUR F: MANAJEMEN PROFIL PELANGGAN
    // =========================================================================

    public function test_feature_f_client_can_view_and_update_own_profile(): void
    {
        $this->actingAs($this->client)
            ->get(route('klien.profile.edit'))
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertSee('PT Mitra Sejahtera');

        $response = $this->actingAs($this->client)
            ->put(route('klien.profile.update'), [
                'name' => 'Budi Santoso Updated',
                'email' => 'budi.baru@mitra.co.id',
                'phone' => '+6281234567891',
                'company_name' => 'PT Mitra Sejahtera Abadi',
                'address' => 'Kawasan Jababeka Blok A5, Cikarang',
                'pic_position' => 'Direktur Operasional',
                'nib' => '1234567890123',
                'npwp' => '94.812.334.2-407.000',
                'business_field' => 'Distributor Alat Kesehatan',
            ]);

        $response->assertRedirect(route('klien.profile.edit'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $this->client->id,
            'name' => 'Budi Santoso Updated',
            'email' => 'budi.baru@mitra.co.id',
            'phone' => '081234567891',
            'company_name' => 'PT Mitra Sejahtera Abadi',
            'nib' => '1234567890123',
            'business_field' => 'Distributor Alat Kesehatan',
        ]);

        // Pastikan profil pelanggan lain tidak ikut berubah
        $this->assertDatabaseHas('users', [
            'id' => $this->otherClient->id,
            'email' => 'siti@lain.co.id',
            'phone' => '081299998888',
        ]);
    }

    public function test_feature_f_profile_rejects_duplicate_email_or_phone_and_invalid_inputs(): void
    {
        // Duplicate email belonging to otherClient
        $this->actingAs($this->client)
            ->put(route('klien.profile.update'), [
                'name' => 'Budi Santoso',
                'email' => $this->otherClient->email,
                'phone' => '081234567890',
                'company_name' => 'PT Mitra Sejahtera',
                'address' => 'Jl. Industri No. 10, Bekasi',
            ])
            ->assertSessionHasErrors(['email']);

        // Duplicate phone belonging to otherClient in +62 format
        $this->actingAs($this->client)
            ->put(route('klien.profile.update'), [
                'name' => 'Budi Santoso',
                'email' => $this->client->email,
                'phone' => '+6281299998888',
                'company_name' => 'PT Mitra Sejahtera',
                'address' => 'Jl. Industri No. 10, Bekasi',
            ])
            ->assertSessionHasErrors(['phone']);

        // Invalid phone & name with numbers & invalid NIB
        $this->actingAs($this->client)
            ->put(route('klien.profile.update'), [
                'name' => 'Budi123',
                'email' => $this->client->email,
                'phone' => '0812abc',
                'company_name' => 'PT Mitra Sejahtera',
                'address' => 'Jl. Industri No. 10, Bekasi',
                'nib' => '12345',
            ])
            ->assertSessionHasErrors(['name', 'phone', 'nib']);
    }

    // =========================================================================
    // FITUR G: KATALOG LAYANAN PERIZINAN DAN SERTIFIKASI
    // =========================================================================

    public function test_feature_g_catalog_search_category_filter_and_service_detail_from_database(): void
    {
        $bpomCat = Category::create([
            'name' => 'BPOM & Kosmetik',
            'slug' => 'bpom',
        ]);
        $bpomService = Service::create([
            'category_id' => $bpomCat->id,
            'name' => 'Notifikasi Kosmetik BPOM',
            'slug' => 'notifikasi-kosmetik-bpom',
            'description' => 'Pendaftaran notifikasi kosmetik.',
            'requirements_info' => 'Formula INCI',
            'timeline_estimation' => '1 Bulan',
            'price' => 7500000,
        ]);

        // Katalog menampilkan data dari DB dan mendukung search + filter kategori
        $this->get(route('catalog.index', ['search' => 'Kosmetik']))
            ->assertOk()
            ->assertSee('Notifikasi Kosmetik BPOM')
            ->assertDontSee('Izin Edar Alat Kesehatan Kemenkes');

        $this->get(route('catalog.index', ['category' => 'kemenkes']))
            ->assertOk()
            ->assertSee('Izin Edar Alat Kesehatan Kemenkes')
            ->assertDontSee('Notifikasi Kosmetik BPOM');

        // Detail layanan menampilkan informasi, biaya, estimasi waktu, dokumen wajib & opsional
        $this->get(route('services.show', $this->service->slug))
            ->assertOk()
            ->assertSee('Izin Edar Alat Kesehatan Kemenkes')
            ->assertSee('1-3 Bulan')
            ->assertSee('Rp 10.000.000')
            ->assertSee('Dokumen Wajib')
            ->assertSee('Dokumen Opsional');

        // Konsultasi lanjutan layanan tersimpan ke database
        $this->post(route('services.consultation'), [
            'name' => 'Hendra Wijaya',
            'phone' => '081311112222',
            'company' => 'PT Uji Coba',
            'service_name' => $this->service->name,
            'missing_doc_category' => 'Belum Memiliki Penanggung Jawab Teknis',
            'message' => 'Bagaimana solusi jika belum ada PJT?',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('free_consultations', [
            'name' => 'Hendra Wijaya',
            'phone' => '081311112222',
            'status' => 'pending',
        ]);
    }

    // =========================================================================
    // FITUR H: VOUCHER PROMOSI DAN CHECKOUT
    // =========================================================================

    public function test_feature_h_checkout_applies_valid_voucher_prevents_duplicates_and_rejects_invalid_vouchers(): void
    {
        $validPromo = Promo::create([
            'code' => 'HEMAT10',
            'title' => 'Diskon 10%',
            'discount_amount' => 0,
            'discount_percentage' => 10,
            'min_transaction' => 5000000,
            'quota' => 5,
            'used_count' => 0,
            'is_active' => true,
            'expired_date' => now()->addDays(10)->toDateString(),
        ]);

        $expiredPromo = Promo::create([
            'code' => 'KADALUWARSA',
            'discount_amount' => 500000,
            'quota' => 10,
            'used_count' => 0,
            'is_active' => true,
            'expired_date' => now()->subDay()->toDateString(),
        ]);

        $exhaustedPromo = Promo::create([
            'code' => 'HABIS',
            'discount_amount' => 500000,
            'quota' => 1,
            'used_count' => 1,
            'is_active' => true,
            'expired_date' => now()->addDays(10)->toDateString(),
        ]);

        $highMinPromo = Promo::create([
            'code' => 'MINIMAL20JT',
            'discount_amount' => 1000000,
            'min_transaction' => 20000000,
            'quota' => 10,
            'used_count' => 0,
            'is_active' => true,
            'expired_date' => now()->addDays(10)->toDateString(),
        ]);

        // Validasi voucher AJAX berhasil
        $this->actingAs($this->client)
            ->postJson(route('klien.checkout.validate-voucher'), [
                'service_id' => $this->service->id,
                'promo_code' => 'HEMAT10',
            ])
            ->assertOk()
            ->assertJson([
                'valid' => true,
                'discount' => 1000000,
                'final_total' => 9000000,
            ]);

        // Voucher kedaluwarsa, kuota habis, dan di bawah minimal transaksi ditolak
        foreach (['KADALUWARSA', 'HABIS', 'MINIMAL20JT', 'TIDAKADA'] as $badCode) {
            $this->actingAs($this->client)
                ->postJson(route('klien.checkout.validate-voucher'), [
                    'service_id' => $this->service->id,
                    'promo_code' => $badCode,
                ])
                ->assertStatus(422)
                ->assertJson(['valid' => false]);

            $this->actingAs($this->client)
                ->post(route('klien.order.checkout'), [
                    'service_id' => $this->service->id,
                    'promo_code' => $badCode,
                ])
                ->assertSessionHasErrors(['promo_code']);
        }

        // Checkout berhasil dengan server-side price calculation & idempotency token
        $token = 'unique-token-abc-123';
        $checkoutRes = $this->actingAs($this->client)
            ->post(route('klien.order.checkout'), [
                'service_id' => $this->service->id,
                'promo_code' => 'HEMAT10',
                'checkout_token' => $token,
                'notes' => 'Order produk elektromedis',
            ]);

        $order = Order::where('checkout_token', $token)->firstOrFail();
        $checkoutRes->assertRedirect(route('klien.orders.confirmation', $order->order_number));

        $this->assertEquals(10000000.00, (float) $order->base_price);
        $this->assertEquals(1000000.00, (float) $order->discount_amount);
        $this->assertEquals(9000000.00, (float) $order->total_cost);
        $this->assertEquals(1, $validPromo->fresh()->used_count);

        // Invoice otomatis dibuat
        $this->assertDatabaseHas('invoices', [
            'order_id' => $order->id,
            'subtotal' => 10000000,
            'discount_amount' => 1000000,
            'total_amount' => 9000000,
            'payment_status' => 'unpaid',
        ]);

        // Pencegahan pesanan ganda dengan checkout_token yang sama
        $duplicateRes = $this->actingAs($this->client)
            ->post(route('klien.order.checkout'), [
                'service_id' => $this->service->id,
                'promo_code' => 'HEMAT10',
                'checkout_token' => $token,
            ]);

        $duplicateRes->assertRedirect(route('klien.orders.confirmation', $order->order_number));
        $this->assertEquals(1, Order::where('user_id', $this->client->id)->count());
        $this->assertEquals(1, $validPromo->fresh()->used_count);

        // Halaman konfirmasi menampilkan kode pesanan & invoice
        $this->actingAs($this->client)
            ->get(route('klien.orders.confirmation', $order->order_number))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Rp 9.000.000');
    }

    // =========================================================================
    // FITUR I: INVOICE DAN UNGGAH BUKTI PEMBAYARAN
    // =========================================================================

    public function test_feature_i_invoice_pdf_payment_upload_rejection_reupload_and_approval(): void
    {
        $order = $this->createOrderForClient($this->client, 9000000);

        // 1. Pelanggan melihat invoice & mengunduh PDF asli
        $this->actingAs($this->client)
            ->get(route('klien.orders.invoice', $order->id))
            ->assertOk()
            ->assertSee($order->invoice->invoice_number)
            ->assertSee('Menunggu Pembayaran');

        $pdfResponse = $this->actingAs($this->client)
            ->get(route('klien.orders.invoice.pdf', $order->id));
        $pdfResponse->assertOk();
        $pdfResponse->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-1.4', $pdfResponse->getContent());

        // Pelanggan lain ditolak (403) melihat invoice / PDF
        $this->actingAs($this->otherClient)
            ->get(route('klien.orders.invoice', $order->id))
            ->assertForbidden();

        // 2. Validasi format & ukuran file bukti pembayaran (tolak .exe / .php / nominal kurang)
        $this->actingAs($this->client)
            ->post(route('klien.orders.payment.store', $order->id), [
                'payment_method' => 'Transfer Bank Mandiri',
                'bank_name' => 'Mandiri',
                'sender_name' => 'Budi Santoso',
                'amount' => 100000, // kurang dari total_cost
                'payment_proof' => UploadedFile::fake()->create('malware.php', 100, 'application/x-php'),
            ])
            ->assertSessionHasErrors(['amount', 'payment_proof']);

        // 3. Unggah bukti pembayaran valid
        $proof1 = UploadedFile::fake()->image('bukti_transfer_1.jpg');
        $this->actingAs($this->client)
            ->post(route('klien.orders.payment.store', $order->id), [
                'payment_method' => 'Transfer Bank Mandiri',
                'bank_name' => 'Mandiri',
                'sender_name' => 'Budi Santoso',
                'amount' => 9000000,
                'payment_proof' => $proof1,
                'notes' => 'Transfer batch 1',
            ])
            ->assertRedirect(route('klien.orders.show', $order->id))
            ->assertSessionHas('success');

        $payment1 = Payment::where('order_id', $order->id)->latest('id')->firstOrFail();
        $this->assertEquals('waiting_confirmation', $payment1->status);
        $this->assertEquals('waiting_verification', $order->invoice->fresh()->payment_status);

        // Pelanggan lain dilarang melihat bukti bayar (403), Finance boleh (200)
        $this->actingAs($this->otherClient)
            ->get(route('payments.proof', $payment1->id))
            ->assertForbidden();
        $this->actingAs($this->financeStaff)
            ->get(route('payments.proof', $payment1->id))
            ->assertOk();

        // 4. Petugas Finance menolak pembayaran dengan alasan penolakan
        $this->actingAs($this->financeStaff)
            ->post(route('finance.payment.verify', $payment1->id), [
                'status' => 'failed',
                'rejection_reason' => 'Foto bukti transfer buram dan nomor rekening tujuan terpotong.',
            ])
            ->assertSessionHas('success');

        $this->assertEquals('failed', $payment1->fresh()->status);
        $this->assertEquals('rejected', $order->invoice->fresh()->payment_status);

        // Pelanggan melihat alasan penolakan di detail pesanan
        $this->actingAs($this->client)
            ->get(route('klien.orders.show', $order->id))
            ->assertOk()
            ->assertSee('Ditolak')
            ->assertSee('Foto bukti transfer buram');

        // 5. Pelanggan mengunggah ulang bukti pembayaran setelah ditolak
        $proof2 = UploadedFile::fake()->create('bukti_transfer_2.pdf', 200, 'application/pdf');
        $this->actingAs($this->client)
            ->post(route('klien.orders.payment.store', $order->id), [
                'payment_method' => 'Transfer Bank BCA',
                'bank_name' => 'BCA',
                'sender_name' => 'PT Mitra Sejahtera',
                'amount' => 9000000,
                'payment_proof' => $proof2,
            ])
            ->assertSessionHas('success');

        $payment2 = Payment::where('order_id', $order->id)->latest('id')->firstOrFail();
        $this->assertNotEquals($payment1->id, $payment2->id);
        $this->assertEquals('waiting_confirmation', $payment2->status);

        // 6. Petugas Finance memverifikasi pembayaran berhasil
        $this->actingAs($this->financeStaff)
            ->post(route('finance.payment.verify', $payment2->id), [
                'status' => 'success',
            ])
            ->assertSessionHas('success');

        $this->assertEquals('success', $payment2->fresh()->status);
        $this->assertEquals('paid', $order->invoice->fresh()->payment_status);
        $this->assertEquals('processing', $order->fresh()->status);
        $this->assertGreaterThanOrEqual(30, $order->fresh()->progress_percentage);

        // Cek seluruh riwayat perubahan status pembayaran tercatat di payment_logs
        $this->assertGreaterThanOrEqual(4, $order->paymentLogs()->count());
    }

    // =========================================================================
    // FITUR J: UNGGAH DAN MANAJEMEN DOKUMEN + FILE VERSIONING + RBAC
    // =========================================================================

    public function test_feature_j_document_upload_revision_versioning_preserves_old_files_and_enforces_access(): void
    {
        $order = $this->createOrderForClient($this->client, 10000000);
        $mandatoryReq = ServiceRequirement::where('service_id', $this->service->id)
            ->where('is_mandatory', true)
            ->firstOrFail();

        // Format file berbahaya ditolak
        $this->actingAs($this->client)
            ->post(route('klien.orders.documents.upload', $order->id), [
                'service_requirement_id' => $mandatoryReq->id,
                'document_file' => UploadedFile::fake()->create('script.js', 50, 'application/javascript'),
            ])
            ->assertSessionHasErrors(['document_file']);

        // Unggah versi v1
        $fileV1 = UploadedFile::fake()->create('nib_v1.pdf', 150, 'application/pdf');
        $this->actingAs($this->client)
            ->post(route('klien.orders.documents.upload', $order->id), [
                'service_requirement_id' => $mandatoryReq->id,
                'document_file' => $fileV1,
                'revision_note' => 'Dokumen awal NIB perusahaan',
            ])
            ->assertRedirect(route('klien.orders.show', $order->id))
            ->assertSessionHas('success');

        $doc = ClientDocument::where('order_id', $order->id)
            ->where('service_requirement_id', $mandatoryReq->id)
            ->firstOrFail();
        $this->assertEquals(1, $doc->version);
        $this->assertEquals('uploaded', $doc->status);

        $v1 = ClientDocumentVersion::where('client_document_id', $doc->id)->where('version_number', 1)->firstOrFail();
        Storage::disk('local')->assertExists($v1->file_path);

        // Petugas Legal menolak dokumen dengan alasan revisi
        $this->actingAs($this->legalStaff)
            ->post(route('legal.document.verify', $doc->id), [
                'status' => 'rejected',
                'notes' => 'Lampiran halaman 2 KBLI terpotong, mohon unggah ulang.',
            ])
            ->assertSessionHas('success');

        $this->assertEquals('rejected', $doc->fresh()->status);

        // Pelanggan mengunggah ulang tanpa catatan revisi ditolak
        $this->actingAs($this->client)
            ->post(route('klien.orders.documents.upload', $order->id), [
                'service_requirement_id' => $mandatoryReq->id,
                'document_file' => UploadedFile::fake()->create('nib_v2.pdf', 180, 'application/pdf'),
            ])
            ->assertSessionHasErrors(['revision_note']);

        // Pelanggan mengunggah versi v2 dengan keterangan revisi
        $fileV2 = UploadedFile::fake()->create('nib_v2.pdf', 180, 'application/pdf');
        $this->actingAs($this->client)
            ->post(route('klien.orders.documents.upload', $order->id), [
                'service_requirement_id' => $mandatoryReq->id,
                'document_file' => $fileV2,
                'revision_note' => 'Sudah dilengkapi halaman KBLI penuh.',
            ])
            ->assertSessionHas('success');

        $doc->refresh();
        $this->assertEquals(2, $doc->version);
        $this->assertEquals('uploaded', $doc->status);

        $v2 = ClientDocumentVersion::where('client_document_id', $doc->id)->where('version_number', 2)->firstOrFail();

        // Versi sebelumnya TIDAK dihapus (v1 dan v2 sama-sama ada di storage & database)
        Storage::disk('local')->assertExists($v1->file_path);
        Storage::disk('local')->assertExists($v2->file_path);
        $this->assertEquals(2, ClientDocumentVersion::where('client_document_id', $doc->id)->count());

        // Petugas Legal menyetujui versi terbaru
        $this->actingAs($this->legalStaff)
            ->post(route('legal.document.verify', $doc->id), [
                'status' => 'approved',
                'notes' => 'Dokumen lengkap dan sesuai regulasi.',
            ])
            ->assertSessionHas('success');

        $this->assertEquals('approved', $doc->fresh()->status);

        // Pembatasan akses file: Pemilik & Legal/Admin boleh mengunduh, pelanggan lain & marketing ditolak (403)
        $this->actingAs($this->client)->get(route('documents.version.download', $v1->id))->assertOk();
        $this->actingAs($this->legalStaff)->get(route('documents.version.download', $v2->id))->assertOk();
        $this->actingAs($this->adminUser)->get(route('documents.current.download', $doc->id))->assertOk();
        $this->actingAs($this->otherClient)->get(route('documents.version.download', $v1->id))->assertForbidden();
        $this->actingAs($this->marketingStaff)->get(route('documents.version.download', $v2->id))->assertForbidden();
    }

    // =========================================================================
    // FITUR K & L: PELACAKAN PROGRES PROYEK & RATING / ULASAN LAYANAN
    // =========================================================================

    public function test_feature_k_and_l_project_tracking_consistency_and_completed_only_reviews(): void
    {
        $order = $this->createOrderForClient($this->client, 10000000);

        // Pelanggan tidak dapat memberi ulasan sebelum order berstatus completed
        $this->actingAs($this->client)
            ->post(route('klien.orders.review.store', $order->id), [
                'rating' => 5,
                'comment' => 'Mencoba review sebelum selesai.',
            ])
            ->assertSessionHas('error');
        $this->assertDatabaseCount('reviews', 0);

        // Pelanggan lain dilarang melihat detail/tracking pesanan milik orang lain
        $this->actingAs($this->otherClient)
            ->get(route('klien.orders.show', $order->id))
            ->assertForbidden();

        // Petugas Finance / Admin / Legal memperbarui tahapan proyek secara bertahap
        $this->actingAs($this->legalStaff)
            ->post(route('legal.tracking.update'), [
                'order_id' => $order->id,
                'stage_name' => 'in_progress',
                'stage_status' => 'completed',
                'description' => 'Submit berkas ke portal Kemenkes RI selesai.',
            ])
            ->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('Proses Pengerjaan', $order->current_stage);
        $this->assertEquals(70, $order->progress_percentage);

        // Petugas menyelesaikan proyek (Selesai = 100%)
        $this->actingAs($this->financeStaff)
            ->post(route('finance.tracking.add'), [
                'order_id' => $order->id,
                'stage_name' => 'completed',
                'stage_status' => 'completed',
                'description' => 'Nomor Izin Edar resmi telah diterbitkan.',
            ])
            ->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('completed', $order->status);
        $this->assertEquals('Selesai', $order->current_stage);
        $this->assertEquals(100, $order->progress_percentage);

        // Riwayat perubahan status tersimpan dan tampil konsisten di dashboard & detail order
        $this->actingAs($this->client)
            ->get(route('klien.orders.show', $order->id))
            ->assertOk()
            ->assertSee('100%')
            ->assertSee('Nomor Izin Edar resmi telah diterbitkan.');

        // Pelanggan lain tidak bisa memberi rating pada pesanan bukan miliknya
        $this->actingAs($this->otherClient)
            ->post(route('klien.orders.review.store', $order->id), [
                'rating' => 1,
                'comment' => 'Ulasan palsu dari akun lain.',
            ])
            ->assertForbidden();

        // Pelanggan pemilik pesanan memberi rating 4 bintang
        $this->actingAs($this->client)
            ->post(route('klien.orders.review.store', $order->id), [
                'rating' => 4,
                'comment' => 'Pendampingan sangat membantu dan komunikatif.',
            ])
            ->assertRedirect(route('klien.orders.show', $order->id))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'order_id' => $order->id,
            'user_id' => $this->client->id,
            'service_id' => $this->service->id,
            'rating' => 4,
            'status' => 'published',
        ]);

        // Pelanggan memperbarui ulasannya (tetap 1 ulasan per pesanan)
        $this->actingAs($this->client)
            ->post(route('klien.orders.review.store', $order->id), [
                'rating' => 5,
                'comment' => 'Sertifikat terbit lebih cepat dari estimasi, luar biasa!',
            ])
            ->assertSessionHas('success');

        $this->assertEquals(1, Review::where('order_id', $order->id)->count());
        $this->assertDatabaseHas('reviews', [
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Sertifikat terbit lebih cepat dari estimasi, luar biasa!',
        ]);

        // Ulasan tampil di halaman detail layanan publik
        $this->get(route('services.show', $this->service->slug))
            ->assertOk()
            ->assertSee('Sertifikat terbit lebih cepat dari estimasi, luar biasa!')
            ->assertSee('Budi Santoso');
    }

    public function test_rbac_portals_password_update_optional_docs_and_staff_views(): void
    {
        $order = $this->createOrderForClient($this->client, 10000000);

        // RBAC: Guest diarahkan ke login
        $this->get(route('klien.dashboard'))->assertRedirect(route('login'));
        $this->get(route('klien.profile.edit'))->assertRedirect(route('login'));
        $this->get(route('klien.orders.index'))->assertRedirect(route('login'));

        // RBAC: Klien tidak boleh masuk dashboard staf / admin
        $this->actingAs($this->client)->get(route('admin.dashboard'))->assertRedirect(route('klien.dashboard'));
        $this->actingAs($this->client)->get(route('finance.dashboard'))->assertRedirect(route('klien.dashboard'));
        $this->actingAs($this->client)->get(route('legal.dashboard'))->assertRedirect(route('klien.dashboard'));
        $this->actingAs($this->client)->get(route('marketing.dashboard'))->assertRedirect(route('klien.dashboard'));

        // RBAC: Staf tidak boleh mengakses route klien
        $this->actingAs($this->financeStaff)->get(route('klien.profile.edit'))->assertRedirect(route('finance.dashboard'));
        $this->actingAs($this->legalStaff)->get(route('klien.orders.index'))->assertRedirect(route('legal.dashboard'));

        // Portal klien halaman dashboard, checkout, daftar pesanan berfungsi normal
        $this->actingAs($this->client)->get(route('klien.dashboard'))->assertOk()->assertSee($order->order_number);
        $this->actingAs($this->client)->get(route('klien.checkout.show', $this->service->slug))->assertOk()->assertSee($this->service->name);
        $this->actingAs($this->client)->get(route('klien.orders.index'))->assertOk()->assertSee($order->order_number);

        // Update kata sandi dengan current_password salah ditolak, yang benar diterima
        $this->actingAs($this->client)
            ->put(route('klien.profile.password'), [
                'current_password' => 'WrongPassword1',
                'password' => 'NewPassword123',
                'password_confirmation' => 'NewPassword123',
            ])
            ->assertSessionHasErrors(['current_password']);

        $this->actingAs($this->client)
            ->put(route('klien.profile.password'), [
                'current_password' => 'password',
                'password' => 'NewPassword123',
                'password_confirmation' => 'NewPassword123',
            ])
            ->assertSessionHas('success');

        // Unggah dokumen opsional berhasil
        $optionalReq = ServiceRequirement::where('service_id', $this->service->id)
            ->where('is_mandatory', false)
            ->firstOrFail();

        $this->actingAs($this->client)
            ->post(route('klien.orders.documents.upload', $order->id), [
                'service_requirement_id' => $optionalReq->id,
                'document_file' => UploadedFile::fake()->create('iso_opsional.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHas('success');

        // Dashboard Finance, Legal, Marketing, Admin dan Invoice PDF staf berjalan tanpa error
        $this->actingAs($this->financeStaff)->get(route('finance.dashboard'))->assertOk();
        $this->actingAs($this->financeStaff)->get(route('finance.invoice.generate', $order->id))->assertOk();
        $staffPdf = $this->actingAs($this->financeStaff)->get(route('finance.invoice.pdf', $order->id));
        $staffPdf->assertOk();
        $staffPdf->assertHeader('Content-Type', 'application/pdf');

        $this->actingAs($this->legalStaff)->get(route('legal.dashboard'))->assertOk();
        $this->actingAs($this->marketingStaff)->get(route('marketing.dashboard'))->assertOk();
        $this->actingAs($this->adminUser)->get(route('admin.dashboard'))->assertOk();
    }

    public function test_client_dashboard_tracking_module_filters_out_cancelled_orders_from_data_and_dropdown(): void
    {
        // Layanan BPOM yang dibatalkan
        $serviceBpom = Service::create([
            'category_id' => $this->category->id,
            'name' => 'Surat Keterangan Impor (SKI) Produk BPOM',
            'slug' => 'ski-bpom-test',
            'price' => 5000000,
            'timeline_estimation' => '7 Hari Kerja',
            'is_active' => true,
        ]);

        // Order 1: Aktif (Izin Edar Alkes)
        $orderActive = Order::create([
            'order_number' => 'ORD-202611-ACT01',
            'user_id' => $this->client->id,
            'service_id' => $this->service->id,
            'base_price' => 10000000,
            'total_cost' => 10000000,
            'status' => 'processing',
            'current_stage' => 'Proses Pengerjaan',
            'progress_percentage' => 70,
        ]);

        Invoice::create([
            'order_id' => $orderActive->id,
            'user_id' => $this->client->id,
            'invoice_number' => 'INV-202611-ACT01',
            'issued_at' => now(),
            'customer_name' => $this->client->name,
            'service_name' => $this->service->name,
            'subtotal' => 10000000,
            'total_amount' => 10000000,
            'payment_status' => 'paid',
        ]);

        // Order 2: Dibatalkan (Surat Keterangan Impor BPOM)
        $orderCancelled = Order::create([
            'order_number' => 'ORD-202611-CANC02',
            'user_id' => $this->client->id,
            'service_id' => $serviceBpom->id,
            'base_price' => 5000000,
            'total_cost' => 5000000,
            'status' => 'cancelled',
            'current_stage' => 'Pesanan Dibatalkan',
            'progress_percentage' => 15,
        ]);

        Invoice::create([
            'order_id' => $orderCancelled->id,
            'user_id' => $this->client->id,
            'invoice_number' => 'INV-202611-CANC02',
            'issued_at' => now(),
            'customer_name' => $this->client->name,
            'service_name' => $serviceBpom->name,
            'subtotal' => 5000000,
            'total_amount' => 5000000,
            'payment_status' => 'cancelled',
        ]);

        $response = $this->actingAs($this->client)->get(route('klien.dashboard'));
        $response->assertOk();

        // Data pelacakan menampilkan proyek aktif
        $response->assertSee('ORD-202611-ACT01');
        $response->assertSee('Proses Pengerjaan');

        // Data pelacakan dan dropdown TIDAK menampilkan pesanan yang dibatalkan
        $response->assertDontSee('ORD-202611-CANC02');
        $response->assertDontSee('Surat Keterangan Impor (SKI) Produk BPOM');

        // Percobaan query eksplisit ?order_id={id_dibatalkan} tetap tidak menampilkan order dibatalkan
        $responseWithCancelledId = $this->actingAs($this->client)->get(route('klien.dashboard', ['order_id' => $orderCancelled->id]));
        $responseWithCancelledId->assertOk();
        $responseWithCancelledId->assertSee('ORD-202611-ACT01');
        $responseWithCancelledId->assertDontSee('ORD-202611-CANC02');
    }

    public function test_client_dashboard_tracking_module_shows_empty_state_when_all_orders_are_cancelled(): void
    {
        $serviceBpom = Service::create([
            'category_id' => $this->category->id,
            'name' => 'Surat Keterangan Impor (SKI) Produk BPOM',
            'slug' => 'ski-bpom-only',
            'price' => 5000000,
            'timeline_estimation' => '7 Hari Kerja',
            'is_active' => true,
        ]);

        $orderCancelled = Order::create([
            'order_number' => 'ORD-202611-CANC03',
            'user_id' => $this->client->id,
            'service_id' => $serviceBpom->id,
            'base_price' => 5000000,
            'total_cost' => 5000000,
            'status' => 'cancelled',
            'current_stage' => 'Pesanan Dibatalkan',
            'progress_percentage' => 15,
        ]);

        Invoice::create([
            'order_id' => $orderCancelled->id,
            'user_id' => $this->client->id,
            'invoice_number' => 'INV-202611-CANC03',
            'issued_at' => now(),
            'customer_name' => $this->client->name,
            'service_name' => $serviceBpom->name,
            'subtotal' => 5000000,
            'total_amount' => 5000000,
            'payment_status' => 'cancelled',
        ]);

        $response = $this->actingAs($this->client)->get(route('klien.dashboard'));
        $response->assertOk();

        // Menampilkan status kosong tanpa data pelacakan atau dropdown proyek
        $response->assertSee('Belum Ada Projek Berjalan');
        $response->assertDontSee('ORD-202611-CANC03');
        $response->assertDontSee('Surat Keterangan Impor (SKI) Produk BPOM');
    }

    private function createOrderForClient(User $owner, float $total): Order
    {
        $order = Order::create([
            'order_number' => 'ORD-'.date('Ymd').'-'.rand(100000, 999999),
            'user_id' => $owner->id,
            'service_id' => $this->service->id,
            'base_price' => $total,
            'discount_amount' => 0,
            'total_cost' => $total,
            'status' => 'pending',
            'current_stage' => 'Pesanan Diterima',
            'progress_percentage' => 15,
        ]);

        Invoice::create([
            'order_id' => $order->id,
            'user_id' => $owner->id,
            'invoice_number' => 'INV-'.date('Ymd').'-'.rand(100000, 999999),
            'issued_at' => now(),
            'due_date' => now()->addDays(7)->toDateString(),
            'customer_name' => $owner->name,
            'customer_company' => $owner->company_name,
            'customer_email' => $owner->email,
            'customer_phone' => $owner->phone,
            'customer_address' => $owner->address,
            'service_name' => $this->service->name,
            'subtotal' => $total,
            'discount_amount' => 0,
            'total_amount' => $total,
            'payment_status' => 'unpaid',
        ]);

        return $order;
    }
}
