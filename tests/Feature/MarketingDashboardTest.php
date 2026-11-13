<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FreeConsultation;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Promo;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MarketingDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $marketingUser;

    private User $adminUser;

    private User $clientUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->marketingUser = User::factory()->create([
            'name' => 'Tim Marketing Operasional',
            'email' => 'marketing.ops@izinyou.com',
            'phone' => '081200009001',
            'role' => 'marketing',
            'email_verified_at' => now(),
        ]);

        $this->adminUser = User::factory()->create([
            'name' => 'Admin Direktur',
            'email' => 'admin.ops@izinyou.com',
            'phone' => '081200009002',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->clientUser = User::factory()->create([
            'name' => 'Klien Biasa',
            'email' => 'klien.biasa@mitra.co.id',
            'phone' => '081200009003',
            'role' => 'klien',
            'email_verified_at' => now(),
        ]);
    }

    public function test_marketing_dashboard_rbac_and_empty_database_render_cleanly(): void
    {
        $marketingRoutes = [
            route('marketing.dashboard'),
            route('marketing.tickets'),
            route('marketing.promos.index'),
            route('marketing.reviews.index'),
            route('marketing.profile.edit'),
        ];

        // Guest ditolak ke halaman login di seluruh rute Marketing
        foreach ($marketingRoutes as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }

        // Klien dialihkan ke dashboard klien di seluruh rute Marketing
        foreach ($marketingRoutes as $url) {
            $this->actingAs($this->clientUser)
                ->get($url)
                ->assertRedirect(route('klien.dashboard'));
        }

        // Marketing & Admin dapat membuka Dashboard (overview only) saat database kosong tanpa division by zero
        $resp = $this->actingAs($this->marketingUser)->get(route('marketing.dashboard'));
        $resp->assertOk()
            ->assertSee('Pertanyaan Masuk')
            ->assertSee('Konversi Calon Pelanggan')
            ->assertSee('Kupon &amp; Kuota Promo', false)
            ->assertSee('Moderasi Ulasan Klien')
            ->assertSee('Profil')
            ->assertSee('Belum ada voucher yang digunakan.')
            ->assertSee('Buka Tiket &amp; Live Chat', false)
            ->assertSee('Kelola Kupon')
            ->assertSee('Lihat Review')
            ->assertDontSee('replyModal')
            ->assertDontSee('addPromoModal')
            ->assertDontSee('editPromoModal');

        foreach ($marketingRoutes as $url) {
            $this->actingAs($this->marketingUser)->get($url)->assertOk();
            $this->actingAs($this->adminUser)->get($url)->assertOk();
        }
    }

    public function test_marketing_dashboard_displays_real_inquiries_conversion_funnel_and_promo_metrics(): void
    {
        $category = Category::create([
            'name' => 'Sertifikasi Industri',
            'slug' => 'sertifikasi-industri',
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'Sertifikasi SNI Produk',
            'slug' => 'sertifikasi-sni-produk',
            'price' => 10000000,
        ]);

        // Unverified second registered client (no order)
        $unverifiedClient = User::factory()->create([
            'name' => 'Calon Belum OTP',
            'email' => 'pending.otp@mitra.co.id',
            'phone' => '081200009004',
            'role' => 'klien',
            'email_verified_at' => null,
        ]);

        // 1) Pertanyaan dari klien terdaftar yang sudah ditangani
        FreeConsultation::create([
            'user_id' => $this->clientUser->id,
            'name' => $this->clientUser->name,
            'email' => $this->clientUser->email,
            'phone' => $this->clientUser->phone,
            'subject' => 'Tanya jadwal audit pabrik',
            'message' => 'Kapan tim auditor bisa berkunjung?',
            'status' => 'responded',
            'response' => 'Minggu depan siap dijadwalkan.',
        ]);

        // 2) Inquiry formulir layanan tamu yang belum ditangani (juga dihitung sebagai prospek tamu)
        FreeConsultation::create([
            'user_id' => null,
            'name' => 'Tamu Prospek Layanan',
            'email' => 'tamu.prospek@industri.co.id',
            'phone' => '081234000111',
            'subject' => 'Konsultasi Layanan: Sertifikasi SNI Produk (Dokumen Belum Lengkap)',
            'message' => 'Kami belum punya dokumen SOP.',
            'status' => 'pending',
        ]);

        // Promos: aktif paling sering dipakai, kedaluwarsa, kuota habis, non-aktif
        $topPromo = Promo::create([
            'code' => 'HEMAT50',
            'title' => 'Promo Terpopuler',
            'discount_amount' => 500000,
            'quota' => 50,
            'used_count' => 12,
            'is_active' => true,
            'expired_date' => now()->addDays(20),
        ]);

        Promo::create([
            'code' => 'MERDEKAOLD',
            'title' => 'Promo Kedaluwarsa',
            'discount_percentage' => 10,
            'quota' => 20,
            'used_count' => 3,
            'is_active' => true,
            'expired_date' => now()->subDays(2),
        ]);

        Promo::create([
            'code' => 'FULL10',
            'title' => 'Promo Kuota Habis',
            'discount_amount' => 200000,
            'quota' => 10,
            'used_count' => 10,
            'is_active' => true,
            'expired_date' => now()->addDays(5),
        ]);

        Promo::create([
            'code' => 'OFF1',
            'title' => 'Promo Non-Aktif',
            'discount_amount' => 100000,
            'quota' => 30,
            'used_count' => 0,
            'is_active' => false,
            'expired_date' => now()->addDays(10),
        ]);

        // Prospek tamu dari klaim voucher
        DB::table('promo_claims')->insert([
            'promo_id' => $topPromo->id,
            'name' => 'Prospek Klaim Promo',
            'phone' => '081355550000',
            'email' => 'klaim@prospek.co.id',
            'ip_address' => '127.0.0.1',
            'claimed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Order nyata oleh $clientUser memakai $topPromo (sehingga hanya 1 dari 2 akun klien yg konversi order)
        $order = Order::create([
            'order_number' => 'ORD-MKT-001',
            'user_id' => $this->clientUser->id,
            'service_id' => $service->id,
            'promo_id' => $topPromo->id,
            'promo_code' => $topPromo->code,
            'base_price' => 10000000,
            'discount_amount' => 500000,
            'total_cost' => 9500000,
            'status' => 'completed',
            'current_stage' => 'Selesai',
            'progress_percentage' => 100,
        ]);

        Review::create([
            'order_id' => $order->id,
            'user_id' => $this->clientUser->id,
            'service_id' => $service->id,
            'rating' => 5,
            'comment' => 'Respons tim marketing sangat cepat.',
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->marketingUser)->get(route('marketing.dashboard'));
        $response->assertOk();

        // Verifikasi viewStats langsung dari DB
        $stats = $response->viewData('stats');
        $this->assertSame(2, $stats['totalConsultations']);
        $this->assertSame(1, $stats['pendingConsultations']);
        $this->assertSame(1, $stats['respondedConsultations']);
        $this->assertSame(1, $stats['serviceInquiryCount']);
        $this->assertSame(1, $stats['directConsultationCount']);

        // 2 registered clients ($clientUser & $unverifiedClient) + 2 unique guest leads (tamu konsultasi & klaim) = 4
        $this->assertSame(2, $stats['guestLeadCount']);
        $this->assertSame(4, $stats['totalProspects']);
        $this->assertSame(2, $stats['registeredClientsCount']);
        $this->assertSame(1, $stats['verifiedClientsCount']);
        $this->assertSame(1, $stats['unverifiedClientsCount']);
        $this->assertSame(1, $stats['convertedCustomersCount']);
        $this->assertSame(50.0, $stats['prospectToRegisteredRate']);
        $this->assertSame(50.0, $stats['registeredToCustomerRate']);
        $this->assertSame(25.0, $stats['prospectToCustomerRate']);

        // Promos: total used = 12 + 3 + 10 + 0 = 25; active (excl MERDEKAOLD) = 2 (HEMAT50 & FULL10); expired = 1
        $this->assertSame(25, $stats['totalPromosUsed']);
        $this->assertSame(2, $stats['activePromos']);
        $this->assertSame(1, $stats['expiredPromos']);
        $this->assertSame(1, $stats['inactivePromos']);
        $this->assertSame(1, $stats['exhaustedPromos']);
        $this->assertSame('HEMAT50', $stats['topPromoCode']);
        $this->assertSame(12, $stats['topPromoUsedCount']);
        $this->assertSame(1, $stats['ordersWithPromoCount']);
        $this->assertSame(1, $stats['monthlyPromoOrdersCount']);

        // Dashboard HANYA menampilkan ringkasan & navigasi CTA, bukan daftar detail atau modal aksi
        $response->assertSee('HEMAT50 (12x)');
        $response->assertSee('1 Kedaluwarsa');
        $response->assertSee('4 Orang');
        $response->assertSee('2 Akun (50%)');
        $response->assertSee('1 Klien (50% dr daftar)');
        $response->assertSee('Buka Tiket &amp; Live Chat', false);
        $response->assertSee('Kelola Kupon');
        $response->assertSee('Lihat Review');
        $response->assertDontSee('MERDEKAOLD');
        $response->assertDontSee('Kami belum punya dokumen SOP.');
        $response->assertDontSee('Respons tim marketing sangat cepat.');
        $response->assertDontSee('replyModal');
        $response->assertDontSee('editPromoModal');

        // Daftar detail kupon promo tampil di menu Kupon & Kuota Promo
        $this->actingAs($this->marketingUser)
            ->get(route('marketing.promos.index'))
            ->assertOk()
            ->assertSee('HEMAT50')
            ->assertSee('12 / 50')
            ->assertSee('MERDEKAOLD')
            ->assertSee('Kedaluwarsa')
            ->assertSee('FULL10')
            ->assertSee('Kuota Habis')
            ->assertSee('OFF1')
            ->assertSee('Non-Aktif');

        // Pastikan $unverifiedClient tetap ada
        $this->assertNotNull($unverifiedClient->id);
    }

    public function test_marketing_can_filter_respond_and_update_consultation_and_inquiry_status(): void
    {
        $clientConsult = FreeConsultation::create([
            'user_id' => $this->clientUser->id,
            'name' => $this->clientUser->name,
            'email' => $this->clientUser->email,
            'phone' => $this->clientUser->phone,
            'subject' => 'Permintaan Konsultasi Sertifikat Halal',
            'message' => 'Mohon info jadwal audit.',
            'status' => 'pending',
        ]);

        $guestInquiry = FreeConsultation::create([
            'user_id' => null,
            'name' => 'Bu Sari',
            'email' => 'sari@industri-maju.co.id',
            'phone' => '081234560000',
            'subject' => 'Konsultasi Layanan: Izin Edar BPOM (Belum Punya Denah)',
            'message' => 'Apakah tim IzinYou bisa bantu buat denah CPPOB?',
            'status' => 'pending',
        ]);

        // Filter sumber = inquiry pada halaman Tiket & Live Chat hanya menampilkan guestInquiry beserta kontak pengirim
        $this->actingAs($this->marketingUser)
            ->get(route('marketing.tickets', ['source' => 'inquiry']))
            ->assertOk()
            ->assertSee('sari@industri-maju.co.id')
            ->assertSee('081234560000')
            ->assertSee('Apakah tim IzinYou bisa bantu buat denah CPPOB?')
            ->assertDontSee('Permintaan Konsultasi Sertifikat Halal');

        // Filter sumber = consultation hanya menampilkan clientConsult
        $this->actingAs($this->marketingUser)
            ->get(route('marketing.tickets', ['source' => 'consultation']))
            ->assertOk()
            ->assertSee('Permintaan Konsultasi Sertifikat Halal')
            ->assertDontSee('sari@industri-maju.co.id');

        // Negative: menandai responded tanpa isi tanggapan ditolak
        $this->actingAs($this->marketingUser)
            ->post(route('marketing.consultation.reply', $guestInquiry->id), [
                'status' => 'responded',
                'response' => '',
            ])
            ->assertSessionHasErrors(['response']);

        // Positive: menjawab & ubah status menjadi responded, lalu kembalikan ke pending
        $this->actingAs($this->marketingUser)
            ->post(route('marketing.consultation.reply', $guestInquiry->id), [
                'status' => 'responded',
                'response' => 'Ya, tim teknis kami siap mendampingi pembuatan denah CPPOB.',
            ])
            ->assertSessionHas('success');

        $guestInquiry->refresh();
        $this->assertSame('responded', $guestInquiry->status);
        $this->assertSame('Ya, tim teknis kami siap mendampingi pembuatan denah CPPOB.', $guestInquiry->response);

        // Filter status=responded pada halaman Tiket menampilkan tanggapan resmi yang tersimpan
        $this->actingAs($this->marketingUser)
            ->get(route('marketing.tickets', ['status' => 'responded']))
            ->assertOk()
            ->assertSee('Ya, tim teknis kami siap mendampingi pembuatan denah CPPOB.')
            ->assertDontSee('Mohon info jadwal audit.');

        $this->actingAs($this->marketingUser)
            ->post(route('marketing.consultation.reply', $guestInquiry->id), [
                'status' => 'pending',
                'response' => '',
            ])
            ->assertSessionHas('success');

        $this->assertSame('pending', $guestInquiry->fresh()->status);

        // Negative: ID tidak ditemukan
        $this->actingAs($this->marketingUser)
            ->post(route('marketing.consultation.reply', 99999), [
                'status' => 'responded',
                'response' => 'Tes',
            ])
            ->assertSessionHas('error');

        $this->assertNotNull($clientConsult->id);
    }

    public function test_marketing_can_create_edit_toggle_delete_promos_with_checkout_validation(): void
    {
        $category = Category::create(['name' => 'Perizinan Usaha', 'slug' => 'perizinan-usaha']);
        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'Pengurusan NIB & OSS RBA',
            'slug' => 'pengurusan-nib-oss-rba',
            'price' => 5000000,
        ]);

        // Negative: persentase > 100 dan tanggal lewat ditolak
        $this->actingAs($this->marketingUser)
            ->post(route('marketing.promos.store'), [
                'code' => 'oversale',
                'discount_type' => 'percentage',
                'discount_value' => 150,
                'quota' => 10,
                'expired_date' => now()->subDay()->toDateString(),
            ])
            ->assertSessionHasErrors(['discount_value', 'expired_date']);

        // Positive: terbitkan voucher baru via dashboard Marketing
        $this->actingAs($this->marketingUser)
            ->post(route('marketing.promos.store'), [
                'code' => 'prasmod15',
                'title' => 'Diskon Awal Bulan 15%',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'min_transaction' => 1000000,
                'quota' => 100,
                'expired_date' => now()->addMonth()->toDateString(),
            ])
            ->assertSessionHas('success');

        $promo = Promo::where('code', 'PRASMOD15')->firstOrFail();
        $this->assertSame(15.0, (float) $promo->discount_percentage);
        $this->assertTrue($promo->is_active);

        // Negative: kode duplikat (meski huruf kecil) ditolak
        $this->actingAs($this->marketingUser)
            ->post(route('marketing.promos.store'), [
                'code' => 'prasmod15',
                'discount_type' => 'nominal',
                'discount_value' => 50000,
                'quota' => 10,
            ])
            ->assertSessionHasErrors(['code']);

        // Edit penuh: ubah kode, persentase 20%, kuota 80
        $this->actingAs($this->marketingUser)
            ->put(route('marketing.promos.update', $promo->id), [
                'code' => 'PRASMOD20',
                'title' => 'Diskon Update 20%',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'min_transaction' => 2000000,
                'quota' => 80,
                'expired_date' => now()->addMonths(2)->toDateString(),
                'is_active' => 1,
            ])
            ->assertSessionHas('success');

        $promo->refresh();
        $this->assertSame('PRASMOD20', $promo->code);
        $this->assertSame(20.0, (float) $promo->discount_percentage);
        $this->assertSame(80, $promo->quota);

        // Halaman Kupon & Kuota Promo menampilkan detail kupon hasil edit serta tombol aksi lengkap
        $this->actingAs($this->marketingUser)
            ->get(route('marketing.promos.index'))
            ->assertOk()
            ->assertSee('PRASMOD20')
            ->assertSee('Diskon Update 20%')
            ->assertSee('Nonaktifkan')
            ->assertSee('Edit')
            ->assertSee('Hapus')
            ->assertSee('editPromoModal');

        // Verifikasi validasi voucher saat checkout pelanggan memakai voucher editan (20% dari 5jt = 1jt -> 4jt)
        $this->actingAs($this->clientUser)
            ->postJson(route('klien.checkout.validate-voucher'), [
                'service_id' => $service->id,
                'promo_code' => 'PRASMOD20',
            ])
            ->assertOk()
            ->assertJson([
                'valid' => true,
                'discount' => 1000000,
                'final_total' => 4000000,
            ]);

        // Quick toggle nonaktifkan -> langsung tertolak pada validasi checkout pelanggan
        $this->actingAs($this->marketingUser)
            ->put(route('marketing.promos.update', $promo->id), [
                'quota' => $promo->quota,
                'is_active' => 0,
            ])
            ->assertSessionHas('success');

        $this->assertFalse($promo->fresh()->is_active);

        $this->actingAs($this->clientUser)
            ->postJson(route('klien.checkout.validate-voucher'), [
                'service_id' => $service->id,
                'promo_code' => 'PRASMOD20',
            ])
            ->assertStatus(422)
            ->assertJson(['valid' => false]);

        // Hapus voucher
        $this->actingAs($this->marketingUser)
            ->delete(route('marketing.promos.destroy', $promo->id))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('promos', ['id' => $promo->id]);
    }

    public function test_marketing_review_moderation_lifecycle_and_public_testimonial_sync(): void
    {
        $category = Category::create(['name' => 'Alat Kesehatan', 'slug' => 'alat-kesehatan']);
        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'Izin Edar Alat Kesehatan AKD',
            'slug' => 'izin-edar-alkes-akd',
            'price' => 8000000,
        ]);

        $orderOne = Order::create([
            'order_number' => 'ORD-REV-MKT-1',
            'user_id' => $this->clientUser->id,
            'service_id' => $service->id,
            'base_price' => 8000000,
            'discount_amount' => 0,
            'total_cost' => 8000000,
            'status' => 'completed',
            'current_stage' => 'Selesai',
            'progress_percentage' => 100,
        ]);

        $orderTwo = Order::create([
            'order_number' => 'ORD-REV-MKT-2',
            'user_id' => $this->clientUser->id,
            'service_id' => $service->id,
            'base_price' => 8000000,
            'discount_amount' => 0,
            'total_cost' => 8000000,
            'status' => 'completed',
            'current_stage' => 'Selesai',
            'progress_percentage' => 100,
        ]);

        $pendingReview = Review::create([
            'order_id' => $orderOne->id,
            'user_id' => $this->clientUser->id,
            'service_id' => $service->id,
            'rating' => 5,
            'comment' => 'Pendampingan AKD super profesional dan cepat terbit.',
            'status' => 'pending',
            'is_featured' => false,
        ]);

        $rejectedReview = Review::create([
            'order_id' => $orderTwo->id,
            'user_id' => $this->clientUser->id,
            'service_id' => $service->id,
            'rating' => 2,
            'comment' => 'Komentar tidak pantas untuk publik.',
            'status' => 'published',
            'is_featured' => true,
        ]);

        // Halaman Moderasi Ulasan menampilkan nama klien, nama layanan, rating, komentar, status & tombol Tolak
        $reviewsPage = $this->actingAs($this->marketingUser)->get(route('marketing.reviews.index'));
        $reviewsPage->assertOk()
            ->assertSee('Klien Biasa')
            ->assertSee('Izin Edar Alat Kesehatan AKD')
            ->assertSee('Pendampingan AKD super profesional dan cepat terbit.')
            ->assertSee('Pending (Menunggu Review)')
            ->assertSee('Tolak');

        // Filter review_status=pending hanya menampilkan pendingReview
        $this->actingAs($this->marketingUser)
            ->get(route('marketing.reviews.index', ['review_status' => 'pending']))
            ->assertOk()
            ->assertSee('Pendampingan AKD super profesional dan cepat terbit.')
            ->assertDontSee('Komentar tidak pantas untuk publik.');

        // Sebelum disetujui, pendingReview belum tampil di halaman utama maupun detail layanan
        $this->get(route('home'))->assertDontSee('Pendampingan AKD super profesional dan cepat terbit.');
        $this->get(route('services.show', $service->slug))->assertDontSee('Pendampingan AKD super profesional dan cepat terbit.');

        // Marketing menolak review kedua -> otomatis lepas is_featured dan hilang dari beranda publik
        $this->actingAs($this->marketingUser)
            ->post(route('marketing.reviews.moderate', $rejectedReview->id), [
                'status' => 'rejected',
                'is_featured' => 1,
            ])
            ->assertSessionHas('success');

        $rejectedReview->refresh();
        $this->assertSame('rejected', $rejectedReview->status);
        $this->assertFalse($rejectedReview->is_featured);
        $this->get(route('home'))->assertDontSee('Komentar tidak pantas untuk publik.');

        // Marketing menyetujui pendingReview (uji juga alias 'approved' -> 'published')
        $this->actingAs($this->marketingUser)
            ->post(route('marketing.reviews.moderate', $pendingReview->id), [
                'status' => 'approved',
                'is_featured' => 1,
            ])
            ->assertSessionHas('success');

        $pendingReview->refresh();
        $this->assertSame('published', $pendingReview->status);
        $this->assertTrue($pendingReview->is_featured);

        // Setelah disetujui, tampil sebagai testimoni di halaman utama & halaman detail layanan
        $this->get(route('home'))->assertOk()->assertSee('Pendampingan AKD super profesional dan cepat terbit.');
        $this->get(route('services.show', $service->slug))->assertOk()->assertSee('Pendampingan AKD super profesional dan cepat terbit.');

        // Negative: klien dilarang memoderasi, status tidak valid ditolak, ID hilang ditangani rapi
        $this->actingAs($this->clientUser)
            ->post(route('marketing.reviews.moderate', $pendingReview->id), ['status' => 'rejected'])
            ->assertRedirect(route('klien.dashboard'));

        $this->actingAs($this->marketingUser)
            ->post(route('marketing.reviews.moderate', $pendingReview->id), ['status' => 'invalid_xyz'])
            ->assertSessionHasErrors(['status']);

        $this->actingAs($this->marketingUser)
            ->post(route('marketing.reviews.moderate', 999999), ['status' => 'published'])
            ->assertSessionHas('error');
    }

    public function test_marketing_end_to_end_integration_across_all_five_modules(): void
    {
        $category = Category::create(['name' => 'Sertifikasi Halal', 'slug' => 'sertifikasi-halal']);
        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'Sertifikasi Halal BPJPH',
            'slug' => 'sertifikasi-halal-bpjph',
            'price' => 7500000,
        ]);

        // 1) Guest kirim inquiry formulir layanan
        $this->post(route('services.consultation'), [
            'name' => 'Hendra Lead',
            'phone' => '081288887777',
            'email' => 'hendra.lead@mitra.co.id',
            'service_name' => $service->name,
            'missing_doc_category' => 'Belum Memiliki Penyelia Halal',
            'message' => 'Mohon panduan penyelia halal.',
        ])->assertSessionHas('success');

        // 2) Marketing menerbitkan promo IZIN10E2E
        $this->actingAs($this->marketingUser)
            ->post(route('marketing.promos.store'), [
                'code' => 'IZIN10E2E',
                'title' => 'Promo Halal E2E',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'quota' => 50,
            ])
            ->assertSessionHas('success');

        // 3) Klien melakukan checkout dengan promo tersebut -> used_count bertambah & order tercatat
        $this->actingAs($this->clientUser)
            ->post(route('klien.order.checkout'), [
                'service_id' => $service->id,
                'promo_code' => 'IZIN10E2E',
                'checkout_token' => 'tok-mkt-e2e-01',
            ])
            ->assertRedirect();

        $order = Order::where('checkout_token', 'tok-mkt-e2e-01')->firstOrFail();
        $this->assertSame(1, Promo::where('code', 'IZIN10E2E')->firstOrFail()->used_count);

        // Selesaikan order -> klien beri ulasan -> Marketing tolak lalu setujui kembali
        $order->update(['status' => 'completed', 'current_stage' => 'Selesai', 'progress_percentage' => 100]);

        $this->actingAs($this->clientUser)
            ->post(route('klien.orders.review.store', $order->id), [
                'rating' => 5,
                'comment' => 'Integrasi proses halal sangat memuaskan.',
            ])
            ->assertRedirect();

        $review = Review::where('order_id', $order->id)->firstOrFail();

        $this->actingAs($this->marketingUser)
            ->post(route('marketing.reviews.moderate', $review->id), [
                'status' => 'published',
                'is_featured' => 1,
            ])
            ->assertSessionHas('success');

        // Dashboard Marketing menggabungkan seluruh ringkasan data nyata tanpa detail tabel/modal
        $dash = $this->actingAs($this->marketingUser)->get(route('marketing.dashboard'));
        $dash->assertOk()
            ->assertDontSee('Mohon panduan penyelia halal.')
            ->assertDontSee('Integrasi proses halal sangat memuaskan.');
        $stats = $dash->viewData('stats');
        $this->assertSame(1, $stats['totalConsultations']);
        $this->assertSame(1, $stats['serviceInquiryCount']);
        $this->assertSame(2, $stats['totalProspects']); // clientUser + Hendra Lead
        $this->assertSame(1, $stats['convertedCustomersCount']);
        $this->assertSame('IZIN10E2E', $stats['topPromoCode']);
        $this->assertSame(1, $stats['totalPromosUsed']);
        $this->assertSame(1, $stats['monthlyPromoOrdersCount']);
        $this->assertSame(1, $stats['publishedReviews']);

        // Masing-masing halaman operasional menampilkan entitas lengkap
        $this->actingAs($this->marketingUser)
            ->get(route('marketing.tickets'))
            ->assertOk()
            ->assertSee('Hendra Lead')
            ->assertSee('Mohon panduan penyelia halal.');

        $this->actingAs($this->marketingUser)
            ->get(route('marketing.promos.index'))
            ->assertOk()
            ->assertSee('IZIN10E2E')
            ->assertSee('Promo Halal E2E');

        $this->actingAs($this->marketingUser)
            ->get(route('marketing.reviews.index'))
            ->assertOk()
            ->assertSee('Integrasi proses halal sangat memuaskan.')
            ->assertSee('Testimoni Unggulan');
    }

    public function test_marketing_coupon_usage_history_shows_real_transactions_and_supports_all_filters(): void
    {
        $category = Category::create(['name' => 'Alat Kesehatan', 'slug' => 'alat-kesehatan-promo']);

        $serviceOne = Service::create([
            'category_id' => $category->id,
            'name' => 'CPPKRTB',
            'slug' => 'cppkrtb',
            'price' => 35000000,
        ]);

        $serviceTwo = Service::create([
            'category_id' => $category->id,
            'name' => 'Sertifikat Laik Higiene',
            'slug' => 'sertifikat-laik-higiene',
            'price' => 4000000,
        ]);

        $clientOne = User::factory()->create([
            'name' => 'Eka Wahyuning',
            'company_name' => 'PT Cinta Sejati',
            'email' => 'eka@cintasejati.co.id',
            'phone' => '081234561111',
            'role' => 'klien',
            'email_verified_at' => now(),
        ]);

        $clientTwo = User::factory()->create([
            'name' => 'Budi Santoso',
            'company_name' => 'PT Maju Makmur',
            'email' => 'budi@majumakmur.co.id',
            'phone' => '081234562222',
            'role' => 'klien',
            'email_verified_at' => now(),
        ]);

        $promoOne = Promo::create([
            'code' => 'IZINYOU10',
            'title' => 'Diskon Layanan 10%',
            'discount_amount' => 0,
            'discount_percentage' => 10,
            'quota' => 50,
            'used_count' => 0,
            'is_active' => true,
            'expired_date' => now()->addMonth(),
        ]);

        $promoTwo = Promo::create([
            'code' => 'HEMAT200',
            'title' => 'Potongan Langsung 200Rb',
            'discount_amount' => 200000,
            'discount_percentage' => null,
            'quota' => 20,
            'used_count' => 0,
            'is_active' => true,
            'expired_date' => now()->addMonth(),
        ]);

        // Order 1 via checkout nyata -> Promo 10% pada Rp 35jt + Invoice otomatis + Payment lunas
        $this->actingAs($clientOne)
            ->post(route('klien.order.checkout'), [
                'service_id' => $serviceOne->id,
                'promo_code' => 'IZINYOU10',
                'checkout_token' => 'tok-hist-001',
            ])
            ->assertRedirect();

        $orderOne = Order::with('invoice')->where('checkout_token', 'tok-hist-001')->firstOrFail();
        $orderOne->created_at = '2026-11-12 03:58:01';
        $orderOne->status = 'completed';
        $orderOne->save();

        Payment::create([
            'order_id' => $orderOne->id,
            'user_id' => $clientOne->id,
            'invoice_id' => $orderOne->invoice->id,
            'payment_method' => 'Transfer Bank Mandiri',
            'amount' => $orderOne->total_cost,
            'status' => 'success',
            'paid_at' => now(),
        ]);

        // Order 2 via checkout nyata -> Promo Rp 200rb pada Rp 4jt, lalu status order dibatalkan
        $this->actingAs($clientTwo)
            ->post(route('klien.order.checkout'), [
                'service_id' => $serviceTwo->id,
                'promo_code' => 'HEMAT200',
                'checkout_token' => 'tok-hist-002',
            ])
            ->assertRedirect();

        $orderTwo = Order::with('invoice')->where('checkout_token', 'tok-hist-002')->firstOrFail();
        $orderTwo->created_at = '2026-10-01 09:00:00';
        $orderTwo->status = 'cancelled';
        $orderTwo->save();

        // Order 3 tanpa promo -> TIDAK boleh muncul di Riwayat Penggunaan Kupon
        $noPromoOrder = Order::create([
            'order_number' => 'ORD-TANPA-KUPON',
            'user_id' => $this->clientUser->id,
            'service_id' => $serviceTwo->id,
            'base_price' => 4000000,
            'discount_amount' => 0,
            'total_cost' => 4000000,
            'status' => 'pending',
        ]);

        // Halaman Kupon menampilkan counter kuota, tombol Lihat Riwayat Penggunaan, serta data lengkap transaksi asli
        $resp = $this->actingAs($this->marketingUser)->get(route('marketing.promos.index'));
        $resp->assertOk()
            ->assertSee('Riwayat Penggunaan Kupon')
            ->assertSee('Digunakan:')
            ->assertSee('1 / 50')
            ->assertSee('Lihat Riwayat Penggunaan')
            ->assertSee('IZINYOU10')
            ->assertSee('Eka Wahyuning')
            ->assertSee('PT Cinta Sejati')
            ->assertSee('CPPKRTB')
            ->assertSee($orderOne->order_number)
            ->assertSee($orderOne->invoice->invoice_number)
            ->assertSee('Rp 35.000.000')
            ->assertSee('- Rp 3.500.000')
            ->assertSee('Rp 31.500.000')
            ->assertSee('12 November 2026')
            ->assertSee('Berhasil')
            ->assertSee('HEMAT200')
            ->assertSee('Budi Santoso')
            ->assertSee('PT Maju Makmur')
            ->assertSee('Sertifikat Laik Higiene')
            ->assertSee('Batal')
            ->assertDontSee($noPromoOrder->order_number);

        // Filter 1: Berdasarkan kode voucher
        $this->actingAs($this->marketingUser)
            ->get(route('marketing.promos.index', ['promo_code' => $promoOne->code]))
            ->assertOk()
            ->assertSee($orderOne->order_number)
            ->assertDontSee($orderTwo->order_number);

        // Filter 2: Berdasarkan nama klien
        $this->actingAs($this->marketingUser)
            ->get(route('marketing.promos.index', ['client' => 'Budi']))
            ->assertOk()
            ->assertSee($orderTwo->order_number)
            ->assertDontSee($orderOne->order_number);

        // Filter 3: Berdasarkan PT/perusahaan
        $this->actingAs($this->marketingUser)
            ->get(route('marketing.promos.index', ['company' => 'Cinta Sejati']))
            ->assertOk()
            ->assertSee($orderOne->order_number)
            ->assertDontSee($orderTwo->order_number);

        // Filter 4: Berdasarkan layanan
        $this->actingAs($this->marketingUser)
            ->get(route('marketing.promos.index', ['service_id' => $serviceTwo->id]))
            ->assertOk()
            ->assertSee($orderTwo->order_number)
            ->assertDontSee($orderOne->order_number);

        // Filter 5: Berdasarkan tanggal (+ validasi tanggal tidak valid tidak error)
        $this->actingAs($this->marketingUser)
            ->get(route('marketing.promos.index', ['date_from' => '2026-11-12', 'date_to' => '2026-11-12']))
            ->assertOk()
            ->assertSee($orderOne->order_number)
            ->assertDontSee($orderTwo->order_number);

        $this->actingAs($this->marketingUser)
            ->get(route('marketing.promos.index', ['date_from' => 'bukan-tanggal']))
            ->assertOk()
            ->assertSee($orderOne->order_number);

        // Filter 6: Berdasarkan status Berhasil vs Batal
        $this->actingAs($this->marketingUser)
            ->get(route('marketing.promos.index', ['usage_status' => 'berhasil']))
            ->assertOk()
            ->assertSee($orderOne->order_number)
            ->assertDontSee($orderTwo->order_number);

        $this->actingAs($this->marketingUser)
            ->get(route('marketing.promos.index', ['usage_status' => 'batal']))
            ->assertOk()
            ->assertSee($orderTwo->order_number)
            ->assertDontSee($orderOne->order_number);

        $this->assertNotNull($promoTwo->id);
    }

    public function test_marketing_profile_view_edit_password_and_role_escalation_protection(): void
    {
        // Halaman Profil menampilkan data akun Marketing yang sedang login saja (bukan milik klien lain)
        $this->actingAs($this->marketingUser)
            ->get(route('marketing.profile.edit'))
            ->assertOk()
            ->assertSee('Tim Marketing Operasional')
            ->assertSee('marketing.ops@izinyou.com')
            ->assertSee('Marketing &amp; Client Relations', false)
            ->assertSee('Edit Profil')
            ->assertSee('Simpan Perubahan Profil')
            ->assertDontSee('Klien Biasa');

        // Positif: update profil (termasuk format nama dengan kurung/simbol resmi & +62 -> 08) + guard anti privilege escalation
        $originalVerifiedAt = $this->marketingUser->email_verified_at;

        $this->actingAs($this->marketingUser)
            ->put(route('marketing.profile.update'), [
                'name' => 'Ari Agusta (Marketing & Client Relations)',
                'pic_position' => 'Senior Client Relations',
                'email' => 'ari.marketing@izinyou.com',
                'phone' => '+6281999887766',
                'company_name' => 'PT Prasta Solusi Indonesia',
                'address' => 'Jl. Sudirman Kav 52, Jakarta Selatan',
                // Simulasi tampering payload terlarang:
                'role' => 'admin',
                'email_verified_at' => null,
            ])
            ->assertRedirect(route('marketing.profile.edit'))
            ->assertSessionHas('success');

        $this->marketingUser->refresh();
        $this->assertSame('Ari Agusta (Marketing & Client Relations)', $this->marketingUser->name);
        $this->assertSame('Senior Client Relations', $this->marketingUser->pic_position);
        $this->assertSame('ari.marketing@izinyou.com', $this->marketingUser->email);
        $this->assertSame('081999887766', $this->marketingUser->phone);
        $this->assertSame('PT Prasta Solusi Indonesia', $this->marketingUser->company_name);
        $this->assertSame('marketing', $this->marketingUser->role);
        $this->assertNotNull($this->marketingUser->email_verified_at);
        $this->assertTrue($originalVerifiedAt->equalTo($this->marketingUser->email_verified_at));

        // Pastikan profil pengguna lain tidak ikut berubah
        $this->assertSame('klien.biasa@mitra.co.id', $this->clientUser->fresh()->email);

        // Negatif: tolak email/HP duplikat milik akun lain serta format nama/HP tidak valid
        $this->actingAs($this->marketingUser)
            ->put(route('marketing.profile.update'), [
                'name' => 'Ari123',
                'email' => $this->clientUser->email,
                'phone' => $this->clientUser->phone,
            ])
            ->assertSessionHasErrors(['name', 'email', 'phone']);

        // Negatif: klien tidak boleh mengakses endpoint update profil / password Marketing
        $this->actingAs($this->clientUser)
            ->put(route('marketing.profile.update'), [
                'name' => 'Hack Marketing',
                'email' => 'hack@izinyou.com',
                'phone' => '081277776666',
            ])
            ->assertRedirect(route('klien.dashboard'));

        // Keamanan kata sandi: tolak sandi lama yang salah, terima pembaruan sandi yang valid
        $this->actingAs($this->marketingUser)
            ->put(route('marketing.profile.password'), [
                'current_password' => 'salah123',
                'password' => 'BaruAman2026',
                'password_confirmation' => 'BaruAman2026',
            ])
            ->assertSessionHasErrors(['current_password']);

        $this->actingAs($this->marketingUser)
            ->put(route('marketing.profile.password'), [
                'current_password' => 'password',
                'password' => 'BaruAman2026',
                'password_confirmation' => 'BaruAman2026',
            ])
            ->assertRedirect(route('marketing.profile.edit'))
            ->assertSessionHas('success');

        $this->assertTrue(
            Hash::check('BaruAman2026', $this->marketingUser->fresh()->password)
        );
    }
}
