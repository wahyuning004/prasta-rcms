<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peninjauan &amp; Audit Keaslian Dokumen - IzinYou</title>
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
                    <h2 class="text-xl font-bold text-corporate">Pemeriksaan Administratif &amp; Audit Keaslian Dokumen</h2>
                    <span class="bg-teal-100 text-teal-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full">Forensik Berkas</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    No. Pesanan: <strong class="text-corporate font-mono">{{ $document->order?->order_number ?? '-' }}</strong> &bull;
                    Layanan: {{ $document->order?->resolvedService()?->name ?? 'Perizinan' }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <span class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 border border-slate-200 text-slate-700 rounded-xl text-xs font-semibold">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-teal-600"></i>
                    <span>Non-Public Storage (RBAC)</span>
                </span>
                <a href="{{ route('officer.documents.download', $document->id) }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition">
                    <i data-lucide="download" class="w-4 h-4 text-teal-400"></i>
                    <span>Unduh Berkas Asli</span>
                </a>
                <a href="{{ route('officer.documents') }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold flex items-center gap-1.5 transition">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Kembali ke Antrean</span>
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        @include('officer.partials.alerts')

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8 space-y-6 sidebar-scroll">
            
            @php
                $auth = $document->authenticity;
                $fileInfo = $auth['file_info'] ?? [];
                $metaAudit = $auth['metadata_audit'] ?? [];
                $sigAudit = $auth['signature_audit'] ?? [];
                $antiReplay = $auth['anti_replay'] ?? [];
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- KOLOM KIRI & TENGAH (2/3): INFORMASI BERKAS & AUDIT KEASLIAN -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- 1. Ringkasan Dokumen & Klien -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-4 border-b border-slate-100">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-lg font-bold text-corporate">
                                        {{ $document->document_name ?: ($document->requirement?->document_name ?: 'Dokumen Persyaratan') }}
                                    </h3>
                                    @if($document->requirement?->is_mandatory ?? true)
                                        <span class="bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold px-2 py-0.5 rounded-md">Wajib</span>
                                    @else
                                        <span class="bg-slate-100 text-slate-600 text-[10px] font-medium px-2 py-0.5 rounded-md">Opsional</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500">
                                    Kategori: <strong class="text-corporate">{{ $document->document_type ?: ($document->requirement?->document_type ?: 'Persyaratan Teknis') }}</strong>
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="bg-teal-50 text-teal-700 border border-teal-200 text-xs font-mono font-bold px-3 py-1 rounded-xl">
                                    Versi {{ $document->version ?? 1 }}
                                </span>
                                @if($document->status === 'approved')
                                    <span class="bg-teal-100 text-teal-800 text-xs font-bold px-3 py-1 rounded-xl flex items-center gap-1">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Disetujui
                                    </span>
                                @elseif($document->status === 'rejected')
                                    <span class="bg-rose-100 text-rose-800 text-xs font-bold px-3 py-1 rounded-xl flex items-center gap-1">
                                        <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Perlu Revisi
                                    </span>
                                @else
                                    <span class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-xl flex items-center gap-1">
                                        <i data-lucide="clock" class="w-3.5 h-3.5"></i> Menunggu Review
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 text-xs">
                            <div>
                                <span class="text-slate-400 block mb-0.5">Mitra Klien</span>
                                <strong class="text-corporate">{{ $document->order?->user?->name ?? '-' }}</strong>
                            </div>
                            <div>
                                <span class="text-slate-400 block mb-0.5">Nama Perusahaan</span>
                                <strong class="text-corporate">{{ $document->order?->user?->company_name ?? '-' }}</strong>
                            </div>
                            <div>
                                <span class="text-slate-400 block mb-0.5">Nama File Asli</span>
                                <span class="font-mono text-corporate truncate block" title="{{ $fileInfo['original_name'] ?? '-' }}">
                                    {{ $fileInfo['original_name'] ?? '-' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-slate-400 block mb-0.5">Ukuran &amp; Format</span>
                                <strong class="text-corporate">{{ $fileInfo['file_size'] ?? '0 B' }} ({{ $fileInfo['is_pdf'] ? 'PDF' : ($fileInfo['is_image'] ? 'Image' : 'Dokumen') }})</strong>
                            </div>
                        </div>
                    </div>

                    <!-- 2. MODUL DETEKSI & VALIDASI KEASLIAN DOKUMEN -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-6">
                        
                        <!-- Header Modul Keaslian & Skor Integritas -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                            <div>
                                <div class="flex items-center gap-2">
                                    <div class="w-9 h-9 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center">
                                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-corporate text-base">Modul Deteksi Keaslian &amp; Integritas Berkas</h4>
                                        <p class="text-xs text-slate-500">Hasil verifikasi forensik digital tanda tangan resmi dan metadata dokumen.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Skor Indikator -->
                            <div class="flex items-center gap-3 bg-slate-50 px-4 py-2 rounded-2xl border border-slate-200/80">
                                <div class="text-right">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">Skor Integritas</p>
                                    <span class="inline-block text-xs font-bold {{ $auth['badge_class'] ?? '' }} px-2 py-0.5 rounded-md mt-0.5">
                                        {{ $auth['verdict_label'] ?? 'Pemeriksaan Berkas' }}
                                    </span>
                                </div>
                                <div class="text-2xl font-extrabold font-mono text-teal-600">
                                    {{ $auth['overall_score'] ?? 0 }}%
                                </div>
                            </div>
                        </div>

                        <!-- 2 Sub-Kartu Utama: A. TTD & Cap, B. Metadata & Anti-Tampering -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            
                            <!-- Sub-kartu A: Tanda Tangan (TTD) Digital/Basah & Stempel Cap -->
                            <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200/80 space-y-3.5">
                                <div class="flex items-center justify-between">
                                    <h5 class="text-xs font-bold text-corporate flex items-center gap-1.5">
                                        <i data-lucide="stamp" class="w-4 h-4 text-teal-600"></i>
                                        <span>Integritas TTD &amp; Cap Instansi</span>
                                    </h5>
                                    <span class="text-xs font-mono font-bold text-teal-700">{{ $sigAudit['score'] ?? 0 }}/100</span>
                                </div>

                                <div class="space-y-2.5 text-xs">
                                    <div class="p-3 bg-white rounded-xl border border-slate-200/60 flex items-start gap-2.5">
                                        @if(!empty($sigAudit['has_digital_signature']) || !empty($sigAudit['has_qr_verification']))
                                            <i data-lucide="check-circle-2" class="w-4 h-4 text-teal-600 shrink-0 mt-0.5"></i>
                                        @else
                                            <i data-lucide="alert-circle" class="w-4 h-4 text-amber-500 shrink-0 mt-0.5"></i>
                                        @endif
                                        <div>
                                            <strong class="text-corporate block">Tanda Tangan Digital / QR Code:</strong>
                                            <span class="text-slate-600">{{ $sigAudit['signature_type'] ?? 'Tanda Tangan Manual' }}</span>
                                            @if(!empty($sigAudit['official_authority']))
                                                <p class="text-[11px] text-teal-700 font-semibold mt-1">
                                                    Otoritas Resmi: {{ $sigAudit['official_authority'] }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="p-3 bg-white rounded-xl border border-slate-200/60 flex items-start gap-2.5">
                                        @if(!empty($sigAudit['stamp_detected']))
                                            <i data-lucide="check-circle-2" class="w-4 h-4 text-teal-600 shrink-0 mt-0.5"></i>
                                        @else
                                            <i data-lucide="help-circle" class="w-4 h-4 text-slate-400 shrink-0 mt-0.5"></i>
                                        @endif
                                        <div>
                                            <strong class="text-corporate block">Validasi Stempel / Cap Instansi:</strong>
                                            @if(!empty($sigAudit['stamp_detected']))
                                                <span class="text-teal-700 font-medium">Stempel terdeteksi: {{ implode(', ', $sigAudit['stamp_keywords_found'] ?? []) }}</span>
                                            @else
                                                <span class="text-slate-500">Tidak terdeteksi teks cap stempel resmi (perlu konfirmasi visual manual).</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="p-3 bg-white rounded-xl border border-slate-200/60 flex items-start gap-2.5">
                                        @if(empty($sigAudit['visual_manipulation_detected']))
                                            <i data-lucide="shield-check" class="w-4 h-4 text-teal-600 shrink-0 mt-0.5"></i>
                                            <div>
                                                <strong class="text-corporate block">Deteksi Manipulasi Visual TTD:</strong>
                                                <span class="text-slate-600">Area tanda tangan dan cap bersih, tidak ditemukan layer tempelan transparan.</span>
                                            </div>
                                        @else
                                            <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600 shrink-0 mt-0.5"></i>
                                            <div>
                                                <strong class="text-rose-700 block">Peringatan Anomali Visual:</strong>
                                                <span class="text-rose-600">Terdeteksi overlay / floating signature layer di atas kertas dokumen.</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Sub-kartu B: Validasi Metadata File & Anti-Tampering -->
                            <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200/80 space-y-3.5">
                                <div class="flex items-center justify-between">
                                    <h5 class="text-xs font-bold text-corporate flex items-center gap-1.5">
                                        <i data-lucide="file-cog" class="w-4 h-4 text-teal-600"></i>
                                        <span>Validasi Metadata &amp; Keaslian</span>
                                    </h5>
                                    <span class="text-xs font-mono font-bold text-teal-700">{{ $metaAudit['score'] ?? 0 }}/100</span>
                                </div>

                                <div class="space-y-2.5 text-xs">
                                    <div class="p-3 bg-white rounded-xl border border-slate-200/60">
                                        <div class="flex justify-between items-center text-[11px] mb-1">
                                            <span class="text-slate-400">Software Pembuat:</span>
                                            <strong class="text-corporate">{{ $metaAudit['producer'] ?? 'Standar Sistem' }}</strong>
                                        </div>
                                        <div class="flex justify-between items-center text-[11px]">
                                            <span class="text-slate-400">Aplikasi Cetak (Creator):</span>
                                            <strong class="text-corporate">{{ $metaAudit['creator'] ?? 'Aplikasi Resmi' }}</strong>
                                        </div>
                                    </div>

                                    <div class="p-3 bg-white rounded-xl border border-slate-200/60">
                                        <div class="flex justify-between items-center text-[11px] mb-1">
                                            <span class="text-slate-400">Tanggal Dibuat (CreationDate):</span>
                                            <strong class="text-corporate">{{ $metaAudit['creation_date'] ?? '-' }}</strong>
                                        </div>
                                        <div class="flex justify-between items-center text-[11px]">
                                            <span class="text-slate-400">Tanggal Diubah (ModDate):</span>
                                            <strong class="text-corporate">{{ $metaAudit['mod_date'] ?? '-' }}</strong>
                                        </div>
                                        @if(($metaAudit['date_discrepancy_days'] ?? 0) > 0)
                                            <p class="text-[10px] text-amber-600 mt-1">
                                                * Terdapat selisih {{ $metaAudit['date_discrepancy_days'] }} hari pasca pembuatan awal.
                                            </p>
                                        @endif
                                    </div>

                                    <div class="p-3 bg-white rounded-xl border border-slate-200/60 flex items-start gap-2">
                                        @if(empty($metaAudit['tampering_detected']))
                                            <i data-lucide="check" class="w-4 h-4 text-teal-600 shrink-0 mt-0.5"></i>
                                            <span class="text-teal-700 font-medium">Bebas dari jejak software editor (Canva, Photoshop, iLovePDF).</span>
                                        @else
                                            <i data-lucide="x-circle" class="w-4 h-4 text-rose-600 shrink-0 mt-0.5"></i>
                                            <span class="text-rose-600 font-bold">Terdeteksi suntingan dari: {{ $metaAudit['detected_editor'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- SHA-256 Checksum Bar -->
                        <div class="p-3.5 bg-slate-900 text-slate-300 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                            <div class="flex items-center gap-2">
                                <i data-lucide="hash" class="w-4 h-4 text-teal-400 shrink-0"></i>
                                <span class="text-slate-400 text-[11px]">SHA-256 Integrity Checksum:</span>
                                <code class="font-mono text-teal-300 text-[11px] break-all select-all">
                                    {{ $fileInfo['sha256'] ?? 'Belum terhitung' }}
                                </code>
                            </div>
                            <span class="text-[10px] text-teal-400 uppercase font-semibold shrink-0">Integritas Biner Terverifikasi</span>
                        </div>

                        <!-- Ringkasan Kesimpulan Otomatis -->
                        <div class="p-4 rounded-2xl {{ $auth['badge_class'] ?? 'bg-slate-50 border border-slate-200' }} text-xs space-y-1">
                            <strong class="font-bold block">Kesimpulan Analisis Sistem:</strong>
                            <p class="leading-relaxed">{{ $auth['summary_notes'] ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- 3. Riwayat Versi Dokumen (Versioning) -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <i data-lucide="history" class="w-5 h-5 text-teal-600"></i>
                                <h4 class="font-bold text-corporate text-sm">Riwayat Versi Berkas (Versioning)</h4>
                            </div>
                            <span class="text-xs text-slate-500 font-medium">Total: {{ $document->versions->count() }} Versi</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-100 bg-slate-50 text-slate-400 font-semibold uppercase">
                                        <th class="py-2.5 px-4">Versi</th>
                                        <th class="py-2.5 px-4">Nama File</th>
                                        <th class="py-2.5 px-4">Waktu Unggah</th>
                                        <th class="py-2.5 px-4">Status Review</th>
                                        <th class="py-2.5 px-4">Catatan Revisi</th>
                                        <th class="py-2.5 px-4 text-right">Aksi Berkas</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($document->versions as $v)
                                        <tr class="hover:bg-slate-50/50">
                                            <td class="py-3 px-4 font-mono font-bold text-corporate">v{{ $v->version_number }}</td>
                                            <td class="py-3 px-4 font-mono text-slate-600">{{ $v->original_filename }}</td>
                                            <td class="py-3 px-4 text-slate-500">{{ \Carbon\Carbon::parse($v->created_at)->translatedFormat('d M Y, H:i') }}</td>
                                            <td class="py-3 px-4">
                                                @if($v->review_status === 'approved')
                                                    <span class="text-teal-600 font-bold">Disetujui</span>
                                                @elseif($v->review_status === 'rejected')
                                                    <span class="text-rose-600 font-bold">Perlu Revisi</span>
                                                @else
                                                    <span class="text-amber-600 font-bold">Menunggu</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-slate-500 italic">{{ $v->reviewer_notes ?: '-' }}</td>
                                            <td class="py-3 px-4 text-right">
                                                <a href="{{ route('officer.documents.download-version', ['id' => $document->id, 'versionId' => $v->id]) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition" title="Unduh Berkas Versi Aman">
                                                    <i data-lucide="download" class="w-3 h-3 text-teal-600"></i>
                                                    <span>v{{ $v->version_number }}</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="py-4 text-center text-slate-400">Tidak ada riwayat versi terdahulu.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- KOLOM KANAN (1/3): FORMULIR KEPUTUSAN VERIFIKASI PETUGAS -->
                <div class="space-y-6">

                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm sticky top-6">
                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                            <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center">
                                <i data-lucide="clipboard-check" class="w-4 h-4"></i>
                            </div>
                            <h4 class="font-bold text-corporate text-sm">Keputusan Verifikasi Petugas</h4>
                        </div>

                        <form action="{{ route('officer.documents.verify', $document->id) }}" method="POST" class="space-y-4">
                            @csrf

                            <!-- Checklist Administratif -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Pemeriksaan Administratif:</label>
                                <div class="space-y-2 text-xs">
                                    <label class="flex items-start gap-2 p-2 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100/70 border border-slate-200/60">
                                        <input type="checkbox" checked class="mt-0.5 rounded text-teal-600 focus:ring-teal-500">
                                        <span class="text-slate-700 text-[11px]">Keterbacaan teks, stempel cap, &amp; kejelasan resolusi berkas</span>
                                    </label>
                                    <label class="flex items-start gap-2 p-2 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100/70 border border-slate-200/60">
                                        <input type="checkbox" checked class="mt-0.5 rounded text-teal-600 focus:ring-teal-500">
                                        <span class="text-slate-700 text-[11px]">Kesesuaian nama badan hukum/PT &amp; penanggung jawab (PIC)</span>
                                    </label>
                                    <label class="flex items-start gap-2 p-2 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100/70 border border-slate-200/60">
                                        <input type="checkbox" checked class="mt-0.5 rounded text-teal-600 focus:ring-teal-500">
                                        <span class="text-slate-700 text-[11px]">Masa berlaku dokumen izin / sertifikasi belum kedaluwarsa</span>
                                    </label>
                                    <label class="flex items-start gap-2 p-2 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100/70 border border-slate-200/60">
                                        <input type="checkbox" checked class="mt-0.5 rounded text-teal-600 focus:ring-teal-500">
                                        <span class="text-slate-700 text-[11px]">Tanda tangan digital / stempel instansi terverifikasi valid</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Pilihan Status Keputusan -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Status Hasil Pemeriksaan:</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="flex items-center gap-2 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-teal-50/50 has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50/30">
                                        <input type="radio" name="status" value="approved" {{ $document->status === 'approved' ? 'checked' : '' }} class="text-teal-600 focus:ring-teal-500">
                                        <span class="text-xs font-bold text-teal-800">Disetujui</span>
                                    </label>

                                    <label class="flex items-center gap-2 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-rose-50/50 has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50/30">
                                        <input type="radio" name="status" value="rejected" {{ $document->status === 'rejected' ? 'checked' : '' }} class="text-rose-600 focus:ring-rose-500">
                                        <span class="text-xs font-bold text-rose-800">Perlu Revisi</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Template Alasan Cepat -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Template Catatan Cepat:</label>
                                <div class="flex flex-wrap gap-1.5 text-[10px]">
                                    <button type="button" onclick="insertNote('Stempel cap instansi buram dan tidak terbaca jelas. Mohon unggah hasil scan asli berwarna.')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                                        Stempel Buram
                                    </button>
                                    <button type="button" onclick="insertNote('QR Code verifikasi tidak dapat diverifikasi ke portal resmi instansi pemerintah.')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                                        QR Code Anomali
                                    </button>
                                    <button type="button" onclick="insertNote('Terdeteksi modifikasi digital pada metadata berkas. Mohon kirimkan berkas PDF asli penerbitan.')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                                        Metadata Tampered
                                    </button>
                                    <button type="button" onclick="insertNote('Lampiran persyaratan belum lengkap sesuai pedoman teknis regulasi.')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                                        Lampiran Kurang
                                    </button>
                                </div>
                            </div>

                            <!-- Catatan / Instruksi Perbaikan -->
                            <div>
                                <label for="officerNotes" class="block text-xs font-bold text-slate-700 mb-1">
                                    Catatan Pemeriksaan / Instruksi Perbaikan:
                                </label>
                                <textarea id="officerNotes" name="notes" rows="4" placeholder="Tuliskan catatan perbaikan rinci bagi klien..." class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-teal-500">{{ old('notes', $document->notes) }}</textarea>
                                <p class="text-[11px] text-slate-400 mt-1">* Catatan ini akan ditampilkan langsung pada Portal Klien.</p>
                            </div>

                            <button type="submit" class="w-full py-3 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center gap-1.5">
                                <i data-lucide="save" class="w-4 h-4"></i>
                                <span>Simpan Keputusan Verifikasi</span>
                            </button>
                        </form>
                    </div>

                </div>

            </div>

        </div>
    </main>

    <script>
        lucide.createIcons();

        function insertNote(text) {
            const textarea = document.getElementById('officerNotes');
            if (textarea.value) {
                textarea.value = textarea.value + '\n- ' + text;
            } else {
                textarea.value = '- ' + text;
            }
        }
    </script>
</body>
</html>

