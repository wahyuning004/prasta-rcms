<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $service->name }} - Detail Layanan | IzinYou</title>
    <meta name="description" content="Persiapan, dasar hukum, persyaratan dokumen lengkap, estimasi biaya dan alur pengurusan {{ $service->name }} oleh CV Prasta Solusi Indonesia.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .text-corporate { color: #0f172a; }
        .bg-corporate { background-color: #0f172a; }
        .text-gold { color: #d4af37; }
        .bg-gold { background-color: #d4af37; }
        .glass-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        .animate-float { animation: float 3s ease-in-out infinite; }
        .card-scrollbar::-webkit-scrollbar { width: 5px; }
        .card-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
        .card-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .card-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-emerald-500 selection:text-white">

    <!-- 1. Top Bar & Navbar -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo & Brand -->
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-3 cursor-pointer">
                    <div class="w-10 h-10 rounded-xl bg-corporate flex items-center justify-center text-white shadow-md border border-slate-700/60 flex-shrink-0">
                        <svg class="w-6 h-6 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L3 7v6c0 5.5 3.8 10.7 9 12 5.2-1.3 9-6.5 9-12V7l-9-5z"/>
                            <path d="m9 12 2 2 4-4" stroke="#d4af37" stroke-width="2.5"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="font-extrabold text-xl leading-tight text-corporate tracking-tight flex items-center gap-1.5">
                            IzinYou
                            <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded-full">.com</span>
                        </h1>
                        <p class="text-[10px] text-slate-500 font-medium">by CV Prasta Solusi Indonesia</p>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex space-x-7 items-center">
                    <a href="{{ route('home') }}#beranda" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Beranda</a>
                    <a href="{{ route('home') }}#tentang" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Tentang Kami</a>

                    <!-- Dropdown Layanan (Mega Menu: Semua Layanan) -->
                    <div class="relative group" id="navLayananDropdown">
                        <button type="button" class="text-sm font-bold text-emerald-600 group-hover:text-emerald-500 transition-colors flex items-center gap-1.5 py-2">
                            <span>Layanan</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200 group-hover:rotate-180"></i>
                        </button>

                        <!-- Mega Menu Panel -->
                        <div class="absolute left-1/2 -translate-x-[35%] top-full pt-2 w-[860px] lg:w-[920px] max-w-[94vw] hidden group-hover:block z-50 transition-all duration-200">
                            <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 p-7 lg:p-8">
                                <!-- Top Bar & Counter -->
                                <div class="flex items-center justify-between pb-3.5 mb-5 border-b border-slate-100">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Semua Layanan Perizinan &amp; Sertifikasi</span>
                                    </div>
                                    <span class="text-[11px] font-semibold text-slate-400">
                                        {{ $relatedServices->count() + 1 }}+ Layanan Resmi Tersedia
                                    </span>
                                </div>

                                @php
                                    $allSvcs = \App\Models\Service::with('category')->orderBy('name')->get();
                                    $totalSvc2 = $allSvcs->count();
                                    $perCol2 = $totalSvc2 > 0 ? (int) ceil($totalSvc2 / 3) : 1;
                                    $serviceCols2 = $allSvcs->chunk($perCol2);
                                @endphp

                                <!-- 3 Columns Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-1">
                                    @foreach($serviceCols2 as $columnSvcs)
                                    <div class="space-y-1">
                                        @foreach($columnSvcs as $sv)
                                        <a href="{{ route('services.show', $sv->slug) }}" class="group/item flex items-center gap-2 py-1.5 {{ $sv->id === $service->id ? 'text-emerald-600 font-semibold' : 'text-slate-700 hover:text-emerald-600' }} transition-colors duration-150">
                                            <span class="text-sm font-medium leading-snug transition-colors">
                                                {{ $sv->name }}
                                            </span>
                                        </a>
                                        @endforeach
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Footer Info -->
                                <div class="mt-6 pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                    <div class="flex items-center gap-1.5">
                                        <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                                        <span>Klik langsung layanan untuk melihat rincian biaya, syarat berkas &amp; alur perizinan &amp; sertifikasi.</span>
                                    </div>
                                    <a href="https://wa.me/6282128765874?text=Halo%20IzinYou,%20saya%20ingin%20konsultasi%20layanan%20perizinan..." target="_blank" class="font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                                        <span>Konsultasi Cepat WA</span>
                                        <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('home') }}#testimoni" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Penilaian Klien</a>
                    <a href="{{ route('home') }}#kontak" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Kontak</a>

                    <div class="h-6 w-px bg-slate-300"></div>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-500 px-5 py-2.5 rounded-full transition-colors flex items-center gap-2 shadow-md">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard Saya
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-white bg-corporate hover:bg-emerald-600 px-5 py-2.5 rounded-full transition-colors flex items-center gap-2 shadow-md">
                            <i data-lucide="log-in" class="w-4 h-4"></i> Login Sistem
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="lg:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-slate-600 hover:text-corporate p-2">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu Dropdown -->
            <div id="mobile-menu" class="hidden lg:hidden bg-white border-t border-slate-100 shadow-lg absolute w-full left-0 top-20 flex-col py-4 px-6 space-y-3 max-h-[85vh] overflow-y-auto">
                <a href="{{ route('home') }}#beranda" class="block text-sm font-semibold text-slate-900 hover:text-emerald-600 py-2">Beranda</a>
                <a href="{{ route('home') }}#tentang" class="block text-sm font-semibold text-slate-600 hover:text-emerald-600 py-2">Tentang Kami</a>

                <!-- Mobile Layanan Submenu -->
                <div>
                    <button type="button" onclick="document.getElementById('mobileSubmenuLayanan').classList.toggle('hidden')" class="w-full flex items-center justify-between text-sm font-bold text-emerald-600 py-2">
                        <span>Layanan (Semua Layanan)</span>
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </button>
                    <div id="mobileSubmenuLayanan" class="pl-2 py-2 space-y-1 bg-slate-50 rounded-2xl mt-1 hidden max-h-72 overflow-y-auto">
                        @php $allSvcsMobile = \App\Models\Service::orderBy('name')->get(); @endphp
                        @foreach($allSvcsMobile as $sv)
                            <a href="{{ route('services.show', $sv->slug) }}" class="flex items-center justify-between py-2 px-3 rounded-xl text-xs font-medium {{ $sv->id === $service->id ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-700 hover:text-emerald-600 hover:bg-emerald-50' }} transition">
                                <span class="truncate">{{ $sv->name }}</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-slate-400 shrink-0 ml-2"></i>
                            </a>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('home') }}#testimoni" class="block text-sm font-semibold text-slate-600 hover:text-emerald-600 py-2">Penilaian Klien</a>
                <a href="{{ route('home') }}#kontak" class="block text-sm font-semibold text-slate-600 hover:text-emerald-600 py-2">Kontak</a>
                <div class="h-px w-full bg-slate-200 my-2"></div>
                @auth
                    <a href="{{ route('dashboard') }}" class="block w-full text-center text-sm font-bold text-white bg-emerald-600 px-5 py-3 rounded-xl shadow-md">Dashboard Saya</a>
                @else
                    <a href="{{ route('login') }}" class="block w-full text-center text-sm font-bold text-white bg-corporate px-5 py-3 rounded-xl shadow-md">Login Sistem</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- 2. Breadcrumbs & Header Section -->
    <section class="pt-28 pb-12 lg:pt-36 lg:pb-16 bg-gradient-to-b from-slate-900 via-corporate to-slate-900 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-800 via-transparent to-transparent opacity-50"></div>
        <div class="absolute -right-20 -top-20 w-96 h-96 bg-emerald-600/20 rounded-full blur-3xl"></div>
        <div class="absolute -left-20 -bottom-20 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-2 text-xs text-slate-400 mb-6">
                <a href="{{ route('home') }}" class="hover:text-emerald-400 transition-colors">Beranda</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i>
                <a href="{{ route('home') }}#layanan" class="hover:text-emerald-400 transition-colors">Layanan</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i>
                <span class="text-emerald-400 font-semibold truncate max-w-xs">{{ $service->name }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <!-- Judul & Kategori -->
                <div class="lg:col-span-8">
                    <div class="flex flex-wrap items-center gap-2.5 mb-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                            {{ $service->category ? $service->category->name : 'Perizinan & Regulasi' }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-400"></i>
                            Pendampingan Konsultan Resmi
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black text-white leading-tight mb-4 tracking-tight">
                        {{ $service->name }}
                    </h1>

                    <p class="text-slate-300 text-sm sm:text-base leading-relaxed max-w-3xl mb-8">
                        {{ $service->description ?: 'Layanan konsultasi dan pendampingan menyeluruh oleh tim Regulatory Specialist CV Prasta Solusi Indonesia mulai dari penyiapan berkas teknis, uji laboratorium, hingga izin resmi terbit.' }}
                    </p>

                    <!-- Timeline Stepper Banner (Alur Singkat) -->
                    <div class="bg-slate-800/80 backdrop-blur border border-slate-700/80 rounded-2xl p-4 sm:p-5">
                        <div class="flex items-center justify-between mb-3 text-xs font-bold text-slate-300">
                            <span class="flex items-center gap-1.5 uppercase tracking-wider text-emerald-400 text-[11px]">
                                <i data-lucide="git-commit" class="w-4 h-4"></i>
                                Alur Tahapan Pengerjaan Proyek:
                            </span>
                            <span class="text-slate-400">Estimasi Selesai: <strong class="text-white">{{ $service->timeline_estimation ?: ($service->estimation_time ?: '1-3 Bulan') }}</strong></span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 text-center">
                            <div class="bg-slate-900/90 rounded-xl p-2.5 border border-slate-700">
                                <span class="text-[10px] font-bold text-emerald-400 block mb-0.5">TAHAP 1</span>
                                <p class="text-xs font-semibold text-white truncate">Audit Berkas</p>
                            </div>
                            <div class="bg-slate-900/90 rounded-xl p-2.5 border border-slate-700">
                                <span class="text-[10px] font-bold text-emerald-400 block mb-0.5">TAHAP 2</span>
                                <p class="text-xs font-semibold text-white truncate">Draf Dokumen SOP</p>
                            </div>
                            <div class="bg-slate-900/90 rounded-xl p-2.5 border border-slate-700">
                                <span class="text-[10px] font-bold text-emerald-400 block mb-0.5">TAHAP 3</span>
                                <p class="text-xs font-semibold text-white truncate">Submit &amp; Sidang</p>
                            </div>
                            <div class="bg-slate-900/90 rounded-xl p-2.5 border border-emerald-500/40 bg-emerald-950/40">
                                <span class="text-[10px] font-bold text-emerald-300 block mb-0.5">TAHAP 4</span>
                                <p class="text-xs font-bold text-emerald-300 truncate">Sertifikat Terbit</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Header Side Card: Estimasi Biaya & CTA Card -->
                <div class="lg:col-span-4">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 text-slate-800 shadow-2xl border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Estimasi Nilai Investasi Jasa:</span>
                        <div class="flex items-baseline gap-2 mb-4">
                            <h2 class="text-3xl sm:text-4xl font-extrabold text-corporate tracking-tight">
                                {{ $service->formatted_price ?: ('Rp ' . number_format($service->price ?: $service->cost ?: 0, 0, ',', '.')) }}
                            </h2>
                        </div>
                        <p class="text-xs text-slate-500 mb-6 leading-relaxed">
                            Sudah termasuk pendampingan teknis, revisi perbaikan audit, serta penjaminan mutu kepatuhan regulasi resmi.
                        </p>

                        <!-- CTA Actions -->
                        <div class="space-y-3">
                            @auth
                                <a href="{{ route('dashboard') }}" class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center gap-2">
                                    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                                    Pesan Layanan di Portal
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center gap-2">
                                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                                    Daftar Akun &amp; Order
                                </a>
                            @endauth

                            <a href="https://wa.me/6282128765874?text={{ rawurlencode('Halo Konsultan IzinYou, saya ingin berkonsultasi mengenai persiapan izin: ' . $service->name) }}" target="_blank" class="w-full py-3.5 px-4 bg-slate-100 hover:bg-slate-200 text-corporate font-bold text-sm rounded-xl transition-all flex items-center justify-center gap-2">
                                <i data-lucide="message-circle" class="w-4 h-4 text-emerald-600"></i>
                                Konsultasi Cepat via WhatsApp
                            </a>
                        </div>

                        <!-- Trust Guarantees -->
                        <div class="mt-6 pt-6 border-t border-slate-100 space-y-2 text-xs text-slate-600">
                            <div class="flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                                <span>Perjanjian Kerjasama (SPK) &amp; Invoice Resmi</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                                <span>Garansi Pendampingan hingga Terbit</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                                <span>Kerahasiaan Dokumen Terjamin (NDA)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Alert Success (jika ada kiriman konsultasi dokumen) -->
    @if(session('success'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 text-emerald-800 text-sm font-semibold">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            {{ session('success') }}
        </div>
    </div>
    @endif

    <!-- 4. Main Content: Tab Navigasi Konten Sesuai KAK -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <!-- Tab Navigation Bar -->
        <div class="bg-white rounded-2xl p-2 shadow-sm border border-slate-200 mb-8 overflow-x-auto">
            <div class="flex items-center space-x-2 min-w-max">
                <button type="button" onclick="switchTab('tab-desc')" class="tab-btn active px-5 py-3 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2 bg-emerald-600 text-white shadow-sm" data-target="tab-desc">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span>Pengantar &amp; Deskripsi</span>
                </button>

                <button type="button" onclick="switchTab('tab-law')" class="tab-btn px-5 py-3 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2 bg-transparent text-slate-600 hover:text-emerald-600 hover:bg-slate-50" data-target="tab-law">
                    <i data-lucide="scale" class="w-4 h-4"></i>
                    <span>Dasar Hukum &amp; Instansi</span>
                </button>

                <button type="button" onclick="switchTab('tab-docs')" class="tab-btn px-5 py-3 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2 bg-transparent text-slate-600 hover:text-emerald-600 hover:bg-slate-50 relative" data-target="tab-docs">
                    <i data-lucide="folder-check" class="w-4 h-4"></i>
                    <span>Persyaratan Dokumen Lengkap</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500 absolute top-2 right-2"></span>
                </button>

                <button type="button" onclick="switchTab('tab-flow')" class="tab-btn px-5 py-3 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2 bg-transparent text-slate-600 hover:text-emerald-600 hover:bg-slate-50" data-target="tab-flow">
                    <i data-lucide="git-merge" class="w-4 h-4"></i>
                    <span>Alur Tahapan Pengerjaan</span>
                </button>

                <button type="button" onclick="switchTab('tab-pricing')" class="tab-btn px-5 py-3 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2 bg-transparent text-slate-600 hover:text-emerald-600 hover:bg-slate-50" data-target="tab-pricing">
                    <i data-lucide="wallet" class="w-4 h-4"></i>
                    <span>Pricing &amp; Retribusi</span>
                </button>

                <button type="button" onclick="switchTab('tab-faq')" class="tab-btn px-5 py-3 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2 bg-transparent text-slate-600 hover:text-emerald-600 hover:bg-slate-50" data-target="tab-faq">
                    <i data-lucide="help-circle" class="w-4 h-4"></i>
                    <span>FAQ &amp; Kendala</span>
                </button>
            </div>
        </div>

        <!-- Tab Content Containers -->
        <div class="space-y-8">

            <!-- TAB 1: Pengantar & Deskripsi -->
            <div id="tab-desc" class="tab-pane space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200">
                    <h3 class="text-xl sm:text-2xl font-extrabold text-corporate mb-4 flex items-center gap-3">
                        <i data-lucide="book-open" class="w-6 h-6 text-emerald-600"></i>
                        Cakupan &amp; Urgensi Perizinan &amp; Sertifikasi Layanan
                    </h3>
                    <div class="prose max-w-none text-slate-600 text-sm leading-relaxed space-y-4">
                        <p>
                            Layanan <strong>{{ $service->name }}</strong> merupakan program pendampingan komprehensif dari CV Prasta Solusi Indonesia untuk memastikan seluruh operasional, sarana produksi, dan distribusi perusahaan Anda memenuhi ketentuan hukum serta standar mutu instansi pemerintah terkait di Republik Indonesia.
                        </p>
                        <p>
                            {{ $service->description ?: 'Melalui layanan ini, tim konsultan teknis kami akan mendampingi pelaku usaha dari tahap pra-audit kelayakan dokumen, penyiapan draf SOP teknis, perbaikan ketidaksesuaian sarana penyimpanan/pabrik, hingga terbitnya sertifikat atau izin edar resmi dari Kementerian/Lembaga terkait.' }}
                        </p>
                    </div>

                    <!-- Benefit Points -->
                    <div class="mt-8 pt-8 border-t border-slate-100">
                        <h4 class="text-sm font-bold text-corporate uppercase tracking-wider mb-4">Keuntungan &amp; Nilai Tambah Bagi Perusahaan:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <h5 class="text-xs font-bold text-corporate mb-1">Kepastian Hukum</h5>
                                    <p class="text-xs text-slate-500">Bebas dari sanksi administratif dan penutupan sarana oleh dinas pengawas.</p>
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <h5 class="text-xs font-bold text-corporate mb-1">Akses Pengadaan &amp; Tender</h5>
                                    <p class="text-xs text-slate-500">Memenuhi syarat mutlak e-Katalog LKPP, tender BUMN, dan jaringan ritel modern.</p>
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                    <i data-lucide="award" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <h5 class="text-xs font-bold text-corporate mb-1">Reputasi &amp; Kredibilitas</h5>
                                    <p class="text-xs text-slate-500">Meningkatkan kepercayaan konsumen dan mitra investor nasional maupun global.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Dasar Hukum & Standar Instansi -->
            <div id="tab-law" class="tab-pane hidden space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200">
                    <h3 class="text-xl sm:text-2xl font-extrabold text-corporate mb-6 flex items-center gap-3">
                        <i data-lucide="scale" class="w-6 h-6 text-emerald-600"></i>
                        Dasar Regulasi &amp; Instansi Penerbit Resmi
                    </h3>

                    @php
                        $nameLow = strtolower($service->name);
                        $regulations = [
                            'Undang-Undang Nomor 6 Tahun 2023 tentang Penetapan Perpu Cipta Kerja Menjadi Undang-Undang',
                            'Peraturan Pemerintah Nomor 5 Tahun 2021 tentang Penyelenggaraan Perizinan Berusaha Berbasis Risiko (OSS RBA)',
                        ];
                        $authority = 'Kementerian Kesehatan RI & Lembaga OSS';

                        if (str_contains($nameLow, 'bpom') || str_contains($nameLow, 'kosmetik') || str_contains($nameLow, 'smkpo')) {
                            $authority = 'Badan Pengawas Obat dan Makanan (BPOM RI)';
                            $regulations[] = 'Peraturan BPOM Nomor 12 Tahun 2020 tentang Tata Cara Pengajuan Notifikasi Kosmetika';
                            $regulations[] = 'Peraturan BPOM Nomor 27 Tahun 2017 tentang Pendaftaran Pangan Olahan';
                            $regulations[] = 'Peraturan BPOM Nomor 10 Tahun 2021 tentang Standar Kegiatan Usaha & Produk Sektor Obat dan Makanan';
                        } elseif (str_contains($nameLow, 'halal')) {
                            $authority = 'Badan Penyelenggara Jaminan Produk Halal (BPJPH Kemenag) & Komisi Fatwa MUI';
                            $regulations[] = 'Undang-Undang Nomor 33 Tahun 2014 tentang Jaminan Produk Halal (JPH)';
                            $regulations[] = 'Peraturan Pemerintah Nomor 39 Tahun 2021 tentang Penyelenggaraan Bidang Jaminan Produk Halal';
                            $regulations[] = 'Keputusan Kepala BPJPH Nomor 57 Tahun 2021 tentang Kriteria Sistem Jaminan Produk Halal (SJPH)';
                        } elseif (str_contains($nameLow, 'sni')) {
                            $authority = 'Badan Standardisasi Nasional (BSN) & Lembaga Sertifikasi Produk (LSPro KAN)';
                            $regulations[] = 'Undang-Undang Nomor 20 Tahun 2014 tentang Standardisasi dan Penilaian Kesesuaian';
                            $regulations[] = 'Peraturan BSN Nomor 2 Tahun 2017 tentang Tata Cara Penggunaan Tanda SNI dan Tanda Kesesuaian';
                        } elseif (str_contains($nameLow, 'cdob') || str_contains($nameLow, 'pbf')) {
                            $authority = 'Badan Pengawas Obat dan Makanan (BPOM RI) & Direktorat Pengawasan Distribusi';
                            $regulations[] = 'Peraturan BPOM Nomor 6 Tahun 2020 tentang Pedoman Teknis Cara Distribusi Obat yang Baik (CDOB)';
                            $regulations[] = 'Peraturan Menteri Kesehatan Nomor 1148/MENKES/PER/VI/2011 tentang Pedagang Besar Farmasi (PBF)';
                        } elseif (str_contains($nameLow, 'idak') || str_contains($nameLow, 'alkes') || str_contains($nameLow, 'cdakb')) {
                            $authority = 'Kementerian Kesehatan RI (Ditjen Farmalkes) & OSS BKPM';
                            $regulations[] = 'Peraturan Menteri Kesehatan Nomor 62 Tahun 2017 tentang Izin Edar Alat Kesehatan, Alat Kesehatan Diagnostik In Vitro dan PKRT';
                            $regulations[] = 'Peraturan Menteri Kesehatan Nomor 14 Tahun 2021 tentang Standar Kegiatan Usaha Sektor Kesehatan';
                            $regulations[] = 'Permenkes Nomor 4 Tahun 2014 tentang Cara Distribusi Alat Kesehatan yang Baik (CDAKB)';
                        }
                    @endphp

                    <!-- Instansi Penerbit Box -->
                    <div class="p-5 rounded-2xl bg-emerald-50/70 border border-emerald-200 flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
                            <i data-lucide="landmark" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider block">Instansi / Kementerian Penerbit Resmi:</span>
                            <h4 class="text-base sm:text-lg font-extrabold text-corporate">{{ $authority }}</h4>
                        </div>
                    </div>

                    <!-- List Regulasi -->
                    <div class="space-y-3">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-2">Daftar Peraturan &amp; Landasan Yuridis:</span>
                        @foreach($regulations as $reg)
                        <div class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs sm:text-sm text-slate-700">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0"></i>
                            <span class="leading-relaxed">{{ $reg }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- TAB 3: Persyaratan Dokumen Lengkap & FITUR KONSULTASI PERSYARATAN -->
            <div id="tab-docs" class="tab-pane hidden space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-6 border-b border-slate-100">
                        <div>
                            <h3 class="text-xl sm:text-2xl font-extrabold text-corporate flex items-center gap-3">
                                <i data-lucide="folder-check" class="w-6 h-6 text-emerald-600"></i>
                                Persyaratan Berkas &amp; Dokumen Lengkap
                            </h3>
                            <p class="text-xs text-slate-500 mt-1">Checklist seluruh berkas administratif, teknis, dan sarana yang wajib disiapkan.</p>
                        </div>
                        <button type="button" onclick="openDocConsultModal()" class="px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs rounded-xl border border-emerald-300 transition flex items-center gap-1.5 shrink-0">
                            <i data-lucide="help-circle" class="w-4 h-4"></i>
                            Tanya Konsultan Dokumen
                        </button>
                    </div>

                    @php
                        $rawDocs = $service->requirements_info ?: $service->requirements_summary;
                        $docLines = array_filter(array_map('trim', explode("\n", $rawDocs ?: '')));
                        if(empty($docLines)) {
                            $docLines = [
                                'Akta Pendirian & Akta Perubahan Terakhir (SK Kemenkumham)',
                                'NIB (Nomor Induk Berusaha) OSS RBA berbasis risiko',
                                'NPWP Perusahaan & KTP Penanggung Jawab / Direktur',
                                'Bukti Kepemilikan atau Surat Perjanjian Sewa Sarana & Gudang',
                                'Struktur Organisasi & Surat Penunjukan Penanggung Jawab Teknis (PJT)',
                                'Ijazah & Surat Tanda Registrasi Tenaga Teknis (STRA/STRTTK/SKK)',
                                'Denah / Layout Tata Ruang Fasilitas & Sarana Penyimpanan (Suhu Terkontrol)',
                                'Dokumen SOP Mutu (Penerimaan, Penyimpanan, Distribusi, Penanganan Keluhan)',
                                'Spesifikasi Teknis Produk, Certificate of Analysis (CoA) / Hasil Uji Lab',
                            ];
                        }
                    @endphp

                    <!-- 3 Kategori Dokumen Checklist -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Grup 1: Administrasi & Persyaratan Dasar Perusahaan -->
                        <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80">
                            <h4 class="text-xs font-bold text-corporate uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i data-lucide="building" class="w-4 h-4 text-emerald-600"></i> Berkas Administrasi Dasar
                            </h4>
                            <ul class="space-y-2.5 text-xs text-slate-700">
                                <li class="flex items-start gap-2.5">
                                    <div class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                                        <i data-lucide="check" class="w-2.5 h-2.5 stroke-[3]"></i>
                                    </div>
                                    <span>Akta Pendirian &amp; SK Kemenkumham</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <div class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                                        <i data-lucide="check" class="w-2.5 h-2.5 stroke-[3]"></i>
                                    </div>
                                    <span>NIB Berbasis Risiko (OSS RBA) dengan KBLI Sesuai</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <div class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                                        <i data-lucide="check" class="w-2.5 h-2.5 stroke-[3]"></i>
                                    </div>
                                    <span>NPWP Badan Usaha &amp; KTP Penanggung Jawab</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <div class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                                        <i data-lucide="check" class="w-2.5 h-2.5 stroke-[3]"></i>
                                    </div>
                                    <span>Bukti Kepemilikan / Sewa Gudang &amp; Kantor</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Grup 2: Dokumen Teknis & Sarana Khusus -->
                        <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80">
                            <h4 class="text-xs font-bold text-corporate uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i data-lucide="clipboard-list" class="w-4 h-4 text-emerald-600"></i> Berkas Teknis &amp; Spesifik
                            </h4>
                            <ul class="space-y-2.5 text-xs text-slate-700">
                                @foreach($docLines as $doc)
                                    @php $clean = ltrim($doc, "•- \t"); @endphp
                                    @if(!empty($clean))
                                    <li class="flex items-start gap-2.5">
                                        <div class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                                            <i data-lucide="check" class="w-2.5 h-2.5 stroke-[3]"></i>
                                        </div>
                                        <span>{{ $clean }}</span>
                                    </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- FITUR UTAMA KONSULTASI PERSYARATAN: Banner Menonjol Sesuai KAK -->
                    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-600 via-emerald-700 to-slate-900 p-8 sm:p-10 text-white shadow-xl">
                        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>
                        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                            <div class="max-w-2xl">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 text-white text-xs font-bold mb-3 backdrop-blur-sm">
                                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-300"></i>
                                    Solusi Kendala Berkas Klien
                                </div>
                                <h4 class="text-xl sm:text-2xl font-black text-white leading-tight mb-2">
                                    Ada Persyaratan yang Belum Lengkap / Kurang? Konsultasikan Saja!
                                </h4>
                                <p class="text-emerald-100 text-xs sm:text-sm leading-relaxed">
                                    Belum memiliki Penanggung Jawab Teknis (PJT), denah gudang belum standar suhu, atau SOP mutu belum tersusun? Tim Regulatory &amp; Legal CV Prasta Solusi Indonesia siap mendampingi penyesuaian dari awal hingga tuntas.
                                </p>
                            </div>
                            <div class="shrink-0 w-full md:w-auto">
                                <button type="button" onclick="openDocConsultModal()" class="w-full md:w-auto px-7 py-4 bg-white hover:bg-emerald-50 text-emerald-800 font-extrabold text-sm rounded-2xl shadow-xl transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                    <i data-lucide="help-circle" class="w-5 h-5 text-emerald-600"></i>
                                    <span>Konsultasikan Dokumen Sekarang</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: Alur Tahapan Pengerjaan -->
            <div id="tab-flow" class="tab-pane hidden space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200">
                    <h3 class="text-xl sm:text-2xl font-extrabold text-corporate mb-2 flex items-center gap-3">
                        <i data-lucide="git-merge" class="w-6 h-6 text-emerald-600"></i>
                        Alur &amp; Prosedur Tahapan Pengerjaan
                    </h3>
                    <p class="text-xs text-slate-500 mb-8">Pola pengerjaan terstruktur dengan pembagian tugas yang transparan antara tim konsultan dan klien.</p>

                    <!-- Stepper Timeline Cards -->
                    <div class="space-y-6 relative before:absolute before:inset-0 before:left-8 before:w-0.5 before:bg-slate-200 before:hidden sm:before:block">
                        <!-- Step 1 -->
                        <div class="relative flex items-start gap-4 sm:gap-6">
                            <div class="w-16 h-16 rounded-2xl bg-emerald-600 text-white flex flex-col items-center justify-center shrink-0 shadow-md font-bold text-xs z-10">
                                <span>TAHAP</span>
                                <span class="text-lg">01</span>
                            </div>
                            <div class="flex-1 bg-slate-50 p-5 rounded-2xl border border-slate-100">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="text-sm font-bold text-corporate">Audit Awal &amp; Gap Analysis Kelengkapan Berkas</h4>
                                    <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Hari ke 1 - 3</span>
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Pemeriksaan kesesuaian data NIB KBLI, akta, sarana operasional, serta kelayakan berkas awal untuk mencegah penolakan atau revisi di kemudian hari.
                                </p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="relative flex items-start gap-4 sm:gap-6">
                            <div class="w-16 h-16 rounded-2xl bg-corporate text-white flex flex-col items-center justify-center shrink-0 shadow-md font-bold text-xs z-10">
                                <span>TAHAP</span>
                                <span class="text-lg">02</span>
                            </div>
                            <div class="flex-1 bg-slate-50 p-5 rounded-2xl border border-slate-100">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="text-sm font-bold text-corporate">Penyusunan Draf SOP Teknis &amp; Manual Mutu</h4>
                                    <span class="text-[11px] font-semibold text-slate-600 bg-slate-200 px-2 py-0.5 rounded-full">Hari ke 4 - 10</span>
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Tim konsultan menyusun draf SOP operasional, denah sarana berstandar kementerian, dokumen Sistem Jaminan Mutu, serta validasi instrumen teknis.
                                </p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="relative flex items-start gap-4 sm:gap-6">
                            <div class="w-16 h-16 rounded-2xl bg-corporate text-white flex flex-col items-center justify-center shrink-0 shadow-md font-bold text-xs z-10">
                                <span>TAHAP</span>
                                <span class="text-lg">03</span>
                            </div>
                            <div class="flex-1 bg-slate-50 p-5 rounded-2xl border border-slate-100">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="text-sm font-bold text-corporate">Pendaftaran Akun &amp; Submit Berkas ke Portal Instansi Resmi</h4>
                                    <span class="text-[11px] font-semibold text-slate-600 bg-slate-200 px-2 py-0.5 rounded-full">Hari ke 11 - 15</span>
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Unggah berkas resmi ke portal Kemenkes / BPOM / BPJPH / OSS RBA, penerbitan Surat Perintah Bayar (SPB) retribusi PNBP, serta verifikasi kelengkapan akun resmi.
                                </p>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="relative flex items-start gap-4 sm:gap-6">
                            <div class="w-16 h-16 rounded-2xl bg-corporate text-white flex flex-col items-center justify-center shrink-0 shadow-md font-bold text-xs z-10">
                                <span>TAHAP</span>
                                <span class="text-lg">04</span>
                            </div>
                            <div class="flex-1 bg-slate-50 p-5 rounded-2xl border border-slate-100">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="text-sm font-bold text-corporate">Pendampingan Asesmen Teknis / Verifikasi Lapangan (Audit)</h4>
                                    <span class="text-[11px] font-semibold text-slate-600 bg-slate-200 px-2 py-0.5 rounded-full">Hari ke 16 - 25</span>
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Simulasi audit internal, pendampingan saat petugas dinas/balai melakukan inspeksi sarana atau audit LPH, serta penyelesaian Corrective Action (CAPA) jika ada catatan evaluasi.
                                </p>
                            </div>
                        </div>

                        <!-- Step 5 -->
                        <div class="relative flex items-start gap-4 sm:gap-6">
                            <div class="w-16 h-16 rounded-2xl bg-emerald-500 text-white flex flex-col items-center justify-center shrink-0 shadow-md font-bold text-xs z-10 ring-4 ring-emerald-100">
                                <span>TAHAP</span>
                                <span class="text-lg">05</span>
                            </div>
                            <div class="flex-1 bg-emerald-50/60 p-5 rounded-2xl border border-emerald-200">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="text-sm font-bold text-emerald-900">Penerbitan Sertifikat / Izin Resmi Terbit</h4>
                                    <span class="text-[11px] font-bold text-emerald-700 bg-white px-2 py-0.5 rounded-full border border-emerald-200">Selesai</span>
                                </div>
                                <p class="text-xs text-emerald-800 leading-relaxed">
                                    Penerbitan Surat Keputusan (SK) dan sertifikat izin edar dengan QR code keaslian dari Kementerian. Penyerahan dokumen asli serta Berita Acara Kajian (BAK) penutupan proyek.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 5: Pricing & Retribusi -->
            <div id="tab-pricing" class="tab-pane hidden space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200">
                    <h3 class="text-xl sm:text-2xl font-extrabold text-corporate mb-2 flex items-center gap-3">
                        <i data-lucide="wallet" class="w-6 h-6 text-emerald-600"></i>
                        Struktur Biaya Jasa &amp; Simulasi Retribusi
                    </h3>
                    <p class="text-xs text-slate-500 mb-8">Transparansi skema pembiayaan tanpa ada biaya tersembunyi (*no hidden fee*).</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Komponen Jasa Konsultan -->
                        <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-full uppercase tracking-wider mb-2 inline-block">Jasa Profesional</span>
                            <h4 class="text-base font-extrabold text-corporate mb-2">CV Prasta Solusi Indonesia</h4>
                            <div class="text-2xl font-black text-corporate mb-4">
                                {{ $service->formatted_price ?: ('Rp ' . number_format($service->price ?: $service->cost ?: 0, 0, ',', '.')) }}
                            </div>
                            <ul class="space-y-2 text-xs text-slate-600">
                                <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4 text-emerald-600"></i> Penyusunan dokumen teknis &amp; SOP mutu</li>
                                <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4 text-emerald-600"></i> Pendampingan audit lapangan &amp; sidang komisi</li>
                                <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4 text-emerald-600"></i> Penyelesaian catatan evaluasi (CAPA)</li>
                                <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4 text-emerald-600"></i> Garansi izin &amp; sertifikat sampai terbit</li>
                            </ul>
                        </div>

                        <!-- Retribusi PNBP Resmi Pemerintah -->
                        <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="text-[10px] font-bold text-slate-700 bg-slate-200 px-2.5 py-1 rounded-full uppercase tracking-wider mb-2 inline-block">PNBP Pemerintah</span>
                            <h4 class="text-base font-extrabold text-corporate mb-2">Retribusi Resmi Kas Negara</h4>
                            <div class="text-2xl font-black text-slate-700 mb-4">
                                Sesuai Tarif PP Resmi
                            </div>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4">
                                Biaya Penerimaan Negara Bukan Pajak (PNBP) dibayarkan langsung oleh klien ke rekening kas negara melalui kode billing SIMPONI / MPN G2 resmi kementerian terkait.
                            </p>
                            <div class="p-3 bg-white rounded-xl border border-slate-200 text-[11px] text-slate-600">
                                <strong>Transparansi:</strong> Bukti bayar NTPN dari bank menjadi arsip resmi dan dilampirkan pada Berita Acara Kajian (BAK).
                            </div>
                        </div>
                    </div>

                    <!-- Payment Milestones -->
                    <div class="p-6 rounded-2xl bg-slate-900 text-white flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-800 text-emerald-400 flex items-center justify-center shrink-0 border border-slate-700">
                            <i data-lucide="shield-check" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-white mb-1">Skema Pembayaran Bertahap (Termin Aman)</h4>
                            <p class="text-xs text-slate-400">Pembayaran jasa dibagi 2 termin: 50% Uang Muka (DP) saat kontrak SPK &amp; 50% Pelunasan setelah Surat Izin Resmi Terbit.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 6: FAQ & Kendala -->
            <div id="tab-faq" class="tab-pane hidden space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200">
                    <h3 class="text-xl sm:text-2xl font-extrabold text-corporate mb-6 flex items-center gap-3">
                        <i data-lucide="help-circle" class="w-6 h-6 text-emerald-600"></i>
                        Pertanyaan Umum Seputar {{ $service->name }}
                    </h3>

                    <div class="space-y-4">
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100">
                            <h4 class="font-bold text-sm text-corporate mb-2 flex items-center gap-2">
                                <i data-lucide="help-circle" class="w-4 h-4 text-emerald-600"></i>
                                Apakah CV Prasta Solusi Indonesia menjamin izin pasti terbit?
                            </h4>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Ya, kami memberikan garansi pendampingan sampai sertifikat/izin resmi terbit. Jika instansi penerbit memberikan catatan perbaikan atau evaluasi teknis, tim kami akan mendampingi revisinya tanpa tambahan biaya jasa konsultan.
                            </p>
                        </div>

                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100">
                            <h4 class="font-bold text-sm text-corporate mb-2 flex items-center gap-2">
                                <i data-lucide="help-circle" class="w-4 h-4 text-emerald-600"></i>
                                Bagaimana jika sarana/gudang kami belum memiliki standar suhu atau kelayakan?
                            </h4>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Konsultan kami akan melakukan pra-inspeksi dan memberikan rekomendasi denah tata letak (*layout mapping*) serta spesifikasi peralatan pemantau suhu yang sesuai dengan pedoman regulasi sebelum permohonan diajukan ke dinas.
                            </p>
                        </div>

                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100">
                            <h4 class="font-bold text-sm text-corporate mb-2 flex items-center gap-2">
                                <i data-lucide="help-circle" class="w-4 h-4 text-emerald-600"></i>
                                Berapa lama masa berlaku sertifikat / izin edar ini setelah terbit?
                            </h4>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Masa berlaku sertifikat umumnya berkisar antara 4 hingga 5 tahun tergantung jenis regulasi (misalnya izin edar alat kesehatan dan notifikasi kosmetik berlaku 5 tahun, sertifikat halal berlaku seumur hidup/4 tahun sesuai ketentuan terbaru, dan sertifikasi sistem mutu diaudit berkala per tahun).
                            </p>
                        </div>

                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100">
                            <h4 class="font-bold text-sm text-corporate mb-2 flex items-center gap-2">
                                <i data-lucide="help-circle" class="w-4 h-4 text-emerald-600"></i>
                                Apakah proses konsultasi mengharuskan klien datang ke kantor kementerian di Jakarta?
                            </h4>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Tidak perlu. Seluruh proses pendaftaran dan verifikasi berkas saat ini diproses secara elektronik (*online system*), dan koordinasi fisik jika diperlukan akan diwakili oleh tim perizinan resmi kami.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- 5. Related Services Section -->
        @if($relatedServices->isNotEmpty())
        <div class="mt-16 pt-12 border-t border-slate-200">
            <h3 class="text-xl font-extrabold text-corporate mb-6">Layanan Terkait Lainnya</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($relatedServices as $rel)
                <div class="bg-white rounded-xl p-4 sm:p-5 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full uppercase tracking-wider mb-2.5 inline-block border border-emerald-100">
                            {{ $rel->category ? $rel->category->name : 'Perizinan' }}
                        </span>
                        <h4 class="font-bold text-sm sm:text-base text-corporate mb-1.5 leading-snug line-clamp-2">{{ $rel->name }}</h4>
                        <p class="text-xs text-slate-500 line-clamp-2 mb-3">{{ $rel->description }}</p>
                    </div>
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs font-bold text-emerald-700">{{ $rel->formatted_price }}</span>
                        <a href="{{ route('services.show', $rel->slug) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                            <span>Detail</span>
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </main>

    <!-- 6. MODAL KONSULTASI PERSYARATAN DOKUMEN -->
    <div id="docConsultModal" class="hidden fixed inset-0 z-[120] overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative">
            <button type="button" onclick="closeDocConsultModal()" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>

            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <i data-lucide="clipboard-check" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Konsultasi Khusus Persyaratan</span>
                    <h3 class="text-lg font-extrabold text-corporate">Bantuan Kelengkapan Dokumen</h3>
                </div>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed mb-6">
                Sampaikan kendala berkas yang belum Anda miliki untuk layanan <strong>{{ $service->name }}</strong>. Tim Regulatory kami akan memberikan rekomendasi solusi teknis.
            </p>

            <form action="{{ route('services.consultation') }}" method="POST" onsubmit="handleDocConsultSubmit(event)" class="space-y-4">
                @csrf
                <input type="hidden" name="service_name" value="{{ $service->name }}">

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama PIC / Penanggung Jawab *</label>
                    <input type="text" name="name" id="docConsultName" required placeholder="Contoh: Hendra Wijaya" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-slate-50">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor WhatsApp Aktif *</label>
                        <input type="tel" name="phone" id="docConsultPhone" required placeholder="08123456789" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Perusahaan</label>
                        <input type="text" name="company" id="docConsultCompany" placeholder="PT / CV / Usaha" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-slate-50">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Kendala / Berkas yang Kurang *</label>
                    <select name="missing_doc_category" id="docConsultCategory" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-slate-50">
                        <option value="">-- Pilih Kendala Utama --</option>
                        <option value="Belum Memiliki Penanggung Jawab Teknis (PJT / Apoteker / Teknis)">Belum ada PJT (Apoteker / Tenaga Teknis ber-STR)</option>
                        <option value="Denah Ruangan & Gudang Belum Sesuai Standar">Denah / Sarana Gudang belum sesuai standar suhu</option>
                        <option value="Belum Memiliki SOP Mutu & Pedoman Kerja Tertulis">Belum memiliki SOP Mutu &amp; Pedoman Kerja tertulis</option>
                        <option value="Hasil Uji Laboratorium / CoA Belum Tersedia">Hasil uji laboratorium / Certificate of Analysis (CoA) belum ada</option>
                        <option value="Kendala KBLI NIB OSS-RBA Belum Tepat">KBLI pada NIB OSS-RBA belum sesuai perizinan</option>
                        <option value="Lainnya / Ingin Pendampingan Penuh">Lainnya / Ingin pendampingan penyiapan berkas dari nol</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan Tambahan / Detail Kendala *</label>
                    <textarea name="message" id="docConsultMsg" rows="3" required placeholder="Jelaskan kondisi berkas saat ini atau pertanyaan spesifik yang ingin dikonsultasikan..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-slate-50"></textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeDocConsultModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-emerald-600/20">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Kirim Permintaan Konsultasi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 7. Floating WhatsApp Button -->
    <a href="https://wa.me/6282128765874?text={{ rawurlencode('Halo IzinYou, saya sedang melihat halaman: ' . $service->name . '. Mohon informasi konsultasinya.') }}" target="_blank" class="fixed bottom-8 right-8 z-[100] flex items-center gap-3 animate-float group">
        <div class="bg-white px-5 py-3 rounded-full shadow-2xl border border-emerald-100 text-sm font-black text-emerald-600 whitespace-nowrap">
            Konsultasi WhatsApp
        </div>
        <div class="w-14 h-14 bg-emerald-500 group-hover:bg-emerald-600 rounded-full text-white shadow-2xl flex items-center justify-center transition-all duration-300 border-4 border-white hover:scale-110 flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
        </div>
    </a>

    <!-- 8. Footer -->
    <footer class="bg-corporate pt-16 pb-12 text-slate-300 relative border-t-8 border-gold">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-12 gap-8 mb-12">
                <div class="md:col-span-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center shadow-md border border-slate-700">
                            <svg class="w-6 h-6 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2L3 7v6c0 5.5 3.8 10.7 9 12 5.2-1.3 9-6.5 9-12V7l-9-5z"/>
                                <path d="m9 12 2 2 4-4" stroke="#d4af37" stroke-width="2.5"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-xl text-white">IzinYou.com</h3>
                            <p class="text-xs text-gold">by CV Prasta Solusi Indonesia</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 max-w-xl leading-relaxed">
                        Cluster New Liverpool Blok P18A No 46, RT 02 / RW 017, Kelurahan Kedung Jaya, Kecamatan Babelan, Kabupaten Bekasi, Jawa Barat 17610.
                    </p>
                </div>
                <div class="md:col-span-4">
                    <h4 class="text-white font-bold mb-4 text-sm">Navigasi</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('home') }}" class="hover:text-emerald-400 transition">Beranda</a></li>
                        <li><a href="{{ route('home') }}#layanan" class="hover:text-emerald-400 transition">Katalog Layanan</a></li>
                        <li><a href="{{ route('home') }}#kontak" class="hover:text-emerald-400 transition">Hubungi Konsultan</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-800 pt-6 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} IzinYou. Hak Cipta Dilindungi. CV Prasta Solusi Indonesia.
            </div>
        </div>
    </footer>

    <!-- ================================================================ -->
    <!-- JAVASCRIPT: Tab switching, modal konsultasi, mobile menu          -->
    <!-- ================================================================ -->
    <script>
        // Inisialisasi semua ikon Lucide di halaman ini
        lucide.createIcons();

        // ── Fungsi Tab Switching ──
        // Mengganti konten yang ditampilkan berdasarkan tab yang diklik
        function switchTab(targetId) {
            // Perbarui style tombol tab: aktif = hijau, lainnya = transparan
            document.querySelectorAll('.tab-btn').forEach(btn => {
                if (btn.dataset.target === targetId) {
                    btn.classList.add('active', 'bg-emerald-600', 'text-white', 'shadow-sm');
                    btn.classList.remove('bg-transparent', 'text-slate-600');
                } else {
                    btn.classList.remove('active', 'bg-emerald-600', 'text-white', 'shadow-sm');
                    btn.classList.add('bg-transparent', 'text-slate-600');
                }
            });

            // Tampilkan panel tab yang sesuai, sembunyikan yang lain
            document.querySelectorAll('.tab-pane').forEach(pane => {
                if (pane.id === targetId) {
                    pane.classList.remove('hidden');
                } else {
                    pane.classList.add('hidden');
                }
            });

            lucide.createIcons(); // Re-init ikon di dalam panel yang baru tampil
        }

        // ── Buka Modal Konsultasi Persyaratan Dokumen ──
        function openDocConsultModal() {
            const modal = document.getElementById('docConsultModal');
            if (modal) {
                modal.classList.remove('hidden'); // Tampilkan modal
                lucide.createIcons();             // Re-init ikon di dalam modal
            }
        }

        // ── Tutup Modal Konsultasi Dokumen ──
        function closeDocConsultModal() {
            const modal = document.getElementById('docConsultModal');
            if (modal) {
                modal.classList.add('hidden'); // Sembunyikan modal
            }
        }

        // ── Handler Submit Form Konsultasi Dokumen ──
        // Tidak benar-benar mengirim form, melainkan membuka WhatsApp langsung
        function handleDocConsultSubmit(e) {
            // Ambil nilai dari setiap field form
            const name     = document.getElementById('docConsultName')?.value;
            const phone    = document.getElementById('docConsultPhone')?.value;
            const company  = document.getElementById('docConsultCompany')?.value || '-';
            const category = document.getElementById('docConsultCategory')?.value;
            const msg      = document.getElementById('docConsultMsg')?.value;

            // Susun pesan WhatsApp terstruktur dan buka di tab baru agar komunikasi instan
            const waText = encodeURIComponent(
                `Halo Tim Legal & Marketing IzinYou (CV Prasta Solusi Indonesia),\n\nSaya ingin berkonsultasi mengenai kekurangan/kendala berkas persyaratan:\n• Layanan: {{ $service->name }}\n• PIC: ${name}\n• Perusahaan: ${company}\n• No WA: ${phone}\n• Kendala Dokumen: ${category}\n• Detail Keterangan: ${msg}`
            );
            window.open(`https://wa.me/6282128765874?text=${waText}`, '_blank');
        }

        // ── Toggle Mobile Menu untuk halaman detail layanan ──
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu    = document.getElementById('mobile-menu');
        if (mobileMenuBtn && mobileMenu) {
            // Tampilkan / sembunyikan menu saat tombol hamburger diklik
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
    </script>
</body>
</html>
