<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Document &amp; Licensing Officer - IzinYou</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon-192.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .text-corporate { color: #0f172a; }
        .bg-corporate { background-color: #0f172a; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-teal-600 selection:text-white flex h-screen overflow-hidden">

    <!-- Dark Sidebar Officer -->
    @include('officer.partials.sidebar')

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/60">
        
        <!-- Header -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0 relative z-10">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-corporate">Dasbor Document &amp; Licensing Officer</h2>
                    <span class="bg-teal-100 text-teal-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full">Operasional Perizinan</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Pemantauan antrean pemeriksaan kelengkapan berkas izin &amp; kendali tenggat waktu proyek (SLA).</p>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="#antrean-dokumen" class="px-3.5 py-2 bg-teal-600 hover:bg-teal-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all">
                    <i data-lucide="file-check-2" class="w-4 h-4"></i>
                    <span>Antrean Dokumen ({{ $stats['pendingDocs'] }})</span>
                </a>
                <a href="#tenggat-proyek" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all">
                    <i data-lucide="timer" class="w-4 h-4"></i>
                    <span>Tenggat Proyek ({{ $stats['activeProjects'] }})</span>
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        @include('officer.partials.alerts')

        <!-- Scrollable Body Content -->
        <div class="flex-1 overflow-y-auto p-8 space-y-8 sidebar-scroll">

            <!-- 1. STATISTIK KPI OPERASIONAL -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                
                <!-- Card 1: Antrean Dokumen Masuk -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden group hover:border-amber-300 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Antrean Pemeriksaan</p>
                            <h3 class="text-3xl font-extrabold text-corporate mt-1">{{ number_format($stats['pendingDocs']) }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="clock" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between text-xs pt-3 border-t border-slate-100">
                        <span class="text-amber-600 font-semibold flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping inline-block"></span>
                            Menunggu Verifikasi
                        </span>
                        <a href="{{ route('officer.documents', ['status' => 'uploaded']) }}" class="text-slate-500 hover:text-teal-600 font-bold">Buka Antrean &rarr;</a>
                    </div>
                </div>

                <!-- Card 2: Dokumen Lengkap / Selesai -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden group hover:border-teal-300 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Dokumen Disetujui</p>
                            <h3 class="text-3xl font-extrabold text-teal-600 mt-1">{{ number_format($stats['approvedDocs']) }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between text-xs pt-3 border-t border-slate-100">
                        <span class="text-slate-500 font-medium">Lolos Standar Kepatuhan</span>
                        <span class="font-bold text-teal-600">{{ $stats['totalDocs'] > 0 ? round(($stats['approvedDocs'] / $stats['totalDocs']) * 100) : 0 }}% Lulus</span>
                    </div>
                </div>

                <!-- Card 3: Dokumen Perlu Revisi -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden group hover:border-rose-300 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Perlu Perbaikan</p>
                            <h3 class="text-3xl font-extrabold text-rose-600 mt-1">{{ number_format($stats['rejectedDocs']) }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between text-xs pt-3 border-t border-slate-100">
                        <span class="text-rose-600 font-semibold">Menunggu Upload Klien</span>
                        <a href="{{ route('officer.documents', ['status' => 'rejected']) }}" class="text-slate-500 hover:text-rose-600 font-bold">Lihat Detail &rarr;</a>
                    </div>
                </div>

                <!-- Card 4: Projek Mendekati / Lewat Tenggat SLA -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden group hover:border-purple-300 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Peringatan Tenggat SLA</p>
                            <h3 class="text-3xl font-extrabold text-corporate mt-1">
                                {{ number_format($stats['criticalProjects'] + $stats['overdueProjects']) }}
                            </h3>
                        </div>
                        <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="alarm-clock" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between text-xs pt-3 border-t border-slate-100">
                        <span class="text-purple-700 font-medium">
                            {{ $stats['overdueProjects'] }} Lewat &bull; {{ $stats['criticalProjects'] }} &le; 3 Hari
                        </span>
                        <a href="{{ route('officer.projects', ['status' => 'critical']) }}" class="text-purple-600 font-bold hover:underline">Pantau SLA &rarr;</a>
                    </div>
                </div>

            </div>

            <!-- 2. SECTION A: ANTREAN PEMERIKSAAN KELENGKAPAN DOKUMEN -->
            <div id="antrean-dokumen" class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden scroll-mt-24">
                <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center">
                                <i data-lucide="file-check-2" class="w-4 h-4"></i>
                            </div>
                            <h3 class="text-lg font-bold text-corporate">Antrean Pemeriksaan Kelengkapan Dokumen</h3>
                            <span class="bg-amber-100 text-amber-800 text-[11px] font-bold px-2 py-0.5 rounded-full">
                                {{ $stats['pendingDocs'] }} Menunggu
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Periksa kelengkapan berkas yang diunggah oleh klien sebelum diteruskan ke tahapan perizinan instansi.
                        </p>
                    </div>

                    <!-- Filter & Search Form Dokumen -->
                    <form method="GET" action="{{ route('officer.dashboard') }}#antrean-dokumen" class="flex flex-wrap items-center gap-2">
                        <div class="relative">
                            <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input type="text" name="doc_q" value="{{ $docSearch }}" placeholder="Cari order, klien, berkas..." class="pl-8 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>

                        <select name="doc_status" onchange="this.form.submit()" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 focus:bg-white focus:outline-none focus:border-teal-500 font-medium">
                            <option value="all" {{ $docFilterStatus === 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="uploaded" {{ $docFilterStatus === 'uploaded' ? 'selected' : '' }}>Menunggu Review</option>
                            <option value="approved" {{ $docFilterStatus === 'approved' ? 'selected' : '' }}>Disetujui / Lengkap</option>
                            <option value="rejected" {{ $docFilterStatus === 'rejected' ? 'selected' : '' }}>Perlu Revisi</option>
                        </select>

                        <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition">
                            Filter
                        </button>

                        <a href="{{ route('officer.documents') }}" class="text-xs font-bold text-teal-600 hover:text-teal-700 ml-2">
                            Lihat Semua &rarr;
                        </a>
                    </form>
                </div>

                <!-- Table Antrean Dokumen -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/75 text-slate-400 text-xs font-semibold uppercase tracking-wider">
                                <th class="py-3.5 px-6">No. Pesanan &amp; Klien</th>
                                <th class="py-3.5 px-6">Nama &amp; Jenis Dokumen</th>
                                <th class="py-3.5 px-6">Audit Integritas TTD &amp; Metadata</th>
                                <th class="py-3.5 px-6">Versi &amp; Waktu</th>
                                <th class="py-3.5 px-6">Status Kelengkapan</th>
                                <th class="py-3.5 px-6 text-right">Aksi Pemeriksaan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($documents as $doc)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-xs font-bold bg-slate-900 text-white px-2 py-0.5 rounded-lg">
                                                {{ $doc->order_number ?? '-' }}
                                            </span>
                                            <span class="text-xs font-semibold text-slate-600 truncate max-w-[150px]">
                                                {{ $doc->order?->resolvedService()?->name ?? 'Layanan Perizinan' }}
                                            </span>
                                        </div>
                                        <div class="mt-1">
                                            <p class="font-bold text-corporate text-xs">{{ $doc->client_name ?? 'Klien' }}</p>
                                            <p class="text-[11px] text-slate-500">{{ $doc->company_name ?? '-' }}</p>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-corporate text-xs">{{ $doc->document_name ?? $doc->req_document_name ?? 'Persyaratan Dokumen' }}</span>
                                            @if($doc->is_mandatory)
                                                <span class="bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold px-1.5 py-0.5 rounded">Wajib</span>
                                            @else
                                                <span class="bg-slate-100 text-slate-600 text-[10px] font-medium px-1.5 py-0.5 rounded">Opsional</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-slate-500 mt-0.5">
                                            Tipe: {{ $doc->req_document_type ?? $doc->document_type ?? 'Berkas Teknis' }}
                                        </p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-1.5">
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-md {{ $doc->authenticity['badge_class'] ?? 'bg-slate-100 text-slate-700' }}">
                                                @if(($doc->authenticity['verdict_code'] ?? '') === 'GENUINE')
                                                    <i data-lucide="shield-check" class="w-3 h-3 text-teal-600"></i>
                                                @elseif(($doc->authenticity['verdict_code'] ?? '') === 'TAMPERED')
                                                    <i data-lucide="shield-alert" class="w-3 h-3 text-rose-600"></i>
                                                @else
                                                    <i data-lucide="alert-triangle" class="w-3 h-3 text-amber-600"></i>
                                                @endif
                                                <span>{{ $doc->authenticity['overall_score'] ?? 0 }}% {{ $doc->authenticity['verdict_code'] === 'GENUINE' ? 'Asli' : ($doc->authenticity['verdict_code'] === 'TAMPERED' ? 'Manipulasi' : 'Perlu Cek') }}</span>
                                            </span>
                                        </div>
                                        <p class="text-[10px] text-slate-500 mt-1 truncate max-w-[170px]" title="{{ $doc->authenticity['signature_audit']['signature_type'] ?? '' }}">
                                            {{ $doc->authenticity['signature_audit']['has_digital_signature'] ? 'TTD Digital / QR' : ($doc->authenticity['signature_audit']['stamp_detected'] ? 'Stempel Cap' : 'TTD Manual') }} &bull; {{ $doc->authenticity['metadata_audit']['status'] === 'tampered' ? 'Metadata Diedit' : 'Metadata Bersih' }}
                                        </p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-block bg-teal-50 text-teal-700 font-mono text-[11px] font-bold px-2 py-0.5 rounded-md border border-teal-200">
                                            v{{ $doc->version ?? 1 }}
                                        </span>
                                        <p class="text-[11px] text-slate-500 mt-1">
                                            {{ \Carbon\Carbon::parse($doc->created_at)->translatedFormat('d M Y, H:i') }}
                                        </p>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($doc->status === 'approved')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                                Lengkap &amp; Valid
                                            </span>
                                        @elseif($doc->status === 'rejected')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                                                Perlu Revisi
                                            </span>
                                            @if($doc->notes)
                                                <p class="text-[11px] text-rose-600 mt-1 italic line-clamp-1 max-w-[200px]" title="{{ $doc->notes }}">
                                                    "{{ $doc->notes }}"
                                                </p>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                                Menunggu Review
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('officer.documents.show', $doc->id) }}" class="p-2 bg-teal-50 hover:bg-teal-100 text-teal-700 rounded-xl transition" title="Tinjau &amp; Audit Keaslian Lengkap">
                                                <i data-lucide="shield-check" class="w-4 h-4"></i>
                                            </a>
                                            <a href="{{ route('officer.documents.download', $doc->id) }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition" title="Unduh / Buka Dokumen">
                                                <i data-lucide="download" class="w-4 h-4"></i>
                                            </a>
                                            <button type="button" onclick="openVerifyModal('{{ $doc->id }}', '{{ addslashes($doc->document_name ?? $doc->req_document_name ?? 'Dokumen') }}', '{{ $doc->status }}', '{{ addslashes($doc->notes ?? '') }}')" class="px-3 py-1.5 bg-teal-600 hover:bg-teal-500 text-white rounded-xl text-xs font-bold flex items-center gap-1 shadow-sm transition">
                                                <i data-lucide="clipboard-check" class="w-3.5 h-3.5"></i>
                                                <span>Kaji Berkas</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">
                                        <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                                        <p class="text-sm font-semibold">Tidak ada antrean dokumen pada filter ini.</p>
                                        <p class="text-xs text-slate-400 mt-0.5">Semua dokumen persyaratan yang diunggah klien telah diproses.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. SECTION B: PEMANTAUAN TENGGAT WAKTU PENGERJAAN PROYEK (SLA) -->
            <div id="tenggat-proyek" class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden scroll-mt-24">
                <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-slate-900 text-teal-400 flex items-center justify-center">
                                <i data-lucide="timer" class="w-4 h-4"></i>
                            </div>
                            <h3 class="text-lg font-bold text-corporate">Pemantauan Tenggat Waktu Pengerjaan Proyek (SLA)</h3>
                            <span class="bg-teal-100 text-teal-800 text-[11px] font-bold px-2 py-0.5 rounded-full">
                                {{ $stats['activeProjects'] }} Projek Berjalan
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Lacak estimasi waktu kerja (SLA), batas tanggal penyelesaian, dan kepatuhan timeline setiap proyek izin.
                        </p>
                    </div>

                    <!-- Filter & Search Form Projek -->
                    <form method="GET" action="{{ route('officer.dashboard') }}#tenggat-proyek" class="flex flex-wrap items-center gap-2">
                        <div class="relative">
                            <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input type="text" name="project_q" value="{{ $projectSearch }}" placeholder="Cari nomor, mitra, layanan..." class="pl-8 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>

                        <select name="deadline_status" onchange="this.form.submit()" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 focus:bg-white focus:outline-none focus:border-teal-500 font-medium">
                            <option value="all" {{ $projectFilterDeadline === 'all' ? 'selected' : '' }}>Semua Status SLA</option>
                            <option value="critical" {{ $projectFilterDeadline === 'critical' ? 'selected' : '' }}>Mendekati Batas (&le; 3 Hari)</option>
                            <option value="overdue" {{ $projectFilterDeadline === 'overdue' ? 'selected' : '' }}>Lewat Tenggat (Overdue)</option>
                            <option value="on_track" {{ $projectFilterDeadline === 'on_track' ? 'selected' : '' }}>Sesuai Jadwal (On-Track)</option>
                            <option value="completed" {{ $projectFilterDeadline === 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>

                        <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition">
                            Filter
                        </button>

                        <a href="{{ route('officer.projects') }}" class="text-xs font-bold text-teal-600 hover:text-teal-700 ml-2">
                            Lihat Semua Projek &rarr;
                        </a>

                        <div class="flex items-center gap-1.5 ml-2 border-l border-slate-200 pl-3">
                            <a href="{{ route('officer.projects.export-excel', request()->only(['deadline_status', 'project_q'])) }}" title="Ekspor Rekapitulasi Excel (.xlsx)" class="px-2.5 py-1.5 text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-xl text-xs font-bold flex items-center gap-1 transition">
                                <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5"></i>
                                <span class="hidden sm:inline">Excel</span>
                            </a>
                            <a href="{{ route('officer.projects.export-pdf', request()->only(['deadline_status', 'project_q'])) }}" target="_blank" title="Ekspor Rekapitulasi PDF (.pdf)" class="px-2.5 py-1.5 text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl text-xs font-bold flex items-center gap-1 transition">
                                <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                <span class="hidden sm:inline">PDF</span>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Table Pemantauan SLA Proyek -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/75 text-slate-400 text-xs font-semibold uppercase tracking-wider">
                                <th class="py-3.5 px-6">Nomor Projek &amp; Layanan</th>
                                <th class="py-3.5 px-6">Mitra Perusahaan</th>
                                <th class="py-3.5 px-6">Mulai &amp; Estimasi SLA</th>
                                <th class="py-3.5 px-6">Batas Waktu &amp; Sisa Hari</th>
                                <th class="py-3.5 px-6">Tahapan &amp; Kelengkapan</th>
                                <th class="py-3.5 px-6 text-right">Aksi Linimasa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($monitoredProjects->take(15) as $proj)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-4 px-6">
                                        <span class="font-mono text-xs font-bold bg-slate-900 text-white px-2 py-0.5 rounded-lg">
                                            {{ $proj->order_number }}
                                        </span>
                                        <p class="font-bold text-corporate text-xs mt-1">{{ $proj->service_name }}</p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="font-bold text-corporate text-xs">{{ $proj->user?->name ?? 'Mitra' }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $proj->user?->company_name ?? '-' }}</p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="text-xs">
                                            <span class="text-slate-400 text-[11px]">Mulai:</span>
                                            <strong class="text-corporate">{{ $proj->start_date->format('d/m/Y') }}</strong>
                                        </div>
                                        <div class="text-[11px] text-teal-700 font-semibold mt-0.5">
                                            SLA: {{ $proj->timeline_text }} ({{ $proj->estimated_days }} Hari)
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div>
                                            <span class="text-xs font-bold text-corporate">
                                                {{ $proj->deadline_date->format('d/m/Y') }}
                                            </span>
                                        </div>
                                        <div class="mt-1">
                                            <span class="inline-block text-[11px] px-2 py-0.5 rounded-md font-bold {{ $proj->deadline_badge }}">
                                                {{ $proj->deadline_label }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center justify-between text-xs mb-1">
                                            <span class="font-bold text-corporate">{{ $proj->current_stage ?? 'Pesanan Diterima' }}</span>
                                            <span class="font-mono text-slate-500 font-bold">{{ $proj->progress_percentage ?? 15 }}%</span>
                                        </div>
                                        <div class="w-36 h-2 bg-slate-100 rounded-full overflow-hidden mb-1.5">
                                            <div class="h-full bg-teal-600 rounded-full" style="width: {{ $proj->progress_percentage ?? 15 }}%"></div>
                                        </div>
                                        <div class="text-[11px]">
                                            @if($proj->is_docs_complete)
                                                <span class="text-teal-600 font-semibold flex items-center gap-1">
                                                    <i data-lucide="check" class="w-3 h-3"></i> Berkas Lengkap ({{ $proj->approved_docs_count }}/{{ max(1, $proj->mandatory_docs_total) }})
                                                </span>
                                            @else
                                                <span class="text-amber-600 font-semibold flex items-center gap-1">
                                                    <i data-lucide="clock" class="w-3 h-3"></i> Berkas ({{ $proj->approved_docs_count }}/{{ max(1, $proj->mandatory_docs_total) }})
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        @if(($proj->status ?? '') === 'cancelled' || ($proj->current_stage ?? '') === 'Pesanan Dibatalkan' || ($proj->current_stage ?? '') === 'Proyek Dibatalkan')
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-500 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-100">
                                                <i data-lucide="slash" class="w-3 h-3"></i> Proyek Dibatalkan
                                            </span>
                                        @else
                                            <div class="flex items-center justify-end gap-1.5">
                                                <button type="button" onclick="openProjectTimelineModal('{{ $proj->id }}')" class="px-2.5 py-1.5 bg-teal-50 hover:bg-teal-100 text-teal-700 rounded-xl text-xs font-bold inline-flex items-center gap-1 transition" title="Lihat Riwayat &amp; Linimasa Lengkap">
                                                    <i data-lucide="history" class="w-3.5 h-3.5"></i>
                                                    <span class="hidden sm:inline">Linimasa</span>
                                                </button>
                                                <button type="button" onclick="openStageModal('{{ $proj->id }}', '{{ $proj->order_number }}', '{{ addslashes($proj->service_name) }}', '{{ $proj->current_stage }}')" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold inline-flex items-center gap-1 shadow-sm transition">
                                                    <i data-lucide="git-commit" class="w-3.5 h-3.5 text-teal-400"></i>
                                                    <span>Update Tahap</span>
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">
                                        <i data-lucide="check-circle" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                                        <p class="text-sm font-semibold">Tidak ada projek pada filter ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <!-- MODAL 1: VERIFIKASI KELENGKAPAN DOKUMEN & AUDIT KEASLIAN -->
    <div id="verifyModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4 shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center">
                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-corporate text-sm">Pemeriksaan Administratif &amp; Audit Berkas</h4>
                        <p id="modalDocName" class="text-xs text-slate-400 truncate max-w-[280px]">-</p>
                    </div>
                </div>
                <button type="button" onclick="closeVerifyModal()" class="text-slate-400 hover:text-slate-600 p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="verifyDocForm" method="POST" action="" class="space-y-4 overflow-y-auto sidebar-scroll pr-1 flex-1">
                @csrf
                
                <!-- Panel Hasil Audit Otomatis (Asynchronous Live Preview) -->
                <div id="modalAuditBox" class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-corporate flex items-center gap-1.5">
                            <i data-lucide="scan" class="w-4 h-4 text-teal-600"></i>
                            <span>Audit Integritas TTD &amp; Metadata</span>
                        </span>
                        <span id="modalAuditScoreBadge" class="px-2 py-0.5 rounded-md font-bold text-[11px] bg-teal-50 text-teal-700 border border-teal-200">
                            Memuat...
                        </span>
                    </div>
                    <div class="space-y-1 text-[11px] text-slate-600">
                        <p id="modalAuditSigStatus" class="flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-teal-500 inline-block"></span>
                            <span class="font-semibold text-slate-700">TTD &amp; Cap:</span> Memindai stempel &amp; tanda tangan...
                        </p>
                        <p id="modalAuditMetaStatus" class="flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-teal-500 inline-block"></span>
                            <span class="font-semibold text-slate-700">Metadata:</span> Memeriksa software pembuat &amp; SHA-256...
                        </p>
                    </div>
                    <div class="pt-1 flex items-center justify-between border-t border-slate-200/60">
                        <span id="modalShaShort" class="font-mono text-[10px] text-slate-400 truncate max-w-[200px]">SHA-256: -</span>
                        <a id="modalFullReviewLink" href="#" target="_blank" class="text-[11px] text-teal-600 font-bold hover:underline flex items-center gap-0.5">
                            <span>Buka Audit Lengkap</span>
                            <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>

                <!-- Pilihan Keputusan Status -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Keputusan Hasil Pemeriksaan:</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-teal-50/50 has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50/30">
                            <input type="radio" name="status" value="approved" id="statusApproved" class="text-teal-600 focus:ring-teal-500">
                            <span class="text-xs font-bold text-teal-800">Lengkap &amp; Disetujui</span>
                        </label>

                        <label class="flex items-center gap-2 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-rose-50/50 has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50/30">
                            <input type="radio" name="status" value="rejected" id="statusRejected" class="text-rose-600 focus:ring-rose-500">
                            <span class="text-xs font-bold text-rose-800">Perlu Revisi</span>
                        </label>
                    </div>
                </div>

                <!-- Template Catatan Perbaikan Cepat -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Template Catatan Cepat:</label>
                    <div class="flex flex-wrap gap-1.5 text-[10px]">
                        <button type="button" onclick="insertDashboardNote('Stempel cap instansi buram/tidak terbaca. Mohon unggah hasil scan asli berwarna.')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                            Stempel Buram
                        </button>
                        <button type="button" onclick="insertDashboardNote('QR Code verifikasi tidak valid / tidak mengarah ke portal resmi instansi pemerintah.')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                            QR Anomali
                        </button>
                        <button type="button" onclick="insertDashboardNote('Terdeteksi modifikasi digital pada metadata file berkas. Mohon kirimkan berkas PDF asli.')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                            Metadata Modifikasi
                        </button>
                        <button type="button" onclick="insertDashboardNote('Lampiran persyaratan belum lengkap sesuai ketentuan teknis.')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                            Lampiran Kurang
                        </button>
                    </div>
                </div>

                <!-- Textarea Catatan -->
                <div>
                    <label for="modalNotes" class="block text-xs font-bold text-slate-700 mb-1">
                        Catatan Pemeriksaan / Instruksi Revisi:
                    </label>
                    <textarea id="modalNotes" name="notes" rows="3" placeholder="Tuliskan catatan perbaikan atau alasan penolakan berkas..." class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-teal-500"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">* Wajib diisi jika status dipilih "Perlu Revisi".</p>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 shrink-0">
                    <button type="button" onclick="closeVerifyModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold rounded-xl shadow-sm transition">
                        Simpan Hasil Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: UPDATE TAHAPAN PROJEK -->
    <div id="stageModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-slate-900 text-teal-400 flex items-center justify-center">
                        <i data-lucide="git-commit" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-corporate text-sm">Pembaruan Tahapan Proyek</h4>
                        <p id="modalProjectNumber" class="text-xs text-slate-400">-</p>
                    </div>
                </div>
                <button type="button" onclick="closeStageModal()" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="stageForm" method="POST" action="">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="stageNameSelect" class="block text-xs font-bold text-slate-700 mb-1">Tahapan Proyek Standar</label>
                        <select id="stageNameSelect" name="stage_name" class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-teal-500 font-medium">
                            @foreach($stages as $stg)
                                <option value="{{ $stg['name'] }}">{{ $stg['order'] }}. {{ $stg['name'] }} ({{ $stg['percentage'] }}%)</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="stageStatusSelect" class="block text-xs font-bold text-slate-700 mb-1">Status Pengerjaan Tahap</label>
                        <select id="stageStatusSelect" name="stage_status" class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-teal-500 font-medium">
                            <option value="in_progress">Dalam Proses (In Progress)</option>
                            <option value="completed">Selesai Tahap Ini (Completed)</option>
                            <option value="pending">Menunggu (Pending)</option>
                        </select>
                    </div>

                    <!-- Template Catatan Perkembangan Cepat -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Template Catatan Cepat:</label>
                        <div class="flex flex-wrap gap-1.5 text-[10px]">
                            <button type="button" onclick="insertStageDescNote('Dokumen persyaratan telah diverifikasi lengkap dan diajukan ke portal instansi resmi.')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                                Dokumen Diajukan
                            </button>
                            <button type="button" onclick="insertStageDescNote('Sedang dalam tahap verifikasi teknis dan penilaian kelayakan oleh instansi terkait.')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                                Verifikasi Teknis
                            </button>
                            <button type="button" onclick="insertStageDescNote('Jadwal audit / inspeksi kesesuaian sarana telah dikoordinasikan dengan pemohon.')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                                Jadwal Audit
                            </button>
                            <button type="button" onclick="insertStageDescNote('Draft izin telah disetujui, menunggu penerbitan sertifikat resmi bertanda tangan digital.')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                                Draft Valid
                            </button>
                            <button type="button" onclick="insertStageDescNote('Izin / sertifikasi resmi telah terbit dan siap diserahkan kepada klien.')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                                Izin Terbit
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="stageDescription" class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Catatan Linimasa</label>
                        <textarea id="stageDescription" name="description" rows="3" required placeholder="Jelaskan progres terbaru atau kendala pengerjaan izin..." class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-teal-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" onclick="closeStageModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-sm transition">
                            Perbarui Tahap
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: RINCIAN LINIMASA & RIWAYAT TRACKING PROYEK -->
    <div id="projectTimelineModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-100 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4 shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-slate-900 text-teal-400 flex items-center justify-center">
                        <i data-lucide="timer" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-corporate text-sm">Linimasa &amp; Pelacakan Progres Proyek</h4>
                        <p id="timelineModalHeaderSubtitle" class="text-xs text-slate-400 truncate max-w-md">-</p>
                    </div>
                </div>
                <button type="button" onclick="closeProjectTimelineModal()" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto sidebar-scroll pr-1 space-y-4">
                <!-- Ringkasan Target SLA & Progres Bar -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Target Penyelesaian SLA</span>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span id="timelineDeadlineDate" class="text-xs font-bold text-corporate">-</span>
                                <span id="timelineDeadlineBadge" class="text-[11px] font-bold px-2 py-0.5 rounded-md bg-slate-200 text-slate-700">-</span>
                            </div>
                        </div>
                        <div class="sm:text-right">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Progres</span>
                            <span id="timelineOverallPercent" class="text-sm font-extrabold font-mono text-teal-700">0%</span>
                        </div>
                    </div>
                    <div class="w-full h-2.5 bg-slate-200 rounded-full overflow-hidden">
                        <div id="timelineProgressBar" class="h-full bg-teal-600 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>

                <!-- 6-Stage Stepper Pelacakan -->
                <div>
                    <h5 class="text-xs font-bold text-corporate mb-2 flex items-center gap-1.5">
                        <i data-lucide="git-merge" class="w-4 h-4 text-teal-600"></i>
                        <span>Linimasa 6 Tahapan Proyek (Tampilan Klien)</span>
                    </h5>
                    <div id="timelineStepperContainer" class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        <!-- Rendered by JS -->
                    </div>
                </div>

                <!-- Riwayat Catatan Linimasa (Tracking History) -->
                <div>
                    <h5 class="text-xs font-bold text-corporate mb-2 flex items-center gap-1.5">
                        <i data-lucide="activity" class="w-4 h-4 text-teal-600"></i>
                        <span>Riwayat Catatan Pembaruan Tahapan</span>
                    </h5>
                    <div id="timelineHistoryList" class="space-y-2">
                        <!-- Rendered by JS -->
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100 mt-4 shrink-0">
                <button type="button" onclick="closeProjectTimelineModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                    Tutup
                </button>
                <button type="button" id="timelineOpenStageBtn" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center gap-1.5">
                    <i data-lucide="git-commit" class="w-3.5 h-3.5 text-teal-400"></i>
                    <span>Perbarui Tahap Ini</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function insertDashboardNote(text) {
            const textarea = document.getElementById('modalNotes');
            if (textarea.value) {
                textarea.value = textarea.value + '\n- ' + text;
            } else {
                textarea.value = '- ' + text;
            }
        }

        function insertStageDescNote(text) {
            const textarea = document.getElementById('stageDescription');
            if (textarea.value) {
                textarea.value = textarea.value + '\n' + text;
            } else {
                textarea.value = text;
            }
        }

        function openVerifyModal(docId, docName, status, notes) {
            const form = document.getElementById('verifyDocForm');
            form.action = '{{ url("/officer/dokumen") }}/' + docId + '/verifikasi';
            document.getElementById('modalDocName').textContent = docName;
            document.getElementById('modalNotes').value = notes || '';
            document.getElementById('modalFullReviewLink').href = '{{ url("/officer/dokumen") }}/' + docId;

            if (status === 'approved') {
                document.getElementById('statusApproved').checked = true;
            } else if (status === 'rejected') {
                document.getElementById('statusRejected').checked = true;
            } else {
                document.getElementById('statusApproved').checked = true;
            }

            // Fetch live audit data
            fetch('{{ url("/officer/dokumen") }}/' + docId + '/audit')
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.audit) {
                        const a = data.audit;
                        const badge = document.getElementById('modalAuditScoreBadge');
                        badge.className = 'px-2 py-0.5 rounded-md font-bold text-[11px] ' + (a.badge_class || '');
                        badge.textContent = a.overall_score + '% ' + a.verdict_label;

                        const sig = a.signature_audit || {};
                        document.getElementById('modalAuditSigStatus').innerHTML =
                            '<span class="w-1.5 h-1.5 rounded-full bg-teal-500 inline-block"></span>' +
                            '<span class="font-semibold text-slate-700">TTD & Cap:</span> ' + (sig.signature_type || 'Manual') +
                            (sig.stamp_detected ? ' (Stempel Terdeteksi)' : '');

                        const meta = a.metadata_audit || {};
                        document.getElementById('modalAuditMetaStatus').innerHTML =
                            '<span class="w-1.5 h-1.5 rounded-full bg-teal-500 inline-block"></span>' +
                            '<span class="font-semibold text-slate-700">Metadata:</span> ' +
                            (meta.tampering_detected ? '<strong class="text-rose-600">Terdeteksi ' + meta.detected_editor + '</strong>' : 'Bersih (' + (meta.producer || 'PDF') + ')');

                        if (data.audit.file_info && data.audit.file_info.sha256) {
                            document.getElementById('modalShaShort').textContent = 'SHA-256: ' + data.audit.file_info.sha256.substring(0, 16) + '...';
                        }
                    }
                })
                .catch(() => {});

            document.getElementById('verifyModal').classList.remove('hidden');
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        function closeVerifyModal() {
            document.getElementById('verifyModal').classList.add('hidden');
        }

        function openStageModal(projId, orderNumber, serviceName, currentStage) {
            const form = document.getElementById('stageForm');
            form.action = '{{ url("/officer/projek") }}/' + projId + '/tahap';
            document.getElementById('modalProjectNumber').textContent = orderNumber + ' \u2014 ' + serviceName;
            
            const select = document.getElementById('stageNameSelect');
            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].value.includes(currentStage)) {
                    select.selectedIndex = i;
                    break;
                }
            }

            document.getElementById('stageDescription').value = 'Pembaruan progres pengerjaan pada ' + new Date().toLocaleDateString('id-ID');
            document.getElementById('stageModal').classList.remove('hidden');
        }

        function closeStageModal() {
            document.getElementById('stageModal').classList.add('hidden');
        }

        function openProjectTimelineModal(projId) {
            const modal = document.getElementById('projectTimelineModal');
            modal.classList.remove('hidden');

            fetch('{{ url("/officer/projek") }}/' + projId + '/linimasa')
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.project) {
                        const p = data.project;
                        document.getElementById('timelineModalHeaderSubtitle').textContent = p.order_number + ' \u2014 ' + p.client_name + ' (' + p.service_name + ')';
                        document.getElementById('timelineDeadlineDate').textContent = p.deadline_date + ' (SLA: ' + p.timeline_text + ')';
                        
                        const badge = document.getElementById('timelineDeadlineBadge');
                        badge.className = 'text-[11px] font-bold px-2 py-0.5 rounded-md ' + (p.deadline_badge || '');
                        badge.textContent = p.deadline_label;

                        document.getElementById('timelineOverallPercent').textContent = p.progress_percentage + '%';
                        document.getElementById('timelineProgressBar').style.width = p.progress_percentage + '%';

                        // Render 6-stage stepper
                        const stepContainer = document.getElementById('timelineStepperContainer');
                        stepContainer.innerHTML = '';
                        (data.timeline || []).forEach(stg => {
                            const isCompleted = stg.status === 'completed';
                            const isInProgress = stg.status === 'in_progress';

                            const card = document.createElement('div');
                            card.className = 'p-2.5 rounded-xl border text-xs ' +
                                (isCompleted ? 'bg-emerald-50/70 border-emerald-200' : (isInProgress ? 'bg-teal-50 border-teal-300 ring-1 ring-teal-200' : 'bg-slate-50 border-slate-200'));

                            card.innerHTML =
                                '<div class="flex items-center justify-between mb-1">' +
                                    '<span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold ' +
                                        (isCompleted ? 'bg-emerald-600 text-white' : (isInProgress ? 'bg-teal-600 text-white' : 'bg-slate-200 text-slate-500')) + '">' +
                                        stg.order +
                                    '</span>' +
                                    '<span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full ' +
                                        (isCompleted ? 'bg-emerald-100 text-emerald-800' : (isInProgress ? 'bg-teal-100 text-teal-800' : 'bg-slate-200 text-slate-600')) + '">' +
                                        (isCompleted ? 'Selesai' : (isInProgress ? 'Berjalan' : 'Menunggu')) +
                                    '</span>' +
                                '</div>' +
                                '<p class="font-bold text-corporate text-[11px] truncate" title="' + stg.name + '">' + stg.name + '</p>' +
                                '<p class="text-[10px] text-slate-500 truncate mt-0.5">' + (stg.updated_at || 'Belum dimulai') + '</p>';

                            stepContainer.appendChild(card);
                        });

                        // Render tracking history
                        const historyList = document.getElementById('timelineHistoryList');
                        historyList.innerHTML = '';
                        if (!data.tracking_history || data.tracking_history.length === 0) {
                            historyList.innerHTML = '<p class="text-xs text-slate-400 italic py-2">Belum ada riwayat catatan pengerjaan.</p>';
                        } else {
                            data.tracking_history.forEach(th => {
                                const item = document.createElement('div');
                                item.className = 'p-3 rounded-xl bg-slate-50 border border-slate-200/70 text-xs space-y-1';
                                item.innerHTML =
                                    '<div class="flex items-center justify-between">' +
                                        '<span class="font-bold text-corporate flex items-center gap-1.5">' +
                                            '<span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>' +
                                            th.stage_name +
                                            '<span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-white border border-slate-200">' + (th.progress_percentage || 0) + '%</span>' +
                                        '</span>' +
                                        '<span class="text-[11px] text-slate-400">' + th.date_recorded + '</span>' +
                                    '</div>' +
                                    '<p class="text-slate-600 text-[11px] leading-relaxed">' + th.description + '</p>' +
                                    '<p class="text-[10px] text-slate-400">Oleh: <strong>' + th.updated_by_name + '</strong></p>';
                                historyList.appendChild(item);
                            });
                        }

                        // Attach stage button
                        document.getElementById('timelineOpenStageBtn').onclick = function() {
                            closeProjectTimelineModal();
                            openStageModal(p.id, p.order_number, p.service_name, p.current_stage);
                        };

                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }
                    }
                })
                .catch(() => {});
        }

        function closeProjectTimelineModal() {
            document.getElementById('projectTimelineModal').classList.add('hidden');
        }
    </script>
</body>
</html>

