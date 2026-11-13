<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ClientDocument;
use App\Models\ClientDocumentVersion;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceRequirement;
use App\Models\User;
use App\Services\DocumentAuthenticityService;
use App\Services\SecureDocumentStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Tests\TestCase;

class OfficerDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $officerUser;

    private User $clientUser;

    private User $financeUser;

    private User $marketingUser;

    private User $adminUser;

    private Service $serviceAlkes;

    private ServiceRequirement $reqAkta;

    private ServiceRequirement $reqNib;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. User Roles
        $this->officerUser = User::factory()->create([
            'name' => 'Bambang Licensing Officer',
            'email' => 'officer@izinyou.com',
            'phone' => '081234567890',
            'role' => 'officer',
            'company_name' => 'CV Prasta Solusi Indonesia',
            'pic_position' => 'Document & Licensing Specialist',
        ]);

        $this->clientUser = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@mitra.co.id',
            'phone' => '081299887766',
            'role' => 'klien',
            'company_name' => 'PT Mitra Sejahtera Medika',
        ]);

        $this->financeUser = User::factory()->create([
            'name' => 'Dania Finance',
            'email' => 'finance@izinyou.com',
            'phone' => '081233445566',
            'role' => 'finance',
        ]);

        $this->marketingUser = User::factory()->create([
            'name' => 'Rina Marketing',
            'email' => 'marketing@izinyou.com',
            'phone' => '081277889900',
            'role' => 'marketing',
        ]);

        $this->adminUser = User::factory()->create([
            'name' => 'Super Administrator',
            'email' => 'admin@izinyou.com',
            'phone' => '081122334455',
            'role' => 'admin',
        ]);

        // 2. Layanan & Persyaratan Berkas
        $category = Category::create([
            'name' => 'Alat Kesehatan & PKRT',
            'slug' => 'alat-kesehatan-pkrt',
            'description' => 'Perizinan izin edar alkes resmi Kemenkes',
        ]);

        $this->serviceAlkes = Service::create([
            'category_id' => $category->id,
            'name' => 'Pengurusan Izin Edar Alkes Luar Negeri',
            'slug' => 'izin-edar-alkes-impor',
            'price' => 15000000,
            'timeline_estimation' => '14 Hari Kerja',
            'estimation_time' => '14 Hari Kerja',
            'description' => 'Izin edar resmi Kementerian Kesehatan RI untuk alkes impor',
            'is_active' => true,
        ]);

        $this->reqAkta = ServiceRequirement::create([
            'service_id' => $this->serviceAlkes->id,
            'document_name' => 'Akta Pendirian PT & SK Kemenkumham',
            'document_type' => 'Legalitas Perusahaan',
            'is_mandatory' => true,
        ]);

        $this->reqNib = ServiceRequirement::create([
            'service_id' => $this->serviceAlkes->id,
            'document_name' => 'NIB Berbasis Risiko & Sertifikat Standar',
            'document_type' => 'Izin Dasar OSS',
            'is_mandatory' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('officer.dashboard'))->assertRedirect(route('login'));
        $this->get(route('officer.documents'))->assertRedirect(route('login'));
        $this->get(route('officer.projects'))->assertRedirect(route('login'));
        $this->get(route('officer.profile.edit'))->assertRedirect(route('login'));
    }

    public function test_client_marketing_and_finance_are_blocked_by_rbac(): void
    {
        // Klien diblokir
        $this->actingAs($this->clientUser)
            ->get(route('officer.dashboard'))
            ->assertRedirect(route('klien.dashboard'))
            ->assertSessionHas('error');

        // Marketing diblokir
        $this->actingAs($this->marketingUser)
            ->get(route('officer.dashboard'))
            ->assertRedirect(route('marketing.dashboard'))
            ->assertSessionHas('error');

        // Finance diblokir dari dasbor officer
        $this->actingAs($this->financeUser)
            ->get(route('officer.dashboard'))
            ->assertRedirect(route('finance.dashboard'))
            ->assertSessionHas('error');
    }

    public function test_officer_can_view_dashboard_with_document_queue_and_sla_deadlines(): void
    {
        // Buat pesanan projek & dokumen
        $order = Order::create([
            'order_number' => 'ORD-202611-OFF01',
            'user_id' => $this->clientUser->id,
            'service_id' => $this->serviceAlkes->id,
            'base_price' => 15000000,
            'discount_amount' => 0,
            'total_cost' => 15000000,
            'status' => 'processing',
            'current_stage' => 'Pemeriksaan Dokumen',
            'progress_percentage' => 50,
            'created_at' => now()->subDays(5),
        ]);

        $doc = ClientDocument::create([
            'order_id' => $order->id,
            'service_requirement_id' => $this->reqAkta->id,
            'document_name' => 'Akta Pendirian PT',
            'document_type' => 'Legalitas Perusahaan',
            'file_path' => 'client_documents/akta_budi.pdf',
            'original_filename' => 'akta_pendirian_mitra.pdf',
            'version' => 1,
            'status' => 'uploaded',
            'uploaded_by' => $this->clientUser->id,
        ]);

        $response = $this->actingAs($this->officerUser)->get(route('officer.dashboard'));

        $response->assertOk()
            ->assertSee('Dasbor Document &amp; Licensing Officer', false)
            ->assertSee('Antrean Pemeriksaan Kelengkapan Dokumen', false)
            ->assertSee('Pemantauan Tenggat Waktu Pengerjaan Proyek (SLA)', false)
            ->assertSee('ORD-202611-OFF01')
            ->assertSee('PT Mitra Sejahtera Medika')
            ->assertSee('Akta Pendirian PT')
            ->assertSee('14 Hari Kerja')
            ->assertSee('Bambang Licensing Officer');
    }

    public function test_officer_can_filter_document_queue_and_verify_approval(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-202611-OFF02',
            'user_id' => $this->clientUser->id,
            'service_id' => $this->serviceAlkes->id,
            'total_cost' => 15000000,
            'status' => 'processing',
            'current_stage' => 'Pemeriksaan Dokumen',
            'progress_percentage' => 50,
        ]);

        $doc = ClientDocument::create([
            'order_id' => $order->id,
            'service_requirement_id' => $this->reqAkta->id,
            'document_name' => 'Akta Pendirian PT PT Mitra',
            'file_path' => 'client_documents/akta_v1.pdf',
            'version' => 1,
            'status' => 'uploaded',
            'uploaded_by' => $this->clientUser->id,
        ]);

        ClientDocumentVersion::create([
            'client_document_id' => $doc->id,
            'version_number' => 1,
            'file_path' => 'client_documents/akta_v1.pdf',
            'original_filename' => 'akta_v1.pdf',
            'review_status' => 'uploaded',
        ]);

        // Verifikasi dokumen: Disetujui
        $verifyResponse = $this->actingAs($this->officerUser)->post(
            route('officer.documents.verify', $doc->id),
            [
                'status' => 'approved',
                'notes' => 'Dokumen akta pendirian telah dicek lengkap dan sesuai format Kemenkumham.',
            ]
        );

        $verifyResponse->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('client_documents', [
            'id' => $doc->id,
            'status' => 'approved',
            'verified_by' => $this->officerUser->id,
        ]);

        $this->assertDatabaseHas('client_document_versions', [
            'client_document_id' => $doc->id,
            'review_status' => 'approved',
        ]);
    }

    public function test_officer_rejection_requires_notes_reason(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-202611-OFF03',
            'user_id' => $this->clientUser->id,
            'service_id' => $this->serviceAlkes->id,
            'total_cost' => 15000000,
            'status' => 'processing',
        ]);

        $doc = ClientDocument::create([
            'order_id' => $order->id,
            'service_requirement_id' => $this->reqNib->id,
            'document_name' => 'NIB Berbasis Risiko',
            'file_path' => 'client_documents/nib.pdf',
            'version' => 1,
            'status' => 'uploaded',
        ]);

        // Coba tolak tanpa catatan
        $response = $this->actingAs($this->officerUser)->post(
            route('officer.documents.verify', $doc->id),
            [
                'status' => 'rejected',
                'notes' => '',
            ]
        );

        $response->assertSessionHasErrors(['notes']);

        // Tolak dengan catatan revisi yang jelas
        $validReject = $this->actingAs($this->officerUser)->post(
            route('officer.documents.verify', $doc->id),
            [
                'status' => 'rejected',
                'notes' => 'Lampiran Sertifikat Standar KBLI 46691 belum ditandatangani direksi.',
            ]
        );

        $validReject->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('client_documents', [
            'id' => $doc->id,
            'status' => 'rejected',
            'notes' => 'Lampiran Sertifikat Standar KBLI 46691 belum ditandatangani direksi.',
        ]);
    }

    public function test_officer_can_monitor_project_deadline_and_update_stage(): void
    {
        // Buat projek yang mendekati tenggat (dibuat 12 hari lalu dengan SLA 14 hari => sisa 2 hari)
        $order = Order::create([
            'order_number' => 'ORD-202611-OFF04',
            'user_id' => $this->clientUser->id,
            'service_id' => $this->serviceAlkes->id,
            'total_cost' => 15000000,
            'status' => 'processing',
            'current_stage' => 'Proses Pengerjaan',
            'progress_percentage' => 70,
        ]);
        $order->created_at = now()->subDays(12);
        $order->saveQuietly();

        $response = $this->actingAs($this->officerUser)->get(route('officer.projects', ['status' => 'critical']));

        $response->assertOk()
            ->assertSee('ORD-202611-OFF04')
            ->assertSee('Mendekati Batas')
            ->assertSee('70%');

        // Update tahap pengerjaan proyek oleh Officer
        $updateStageResponse = $this->actingAs($this->officerUser)->post(
            route('officer.projects.update-stage', $order->id),
            [
                'stage_name' => 'Menunggu Persetujuan',
                'stage_status' => 'completed',
                'description' => 'Berkas pendaftaran izin edar telah diserahkan dan diverifikasi di portal e-Alkes Kemenkes.',
            ]
        );

        $updateStageResponse->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('project_trackings', [
            'order_id' => $order->id,
            'stage_name' => 'Menunggu Persetujuan',
            'status' => 'completed',
            'updated_by' => $this->officerUser->id,
        ]);

        $order->refresh();
        $this->assertSame(85, (int) $order->progress_percentage);
    }

    public function test_officer_can_update_profile_and_password(): void
    {
        // 1. Update Profil
        $profileResponse = $this->actingAs($this->officerUser)->put(
            route('officer.profile.update'),
            [
                'name' => 'Bambang Nugroho SH',
                'email' => 'bambang.licensing@izinyou.com',
                'phone' => '081234567899',
                'pic_position' => 'Head of Document & Licensing',
                'company_name' => 'CV Prasta Solusi Indonesia',
                'address' => 'Gedung Wisma Perizinan Lt 3 Jakarta Selatan',
            ]
        );

        $profileResponse->assertRedirect(route('officer.profile.edit'))
            ->assertSessionHas('success');

        $this->officerUser->refresh();
        $this->assertSame('Bambang Nugroho SH', $this->officerUser->name);
        $this->assertSame('bambang.licensing@izinyou.com', $this->officerUser->email);
        $this->assertSame('Head of Document & Licensing', $this->officerUser->pic_position);

        // 2. Update Password
        $passwordResponse = $this->actingAs($this->officerUser)->put(
            route('officer.profile.password'),
            [
                'current_password' => 'password',
                'password' => 'NewSecretPassword123!',
                'password_confirmation' => 'NewSecretPassword123!',
            ]
        );

        $passwordResponse->assertRedirect(route('officer.profile.edit'))
            ->assertSessionHas('success');

        $this->officerUser->refresh();
        $this->assertTrue(Hash::check('NewSecretPassword123!', $this->officerUser->password));
    }

    public function test_legacy_petugas_redirects_to_officer_dashboard(): void
    {
        $petugasUser = User::factory()->create([
            'name' => 'Agus Petugas',
            'email' => 'agus.petugas@izinyou.com',
            'role' => 'petugas',
        ]);

        $this->actingAs($petugasUser)
            ->get(route('petugas.dashboard'))
            ->assertRedirect(route('officer.dashboard'));

        $this->actingAs($this->officerUser)
            ->get(route('petugas.dashboard'))
            ->assertRedirect(route('officer.dashboard'));
    }

    public function test_officer_can_view_document_review_page_with_authenticity_audit(): void
    {
        Storage::fake('local');
        $samplePdf = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n/Producer (Official PDF Printer)\n/ByteRange [0 100 200 300]\n/Type /Sig\n/adbe.pkcs7.detached\nhttps://kemenkes.go.id/verify?cert=12345\nKementerian Kesehatan Republik Indonesia\n%%EOF";
        Storage::disk('local')->put('client_documents/surat_resmi.pdf', $samplePdf);

        $order = Order::create([
            'order_number' => 'ORD-202611-OFF05',
            'user_id' => $this->clientUser->id,
            'service_id' => $this->serviceAlkes->id,
            'total_cost' => 15000000,
            'status' => 'processing',
        ]);

        $doc = ClientDocument::create([
            'order_id' => $order->id,
            'service_requirement_id' => $this->reqAkta->id,
            'document_name' => 'Surat Keputusan Kemenkes',
            'file_path' => 'client_documents/surat_resmi.pdf',
            'original_filename' => 'sk_kemenkes_asli.pdf',
            'version' => 1,
            'status' => 'uploaded',
            'uploaded_by' => $this->clientUser->id,
        ]);

        ClientDocumentVersion::create([
            'client_document_id' => $doc->id,
            'version_number' => 1,
            'file_path' => 'client_documents/surat_resmi.pdf',
            'original_filename' => 'sk_kemenkes_asli.pdf',
            'review_status' => 'uploaded',
        ]);

        $response = $this->actingAs($this->officerUser)->get(route('officer.documents.show', $doc->id));

        $response->assertOk()
            ->assertSee('Pemeriksaan Administratif &amp; Audit Keaslian Dokumen', false)
            ->assertSee('Surat Keputusan Kemenkes')
            ->assertSee('ORD-202611-OFF05')
            ->assertSee('Modul Deteksi Keaslian &amp; Integritas Berkas', false)
            ->assertSee('Integritas TTD &amp; Cap Instansi', false)
            ->assertSee('Validasi Metadata &amp; Keaslian', false)
            ->assertSee('Pemeriksaan Administratif:', false)
            ->assertSee('Keputusan Verifikasi Petugas', false);
    }

    public function test_officer_can_fetch_document_audit_json(): void
    {
        Storage::fake('local');
        $samplePdf = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n/Producer (Canva Graphics Studio)\n%%EOF";
        Storage::disk('local')->put('client_documents/edited_canva.pdf', $samplePdf);

        $order = Order::create([
            'order_number' => 'ORD-202611-OFF06',
            'user_id' => $this->clientUser->id,
            'service_id' => $this->serviceAlkes->id,
            'total_cost' => 15000000,
            'status' => 'processing',
        ]);

        $doc = ClientDocument::create([
            'order_id' => $order->id,
            'service_requirement_id' => $this->reqAkta->id,
            'document_name' => 'Akta Canva Edit',
            'file_path' => 'client_documents/edited_canva.pdf',
            'original_filename' => 'akta_canva.pdf',
            'version' => 1,
            'status' => 'uploaded',
            'uploaded_by' => $this->clientUser->id,
        ]);

        $response = $this->actingAs($this->officerUser)->get(route('officer.documents.audit', $doc->id));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'document' => [
                    'id' => $doc->id,
                    'document_name' => 'Akta Canva Edit',
                ],
            ]);

        $data = $response->json();
        $this->assertArrayHasKey('audit', $data);
        $this->assertSame('TAMPERED', $data['audit']['verdict_code']);
        $this->assertTrue($data['audit']['metadata_audit']['tampering_detected']);
        $this->assertSame('Canva Graphics Studio', $data['audit']['metadata_audit']['detected_editor']);
    }

    public function test_document_authenticity_service_detects_genuine_and_tampered_files(): void
    {
        $service = app(DocumentAuthenticityService::class);
        Storage::fake('local');

        // 1. File dengan indikasi manipulasi Canva
        Storage::disk('local')->put('client_documents/fake.pdf', "%PDF-1.5\n/Producer (Canva Graphics Studio)\n%%EOF");
        $tamperedAudit = $service->analyzeFilePath('client_documents/fake.pdf', 'fake.pdf');

        $this->assertSame('TAMPERED', $tamperedAudit['verdict_code']);
        $this->assertTrue($tamperedAudit['metadata_audit']['tampering_detected']);
        $this->assertSame('Canva Graphics Studio', $tamperedAudit['metadata_audit']['detected_editor']);

        // 2. File resmi dengan TTD digital Kemenkes & BSrE
        $genuinePdf = "%PDF-1.6\n/adbe.pkcs7.detached\n/DocTimeStamp\nhttps://kemenkes.go.id/sertifikat/verify\nKementerian Kesehatan RI\n%%EOF";
        Storage::disk('local')->put('client_documents/genuine.pdf', $genuinePdf);
        $genuineAudit = $service->analyzeFilePath('client_documents/genuine.pdf', 'genuine.pdf');

        $this->assertSame('GENUINE', $genuineAudit['verdict_code']);
        $this->assertTrue($genuineAudit['signature_audit']['has_digital_signature']);
        $this->assertTrue($genuineAudit['signature_audit']['has_qr_verification']);
        $this->assertTrue($genuineAudit['signature_audit']['stamp_detected']);
        $this->assertGreaterThanOrEqual(80, $genuineAudit['overall_score']);
    }

    public function test_unauthorized_user_cannot_access_officer_document_review(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-202611-OFF07',
            'user_id' => $this->clientUser->id,
            'service_id' => $this->serviceAlkes->id,
            'total_cost' => 15000000,
            'status' => 'processing',
        ]);

        $doc = ClientDocument::create([
            'order_id' => $order->id,
            'service_requirement_id' => $this->reqAkta->id,
            'document_name' => 'Akta Klien',
            'file_path' => 'client_documents/akta.pdf',
            'version' => 1,
            'status' => 'uploaded',
        ]);

        // Klien diarahkan kembali dengan pesan error (RBAC)
        $this->actingAs($this->clientUser)
            ->get(route('officer.documents.show', $doc->id))
            ->assertRedirect(route('klien.dashboard'))
            ->assertSessionHas('error');

        // Finance diarahkan kembali dengan pesan error (RBAC)
        $this->actingAs($this->financeUser)
            ->get(route('officer.documents.show', $doc->id))
            ->assertRedirect(route('finance.dashboard'))
            ->assertSessionHas('error');
    }

    public function test_officer_can_securely_download_document_from_non_public_storage(): void
    {
        Storage::fake('local');
        $filePath = 'client_documents/vault_123/legalitas_resmi.pdf';
        Storage::disk('local')->put($filePath, "%PDF-1.4\nLegalitas Resmi Perusahaan\n%%EOF");

        $order = Order::create([
            'order_number' => 'ORD-202611-VAULT01',
            'user_id' => $this->clientUser->id,
            'service_id' => $this->serviceAlkes->id,
            'total_cost' => 15000000,
            'status' => 'processing',
        ]);

        $doc = ClientDocument::create([
            'order_id' => $order->id,
            'service_requirement_id' => $this->reqAkta->id,
            'document_name' => 'Akta PT Vault',
            'file_path' => $filePath,
            'original_filename' => 'akta_pt_vault.pdf',
            'version' => 1,
            'status' => 'uploaded',
        ]);

        $response = $this->actingAs($this->officerUser)->get(route('officer.documents.download', $doc->id));

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
    }

    public function test_officer_can_securely_download_specific_version_from_non_public_storage(): void
    {
        Storage::fake('local');
        $v1Path = 'client_documents/vault_123/v1_legalitas.pdf';
        Storage::disk('local')->put($v1Path, "%PDF-1.4\nVersi 1 Legalitas\n%%EOF");

        $order = Order::create([
            'order_number' => 'ORD-202611-VAULT02',
            'user_id' => $this->clientUser->id,
            'service_id' => $this->serviceAlkes->id,
            'total_cost' => 15000000,
            'status' => 'processing',
        ]);

        $doc = ClientDocument::create([
            'order_id' => $order->id,
            'service_requirement_id' => $this->reqAkta->id,
            'document_name' => 'Akta PT Versi',
            'file_path' => $v1Path,
            'version' => 1,
            'status' => 'uploaded',
        ]);

        $ver = ClientDocumentVersion::create([
            'client_document_id' => $doc->id,
            'version_number' => 1,
            'file_path' => $v1Path,
            'original_filename' => 'v1_legalitas.pdf',
            'review_status' => 'uploaded',
        ]);

        $response = $this->actingAs($this->officerUser)->get(route('officer.documents.download-version', [
            'id' => $doc->id,
            'versionId' => $ver->id,
        ]));

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_secure_document_storage_service_enforces_strict_rbac_and_denies_unauthorized_users(): void
    {
        $storageService = app(SecureDocumentStorageService::class);

        $order = Order::create([
            'order_number' => 'ORD-202611-VAULT03',
            'user_id' => $this->clientUser->id,
            'service_id' => $this->serviceAlkes->id,
            'total_cost' => 15000000,
            'status' => 'processing',
        ]);

        $doc = ClientDocument::create([
            'order_id' => $order->id,
            'service_requirement_id' => $this->reqAkta->id,
            'document_name' => 'Sertifikat Standar',
            'file_path' => 'client_documents/sertifikat.pdf',
            'version' => 1,
        ]);

        $otherClient = User::factory()->create([
            'name' => 'Klien Lain',
            'email' => 'other@mitra.co.id',
            'role' => 'klien',
        ]);

        // Berwenang
        $this->assertTrue($storageService->canAccess($this->officerUser, $doc));
        $this->assertTrue($storageService->canAccess($this->adminUser, $doc));
        $this->assertTrue($storageService->canAccess($this->clientUser, $doc)); // Pemilik

        // Ditolak
        $this->assertFalse($storageService->canAccess($otherClient, $doc)); // Klien lain
        $this->assertFalse($storageService->canAccess($this->marketingUser, $doc)); // Marketing
        $this->assertFalse($storageService->canAccess(null, $doc)); // Guest
    }

    public function test_secure_document_storage_service_detects_path_traversal_attempts(): void
    {
        $storageService = app(SecureDocumentStorageService::class);

        $this->expectException(InvalidArgumentException::class);
        $storageService->sanitizePath('../../windows/system32/cmd.exe');
    }

    public function test_secure_document_storage_service_rejects_disguised_executable_files(): void
    {
        $storageService = app(SecureDocumentStorageService::class);
        Storage::fake('local');

        $order = Order::create([
            'order_number' => 'ORD-202611-VAULT04',
            'user_id' => $this->clientUser->id,
            'service_id' => $this->serviceAlkes->id,
            'total_cost' => 15000000,
            'status' => 'processing',
        ]);

        $fakePhpFile = UploadedFile::fake()->createWithContent('exploit.php', '<?php phpinfo(); ?>');

        $this->expectException(InvalidArgumentException::class);
        $storageService->storeUploadedFile($fakePhpFile, $order, $this->reqAkta);
    }

    public function test_officer_can_fetch_project_timeline_json(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-202611-SLA01',
            'user_id' => $this->clientUser->id,
            'service_id' => $this->serviceAlkes->id,
            'total_cost' => 15000000,
            'status' => 'processing',
            'current_stage' => 'Pemeriksaan Dokumen',
            'progress_percentage' => 50,
        ]);

        $response = $this->actingAs($this->officerUser)->get(route('officer.projects.timeline', $order->id));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'project' => [
                'id',
                'order_number',
                'client_name',
                'company_name',
                'service_name',
                'current_stage',
                'progress_percentage',
                'order_status',
                'start_date',
                'deadline_date',
                'timeline_text',
                'estimated_days',
                'remaining_days',
                'deadline_code',
                'deadline_label',
                'deadline_badge',
                'sla_percent',
                'is_completed',
            ],
            'timeline',
            'tracking_history',
        ]);
        $response->assertJson([
            'success' => true,
            'project' => [
                'order_number' => 'ORD-202611-SLA01',
                'client_name' => $this->clientUser->name,
                'current_stage' => 'Pemeriksaan Dokumen',
                'progress_percentage' => 50,
            ],
        ]);
    }

    public function test_officer_can_update_project_stage_and_synchronize_with_client_tracking(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-202611-SLA02',
            'user_id' => $this->clientUser->id,
            'service_id' => $this->serviceAlkes->id,
            'total_cost' => 15000000,
            'status' => 'processing',
            'current_stage' => 'Pemeriksaan Dokumen',
            'progress_percentage' => 50,
        ]);

        $response = $this->actingAs($this->officerUser)->post(route('officer.projects.update-stage', $order->id), [
            'stage_name' => 'Proses Pengerjaan',
            'stage_status' => 'in_progress',
            'description' => 'Sedang dilakukan penyusunan berkas teknis dan koordinasi instansi.',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'current_stage' => 'Proses Pengerjaan',
        ]);

        $this->assertDatabaseHas('project_trackings', [
            'order_id' => $order->id,
            'stage_name' => 'Proses Pengerjaan',
            'status' => 'in_progress',
            'description' => 'Sedang dilakukan penyusunan berkas teknis dan koordinasi instansi.',
            'updated_by' => $this->officerUser->id,
        ]);

        // Klien membuka halaman detail pesanan & tracking
        $clientOrderResponse = $this->actingAs($this->clientUser)->get(route('klien.orders.show', $order->id));
        $clientOrderResponse->assertOk();
        $clientOrderResponse->assertSee('Proses Pengerjaan');
        $clientOrderResponse->assertSee('Sedang dilakukan penyusunan berkas teknis dan koordinasi instansi.');

        // Klien membuka dashboard klien
        $clientDashboardResponse = $this->actingAs($this->clientUser)->get(route('klien.dashboard'));
        $clientDashboardResponse->assertOk();
        $clientDashboardResponse->assertSee('Proses Pengerjaan');
    }

    public function test_unauthorized_user_cannot_update_project_stage(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-202611-SLA03',
            'user_id' => $this->clientUser->id,
            'service_id' => $this->serviceAlkes->id,
            'total_cost' => 15000000,
            'status' => 'processing',
        ]);

        // Klien biasa tidak boleh mengubah tahap projek
        $response = $this->actingAs($this->clientUser)->post(route('officer.projects.update-stage', $order->id), [
            'stage_name' => 'Selesai',
            'stage_status' => 'completed',
            'description' => 'Mencoba bypass status selesai.',
        ]);

        $response->assertRedirect(route('klien.dashboard'));

        // Pengguna role marketing tidak boleh mengubah tahap projek
        $marketingResponse = $this->actingAs($this->marketingUser)->post(route('officer.projects.update-stage', $order->id), [
            'stage_name' => 'Selesai',
            'stage_status' => 'completed',
            'description' => 'Marketing bypass.',
        ]);

        $marketingResponse->assertRedirect(route('marketing.dashboard'));
    }

    public function test_officer_can_export_projects_recapitulation_to_excel_xlsx(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-202611-EXP01',
            'user_id' => $this->clientUser->id,
            'service_id' => $this->serviceAlkes->id,
            'total_cost' => 15000000,
            'status' => 'processing',
            'current_stage' => 'Proses Pengerjaan',
            'progress_percentage' => 70,
        ]);

        $response = $this->actingAs($this->officerUser)->get(route('officer.projects.export-excel'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString('Rekapitulasi_Status_Projek_', (string) $response->headers->get('Content-Disposition'));

        $content = $response->getContent();
        $this->assertNotEmpty($content);
        // Validasi header PKZip (0x50 0x4B 0x03 0x04)
        $this->assertStringStartsWith("PK\x03\x04", $content);

        if (class_exists(\ZipArchive::class)) {
            $tempPath = tempnam(sys_get_temp_dir(), 'xlsx_test_');
            file_put_contents($tempPath, $content);

            $zip = new \ZipArchive;
            $this->assertTrue($zip->open($tempPath));
            $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
            $this->assertNotFalse($sheetXml);
            $this->assertStringContainsString('ORD-202611-EXP01', $sheetXml);
            $this->assertStringContainsString('PT Mitra Sejahtera Medika', $sheetXml);
            $zip->close();
            @unlink($tempPath);
        } else {
            // Validasi struktur manifest PKZip OpenXML
            $this->assertStringContainsString('xl/worksheets/sheet1.xml', $content);
            $this->assertStringContainsString('xl/workbook.xml', $content);
        }
    }

    public function test_officer_can_export_projects_recapitulation_to_pdf(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-202611-EXP02',
            'user_id' => $this->clientUser->id,
            'service_id' => $this->serviceAlkes->id,
            'total_cost' => 15000000,
            'status' => 'processing',
            'current_stage' => 'Pemeriksaan Dokumen',
            'progress_percentage' => 50,
        ]);

        $response = $this->actingAs($this->officerUser)->get(route('officer.projects.export-pdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('Rekapitulasi_Status_Projek_', (string) $response->headers->get('Content-Disposition'));

        $content = $response->getContent();
        $this->assertNotEmpty($content);
        $this->assertStringStartsWith('%PDF-1.4', $content);
        $this->assertStringContainsString('CV PRASTA SOLUSI INDONESIA', $content);
        $this->assertStringContainsString('ORD-202611-EXP02', $content);
        $this->assertStringContainsString('PT Mitra Sejahtera', $content);
    }

    public function test_officer_can_export_filtered_projects(): void
    {
        Order::create([
            'order_number' => 'ORD-202611-FILT01',
            'user_id' => $this->clientUser->id,
            'service_id' => $this->serviceAlkes->id,
            'total_cost' => 15000000,
            'status' => 'completed',
            'current_stage' => 'Selesai',
            'progress_percentage' => 100,
        ]);

        $response = $this->actingAs($this->officerUser)->get(route('officer.projects.export-excel', [
            'status' => 'completed',
        ]));

        $response->assertOk();
        $this->assertStringStartsWith("PK\x03\x04", $response->getContent());
    }

    public function test_unauthorized_user_cannot_export_officer_projects(): void
    {
        $excelResponse = $this->actingAs($this->clientUser)->get(route('officer.projects.export-excel'));
        $excelResponse->assertRedirect(route('klien.dashboard'));

        $pdfResponse = $this->actingAs($this->marketingUser)->get(route('officer.projects.export-pdf'));
        $pdfResponse->assertRedirect(route('marketing.dashboard'));
    }
}
