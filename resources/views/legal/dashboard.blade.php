<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Legal &amp; Regulatory Specialist - IzinYou</title>
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
        .text-gold { color: #d4af37; }
        .bg-gold { background-color: #d4af37; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-sky-600 selection:text-white flex h-screen overflow-hidden">

    <!-- Dark Sidebar -->
    <aside class="w-72 bg-corporate text-slate-300 flex flex-col h-full shrink-0 shadow-2xl relative z-20 border-r border-slate-800">
        <!-- Logo Header (PRASTA SOLUSI INDONESIA) -->
        <div class="h-20 flex items-center px-6 border-b border-slate-800 gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center shadow-inner flex-shrink-0 border border-slate-700 p-1">
                <img src="{{ asset('images/logo-symbol-transparent.png') }}" alt="Prasta Solusi" class="w-8 h-auto object-contain">
            </div>
            <div>
                <h1 class="font-extrabold text-white text-base leading-tight">PRASTA SOLUSI</h1>
                <p class="text-[10px] text-sky-400 font-semibold tracking-widest uppercase">Legal Specialist Panel</p>
            </div>
        </div>

        <!-- Menu Navigation -->
        <div class="flex-1 overflow-y-auto sidebar-scroll py-6 px-4">
            <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Divisi Legalitas &amp; Teknis</p>
            
            <nav class="space-y-1.5">
                <a href="#overview" class="flex items-center gap-3 px-3 py-2.5 bg-sky-600/15 text-sky-400 rounded-xl group transition-all font-medium text-sm">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Dashboard Legalitas</span>
                </a>

                <a href="#antrean-berkas" class="flex items-center justify-between px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl group transition-all font-medium text-sm">
                    <div class="flex items-center gap-3">
                        <i data-lucide="file-check-2" class="w-5 h-5"></i>
                        <span>Kajian Berkas Klien</span>
                    </div>
                    @if($stats['pendingDocs'] > 0)
                        <span class="bg-amber-500 text-slate-900 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $stats['pendingDocs'] }} Butuh Review</span>
                    @endif
                </a>

                <a href="#checksheet" class="flex items-center justify-between px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl group transition-all font-medium text-sm">
                    <div class="flex items-center gap-3">
                        <i data-lucide="clipboard-check" class="w-5 h-5"></i>
                        <span>Checksheet Regulasi</span>
                    </div>
                    <span class="bg-sky-500/20 text-sky-300 text-[10px] font-bold px-2 py-0.5 rounded-full">SOP 2026</span>
                </a>

                <a href="#berita-acara" class="flex items-center justify-between px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl group transition-all font-medium text-sm">
                    <div class="flex items-center gap-3">
                        <i data-lucide="scroll-text" class="w-5 h-5"></i>
                        <span>Berita Acara Kajian (BAK)</span>
                    </div>
                    <span class="bg-slate-800 text-slate-400 text-[10px] font-bold px-2 py-0.5 rounded-full">Resmi</span>
                </a>

                <div class="pt-6">
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700/60 space-y-2">
                        <div class="flex items-center gap-2 text-sky-400 text-xs font-bold">
                            <i data-lucide="shield" class="w-4 h-4"></i>
                            <span>Standar Terakreditasi</span>
                        </div>
                        <ul class="text-[11px] text-slate-400 space-y-1 list-disc list-inside">
                            <li>Permenkes No. 62/2017 (Alkes)</li>
                            <li>Perka BPOM CDOB &amp; Kosmetik</li>
                            <li>SJPH Halal BPJPH Kemenag</li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>

        <!-- User Profile (Bottom) -->
        <div class="p-4 border-t border-slate-800">
            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-800/60 border border-slate-700/80">
                <div class="w-10 h-10 rounded-full bg-sky-900/60 text-sky-300 border border-sky-500/30 flex items-center justify-center font-bold">
                    {{ strtoupper(substr($user->name ?? 'L', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate">{{ $user->name }}</p>
                    <p class="text-[11px] text-sky-400 truncate flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-400 inline-block"></span>
                        Legal &amp; Regulatory
                    </p>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" title="Keluar / Logout" class="text-slate-400 hover:text-rose-400 p-1.5 rounded-lg hover:bg-slate-700 transition-colors">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/60">
        
        <!-- Header -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0 relative z-10">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-corporate">Dashboard Legal &amp; Regulatory Specialist</h2>
                    <span class="bg-sky-100 text-sky-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full">Sistem IZINYOU</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Kajian kepatuhan dokumen teknis, checksheet standar kementerian, dan penerbitan BAK.</p>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-500 font-medium bg-slate-100 px-3 py-1.5 rounded-xl flex items-center gap-1.5">
                    <i data-lucide="shield-check" class="w-4 h-4 text-sky-600"></i> Auditor Bersertifikat
                </span>
            </div>
        </header>

        <!-- Flash Messages -->
        @include('klien.partials.alerts')

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8 pt-4 space-y-8" id="overview">

            <!-- 1. Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Menunggu Kajian Teknis</p>
                            <h3 class="text-3xl font-extrabold text-amber-600">{{ $stats['pendingDocs'] }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="clock" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-xs text-amber-700 font-medium">Perlu diteliti kesesuaian SOP</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Dokumen Valid (Disetujui)</p>
                            <h3 class="text-3xl font-extrabold text-sky-600">{{ $stats['approvedDocs'] }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-xs text-sky-700 font-medium">Lolos audit teknis internal</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Perlu Perbaikan Dokumen</p>
                            <h3 class="text-3xl font-extrabold text-rose-600">{{ $stats['rejectedDocs'] }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-xs text-rose-700 font-medium">Memerlukan revisi klien</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Total Arsip Berkas</p>
                            <h3 class="text-3xl font-extrabold text-corporate">{{ $stats['totalDocs'] }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="folder" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">Tercatat dalam repositori</p>
                </div>
            </div>

            <!-- 2. Section A: Antrean Kajian Berkas Teknis / Legalitas -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm" id="antrean-berkas">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="file-search" class="w-6 h-6 text-sky-600"></i>
                            <h3 class="text-lg font-bold text-corporate">Antrean Kajian Berkas Teknis &amp; Legalitas Klien</h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Verifikasi dokumen persyaratan izin edar, sertifikasi halal, dan sertifikat CDOB.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('legal.dashboard') }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ empty($filterStatus) || $filterStatus === 'all' ? 'bg-corporate text-white' : 'bg-slate-100 text-slate-600' }}">Semua</a>
                        <a href="{{ route('legal.dashboard', ['status' => 'uploaded']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ $filterStatus === 'uploaded' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600' }}">Menunggu Review</a>
                        <a href="{{ route('legal.dashboard', ['status' => 'approved']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ $filterStatus === 'approved' ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-600' }}">Disetujui (Valid)</a>
                        <a href="{{ route('legal.dashboard', ['status' => 'rejected']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ $filterStatus === 'rejected' ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-600' }}">Perlu Revisi</a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-4 px-6">Nama Berkas Teknis</th>
                                <th class="py-4 px-6">Perusahaan Klien</th>
                                <th class="py-4 px-6">No. Order</th>
                                <th class="py-4 px-6">Status Kajian</th>
                                <th class="py-4 px-6">Catatan Verifikasi</th>
                                <th class="py-4 px-6 text-right">Aksi Kajian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($documents as $doc)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2.5">
                                            <i data-lucide="file-text" class="w-5 h-5 text-sky-600 shrink-0"></i>
                                            <div>
                                                <p class="font-bold text-corporate text-xs">{{ $doc->req_document_name ?? $doc->document_name ?? basename($doc->file_path) }}</p>
                                                <p class="text-[11px] text-slate-500">
                                                    <span class="font-mono font-bold text-sky-700">v{{ $doc->version ?? 1 }}</span> &bull;
                                                    {{ $doc->req_document_type ?? $doc->document_type ?? 'Persyaratan Teknis' }} &bull;
                                                    {{ $doc->is_mandatory ? 'Wajib' : 'Opsional' }}
                                                </p>
                                                <p class="text-[10px] text-slate-400">{{ $doc->original_filename ?: basename($doc->file_path) }} &bull; {{ date('d M Y, H:i', strtotime($doc->updated_at ?? $doc->created_at)) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="font-bold text-corporate text-xs">{{ $doc->company_name ?? '-' }}</p>
                                        <p class="text-[11px] text-slate-500">PIC: {{ $doc->client_name ?? '-' }}</p>
                                    </td>
                                    <td class="py-4 px-6 font-mono text-xs font-bold text-slate-600">
                                        {{ $doc->order_number ?? '-' }}
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($doc->status === 'approved')
                                            <span class="inline-flex items-center gap-1 bg-sky-50 text-sky-700 border border-sky-200 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i> Diterima / Valid
                                            </span>
                                        @elseif($doc->status === 'rejected')
                                            <span class="inline-flex items-center gap-1 bg-rose-50 text-rose-700 border border-rose-200 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> Perlu Revisi
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                                <i data-lucide="clock" class="w-3.5 h-3.5"></i> Menunggu Pemeriksaan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-xs text-slate-600 max-w-xs truncate" title="{{ $doc->notes }}">
                                        {{ $doc->notes ?? 'Belum ada catatan telaah.' }}
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                            <a href="{{ route('documents.current.download', $doc->id) }}" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-corporate rounded-lg text-xs font-bold flex items-center gap-1 transition-all" title="Unduh Berkas">
                                                <i data-lucide="download" class="w-3.5 h-3.5 text-sky-600"></i> File
                                            </a>

                                            <!-- Tombol Kaji Berkas / Validasi Teknis -->
                                            <button type="button" onclick="openVerifyModal({{ json_encode(['id' => $doc->id, 'status' => $doc->status, 'notes' => $doc->notes, 'file_path' => $doc->file_path, 'company_name' => $doc->company_name, 'order_number' => $doc->order_number]) }})" class="px-2.5 py-1.5 bg-sky-600 hover:bg-sky-500 text-white rounded-lg text-xs font-bold flex items-center gap-1 transition-all">
                                                <i data-lucide="clipboard-check" class="w-3.5 h-3.5"></i> Kaji Berkas
                                            </button>

                                            <!-- Tombol Cetak Berita Acara Kajian (BAK) -->
                                            <a href="{{ route('legal.document.bak', $doc->id) }}" target="_blank" class="px-2.5 py-1.5 bg-corporate hover:bg-slate-800 text-white rounded-lg text-xs font-bold flex items-center gap-1 transition-all" title="Penerbitan Berita Acara Kajian">
                                                <i data-lucide="printer" class="w-3.5 h-3.5 text-gold"></i> BAK
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                        Tidak ada berkas yang perlu dikaji pada filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. Section B: Pembaruan Progres Proyek (Tracking) oleh Legal/Konsultan -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm" id="tracking-update">
                <div class="flex items-center gap-2 pb-4 mb-5 border-b border-slate-100">
                    <i data-lucide="git-merge" class="w-6 h-6 text-sky-600"></i>
                    <div>
                        <h3 class="text-lg font-bold text-corporate">Perbarui Progres Pengerjaan Proyek</h3>
                        <p class="text-xs text-slate-500">Perbarui tahapan pengerjaan klien secara real-time (Pemeriksaan Dokumen hingga Selesai).</p>
                    </div>
                </div>
                <form action="{{ route('legal.tracking.update') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Pesanan *</label>
                        <select name="order_id" required class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50">
                            @foreach($orders as $ord)
                                <option value="{{ $ord->id }}">{{ $ord->order_number }} - {{ $ord->company_name ?? $ord->client_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tahapan *</label>
                        <select name="stage_name" required class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50">
                            @foreach($stages as $st)
                                <option value="{{ $st['code'] }}">{{ $st['order'] }}. {{ $st['name'] }} ({{ $st['percentage'] }}%)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status Tahap *</label>
                        <select name="stage_status" required class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50">
                            <option value="in_progress">Sedang Berjalan</option>
                            <option value="completed" selected>Selesai</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan *</label>
                        <div class="flex gap-2">
                            <input type="text" name="description" required placeholder="Catatan pembaruan..." class="flex-1 text-xs px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50">
                            <button type="submit" class="px-4 py-2.5 bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold rounded-xl shrink-0">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- 4. Section C: Checksheet Kepatuhan Regulasi Standar -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm" id="checksheet">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="clipboard-check" class="w-6 h-6 text-sky-600"></i>
                            <h3 class="text-lg font-bold text-corporate">Checksheet Kepatuhan Regulasi Standar Industri</h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Audit kesesuaian dokumen terhadap standar SOP Kemenkes, BPOM, Halal BPJPH, dan CDOB.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Standard 1: Kemenkes (Izin Edar Alkes) -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/60 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-xs">KM</span>
                                <h4 class="font-bold text-corporate text-sm">SOP Kemenkes (Permenkes 62/2017)</h4>
                            </div>
                            <span class="text-xs font-bold text-sky-600 bg-sky-50 px-2 py-0.5 rounded-full border border-sky-100">Alkes &amp; PKRT</span>
                        </div>
                        <div class="space-y-2 text-xs text-slate-700 pt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="w-4 h-4 text-sky-600 rounded">
                                <span>Izin Distribusi / Sertifikat CPAKB / CPPKRT masih berlaku</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="w-4 h-4 text-sky-600 rounded">
                                <span>Uji Laboratorium &amp; Certificate of Analysis (CoA) terakreditasi</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="w-4 h-4 text-sky-600 rounded">
                                <span>Penandaan &amp; Brosur Informasi Penggunaan Bahasa Indonesia</span>
                            </label>
                        </div>
                    </div>

                    <!-- Standard 2: BPOM (Kosmetik & Pangan Olahan) -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/60 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">BP</span>
                                <h4 class="font-bold text-corporate text-sm">Standar BPOM (Notifikasi &amp; CPKB)</h4>
                            </div>
                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">BPOM RI</span>
                        </div>
                        <div class="space-y-2 text-xs text-slate-700 pt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="w-4 h-4 text-blue-600 rounded">
                                <span>Surat Perjanjian Kerjasama Maklon (Toll Manufacturing) legalisir</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="w-4 h-4 text-blue-600 rounded">
                                <span>Formula Komposisi INCI &amp; Batas Bahan Berbahaya Sesuai Perka</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="w-4 h-4 text-blue-600 rounded">
                                <span>Uji Stabilitas &amp; Uji Mikrobiologi Lolos Ambang Batas</span>
                            </label>
                        </div>
                    </div>

                    <!-- Standard 3: BPJPH (Sertifikasi Halal) -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/60 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-xs">HL</span>
                                <h4 class="font-bold text-corporate text-sm">Sertifikasi Halal BPJPH (SJPH)</h4>
                            </div>
                            <span class="text-xs font-bold text-teal-600 bg-teal-50 px-2 py-0.5 rounded-full border border-teal-100">Kemenag</span>
                        </div>
                        <div class="space-y-2 text-xs text-slate-700 pt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="w-4 h-4 text-teal-600 rounded">
                                <span>Dokumen Sistem Jaminan Produk Halal (Manual SJPH) lengkap</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="w-4 h-4 text-teal-600 rounded">
                                <span>Penetapan &amp; Pelatihan Tim Penyelia Halal Internal Ber-SK</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="w-4 h-4 text-teal-600 rounded">
                                <span>Pohon Bahan &amp; Bebas Dari Unsur Najis / Fasilitas Terpisah</span>
                            </label>
                        </div>
                    </div>

                    <!-- Standard 4: CDOB (Cara Distribusi Obat yang Baik) -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/60 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs">CD</span>
                                <h4 class="font-bold text-corporate text-sm">Sertifikat CDOB Distribusi Farmasi</h4>
                            </div>
                            <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full border border-purple-100">PBF / Farmasi</span>
                        </div>
                        <div class="space-y-2 text-xs text-slate-700 pt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="w-4 h-4 text-purple-600 rounded">
                                <span>Manajemen Mutu Gudang &amp; Monitoring Suhu (Cold Chain)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="w-4 h-4 text-purple-600 rounded">
                                <span>Kualifikasi Penanggung Jawab Apoteker (STRA &amp; SIPA Aktif)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="w-4 h-4 text-purple-600 rounded">
                                <span>SOP Penanganan Recall Produk &amp; Keluhan Distribusi</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Modal Verifikasi Berkas -->
    <div id="verifyModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold">
                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-corporate text-base" id="modalFileName">Kajian Dokumen Teknis</h3>
                        <p class="text-xs text-slate-400" id="modalCompanyInfo">Mitra Perusahaan</p>
                    </div>
                </div>
                <button onclick="document.getElementById('verifyModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="verifyForm" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Keputusan Status Validasi Teknis <span class="text-rose-500">*</span></label>
                    <select name="status" id="verifyStatusSelect" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-500">
                        <option value="approved">Diterima (Valid / Lolos Kepatuhan Standar)</option>
                        <option value="rejected">Perlu Revisi (Memerlukan Perbaikan Klien)</option>
                        <option value="uploaded">Menunggu Pemeriksaan Lanjutan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Kajian / Rekomendasi Legalitas <span class="text-rose-500">*</span></label>
                    <textarea name="notes" id="verifyNotes" rows="4" required class="w-full text-xs p-3.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-500"></textarea>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('verifyModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-500">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs rounded-xl shadow-md transition-all">
                        Simpan Hasil Validasi Teknis
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function openVerifyModal(doc) {
            document.getElementById('modalFileName').innerText = doc.file_path ? doc.file_path.split('/').pop() : 'Dokumen Teknis';
            document.getElementById('modalCompanyInfo').innerText = (doc.company_name || 'Mitra') + ' (' + (doc.order_number || '-') + ')';
            document.getElementById('verifyStatusSelect').value = doc.status;
            document.getElementById('verifyNotes').value = doc.notes || (doc.status === 'approved' ? 'Dokumen teknis telah memenuhi seluruh kepatuhan regulasi instansi terkait (Valid).' : 'Dokumen memerlukan perbaikan.');
            document.getElementById('verifyForm').action = "/legal/document/" + doc.id + "/verify";
            document.getElementById('verifyModal').classList.remove('hidden');
        }
    </script>
</body>
</html>
