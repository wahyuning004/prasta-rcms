<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Klien - IzinYou</title>
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
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-sky-500 selection:text-white flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-72 bg-corporate text-slate-300 flex flex-col h-full shrink-0 shadow-2xl relative z-20 border-r border-slate-800">
        <!-- Logo Header (PRASTA SOLUSI INDONESIA) -->
        <div class="h-20 flex items-center px-6 border-b border-slate-800 gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center shadow-inner flex-shrink-0 border border-slate-700 p-1">
                <img src="{{ asset('images/logo-symbol-transparent.png') }}" alt="Prasta Solusi" class="w-8 h-auto object-contain">
            </div>
            <div>
                <h1 class="font-extrabold text-white text-base leading-tight">PRASTA SOLUSI</h1>
                <p class="text-[10px] text-sky-400 font-semibold tracking-widest uppercase">Klien Portal &bull; Mitra</p>
            </div>
        </div>

        <!-- Menu Navigation -->
        <div class="flex-1 overflow-y-auto sidebar-scroll py-6 px-4">
            <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Menu Klien</p>
            
            <nav class="space-y-1.5">
                <a href="#overview" class="flex items-center gap-3 px-3 py-2.5 bg-sky-600/15 text-sky-400 rounded-xl group transition-all font-medium text-sm">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Dashboard Saya</span>
                </a>

                <a href="#tracking" class="flex items-center justify-between px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl group transition-all font-medium text-sm">
                    <div class="flex items-center gap-3">
                        <i data-lucide="git-commit" class="w-5 h-5"></i>
                        <span>Pelacakan Projek</span>
                    </div>
                    @if($activeOrder)
                        <span class="bg-sky-500/20 text-sky-300 text-[10px] font-bold px-2 py-0.5 rounded-full">Progres Aktif</span>
                    @endif
                </a>

                <a href="#dokumen" class="flex items-center justify-between px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl group transition-all font-medium text-sm">
                    <div class="flex items-center gap-3">
                        <i data-lucide="file-check" class="w-5 h-5"></i>
                        <span>Dokumen Persyaratan</span>
                    </div>
                    <span class="bg-slate-700 text-slate-300 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $myDocuments->count() }} File</span>
                </a>

                <a href="#konsultasi" class="flex items-center justify-between px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl group transition-all font-medium text-sm">
                    <div class="flex items-center gap-3">
                        <i data-lucide="help-circle" class="w-5 h-5"></i>
                        <span>Konsultasi Perizinan</span>
                    </div>
                    <span class="bg-gold/20 text-yellow-300 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $myConsultations->count() }} Sesi</span>
                </a>

                <a href="#layanan" class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl group transition-all font-medium text-sm">
                    <i data-lucide="layers" class="w-5 h-5"></i>
                    <span>Katalog Layanan</span>
                </a>

                <!-- Menu Pusat Bantuan & FAQ Interaktif -->
                <button onclick="document.getElementById('helpCenterModal').classList.remove('hidden')" class="w-full flex items-center justify-between px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl group transition-all font-medium text-sm text-left">
                    <div class="flex items-center gap-3">
                        <i data-lucide="help-circle" class="w-5 h-5 text-amber-400"></i>
                        <span>Pusat Bantuan &amp; FAQ</span>
                    </div>
                    <span class="bg-amber-500/20 text-amber-300 text-[10px] font-bold px-2 py-0.5 rounded-full">Panduan</span>
                </button>

                <div class="pt-6">
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700/60 text-center">
                        <div class="w-9 h-9 rounded-full bg-sky-500/20 text-sky-400 flex items-center justify-center mx-auto mb-2">
                            <i data-lucide="headphones" class="w-5 h-5"></i>
                        </div>
                        <h4 class="text-xs font-bold text-white mb-1">Butuh Bantuan Cepat?</h4>
                        <p class="text-[11px] text-slate-400 leading-tight mb-3">Tim konsultan kami siap mendampingi proses perizinan &amp; sertifikasi Anda.</p>
                        <a href="#konsultasi" class="block w-full py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-lg text-xs font-bold transition-all shadow-md">
                            Tanya Konsultan
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- User Profile (Bottom) -->
        <div class="p-4 border-t border-slate-800">
            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-800/60 border border-slate-700/80">
                <div class="w-10 h-10 rounded-full bg-amber-900/50 text-amber-300 border border-amber-500/30 flex items-center justify-center font-bold">
                    {{ strtoupper(substr($user->name ?? 'K', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate">{{ $user->name }}</p>
                    <p class="text-[11px] text-amber-400 truncate">{{ $user->company_name ?? 'Klien Perusahaan' }}</p>
                    <button onclick="document.getElementById('twoFactorModal').classList.remove('hidden')" class="flex items-center gap-1 text-[10px] text-sky-400 hover:underline mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        <span>2FA WhatsApp Aktif</span>
                    </button>
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

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/60">
        <!-- Top Header -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0 relative z-10">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-corporate">Portal Klien Mitra</h2>
                    <span class="bg-sky-100 text-sky-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full">{{ $user->company_name ?? 'Klien Mitra IzinYou' }}</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Pantau status izin edar, sertifikasi, dan dokumen kepatuhan industri Anda.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- Indikator 2-Step Verification WhatsApp -->
                <button onclick="document.getElementById('twoFactorModal').classList.remove('hidden')" class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 bg-sky-50 hover:bg-sky-100 border border-sky-200 text-sky-800 rounded-xl text-xs font-semibold transition-all" title="Status Keamanan Akun">
                    <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-sky-600"></i>
                    <span>2FA WhatsApp Aktif</span>
                </button>

                <!-- Tombol Pusat Bantuan & Panduan Pengguna -->
                <button onclick="document.getElementById('helpCenterModal').classList.remove('hidden')" class="flex items-center gap-1.5 px-3.5 py-2 bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-900 rounded-xl text-xs font-bold transition-all shadow-sm">
                    <i data-lucide="help-circle" class="w-4 h-4 text-amber-600"></i>
                    <span>Pusat Bantuan &amp; Panduan</span>
                </button>

                <a href="#konsultasi" class="px-4 py-2 bg-corporate hover:bg-slate-800 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all">
                    <i data-lucide="message-square-plus" class="w-4 h-4 text-sky-400"></i>
                    <span>Ajukan Konsultasi</span>
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-8 pt-4">
            @if (session('success'))
                <div class="flex items-center gap-3 bg-sky-50 border border-sky-200 text-sky-800 px-4 py-3 rounded-2xl text-sm font-medium shadow-sm">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-sky-600 flex-shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl text-sm font-medium shadow-sm">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 flex-shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
        </div>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8 pt-4">
            <div class="max-w-7xl mx-auto space-y-8" id="overview">
                
                <!-- 1. Statistik Ringkasan Klien -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Card 1 -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group hover:border-sky-200 transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Projek / Izin Aktif</p>
                                <h3 class="text-3xl font-extrabold text-corporate">{{ $stats['ordersCount'] ?? 0 }}</h3>
                            </div>
                            <div class="w-12 h-12 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center">
                                <i data-lucide="folder-kanban" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <p class="text-xs text-sky-700 font-medium">Dalam pendampingan perizinan</p>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group hover:border-blue-200 transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Dokumen Valid</p>
                                <h3 class="text-3xl font-extrabold text-corporate">{{ $stats['approvedDocsCount'] ?? 0 }}</h3>
                            </div>
                            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                                <i data-lucide="shield-check" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <p class="text-xs text-blue-700 font-medium">Lolos verifikasi teknis</p>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group hover:border-amber-200 transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Dokumen Diproses</p>
                                <h3 class="text-3xl font-extrabold text-corporate">{{ $stats['pendingDocsCount'] ?? 0 }}</h3>
                            </div>
                            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                                <i data-lucide="clock" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <p class="text-xs text-amber-700 font-medium">Sedang ditinjau konsultan</p>
                    </div>

                    <!-- Card 4 -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group hover:border-purple-200 transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Konsultasi Selesai</p>
                                <h3 class="text-3xl font-extrabold text-corporate">{{ $stats['consultationsCount'] ?? 0 }}</h3>
                            </div>
                            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                                <i data-lucide="messages-square" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <p class="text-xs text-purple-700 font-medium">Pertanyaan telah dijawab</p>
                    </div>
                </div>

                <!-- 2. Section Pelacakan Real-time Projek Saya -->
                <div class="bg-white rounded-3xl p-6 lg:p-8 border border-slate-200/80 shadow-sm" id="tracking">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <i data-lucide="git-merge" class="w-6 h-6 text-sky-600"></i>
                                <h3 class="text-lg font-bold text-corporate">Pelacakan Tahapan Projek & Sertifikasi</h3>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Status transparan tahapan permohonan sertifikasi perusahaan Anda.</p>
                        </div>
                        @if($activeOrder)
                            <div class="text-right">
                                <span class="text-xs font-bold font-mono bg-slate-900 text-white px-3 py-1 rounded-lg">
                                    {{ $activeOrder->order_number }}
                                </span>
                                <p class="text-[11px] text-slate-400 mt-1">Status: <span class="capitalize font-semibold text-sky-600">{{ $activeOrder->status }}</span></p>
                            </div>
                        @endif
                    </div>

                    @if($activeOrder)
                        <!-- Stepper Progress Bar -->
                        <div class="bg-slate-50/80 rounded-2xl p-6 border border-slate-100 mb-8">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Timeline Kemajuan Perizinan</h4>
                            <div class="relative">
                                <!-- Line connector -->
                                <div class="absolute top-5 left-6 right-6 h-1 bg-slate-200 -z-0 hidden md:block">
                                    <div class="h-1 bg-sky-500 w-2/3"></div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 relative z-10">
                                    <!-- Step 1 -->
                                    <div class="flex md:flex-col items-center gap-3 md:text-center">
                                        <div class="w-10 h-10 rounded-full bg-sky-500 text-white flex items-center justify-center font-bold text-sm shadow-md shrink-0">
                                            <i data-lucide="check" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-corporate">Verifikasi Dokumen</p>
                                            <p class="text-[11px] text-sky-600 font-medium">Selesai</p>
                                        </div>
                                    </div>

                                    <!-- Step 2 -->
                                    <div class="flex md:flex-col items-center gap-3 md:text-center">
                                        <div class="w-10 h-10 rounded-full bg-sky-500 text-white flex items-center justify-center font-bold text-sm shadow-md shrink-0">
                                            <i data-lucide="check" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-corporate">Pengajuan Instansi</p>
                                            <p class="text-[11px] text-sky-600 font-medium">Diproses Kemenkes/BPOM</p>
                                        </div>
                                    </div>

                                    <!-- Step 3 -->
                                    <div class="flex md:flex-col items-center gap-3 md:text-center">
                                        <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-md shrink-0 animate-pulse">
                                            <i data-lucide="clock" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-corporate">Audit & Uji Teknis</p>
                                            <p class="text-[11px] text-blue-600 font-medium">Sedang Berjalan</p>
                                        </div>
                                    </div>

                                    <!-- Step 4 -->
                                    <div class="flex md:flex-col items-center gap-3 md:text-center">
                                        <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center font-bold text-sm shrink-0">
                                            <i data-lucide="award" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-500">Penerbitan Sertifikat</p>
                                            <p class="text-[11px] text-slate-400">Tahap Akhir</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Riwayat Tahapan -->
                        <div class="space-y-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Catatan Riwayat Tahapan Terinci:</h4>
                            @forelse ($myTrackings as $track)
                                <div class="flex items-start gap-4 p-4 rounded-xl bg-slate-50 border border-slate-100">
                                    <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center shrink-0 mt-0.5">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1">
                                            <span class="font-bold text-corporate text-xs">{{ $track->stage_name }}</span>
                                            <span class="text-[11px] text-slate-400 font-medium">
                                                {{ \Carbon\Carbon::parse($track->date_recorded)->format('d F Y, H:i') }} WIB
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 leading-relaxed">{{ $track->description }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 italic">Belum ada pembaruan tahapan untuk projek ini.</p>
                            @endforelse
                        </div>
                    @else
                        <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            <i data-lucide="inbox" class="w-10 h-10 text-slate-400 mx-auto mb-2"></i>
                            <h4 class="text-sm font-bold text-corporate">Belum Ada Projek Berjalan</h4>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Anda belum memiliki pesanan aktif. Silakan pilih layanan pada katalog di bawah untuk memulai pengajuan sertifikasi.</p>
                        </div>
                    @endif
                </div>

                <!-- 3. Section Dokumen Persyaratan Klien -->
                <div class="bg-white rounded-3xl p-6 lg:p-8 border border-slate-200/80 shadow-sm" id="dokumen">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <div class="flex items-center gap-2">
                                <i data-lucide="folder-check" class="w-6 h-6 text-sky-600"></i>
                                <h3 class="text-lg font-bold text-corporate">Dokumen &amp; Berkas Perizinan Anda</h3>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Daftar berkas perizinan yang telah diunggah dan status validasinya.</p>
                        </div>
                        <span class="text-xs font-semibold bg-sky-50 text-sky-700 px-3 py-1.5 rounded-full">
                            {{ $myDocuments->count() }} Berkas Terunggah
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 text-xs font-semibold uppercase tracking-wider">
                                    <th class="py-3 px-4">Nama Dokumen</th>
                                    <th class="py-3 px-4">Nomor Projek</th>
                                    <th class="py-3 px-4">Tanggal Unggah</th>
                                    <th class="py-3 px-4">Status Verifikasi</th>
                                    <th class="py-3 px-4">Catatan Konsultan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @forelse ($myDocuments as $doc)
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="py-4 px-4 font-mono text-xs text-slate-700">
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="file-text" class="w-4 h-4 text-sky-600 shrink-0"></i>
                                                <span class="font-semibold text-corporate truncate max-w-[220px]" title="{{ basename($doc->file_path) }}">
                                                    {{ basename($doc->file_path) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 font-mono text-xs text-slate-600">
                                            {{ $doc->order_number ?? '-' }}
                                        </td>
                                        <td class="py-4 px-4 text-xs text-slate-500">
                                            {{ \Carbon\Carbon::parse($doc->created_at)->format('d M Y, H:i') }}
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($doc->status === 'approved')
                                                <span class="inline-flex items-center gap-1 bg-sky-50 text-sky-700 border border-sky-200 px-2.5 py-1 rounded-full text-xs font-semibold">
                                                    <i data-lucide="check" class="w-3.5 h-3.5"></i> Valid / Disetujui
                                                </span>
                                            @elseif($doc->status === 'rejected')
                                                <span class="inline-flex items-center gap-1 bg-rose-50 text-rose-700 border border-rose-200 px-2.5 py-1 rounded-full text-xs font-semibold">
                                                    <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> Perlu Revisi
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-full text-xs font-semibold">
                                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i> Menunggu Verifikasi
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-xs text-slate-600 max-w-[240px] truncate" title="{{ $doc->notes }}">
                                            {{ $doc->notes ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-8 text-slate-400 text-sm">
                                            Belum ada dokumen yang diunggah.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. Section Konsultasi Perizinan Klien -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" id="konsultasi">
                    <!-- Form Ajukan Konsultasi -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm lg:col-span-1">
                        <div class="flex items-center gap-2 mb-3">
                            <i data-lucide="help-circle" class="w-5 h-5 text-sky-600"></i>
                            <h3 class="text-base font-bold text-corporate">Ajukan Pertanyaan Perizinan &amp; Sertifikasi</h3>
                        </div>
                        <p class="text-xs text-slate-500 mb-5 leading-relaxed">
                            Punya kendala mengenai izin edar, sertifikasi halal, atau uji lab? Tanyakan langsung ke tim konsultan teknis kami.
                        </p>

                        <form action="{{ route('klien.consultation.submit') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Topik / Subjek Pertanyaan</label>
                                <input type="text" name="subject" required placeholder="Contoh: Syarat Izin Edar Alkes Elektromedis" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-sky-500 focus:bg-white transition-colors">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Detail Pertanyaan</label>
                                <textarea name="message" rows="4" required placeholder="Jelaskan jenis produk, fasilitas, atau kendala perizinan yang sedang dihadapi..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:outline-none focus:border-sky-500 focus:bg-white transition-colors"></textarea>
                            </div>

                            <button type="submit" class="w-full py-2.5 px-4 bg-corporate hover:bg-slate-800 text-white font-bold rounded-xl text-xs flex items-center justify-center gap-2 transition-all shadow-md">
                                <i data-lucide="send" class="w-4 h-4 text-sky-400"></i>
                                Kirim Pertanyaan Konsultasi
                            </button>
                        </form>
                    </div>

                    <!-- Riwayat Konsultasi Klien -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm lg:col-span-2">
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-2">
                                <i data-lucide="messages-square" class="w-5 h-5 text-sky-600"></i>
                                <h3 class="text-base font-bold text-corporate">Riwayat Tanya-Jawab Konsultasi</h3>
                            </div>
                            <span class="text-xs text-slate-400">Total: {{ $myConsultations->count() }} Sesi</span>
                        </div>

                        <div class="space-y-4">
                            @forelse ($myConsultations as $item)
                                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-100 space-y-3">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-slate-200/60 pb-2">
                                        <h4 class="font-bold text-corporate text-xs">{{ $item->subject }}</h4>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[11px] text-slate-400">
                                                {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }}
                                            </span>
                                            @if($item->status === 'responded')
                                                <span class="bg-sky-100 text-sky-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Dijawab</span>
                                            @else
                                                <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Menunggu</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <p class="text-xs text-slate-600 leading-relaxed">{{ $item->message }}</p>

                                    @if($item->response)
                                        <div class="bg-sky-50/70 p-3 rounded-xl border border-sky-100 text-xs text-sky-900 leading-relaxed">
                                            <div class="flex items-center gap-1.5 font-bold text-sky-800 mb-1">
                                                <i data-lucide="badge-check" class="w-4 h-4 text-sky-600"></i>
                                                <span>Tanggapan Tim Konsultan IzinYou:</span>
                                            </div>
                                            <p class="text-sky-950">{{ $item->response }}</p>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-8 text-slate-400 text-sm">
                                    Belum ada pertanyaan konsultasi yang Anda ajukan.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- 5. Katalog Layanan & Sertifikasi Tersedia -->
                <div class="bg-white rounded-3xl p-6 lg:p-8 border border-slate-200/80 shadow-sm" id="layanan">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <div class="flex items-center gap-2">
                                <i data-lucide="sparkles" class="w-6 h-6 text-gold"></i>
                                <h3 class="text-lg font-bold text-corporate">Katalog Layanan &amp; Pemesanan Perizinan</h3>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Pilih layanan sertifikasi resmi industri dan gunakan kupon promo untuk potongan biaya.</p>
                        </div>
                    </div>

                    <!-- Kupon Promo Diskon Tersedia Banner -->
                    <div class="mb-6 p-4 rounded-2xl bg-gradient-to-r from-amber-500/10 via-sky-500/10 to-indigo-500/10 border border-amber-200/70 flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold shrink-0 shadow-md">
                                <i data-lucide="tag" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-corporate">Kupon Promo Diskon Tersedia!</h4>
                                <p class="text-[11px] text-slate-600">Klik badge kode kupon di samping untuk langsung menyalin saat pemesanan layanan:</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach ($promos as $p)
                                <button type="button" onclick="selectPromoCode('{{ $p->code }}')" class="group flex items-center gap-1.5 px-3 py-1.5 bg-white border border-amber-300 rounded-xl shadow-sm hover:border-sky-500 transition-all text-left">
                                    <span class="font-mono text-xs font-extrabold text-corporate group-hover:text-sky-600">{{ $p->code }}</span>
                                    <span class="text-[10px] font-bold text-sky-600 bg-sky-50 px-1.5 py-0.5 rounded">
                                        {{ $p->discount_amount > 0 ? 'Potongan Rp ' . number_format($p->discount_amount, 0, ',', '.') : 'Diskon ' . $p->discount_percentage . '%' }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3.5">
                        @foreach ($services as $srv)
                            @php
                                $price = (float)($srv->price ?? $srv->cost ?? 3500000);
                            @endphp
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 hover:border-sky-300 hover:shadow-md transition-all flex flex-col justify-between">
                                <div>
                                    <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center font-bold mb-2.5">
                                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                                    </div>
                                    <h4 class="font-bold text-corporate text-xs mb-1 line-clamp-2">{{ $srv->name }}</h4>
                                    <p class="text-[11px] text-slate-500 line-clamp-2 mb-2.5">{{ $srv->description }}</p>
                                </div>
                                <div>
                                    <div class="pt-2.5 border-t border-slate-200/60 flex items-center justify-between text-xs">
                                        <span class="text-[10px] text-slate-400 flex items-center gap-1">
                                            <i data-lucide="clock" class="w-3 h-3"></i> {{ $srv->timeline_estimation ?? $srv->estimation_time ?? '30 Hari' }}
                                        </span>
                                        <span class="font-bold text-corporate text-xs">
                                            Rp {{ number_format($price, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <button onclick="openCheckoutModal({{ json_encode($srv) }})" class="mt-2.5 w-full py-1.5 bg-corporate hover:bg-slate-800 text-white rounded-lg text-xs font-bold flex items-center justify-center gap-1.5 transition-all shadow-sm">
                                        <i data-lucide="shopping-cart" class="w-3.5 h-3.5 text-sky-400"></i>
                                        <span>Pesan Layanan</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- 1. Modal Pusat Bantuan & Panduan Pengguna (FAQ Interaktif) -->
    <div id="helpCenterModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        <i data-lucide="help-circle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-corporate text-base">Pusat Bantuan &amp; Panduan Pengguna</h3>
                        <p class="text-xs text-slate-400">FAQ Regulasi Kemenkes, BPOM, Halal BPJPH &amp; Petunjuk Portal</p>
                    </div>
                </div>
                <button onclick="document.getElementById('helpCenterModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Search FAQ -->
            <div class="mb-4 shrink-0">
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" id="faqSearchInput" onkeyup="filterFaq()" placeholder="Cari pertanyaan, regulasi, alur izin..." class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-sky-500">
                </div>
            </div>

            <!-- Scrollable Content FAQ -->
            <div class="flex-1 overflow-y-auto space-y-3 pr-1 sidebar-scroll text-xs">
                
                <!-- Alur Step by Step -->
                <div class="p-4 rounded-2xl bg-sky-50 border border-sky-100 mb-4">
                    <h4 class="font-bold text-sky-900 text-xs mb-2 flex items-center gap-1.5">
                        <i data-lucide="git-merge" class="w-4 h-4 text-sky-600"></i> Alur Pengajuan Layanan Perizinan:
                    </h4>
                    <ol class="list-decimal list-inside space-y-1 text-sky-800 text-[11px] leading-relaxed">
                        <li><strong>Pilih Layanan:</strong> Tentukan jenis izin edar/sertifikasi &amp; masukkan kupon promo.</li>
                        <li><strong>Unggah Dokumen Teknis:</strong> Masukkan berkas persyaratan perizinan sesuai SOP kementerian.</li>
                        <li><strong>Kajian Teknis:</strong> Tim Legal &amp; Regulatory Specialist memeriksa kepatuhan berkas.</li>
                        <li><strong>Verifikasi Pembayaran:</strong> Tim Finance mengonfirmasi mutasi &amp; mencetak invoice.</li>
                        <li><strong>Penerbitan Izin Resmi:</strong> Surat Keputusan / Sertifikat resmi diterbitkan &amp; diarsipkan.</li>
                    </ol>
                </div>

                <!-- FAQ Items -->
                <div class="faq-item p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                    <button class="w-full font-bold text-left text-corporate flex justify-between items-center" onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <span>Berapa lama proses verifikasi berkas teknis oleh Tim Legal?</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                    </button>
                    <p class="text-slate-600 mt-2 leading-relaxed hidden">
                        Tim Legal &amp; Regulatory Specialist CV Prasta Solusi Indonesia melakukan telaah kepatuhan dokumen dalam waktu maksimal 1x24 jam kerja setelah berkas diunggah secara lengkap.
                    </p>
                </div>

                <div class="faq-item p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                    <button class="w-full font-bold text-left text-corporate flex justify-between items-center" onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <span>Bagaimana cara menggunakan kode kupon atau promo diskon?</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                    </button>
                    <p class="text-slate-600 mt-2 leading-relaxed hidden">
                        Pada bagian Katalog Layanan, klik tombol "Pesan Layanan", masukkan kode kupon aktif (misal: PRASMAS25 atau IZINHEMAT), lalu klik "Terapkan". Potongan harga akan otomatis mengurangi total tagihan.
                    </p>
                </div>

                <div class="faq-item p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                    <button class="w-full font-bold text-left text-corporate flex justify-between items-center" onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <span>Apakah sistem IzinYou mendukung integrasi WhatsApp untuk notifikasi?</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                    </button>
                    <p class="text-slate-600 mt-2 leading-relaxed hidden">
                        Ya. Sistem dilengkapi layanan WhatsApp Gateway resmi untuk pengiriman token OTP 2 Langkah saat login/registrasi klien baru, serta notifikasi update tahapan pengerjaan perizinan.
                    </p>
                </div>

                <div class="faq-item p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                    <button class="w-full font-bold text-left text-corporate flex justify-between items-center" onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <span>Apa saja standar regulasi yang diterapkan oleh CV Prasta Solusi Indonesia?</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                    </button>
                    <p class="text-slate-600 mt-2 leading-relaxed hidden">
                        Kami mengacu pada regulasi resmi Kementerian Kesehatan RI (Permenkes 62/2017 tentang Izin Edar Alkes &amp; PKRT), BPOM RI (Standar CPKB &amp; CDOB), serta BPJPH Kementerian Agama (Sistem Jaminan Produk Halal - SJPH).
                    </p>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs shrink-0">
                <span class="text-slate-400">Butuh bantuan konsultasi khusus?</span>
                <a href="#konsultasi" onclick="document.getElementById('helpCenterModal').classList.add('hidden')" class="font-bold text-sky-600 hover:underline">
                    Kirim Pertanyaan ke Konsultan &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Modal Pemesanan Layanan & Input Kupon Promo -->
    <div id="checkoutModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-corporate text-sky-400 flex items-center justify-center font-bold">
                        <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-corporate text-base" id="checkoutServiceName">Pemesanan Layanan</h3>
                        <p class="text-xs text-slate-400">Layanan Perizinan &amp; Sertifikasi Resmi</p>
                    </div>
                </div>
                <button onclick="document.getElementById('checkoutModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('klien.order.checkout') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="service_id" id="checkoutServiceId">

                <!-- Detail Biaya Normal -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/60 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Biaya Layanan Normal:</span>
                        <strong class="text-corporate" id="checkoutNormalPrice">Rp 0</strong>
                    </div>
                    <div class="flex justify-between text-sky-600 font-semibold" id="discountRow" style="display: none;">
                        <span>Potongan Kupon Promo:</span>
                        <span id="checkoutDiscountAmount">- Rp 0</span>
                    </div>
                    <div class="pt-2 border-t border-slate-200 flex justify-between text-sm font-extrabold text-corporate">
                        <span>Total Pembayaran:</span>
                        <span class="text-sky-600" id="checkoutFinalPrice">Rp 0</span>
                    </div>
                </div>

                <!-- Input Kupon Diskon -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                        <span>Kode Kupon / Promo Diskon:</span>
                        <span class="text-[10px] text-amber-600 font-bold bg-amber-50 px-2 py-0.5 rounded">Opsional</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <i data-lucide="tag" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input type="text" name="promo_code" id="checkoutPromoInput" placeholder="Contoh: PRASMAS25" class="w-full pl-9 pr-3 py-2 text-xs font-mono uppercase bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-sky-500">
                        </div>
                        <button type="button" onclick="calculateCheckoutDiscount()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition-all">
                            Terapkan
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1" id="couponFeedback">Masukkan kode kupon yang valid untuk mendapatkan diskon langsung.</p>
                </div>

                <!-- Quick Click Coupon Badges -->
                <div class="pt-1">
                    <span class="text-[11px] font-bold text-slate-500 block mb-1.5">Pilihan Kupon Cepat:</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($promos as $promo)
                            <button type="button" onclick="quickApplyCoupon('{{ $promo->code }}', {{ (float)$promo->discount_amount }}, {{ (float)($promo->discount_percentage ?? 0) }})" class="text-xs font-mono font-bold bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 px-2.5 py-1 rounded-lg transition-all">
                                {{ $promo->code }} ({{ $promo->discount_amount > 0 ? '-Rp ' . number_format($promo->discount_amount, 0, ',', '.') : '-' . $promo->discount_percentage . '%' }})
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('checkoutModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-500">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i> Konfirmasi &amp; Ajukan Projek
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. Modal Indikator Verifikasi 2 Langkah (WhatsApp OTP) -->
    <div id="twoFactorModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative text-center">
            <button onclick="document.getElementById('twoFactorModal').classList.add('hidden')" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>

            <div class="w-16 h-16 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center mx-auto mb-4 border border-sky-200 shadow-sm">
                <i data-lucide="shield-check" class="w-8 h-8"></i>
            </div>

            <h3 class="text-lg font-bold text-corporate mb-1">Verifikasi 2-Langkah WhatsApp</h3>
            <p class="text-xs font-semibold text-sky-600 bg-sky-50 py-1 px-3 rounded-full inline-block mb-4 border border-sky-100">
                Status: Aktif &amp; Terlindungi
            </p>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/60 text-left text-xs space-y-2 mb-5">
                <div class="flex justify-between text-slate-600">
                    <span>Nomor WhatsApp Terhubung:</span>
                    <strong class="text-corporate font-mono">{{ $user->phone ?? '08xxxxxxxxxx' }}</strong>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Metode Otentikasi:</span>
                    <strong class="text-corporate">One-Time Password (OTP) 6 Digit</strong>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Enkripsi Sesi:</span>
                    <strong class="text-sky-700">SHA-256 Protected</strong>
                </div>
            </div>

            <p class="text-xs text-slate-500 leading-relaxed mb-6">
                Setiap login dari perangkat baru atau perubahan dokumen penting diproteksi menggunakan token verifikasi WhatsApp otomatis untuk mencegah akses tanpa izin.
            </p>

            <button onclick="document.getElementById('twoFactorModal').classList.add('hidden')" class="w-full py-2.5 bg-corporate hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-md transition-all">
                Mengerti &amp; Tutup
            </button>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Data Kupon & Layanan untuk Checkout
        let currentServicePrice = 0;
        const availablePromos = @json($promos);

        function openCheckoutModal(service) {
            currentServicePrice = parseFloat(service.price || service.cost || 3500000);
            document.getElementById('checkoutServiceId').value = service.id;
            document.getElementById('checkoutServiceName').innerText = service.name;
            document.getElementById('checkoutNormalPrice').innerText = 'Rp ' + currentServicePrice.toLocaleString('id-ID');
            document.getElementById('checkoutFinalPrice').innerText = 'Rp ' + currentServicePrice.toLocaleString('id-ID');
            document.getElementById('checkoutPromoInput').value = '';
            document.getElementById('discountRow').style.display = 'none';
            document.getElementById('couponFeedback').innerText = 'Masukkan kode kupon yang valid untuk mendapatkan diskon langsung.';
            document.getElementById('couponFeedback').className = 'text-[10px] text-slate-400 mt-1';
            document.getElementById('checkoutModal').classList.remove('hidden');
        }

        function selectPromoCode(code) {
            // Jika modal belum buka, buka layanan pertama
            const firstService = @json($services->first());
            if (firstService) {
                openCheckoutModal(firstService);
                document.getElementById('checkoutPromoInput').value = code;
                calculateCheckoutDiscount();
            }
        }

        function quickApplyCoupon(code, amount, percentage) {
            document.getElementById('checkoutPromoInput').value = code;
            calculateCheckoutDiscount();
        }

        function calculateCheckoutDiscount() {
            const inputCode = document.getElementById('checkoutPromoInput').value.trim().toUpperCase();
            const promo = availablePromos.find(p => p.code.toUpperCase() === inputCode);

            if (promo) {
                let discount = 0;
                if (parseFloat(promo.discount_amount) > 0) {
                    discount = parseFloat(promo.discount_amount);
                } else if (parseFloat(promo.discount_percentage) > 0) {
                    discount = currentServicePrice * (parseFloat(promo.discount_percentage) / 100);
                }

                const finalPrice = Math.max(0, currentServicePrice - discount);

                document.getElementById('discountRow').style.display = 'flex';
                document.getElementById('checkoutDiscountAmount').innerText = '- Rp ' + discount.toLocaleString('id-ID');
                document.getElementById('checkoutFinalPrice').innerText = 'Rp ' + finalPrice.toLocaleString('id-ID');
                document.getElementById('couponFeedback').innerText = 'Kupon ' + promo.code + ' berhasil diterapkan!';
                document.getElementById('couponFeedback').className = 'text-[10px] text-sky-600 font-bold mt-1';
            } else if (inputCode !== '') {
                document.getElementById('discountRow').style.display = 'none';
                document.getElementById('checkoutFinalPrice').innerText = 'Rp ' + currentServicePrice.toLocaleString('id-ID');
                document.getElementById('couponFeedback').innerText = 'Kode kupon tidak valid atau kuota habis.';
                document.getElementById('couponFeedback').className = 'text-[10px] text-rose-500 font-bold mt-1';
            }
        }

        function filterFaq() {
            const input = document.getElementById('faqSearchInput').value.toLowerCase();
            const items = document.querySelectorAll('.faq-item');
            items.forEach(item => {
                const text = item.innerText.toLowerCase();
                item.style.display = text.includes(input) ? 'block' : 'none';
            });
        }
    </script>
</body>
</html>

