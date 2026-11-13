<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Concerns\ManagesUserProfile;
use App\Http\Controllers\Controller;
use App\Models\ClientDocument;
use App\Models\ClientDocumentVersion;
use App\Models\Order;
use App\Models\ServiceRequirement;
use App\Services\DocumentAuthenticityService;
use App\Services\OrderProgressService;
use App\Services\ProjectReportExcelService;
use App\Services\ProjectReportPdfService;
use App\Services\SecureDocumentStorageService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OfficerController extends Controller
{
    use ManagesUserProfile;

    /**
     * Dasbor Operasional Utama Document & Licensing Officer.
     * Fokus pada:
     * 1. Pemantauan antrean pemeriksaan kelengkapan dokumen persyaratan izin & sertifikasi.
     * 2. Pemantauan tenggat waktu pengerjaan proyek (SLA deadlines & target timelines).
     */
    public function dashboard(Request $request): View
    {
        $user = Auth::user();
        $stats = $this->buildOfficerStats();

        // 1. Antrean Pemeriksaan Kelengkapan Dokumen (Prioritas: uploaded / baru, disaring berdasarkan request)
        $docFilterStatus = $request->get('doc_status', 'all');
        $docSearch = trim((string) $request->get('doc_q', ''));

        $documentsQuery = ClientDocument::with(['order.user', 'order.service', 'requirement', 'versions'])
            ->leftJoin('orders', 'client_documents.order_id', '=', 'orders.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->leftJoin('service_requirements', 'client_documents.service_requirement_id', '=', 'service_requirements.id')
            ->select(
                'client_documents.*',
                'orders.order_number',
                'orders.status as order_status',
                'orders.current_stage as order_stage',
                'users.name as client_name',
                'users.company_name',
                'users.phone as client_phone',
                'service_requirements.document_name as req_document_name',
                'service_requirements.document_type as req_document_type',
                'service_requirements.is_mandatory'
            );

        if ($docFilterStatus && $docFilterStatus !== 'all') {
            $documentsQuery->where('client_documents.status', $docFilterStatus);
        }

        if ($docSearch !== '') {
            $documentsQuery->where(function (Builder $q) use ($docSearch): void {
                $q->where('orders.order_number', 'like', "%{$docSearch}%")
                    ->orWhere('users.name', 'like', "%{$docSearch}%")
                    ->orWhere('users.company_name', 'like', "%{$docSearch}%")
                    ->orWhere('client_documents.document_name', 'like', "%{$docSearch}%")
                    ->orWhere('service_requirements.document_name', 'like', "%{$docSearch}%");
            });
        }

        $documents = $documentsQuery->latest('client_documents.updated_at')->take(15)->get();

        // Audit keaslian berkas & integritas tanda tangan digital/stempel untuk setiap dokumen
        $authenticityService = app(DocumentAuthenticityService::class);
        $documents->transform(function ($doc) use ($authenticityService) {
            $doc->authenticity = $authenticityService->analyze($doc);

            return $doc;
        });

        // 2. Pemantauan Tenggat Waktu Pengerjaan Proyek (Active projects with SLA calculations)
        $projectFilterDeadline = $request->get('deadline_status', 'all');
        $projectSearch = trim((string) $request->get('project_q', ''));

        $allOrders = Order::with(['user', 'service', 'items.service', 'documents.requirement', 'trackings', 'latestPayment'])
            ->latest('created_at')
            ->get();

        $monitoredProjects = $this->transformProjectsWithDeadline($allOrders);

        // Filter berdasarkan batas waktu (SLA deadline)
        if ($projectFilterDeadline && $projectFilterDeadline !== 'all') {
            $monitoredProjects = $monitoredProjects->filter(function ($proj) use ($projectFilterDeadline) {
                return $proj->deadline_code === $projectFilterDeadline;
            });
        }

        // Filter pencarian teks projek
        if ($projectSearch !== '') {
            $keyword = strtolower($projectSearch);
            $monitoredProjects = $monitoredProjects->filter(function ($proj) use ($keyword) {
                return str_contains(strtolower($proj->order_number ?? ''), $keyword)
                    || str_contains(strtolower($proj->user?->name ?? ''), $keyword)
                    || str_contains(strtolower($proj->user?->company_name ?? ''), $keyword)
                    || str_contains(strtolower($proj->service_name ?? ''), $keyword);
            });
        }

        $stages = OrderProgressService::stages();

        return view('officer.dashboard', compact(
            'user',
            'stats',
            'documents',
            'monitoredProjects',
            'docFilterStatus',
            'docSearch',
            'projectFilterDeadline',
            'projectSearch',
            'stages'
        ));
    }

    /**
     * Halaman Penuh: Antrean Pemeriksaan Kelengkapan Dokumen Persyaratan Klien.
     */
    public function documents(Request $request): View
    {
        $user = Auth::user();
        $stats = $this->buildOfficerStats();

        $docFilterStatus = $request->get('status', 'all');
        $docSearch = trim((string) $request->get('q', ''));

        $documentsQuery = ClientDocument::with(['order.user', 'order.service', 'requirement', 'versions'])
            ->leftJoin('orders', 'client_documents.order_id', '=', 'orders.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->leftJoin('service_requirements', 'client_documents.service_requirement_id', '=', 'service_requirements.id')
            ->select(
                'client_documents.*',
                'orders.order_number',
                'orders.status as order_status',
                'orders.current_stage as order_stage',
                'users.name as client_name',
                'users.company_name',
                'users.phone as client_phone',
                'service_requirements.document_name as req_document_name',
                'service_requirements.document_type as req_document_type',
                'service_requirements.is_mandatory'
            );

        if ($docFilterStatus && $docFilterStatus !== 'all') {
            $documentsQuery->where('client_documents.status', $docFilterStatus);
        }

        if ($docSearch !== '') {
            $documentsQuery->where(function (Builder $q) use ($docSearch): void {
                $q->where('orders.order_number', 'like', "%{$docSearch}%")
                    ->orWhere('users.name', 'like', "%{$docSearch}%")
                    ->orWhere('users.company_name', 'like', "%{$docSearch}%")
                    ->orWhere('client_documents.document_name', 'like', "%{$docSearch}%")
                    ->orWhere('service_requirements.document_name', 'like', "%{$docSearch}%");
            });
        }

        $documents = $documentsQuery->latest('client_documents.updated_at')->paginate(20)->withQueryString();

        $authenticityService = app(DocumentAuthenticityService::class);
        $documents->getCollection()->transform(function ($doc) use ($authenticityService) {
            $doc->authenticity = $authenticityService->analyze($doc);

            return $doc;
        });

        return view('officer.documents', compact(
            'user',
            'stats',
            'documents',
            'docFilterStatus',
            'docSearch'
        ));
    }

    /**
     * Halaman Detail Peninjauan & Audit Keaslian Berkas Persyaratan.
     */
    public function showDocument(int $id, DocumentAuthenticityService $authenticityService, SecureDocumentStorageService $storageService): View
    {
        $user = Auth::user();
        $stats = $this->buildOfficerStats();

        $document = ClientDocument::with(['order.user', 'order.service', 'requirement', 'versions.uploader', 'uploader'])
            ->findOrFail($id);

        $storageService->authorize($user, $document);

        $document->authenticity = $authenticityService->analyze($document);

        // Analisis setiap versi riwayat berkas
        foreach ($document->versions as $ver) {
            $ver->authenticity = $authenticityService->analyzeVersion($ver);
        }

        return view('officer.document_show', compact('user', 'stats', 'document'));
    }

    /**
     * Endpoint API JSON untuk modul peninjauan keaslian berkas pada modal popup.
     */
    public function auditDocumentJson(int $id, DocumentAuthenticityService $authenticityService, SecureDocumentStorageService $storageService): JsonResponse
    {
        $document = ClientDocument::with(['order.user', 'requirement'])->findOrFail($id);
        $storageService->authorize(Auth::user(), $document);
        $analysis = $authenticityService->analyze($document);

        return response()->json([
            'success' => true,
            'document' => [
                'id' => $document->id,
                'order_number' => $document->order?->order_number,
                'client_name' => $document->order?->user?->name,
                'company_name' => $document->order?->user?->company_name,
                'service_name' => $document->order?->resolvedService()?->name,
                'document_name' => $document->document_name ?: ($document->requirement?->document_name ?: 'Dokumen Persyaratan'),
                'document_type' => $document->document_type ?: ($document->requirement?->document_type ?: 'Berkas Teknis'),
                'is_mandatory' => (bool) ($document->requirement?->is_mandatory ?? true),
                'version' => $document->version ?? 1,
                'status' => $document->status,
                'notes' => $document->notes,
                'download_url' => route('officer.documents.download', $document->id),
                'review_url' => route('officer.documents.show', $document->id),
            ],
            'audit' => $analysis,
        ]);
    }

    /**
     * Halaman Penuh: Pemantauan Tenggat Waktu & Linimasa Pengerjaan Seluruh Proyek (SLA Tracker).
     */
    public function projects(Request $request): View
    {
        $user = Auth::user();
        $stats = $this->buildOfficerStats();

        $projectFilterDeadline = $request->get('status', 'all');
        $projectSearch = trim((string) $request->get('q', ''));

        $allOrders = Order::with(['user', 'service', 'items.service', 'documents.requirement', 'trackings', 'latestPayment'])
            ->latest('created_at')
            ->get();

        $monitoredProjects = $this->transformProjectsWithDeadline($allOrders);

        if ($projectFilterDeadline && $projectFilterDeadline !== 'all') {
            $monitoredProjects = $monitoredProjects->filter(function ($proj) use ($projectFilterDeadline) {
                return $proj->deadline_code === $projectFilterDeadline;
            });
        }

        if ($projectSearch !== '') {
            $keyword = strtolower($projectSearch);
            $monitoredProjects = $monitoredProjects->filter(function ($proj) use ($keyword) {
                return str_contains(strtolower($proj->order_number ?? ''), $keyword)
                    || str_contains(strtolower($proj->user?->name ?? ''), $keyword)
                    || str_contains(strtolower($proj->user?->company_name ?? ''), $keyword)
                    || str_contains(strtolower($proj->service_name ?? ''), $keyword);
            });
        }

        $stages = OrderProgressService::stages();

        return view('officer.projects', compact(
            'user',
            'stats',
            'monitoredProjects',
            'projectFilterDeadline',
            'projectSearch',
            'stages'
        ));
    }

    /**
     * Ekspor Laporan Rekapitulasi Status Pengerjaan Proyek ke format Excel (.xlsx).
     */
    public function exportProjectsExcel(Request $request, ProjectReportExcelService $excelService): Response
    {
        $data = $this->getFilteredProjectsForExport($request);
        $filename = 'Rekapitulasi_Status_Projek_'.date('Ymd_His').'.xlsx';
        $xlsxBinary = $excelService->exportProjects($data['projects'], $data['meta']);

        return response($xlsxBinary, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Ekspor Laporan Rekapitulasi Status Pengerjaan Proyek ke format PDF (.pdf).
     */
    public function exportProjectsPdf(Request $request, ProjectReportPdfService $pdfService): Response
    {
        $data = $this->getFilteredProjectsForExport($request);
        $filename = 'Rekapitulasi_Status_Projek_'.date('Ymd_His').'.pdf';
        $pdfBinary = $pdfService->exportProjects($data['projects'], $data['meta']);

        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Helper: Ambil dan filter data projek untuk ekspor laporan rekapitulasi.
     *
     * @return array{projects: Collection, meta: array}
     */
    protected function getFilteredProjectsForExport(Request $request): array
    {
        $projectFilterDeadline = $request->get('status', $request->get('deadline_status', 'all'));
        $projectSearch = trim((string) $request->get('q', $request->get('project_q', '')));

        $allOrders = Order::with(['user', 'service', 'items.service', 'documents.requirement', 'trackings.updater', 'latestPayment'])
            ->latest('created_at')
            ->get();

        $monitoredProjects = $this->transformProjectsWithDeadline($allOrders);

        if ($projectFilterDeadline && $projectFilterDeadline !== 'all') {
            $monitoredProjects = $monitoredProjects->filter(function ($proj) use ($projectFilterDeadline) {
                return $proj->deadline_code === $projectFilterDeadline;
            });
        }

        if ($projectSearch !== '') {
            $keyword = strtolower($projectSearch);
            $monitoredProjects = $monitoredProjects->filter(function ($proj) use ($keyword) {
                return str_contains(strtolower($proj->order_number ?? ''), $keyword)
                    || str_contains(strtolower($proj->user?->name ?? ''), $keyword)
                    || str_contains(strtolower($proj->user?->company_name ?? ''), $keyword)
                    || str_contains(strtolower($proj->service_name ?? ''), $keyword);
            });
        }

        $filterLabels = [
            'all' => 'Semua Status SLA',
            'critical' => 'Mendekati Batas (<= 3 Hari)',
            'overdue' => 'Lewat Tenggat (Overdue)',
            'on_track' => 'Sesuai Jadwal (On-Track)',
            'completed' => 'Selesai',
        ];

        $filterLabel = $filterLabels[$projectFilterDeadline] ?? 'Semua Status SLA';
        if ($projectSearch !== '') {
            $filterLabel .= " (Kata Kunci: \"{$projectSearch}\")";
        }

        return [
            'projects' => $monitoredProjects,
            'meta' => [
                'officerName' => Auth::user()->name ?? 'Document & Licensing Officer',
                'filterLabel' => $filterLabel,
            ],
        ];
    }

    /**
     * Unduh berkas dokumen persyaratan aktif secara aman dari penyimpanan non-publik.
     */
    public function downloadDocument(int $id, SecureDocumentStorageService $storageService): BinaryFileResponse
    {
        $document = ClientDocument::with('order')->findOrFail($id);

        return $storageService->serveDocumentDownload($document, Auth::user());
    }

    /**
     * Unduh versi riwayat berkas persyaratan secara aman dari penyimpanan non-publik.
     */
    public function downloadVersion(int $id, int $versionId, SecureDocumentStorageService $storageService): BinaryFileResponse
    {
        $document = ClientDocument::with('order')->findOrFail($id);
        $version = ClientDocumentVersion::where('client_document_id', $document->id)->findOrFail($versionId);

        return $storageService->serveVersionDownload($version, Auth::user());
    }

    /**
     * Verifikasi kelengkapan berkas dokumen persyaratan (Disetujui vs Perlu Revisi Dokumen).
     * Sinkronkan riwayat versi dokumen dan perbarui tahapan progres pengerjaan proyek.
     */
    public function verifyDocument(Request $request, int $id, OrderProgressService $progressService): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected,uploaded'],
            'notes' => ['required_if:status,rejected', 'nullable', 'string', 'max:1000'],
        ], [
            'notes.required_if' => 'Alasan atau catatan perbaikan wajib diisi ketika dokumen berstatus Perlu Revisi.',
        ]);

        $document = ClientDocument::with('order')->findOrFail($id);
        $newStatus = $validated['status'];
        $actorId = Auth::id();

        $defaultNotes = match ($newStatus) {
            'approved' => 'Dokumen persyaratan telah diverifikasi lengkap, valid, dan memenuhi standar regulasi perizinan.',
            'rejected' => 'Dokumen persyaratan belum lengkap / membutuhkan perbaikan format sesuai instruksi regulasi.',
            default => 'Dokumen berada dalam antrean peninjauan kelengkapan teknis.',
        };

        $finalNotes = ! empty($validated['notes']) ? trim($validated['notes']) : $defaultNotes;

        DB::transaction(function () use ($document, $newStatus, $finalNotes, $actorId, $progressService): void {
            $document->update([
                'status' => $newStatus,
                'notes' => $finalNotes,
                'verified_by' => $actorId,
                'verified_at' => now(),
            ]);

            // Sinkronkan status pada versi berkas terbaru
            $latestVersion = ClientDocumentVersion::where('client_document_id', $document->id)
                ->orderByDesc('version_number')
                ->first();

            if ($latestVersion) {
                $latestVersion->update([
                    'review_status' => $newStatus,
                    'reviewer_notes' => $finalNotes,
                ]);
            }

            // Integrasi ke OrderProgressService
            $order = $document->order;
            if ($order) {
                if ($newStatus === 'rejected') {
                    $docTitle = $document->document_name ?: 'Persyaratan';
                    $progressService->recordStage(
                        $order,
                        'document_review',
                        'in_progress',
                        "Dokumen \"{$docTitle}\" memerlukan revisi: {$finalNotes}",
                        $actorId
                    );
                } elseif ($newStatus === 'approved') {
                    $service = $order->resolvedService();
                    $mandatoryIds = $service
                        ? ServiceRequirement::where('service_id', $service->id)->where('is_mandatory', true)->pluck('id')
                        : collect();

                    if ($mandatoryIds->isNotEmpty()) {
                        $approvedCount = ClientDocument::where('order_id', $order->id)
                            ->whereIn('service_requirement_id', $mandatoryIds)
                            ->where('status', 'approved')
                            ->count();

                        if ($approvedCount >= $mandatoryIds->count()) {
                            $progressService->recordStage(
                                $order,
                                'document_review',
                                'completed',
                                'Seluruh dokumen persyaratan wajib telah diverifikasi lengkap dan valid oleh Document & Licensing Officer.',
                                $actorId
                            );
                        }
                    }
                }
            }
        });

        return back()->with('success', "Status pemeriksaan dokumen \"{$document->document_name}\" berhasil diperbarui.");
    }

    /**
     * Endpoint API JSON untuk melihat rincian linimasa, riwayat tahapan pengerjaan, dan estimasi SLA proyek.
     */
    public function projectTimelineJson(int $id, OrderProgressService $progressService): JsonResponse
    {
        $order = Order::with(['user', 'service', 'trackings.updater', 'documents.requirement', 'latestPayment'])
            ->findOrFail($id);

        if ($order->isCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini telah dibatalkan dan linimasa tidak dapat diakses.',
            ], 403);
        }

        $timeline = $progressService->buildTimeline($order);
        $transformed = $this->transformProjectsWithDeadline(collect([$order]))->first();

        $trackingHistory = $order->trackings->sortByDesc('id')->map(function ($track) {
            return [
                'id' => $track->id,
                'stage_name' => $track->stage_name,
                'status' => $track->status,
                'status_label' => match ($track->status) {
                    'completed' => 'Selesai',
                    'in_progress' => 'Dalam Proses',
                    default => 'Menunggu',
                },
                'progress_percentage' => $track->progress_percentage,
                'description' => $track->description,
                'date_recorded' => $track->date_recorded ? Carbon::parse($track->date_recorded)->translatedFormat('d M Y, H:i').' WIB' : '-',
                'updated_by_name' => $track->updater?->name ?? 'Document & Licensing Officer',
            ];
        })->values();

        return response()->json([
            'success' => true,
            'project' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'client_name' => $order->user?->name,
                'company_name' => $order->user?->company_name ?? '-',
                'service_name' => $transformed->service_name,
                'current_stage' => $order->current_stage ?? 'Pesanan Diterima',
                'progress_percentage' => (int) ($order->progress_percentage ?? 15),
                'order_status' => $order->status,
                'start_date' => $transformed->start_date->format('d/m/Y'),
                'deadline_date' => $transformed->deadline_date->format('d/m/Y'),
                'timeline_text' => $transformed->timeline_text,
                'estimated_days' => $transformed->estimated_days,
                'remaining_days' => $transformed->remaining_days,
                'deadline_code' => $transformed->deadline_code,
                'deadline_label' => $transformed->deadline_label,
                'deadline_badge' => $transformed->deadline_badge,
                'sla_percent' => $transformed->sla_percent,
                'is_completed' => $transformed->is_completed,
            ],
            'timeline' => $timeline,
            'tracking_history' => $trackingHistory,
        ]);
    }

    /**
     * Perbarui tahapan progres pengerjaan proyek dari dashboard Officer.
     */
    public function updateProjectStage(Request $request, int $id, OrderProgressService $progressService): RedirectResponse
    {
        $validated = $request->validate([
            'stage_name' => ['required', 'string', 'max:200'],
            'stage_status' => ['required', 'in:pending,in_progress,completed'],
            'description' => ['required', 'string', 'max:1500'],
        ]);

        $order = Order::findOrFail($id);
        if ($order->isCancelled()) {
            return back()->with('error', "Pesanan {$order->order_number} telah dibatalkan dan tahapan linimasa tidak dapat diperbarui.");
        }
        $progressService->recordStage(
            $order,
            $validated['stage_name'],
            $validated['stage_status'],
            trim($validated['description']),
            Auth::id()
        );

        return back()->with('success', "Progres pengerjaan projek {$order->order_number} berhasil diperbarui.");
    }

    /**
     * Halaman Profil Staf Document & Licensing Officer.
     */
    public function profile(Request $request): View
    {
        $user = Auth::user();
        $stats = $this->buildOfficerStats();

        return view('officer.profile', compact('user', 'stats'));
    }

    /**
     * Perbarui data profil akun staf Document & Licensing Officer.
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

        // Jaga keamanan hierarki role staf
        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $this->normalizePhoneToLocal($validated['phone']),
            'pic_position' => $validated['pic_position'] ?? $user->pic_position,
            'company_name' => $validated['company_name'] ?? $user->company_name,
            'address' => $validated['address'] ?? $user->address,
        ])->save();

        return redirect()->route('officer.profile.edit')
            ->with('success', 'Data profil Document & Licensing Officer berhasil diperbarui.');
    }

    /**
     * Perbarui kata sandi akun Document & Licensing Officer.
     */
    public function updateProfilePassword(Request $request): RedirectResponse
    {
        return $this->performPasswordUpdate($request, 'officer.profile.edit');
    }

    /**
     * Menghitung metrik operasional khusus antrean dokumen & tenggat waktu proyek.
     *
     * @return array<string, int|float>
     */
    protected function buildOfficerStats(): array
    {
        $totalDocs = DB::table('client_documents')->count();
        $pendingDocs = DB::table('client_documents')->where('status', 'uploaded')->count();
        $approvedDocs = DB::table('client_documents')->where('status', 'approved')->count();
        $rejectedDocs = DB::table('client_documents')->where('status', 'rejected')->count();

        $orders = Order::with(['service', 'latestPayment'])->latest('created_at')->get();
        $transformed = $this->transformProjectsWithDeadline($orders);

        $activeProjects = $transformed->where('is_completed', false)->count();
        $criticalProjects = $transformed->where('is_completed', false)->where('deadline_code', 'critical')->count();
        $overdueProjects = $transformed->where('is_completed', false)->where('deadline_code', 'overdue')->count();
        $onTrackProjects = $transformed->where('is_completed', false)->where('deadline_code', 'on_track')->count();
        $completedProjects = $transformed->where('is_completed', true)->count();

        return [
            'totalDocs' => $totalDocs,
            'pendingDocs' => $pendingDocs,
            'approvedDocs' => $approvedDocs,
            'rejectedDocs' => $rejectedDocs,
            'activeProjects' => $activeProjects,
            'criticalProjects' => $criticalProjects,
            'overdueProjects' => $overdueProjects,
            'onTrackProjects' => $onTrackProjects,
            'completedProjects' => $completedProjects,
        ];
    }

    /**
     * Memperkaya model Order dengan kalkulasi tenggat waktu SLA, estimasi hari,
     * status keterlambatan, dan indikator kelengkapan berkas dokumen.
     *
     * @param  Collection<int, Order>  $orders
     * @return Collection<int, Order>
     */
    protected function transformProjectsWithDeadline(Collection $orders): Collection
    {
        return $orders->map(function (Order $order) {
            $service = $order->resolvedService();
            $order->service_resolved = $service;
            $order->service_name = $service?->name ?? 'Layanan Perizinan';

            $timelineStr = $service?->timeline_estimation ?: ($service?->estimation_time ?: '30 Hari Kerja');
            $order->timeline_text = $timelineStr;

            $estimatedDays = $this->parseEstimatedDays($timelineStr);
            $order->estimated_days = $estimatedDays;

            // Tanggal acuan mulai pengerjaan
            $startDate = $order->latestPayment?->verified_at
                ? Carbon::parse($order->latestPayment->verified_at)
                : Carbon::parse($order->created_at);
            $order->start_date = $startDate;

            $targetDeadline = $startDate->copy()->addDays($estimatedDays);
            $order->deadline_date = $targetDeadline;

            $isCompleted = $order->status === 'completed' || (int) $order->progress_percentage === 100;
            $order->is_completed = $isCompleted;

            $now = Carbon::now();
            $daysElapsed = max(0, $startDate->diffInDays($now));
            $remainingDays = (int) $now->diffInDays($targetDeadline, false);

            $order->days_elapsed = $daysElapsed;
            $order->remaining_days = $remainingDays;

            // Persentase durasi SLA yang terpakai
            $slaPercent = $estimatedDays > 0 ? (int) min(100, max(0, round(($daysElapsed / $estimatedDays) * 100))) : 0;
            $order->sla_percent = $slaPercent;

            // Klasifikasi Status Tenggat Waktu (SLA Deadline Code)
            if ($isCompleted) {
                $order->deadline_code = 'completed';
                $order->deadline_label = 'Selesai';
                $order->deadline_badge = 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20';
                $order->deadline_status_desc = 'Pengerjaan izin / sertifikasi telah selesai seluruhnya.';
            } elseif ($remainingDays < 0) {
                $order->deadline_code = 'overdue';
                $order->deadline_label = 'Lewat Tenggat ('.abs($remainingDays).' Hari)';
                $order->deadline_badge = 'bg-rose-500/15 text-rose-600 border border-rose-500/30 animate-pulse font-bold';
                $order->deadline_status_desc = 'Proyek telah melewati estimasi SLA maksimal!';
            } elseif ($remainingDays <= 3) {
                $order->deadline_code = 'critical';
                $order->deadline_label = 'Mendekati Batas ('.$remainingDays.' Hari)';
                $order->deadline_badge = 'bg-amber-500/15 text-amber-600 border border-amber-500/30 font-bold';
                $order->deadline_status_desc = 'Sisa waktu kritis, segera selesaikan verifikasi dan koordinasi!';
            } else {
                $order->deadline_code = 'on_track';
                $order->deadline_label = 'Sesuai Jadwal ('.$remainingDays.' Hari)';
                $order->deadline_badge = 'bg-sky-500/10 text-sky-600 border border-sky-500/20';
                $order->deadline_status_desc = 'Pengerjaan berjalan normal sesuai estimasi waktu.';
            }

            // Analisis kelengkapan dokumen pesanan
            $mandatoryTotal = $service ? ServiceRequirement::where('service_id', $service->id)->where('is_mandatory', true)->count() : 0;
            $approvedDocsCount = $order->documents->where('status', 'approved')->count();
            $uploadedDocsCount = $order->documents->count();

            $order->mandatory_docs_total = $mandatoryTotal;
            $order->approved_docs_count = $approvedDocsCount;
            $order->uploaded_docs_count = $uploadedDocsCount;

            $isDocsComplete = $mandatoryTotal > 0 ? ($approvedDocsCount >= $mandatoryTotal) : ($uploadedDocsCount > 0);
            $order->is_docs_complete = $isDocsComplete;

            return $order;
        });
    }

    /**
     * Parsing durasi string estimasi waktu ke jumlah hari kerja numerik.
     */
    protected function parseEstimatedDays(?string $estimation): int
    {
        if (empty($estimation)) {
            return 30;
        }

        $str = strtolower(trim($estimation));

        if (preg_match('/(\d+)\s*-\s*(\d+)\s*(?:bulan|month)/i', $str, $matches)) {
            return (int) $matches[2] * 30;
        }

        if (preg_match('/(\d+)\s*(?:bulan|month)/i', $str, $matches)) {
            return (int) $matches[1] * 30;
        }

        if (preg_match('/(\d+)\s*-\s*(\d+)\s*(?:minggu|week)/i', $str, $matches)) {
            return (int) $matches[2] * 7;
        }

        if (preg_match('/(\d+)\s*(?:minggu|week)/i', $str, $matches)) {
            return (int) $matches[1] * 7;
        }

        if (preg_match('/(\d+)\s*-\s*(\d+)\s*(?:hari|hr|day|days)/i', $str, $matches)) {
            return (int) $matches[2];
        }

        if (preg_match('/(\d+)\s*(?:hari|hr|day|days)/i', $str, $matches)) {
            return (int) $matches[1];
        }

        if (preg_match('/(\d+)/', $str, $matches)) {
            return (int) $matches[1];
        }

        return 30;
    }
}
