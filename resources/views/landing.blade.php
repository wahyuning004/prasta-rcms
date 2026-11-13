<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRASTA SOLUSI INDONESIA | Platform Perizinan & Sertifikasi (IzinYou)</title>
    <meta name="description" content="PRASTA SOLUSI INDONESIA (IzinYou) - Platform resmi spesialis pendampingan perizinan dan sertifikasi bisnis (BPOM, Kemenkes, Halal BPJPH, SNI, OSS).">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon-192.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-nav { 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(12px); 
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }
        .text-corporate { color: #0f172a; }
        .bg-corporate { background-color: #0f172a; }
        .text-gold { color: #d4af37; }
        .bg-gold { background-color: #d4af37; }
        .bg-pattern {
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 24px 24px;
        }
        
        /* Float Animation for WhatsApp Button */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
            100% { transform: translateY(0px); }
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        .card-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .card-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .card-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-sky-500 selection:text-white">

    <!-- 1. Top Bar & Navbar -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo & Name (IzinYou dengan Logo Hexagon Resmi) -->
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-3 cursor-pointer group" onclick="window.scrollTo({top:0, behavior:'smooth'})">
                    <img src="{{ asset('images/logo-symbol-transparent.png') }}" alt="IzinYou" class="h-10 sm:h-11 w-auto object-contain transition-transform duration-300 group-hover:scale-105">
                    <div>
                        <h1 class="font-black text-2xl leading-none text-slate-900 tracking-tight flex items-center gap-1.5">
                            Izin<span class="text-sky-600">You</span>
                            <span class="text-[10px] bg-sky-100 text-sky-800 font-bold px-2 py-0.5 rounded-full">.com</span>
                        </h1>
                        <p class="text-[10px] text-slate-500 font-medium leading-none mt-1">by CV Prasta Solusi Indonesia</p>
                    </div>
                </a>

                <!-- Desktop Menu: Beranda, Tentang Kami, Layanan (Dropdown), Katalog & Promo, Penilaian Klien, Kontak, Login Sistem -->
                <div class="hidden lg:flex space-x-7 items-center">
                    <a href="#beranda" class="text-sm font-semibold text-slate-900 hover:text-sky-600 transition-colors">Beranda</a>
                    <a href="#tentang" class="text-sm font-semibold text-slate-600 hover:text-sky-600 transition-colors">Tentang Kami</a>
                    <a href="{{ route('catalog.index') }}" class="text-sm font-bold text-sky-600 hover:text-sky-700 transition-colors flex items-center gap-1.5 py-1 px-3 bg-sky-50 rounded-full border border-sky-200">
                        <span>Katalog &amp; Promo</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                    </a>
                    
                    <!-- Dropdown Layanan (Mega Menu: Semua Layanan Tanpa Kelompok) -->
                    <div class="relative group" id="navLayananDropdown">
                        <button type="button" class="text-sm font-semibold text-slate-600 group-hover:text-sky-600 transition-colors flex items-center gap-1.5 py-2">
                            <span>Layanan</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200 group-hover:rotate-180"></i>
                        </button>
                        
                        <!-- Mega Menu Panel -->
                        <div class="absolute left-1/2 -translate-x-[35%] top-full pt-2 w-[860px] lg:w-[920px] max-w-[94vw] hidden group-hover:block z-50 transition-all duration-200">
                            <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 p-7 lg:p-8">
                                <!-- Top Bar & Counter -->
                                <div class="flex items-center justify-between pb-3.5 mb-5 border-b border-slate-100">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-sky-500 animate-pulse"></span>
                                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Semua Layanan Perizinan &amp; Sertifikasi</span>
                                    </div>
                                    <span class="text-[11px] font-semibold text-slate-400">
                                        {{ $services->count() }} Layanan Resmi Tersedia
                                    </span>
                                </div>

                                @php
                                    $sortedServices = $services->sortBy('name')->values();
                                    $totalSvc = $sortedServices->count();
                                    $perCol = $totalSvc > 0 ? (int) ceil($totalSvc / 3) : 1;
                                    $serviceCols = $sortedServices->chunk($perCol);
                                @endphp

                                <!-- 3 Columns Grid Layanan (Tanpa Pengelompokan Kategori) -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-1">
                                    @foreach($serviceCols as $colIndex => $columnServices)
                                    <div class="space-y-1">
                                        @foreach($columnServices as $svc)
                                        <a href="{{ route('services.show', $svc->slug) }}" class="group/item flex items-center gap-2 py-1.5 text-slate-700 hover:text-sky-600 transition-colors duration-150">
                                            <span class="text-sm font-medium leading-snug group-hover/item:text-sky-600 transition-colors">
                                                {{ $svc->name }}
                                            </span>
                                        </a>
                                        @endforeach
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Footer Info -->
                                <div class="mt-6 pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                    <div class="flex items-center gap-1.5">
                                        <i data-lucide="shield-check" class="w-4 h-4 text-sky-600"></i>
                                        <span>Klik langsung layanan untuk melihat rincian biaya, syarat berkas &amp; alur perizinan &amp; sertifikasi.</span>
                                    </div>
                                    <a href="{{ route('catalog.index') }}" class="font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1">
                                        <span>Buka Halaman Katalog Lengkap</span>
                                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="#testimoni" class="text-sm font-semibold text-slate-600 hover:text-sky-600 transition-colors">Penilaian Klien</a>
                    <a href="#kontak" class="text-sm font-semibold text-slate-600 hover:text-sky-600 transition-colors">Kontak</a>
                    
                    <div class="h-6 w-px bg-slate-300"></div>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-bold text-white bg-sky-600 hover:bg-sky-500 px-5 py-2.5 rounded-full transition-colors flex items-center gap-2 shadow-md">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard Saya
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-white bg-corporate hover:bg-sky-600 px-5 py-2.5 rounded-full transition-colors flex items-center gap-2 shadow-md">
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
                <a href="#beranda" class="mobile-link block text-sm font-semibold text-slate-900 hover:text-sky-600 transition-colors py-2">Beranda</a>
                <a href="#tentang" class="mobile-link block text-sm font-semibold text-slate-600 hover:text-sky-600 transition-colors py-2">Tentang Kami</a>
                
                <!-- Mobile Layanan Submenu (Semua Layanan Langsung ke Detail) -->
                <div>
                    <button type="button" onclick="document.getElementById('mobileSubmenuLayanan').classList.toggle('hidden')" class="w-full flex items-center justify-between text-sm font-semibold text-slate-600 hover:text-sky-600 transition-colors py-2">
                        <span>Layanan (Semua Layanan)</span>
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </button>
                    <div id="mobileSubmenuLayanan" class="pl-2 py-2 space-y-1 bg-slate-50 rounded-2xl mt-1 hidden max-h-72 overflow-y-auto card-scrollbar">
                        @foreach($services->sortBy('name') as $svc)
                            <a href="{{ route('services.show', $svc->slug) }}" class="flex items-center justify-between py-2 px-3 rounded-xl text-xs font-medium text-slate-700 hover:text-sky-600 hover:bg-sky-50 transition">
                                <span class="truncate">{{ $svc->name }}</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-slate-400 shrink-0 ml-2"></i>
                            </a>
                        @endforeach
                    </div>
                </div>

                <a href="#testimoni" class="mobile-link block text-sm font-semibold text-slate-600 hover:text-sky-600 transition-colors py-2">Penilaian Klien</a>
                <a href="#kontak" class="mobile-link block text-sm font-semibold text-slate-600 hover:text-sky-600 transition-colors py-2">Kontak</a>
                
                <div class="h-px w-full bg-slate-200 my-2"></div>
                @auth
                    <a href="{{ route('dashboard') }}" class="block w-full text-center text-sm font-bold text-white bg-sky-600 hover:bg-sky-500 px-5 py-3 rounded-xl transition-colors shadow-md flex justify-center items-center gap-2">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard Saya
                    </a>
                @else
                    <a href="{{ route('login') }}" class="block w-full text-center text-sm font-bold text-white bg-corporate hover:bg-sky-600 px-5 py-3 rounded-xl transition-colors shadow-md flex justify-center items-center gap-2">
                        <i data-lucide="log-in" class="w-4 h-4"></i> Login Sistem
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Scroll to Top Button -->
    <button id="scrollToTopBtn" class="fixed bottom-28 right-8 z-[90] w-12 h-12 bg-corporate hover:bg-slate-800 text-white rounded-full shadow-lg flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300 transform translate-y-4">
        <i data-lucide="arrow-up" class="w-6 h-6 text-gold"></i>
    </button>

    <!-- Floating WhatsApp Button (Pusat Komunikasi Tunggal) -->
    <a href="https://wa.me/6282128765874?text=Halo%20IzinYou,%20saya%20ingin%20berkonsultasi%20mengenai%20perizinan/sertifikasi..." target="_blank" class="fixed bottom-8 right-8 z-[100] flex items-center gap-3 animate-float group">
        <div class="bg-white px-5 py-3 rounded-full shadow-2xl border border-sky-100 text-sm font-black text-sky-600 whitespace-nowrap flex items-center gap-2 group-hover:text-sky-700 transition-colors">
            <span>Konsultasi Gratis</span>
            <i data-lucide="arrow-up" class="w-4 h-4 text-sky-600 stroke-[2.5] transition-transform group-hover:-translate-y-0.5"></i>
        </div>
        <div class="w-16 h-16 bg-[#25D366] hover:bg-[#20ba59] rounded-full text-white shadow-2xl flex items-center justify-center transition-all duration-300 border-4 border-white hover:scale-110 flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
        </div>
    </a>

    <!-- 2. Hero Section (Harmonis dengan Identitas Corporate & Sky Blue Prasta) -->
    <section id="beranda" class="relative pt-32 pb-20 lg:pt-40 lg:pb-32 overflow-hidden bg-gradient-to-b from-sky-50/50 via-slate-50 to-white">
        <!-- Soft Harmonious Ambient Glows -->
        <div class="absolute -top-24 -left-24 w-[32rem] h-[32rem] bg-sky-100/40 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/4 -right-20 w-[30rem] h-[30rem] bg-slate-200/50 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 left-1/3 w-[28rem] h-[28rem] bg-sky-50/60 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                <!-- Left Column: Hero Text & Actions -->
                <div class="lg:col-span-7 text-left">
                    <!-- Ribbon Badge: 🏆 #1 Spesialis Perizinan & Sertifikasi di Indonesia -->
                    <div class="inline-flex items-center relative mb-6">
                        <div class="relative z-10 px-5 py-2.5 bg-white rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.05)] border border-sky-100 flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-500 animate-pulse"></span>
                            <span class="text-xs sm:text-sm font-bold text-slate-900 tracking-wide">🏆 #1 Spesialis Perizinan &amp; Sertifikasi di Indonesia</span>
                        </div>
                    </div>

                    <!-- Main Headline with Rotating Animated Words -->
                    <h1 class="text-4xl sm:text-5xl lg:text-[3.8rem] font-black text-slate-900 leading-[1.12] tracking-tight mb-6">
                        Layanan Perizinan &amp;<br>
                        Sertifikasi <span id="heroAnimatedWord" class="inline-block text-sky-600 font-black transition-all duration-300 transform">Akuntabel.</span>
                    </h1>

                    <!-- Subtitle / Description -->
                    <p class="text-base sm:text-lg text-slate-600 leading-relaxed max-w-xl font-normal mb-8">
                        IzinYou di bawah naungan <strong>CV Prasta Solusi Indonesia</strong> memberikan pendampingan perizinan dan sertifikasi resmi (BPOM, Kemenkes, Halal BPJPH, SNI, dan OSS) secara transparan, akuntabel, dan terpercaya di Indonesia.
                    </p>

                    <!-- Action Button Group -->
                    <div class="flex flex-wrap items-center gap-4">
                        <!-- Sky Brand Action Button -->
                        <a href="{{ route('catalog.index') }}" class="group px-7 py-3.5 bg-sky-600 hover:bg-sky-500 active:bg-sky-700 text-white font-bold text-sm sm:text-base rounded-xl shadow-lg shadow-sky-600/25 hover:shadow-sky-600/40 transition-all duration-300 flex items-center gap-2.5">
                            <span>Lihat Katalog Layanan</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
                        </a>

                        <!-- Direct WA Button -->
                        <a href="https://wa.me/6282128765874?text=Halo%20Tim%20IzinYou,%20saya%20ingin%20berkonsultasi%20mengenai%20perizinan%20usaha..." target="_blank" class="px-6 py-3.5 bg-white hover:bg-slate-50 text-slate-800 font-bold text-sm sm:text-base rounded-xl border border-slate-200 shadow-sm hover:border-sky-300 transition-all duration-300 flex items-center gap-2">
                            <i data-lucide="message-circle" class="w-4 h-4 text-sky-600"></i>
                            <span>Konsultasi WhatsApp</span>
                        </a>
                    </div>
                </div>

                <!-- Right Column: Interactive Hero Feature Showcase Card (15% Lebih Kompak & Posisi Gelembung Dioptimalkan) -->
                <div class="lg:col-span-5 relative mt-8 lg:mt-0 flex justify-center w-full max-w-[370px] sm:max-w-[385px] mx-auto">
                    
                    <!-- Floating Top-Left Badge: Pelayanan Cepat (Dijauhkan ke kiri atas agar tidak menutupi isi kartu) -->
                    <div class="absolute -top-7 -left-5 sm:-left-10 lg:-left-12 z-20 bg-white/95 backdrop-blur-md px-3.5 py-2 rounded-xl shadow-lg border border-slate-100 flex items-center gap-2.5 animate-float pointer-events-none">
                        <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                            <i data-lucide="sparkles" class="w-4 h-4 text-sky-500"></i>
                        </div>
                        <div>
                            <p class="text-[8.5px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-0.5">PELAYANAN CEPAT</p>
                            <p class="text-[11px] font-black text-slate-800 leading-none">Audit &amp; Proses Cepat</p>
                        </div>
                    </div>

                    <!-- Floating Bottom-Left Badge: Garansi 100% Sah (Dijauhkan ke kiri bawah agar harga Rp 7juta terlihat jelas) -->
                    <div class="absolute -bottom-7 -left-5 sm:-left-10 lg:-left-12 z-20 bg-white/95 backdrop-blur-md px-3.5 py-2 rounded-xl shadow-lg border border-slate-100 flex items-center gap-2.5 animate-float pointer-events-none" style="animation-delay: 1.5s;">
                        <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                            <i data-lucide="shield-check" class="w-4 h-4 text-sky-600"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-1 mb-0.5">
                                <i data-lucide="check-circle-2" class="w-3 h-3 text-sky-500"></i>
                                <span class="text-[11px] font-bold text-sky-700">Garansi 100% Sah</span>
                            </div>
                            <p class="text-[10px] font-black text-slate-800 leading-none">Sertifikat Resmi Terbit</p>
                        </div>
                    </div>

                    <!-- Layered Cards Background Shadow Effect (Kanan Belakang) -->
                    <div class="absolute inset-0 translate-x-2.5 translate-y-2.5 bg-sky-200/50 rounded-3xl blur-[1px] -z-10"></div>
                    <div class="absolute inset-0 translate-x-5 translate-y-5 bg-slate-300/40 rounded-3xl blur-[2px] -z-20 hidden sm:block"></div>

                    <!-- Main Hero Card Container (15% Lebih Proporsional) -->
                    <div class="w-full bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden relative z-10">
                        
                        <!-- Top Half: Blue Header with Columns -->
                        <div class="bg-gradient-to-br from-sky-600 via-sky-700 to-blue-800 text-white p-4 sm:p-5 relative overflow-hidden">
                            <!-- Subtle grid pattern -->
                            <div class="absolute inset-0 bg-[radial-gradient(#ffffff15_1px,transparent_1px)] [background-size:14px_14px] pointer-events-none"></div>

                            <!-- Top Bar: Layanan Unggulan & Domain -->
                            <div class="flex items-center justify-between relative z-10 mb-3">
                                <span class="text-[9px] font-extrabold bg-white/20 text-white px-2.5 py-0.5 rounded-full uppercase tracking-wider backdrop-blur-sm border border-white/20 shadow-sm">
                                    LAYANAN UNGGULAN
                                </span>
                                <span class="text-[11px] font-bold text-sky-100/90 tracking-wide">
                                    IzinYou.com
                                </span>
                            </div>

                            <!-- Header Titles -->
                            <div class="text-center relative z-10 mb-3.5">
                                <h3 class="font-black text-base sm:text-lg tracking-tight text-white uppercase">
                                    PERIZINAN &amp; SERTIFIKASI
                                </h3>
                                <p class="text-[9px] font-bold tracking-widest text-sky-200 uppercase mt-0.5">
                                    STANDAR RESMI NASIONAL
                                </p>
                            </div>

                            <!-- 3 Comparison Columns -->
                            <div class="bg-white rounded-xl p-1.5 sm:p-2 grid grid-cols-3 gap-1 sm:gap-1.5 text-slate-800 shadow-lg border border-white/40 relative z-10">
                                
                                <!-- Column 1: SERTIFIKASI -->
                                <button type="button" onclick="selectHeroTab(0)" id="heroTab0" class="hero-tab-col p-1.5 rounded-lg text-center flex flex-col justify-between transition-all cursor-pointer hover:bg-slate-50 border border-transparent">
                                    <div>
                                        <span class="text-[9px] font-black text-slate-700 uppercase tracking-wider block mb-0.5">SERTIFIKASI</span>
                                        <span class="text-[8.5px] text-slate-500 leading-tight block">Halal BPJPH</span>
                                        <span class="text-[8.5px] text-slate-500 leading-tight block mt-0.5">Standar SNI</span>
                                    </div>
                                    <div class="mt-2 pt-1 border-t border-slate-100">
                                        <span class="text-[8px] text-slate-400 font-medium block">Mulai</span>
                                        <span class="text-sm sm:text-base font-black text-sky-700 leading-none">8.5</span>
                                        <span class="text-[8px] font-bold text-slate-500 block uppercase mt-0.5">JUTA</span>
                                    </div>
                                </button>

                                <!-- Column 2: IZIN EDAR (POPULER - ACTIVE) -->
                                <button type="button" onclick="selectHeroTab(1)" id="heroTab1" class="hero-tab-col relative p-1.5 rounded-lg text-center flex flex-col justify-between transition-all cursor-pointer bg-sky-50/80 border-2 border-sky-400 ring-2 ring-sky-200/50 shadow-sm">
                                    <span class="absolute -top-2 left-1/2 -translate-x-1/2 bg-amber-400 text-slate-900 text-[7px] font-black px-1.5 py-0.5 rounded-full uppercase tracking-wider shadow-sm">POPULER</span>
                                    <div>
                                        <span class="text-[9px] font-black text-sky-900 uppercase tracking-wider block mb-0.5">IZIN EDAR</span>
                                        <span class="text-[8.5px] text-slate-600 font-medium leading-tight block">BPOM RI</span>
                                        <span class="text-[8.5px] text-slate-600 font-medium leading-tight block mt-0.5">Alkes Kemenkes</span>
                                        <span class="text-[8.5px] text-slate-600 font-medium leading-tight block mt-0.5">PKRT</span>
                                    </div>
                                    <div class="mt-1.5 pt-0.5 border-t border-sky-200/70">
                                        <span class="text-[8px] text-sky-600 font-bold block">Mulai</span>
                                        <span class="text-sm sm:text-base font-black text-sky-700 leading-none">7.0</span>
                                        <span class="text-[8px] font-bold text-sky-800 block uppercase mt-0.5">JUTA</span>
                                    </div>
                                </button>

                                <!-- Column 3: DISTRIBUSI -->
                                <button type="button" onclick="selectHeroTab(2)" id="heroTab2" class="hero-tab-col p-1.5 rounded-lg text-center flex flex-col justify-between transition-all cursor-pointer hover:bg-slate-50 border border-transparent">
                                    <div>
                                        <span class="text-[9px] font-black text-slate-700 uppercase tracking-wider block mb-0.5">DISTRIBUSI</span>
                                        <span class="text-[8.5px] text-slate-500 leading-tight block">Izin IDAK Alkes</span>
                                        <span class="text-[8.5px] text-slate-500 leading-tight block mt-0.5">CDOB Farmasi</span>
                                        <span class="text-[8.5px] text-slate-500 leading-tight block mt-0.5">CDAKB Mutu</span>
                                    </div>
                                    <div class="mt-2 pt-1 border-t border-slate-100">
                                        <span class="text-[8px] text-slate-400 font-medium block">Mulai</span>
                                        <span class="text-sm sm:text-base font-black text-sky-700 leading-none">15</span>
                                        <span class="text-[8px] font-bold text-slate-500 block uppercase mt-0.5">JUTA</span>
                                    </div>
                                </button>

                            </div>

                            <!-- Sub-banner under columns with next/slider circle button -->
                            <div class="mt-3 flex items-center justify-between text-[9px] text-sky-100/95 pl-1 relative z-10">
                                <span class="font-medium truncate pr-2">Layanan pengurusan terpadu hingga sertifikat resmi terbit</span>
                                <button type="button" onclick="nextHeroTab()" class="w-7 h-7 rounded-full bg-slate-900/80 hover:bg-slate-900 text-white flex items-center justify-center transition-transform hover:scale-110 shadow-lg shrink-0" title="Layanan berikutnya">
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>

                        </div>

                        <!-- Bottom Half: White Details Card -->
                        <div class="p-4 sm:p-5 bg-white space-y-3">
                            <!-- Title & Status Badge -->
                            <div class="flex items-center justify-between gap-3">
                                <h4 id="heroCardTitle" class="font-extrabold text-sm sm:text-base text-slate-900 leading-snug">
                                    Izin Edar Alkes &amp; BPOM
                                </h4>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-sky-50 text-sky-700 border border-sky-200 shrink-0">
                                    Layanan Aktif
                                </span>
                            </div>

                            <!-- Description -->
                            <p id="heroCardDesc" class="text-[11px] text-slate-500 leading-relaxed font-normal">
                                Pendampingan pengurusan perizinan dan sertifikasi lengkap dari audit berkas teknis hingga sertifikat resmi kementerian terbit.
                            </p>

                            <!-- Ratings -->
                            <div class="flex items-center justify-between pt-0.5">
                                <div class="flex items-center gap-1">
                                    <div class="flex text-amber-400 gap-0.5">
                                        <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                    </div>
                                    <span class="text-[11px] font-extrabold text-slate-800">5.0</span>
                                </div>
                                <a href="#testimoni" class="text-[11px] font-bold text-sky-600 hover:text-sky-700 transition-colors">
                                    (Lihat ulasan lengkap)
                                </a>
                            </div>

                            <!-- Price & Catalog Button -->
                            <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between">
                                <div>
                                    <span class="text-[9px] font-semibold text-slate-400 block leading-tight">mulai dari</span>
                                    <span id="heroCardPrice" class="text-lg sm:text-xl font-black text-slate-900 leading-tight">Rp 7juta</span>
                                </div>
                                <a href="{{ route('catalog.index') }}" class="px-4 py-2 bg-sky-50 hover:bg-sky-100 text-sky-700 font-bold text-[11px] rounded-lg border border-sky-200 transition-all flex items-center gap-1.5 shadow-sm hover:shadow active:scale-95">
                                    <span>Lihat Katalog</span>
                                    <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 3. Section Mengapa Memilih IzinYou -->
    <section class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h3 class="text-sky-600 font-bold tracking-wide uppercase text-sm mb-3">Keunggulan Platform</h3>
                <h2 class="text-3xl md:text-4xl font-extrabold text-corporate mb-4">Mengapa Memilih IzinYou?</h2>
                <p class="text-slate-600 text-lg">Platform perizinan cerdas yang memprioritaskan kepastian hukum, kecepatan proses, dan transparansi bagi seluruh pelaku usaha.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Poin 1 -->
                <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 text-center hover:shadow-xl transition-all group">
                    <div class="w-16 h-16 bg-white shadow-sm border border-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                        <i data-lucide="wallet" class="w-8 h-8 text-gold"></i>
                    </div>
                    <h4 class="text-lg font-bold text-corporate mb-3">Transparansi Biaya & Waktu</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">Seluruh estimasi biaya operasional dan timeline pengerjaan disampaikan secara rinci dan jujur sejak awal konsultasi.</p>
                </div>
                <!-- Poin 2 -->
                <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 text-center hover:shadow-xl transition-all group">
                    <div class="w-16 h-16 bg-white shadow-sm border border-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                        <i data-lucide="users-2" class="w-8 h-8 text-sky-500"></i>
                    </div>
                    <h4 class="text-lg font-bold text-corporate mb-3">Tim Ahli Berpengalaman</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">Ditangani konsultan perizinan dan sertifikasi yang menguasai birokrasi Kemenkes, BPOM, Halal BPJPH, Kemenperin, dan DJKI.</p>
                </div>
                <!-- Poin 3 -->
                <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 text-center hover:shadow-xl transition-all group">
                    <div class="w-16 h-16 bg-white shadow-sm border border-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                        <i data-lucide="file-check-2" class="w-8 h-8 text-blue-500"></i>
                    </div>
                    <h4 class="text-lg font-bold text-corporate mb-3">Pendampingan dari Nol</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">Kami membimbing penyusunan dokumen dari tahap persiapan awal hingga sertifikat / izin edar resmi diterbitkan.</p>
                </div>
                <!-- Poin 4 -->
                <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 text-center hover:shadow-xl transition-all group">
                    <div class="w-16 h-16 bg-white shadow-sm border border-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                        <i data-lucide="scale" class="w-8 h-8 text-rose-500"></i>
                    </div>
                    <h4 class="text-lg font-bold text-corporate mb-3">Izin &amp; Sertifikat Resmi</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">Mendukung kepastian regulasi mutlak bagi kelayakan edar produk dan operasional bisnis yang Anda jalankan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Section Tentang Kami (Background Mendalam) -->
    <section id="tentang" class="py-24 bg-slate-900 text-white relative">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-slate-800 via-slate-900 to-slate-900"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h3 class="text-gold font-bold tracking-wide uppercase text-sm mb-3">Tentang Kami</h3>
                    <h2 class="text-3xl md:text-4xl font-extrabold mb-6 leading-tight">Platform Layanan Perizinan & Sertifikasi Terdepan di Indonesia</h2>
                    <p class="text-slate-300 text-lg mb-6 leading-relaxed">
                        IzinYou didirikan di bawah naungan <strong>CV Prasta Solusi Indonesia</strong> dengan dedikasi penuh untuk memfasilitasi dan mengakselerasi proses perizinan dan sertifikasi bisnis di Tanah Air. Kami menyadari bahwa birokrasi dan persyaratan teknis perizinan seringkali menjadi tantangan terbesar bagi pelaku usaha.
                    </p>
                    <p class="text-slate-400 mb-8 leading-relaxed">
                        Melalui portal <strong>IzinYou.com</strong>, kami menjembatani perusahaan Anda dengan instansi pemerintah terkait (seperti BPOM, Kemenkes, Halal BPJPH, Kemenperin, dan DJKI). Tim konsultan spesialis kami siap memberikan edukasi, audit internal, dan pendampingan menyeluruh agar proses perizinan dan sertifikasi perusahaan Anda selesai tepat waktu.
                    </p>
                    <div class="flex gap-8 border-t border-slate-700 pt-8">
                        <div>
                            <p class="text-4xl font-black text-sky-400 mb-1">100%</p>
                            <p class="text-xs text-slate-400 uppercase tracking-widest font-semibold">Legal & Tervalidasi</p>
                        </div>
                        <div>
                            <p class="text-4xl font-black text-gold mb-1">Cepat</p>
                            <p class="text-xs text-slate-400 uppercase tracking-widest font-semibold">Proses Efisien</p>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="absolute inset-0 bg-sky-500 rounded-3xl transform rotate-3 opacity-20"></div>
                    <div class="bg-slate-800 border border-slate-700 p-10 rounded-3xl relative z-10 shadow-2xl">
                        <h4 class="text-2xl font-bold border-l-4 border-sky-500 pl-4 mb-4">Visi</h4>
                        <p class="text-slate-400 leading-relaxed mb-8">Menjadi pusat rujukan dan konsultan perizinan serta sertifikasi berskala nasional yang paling terpercaya, inovatif, dan menjadi tolak ukur kepatuhan regulasi bagi seluruh ekosistem industri.</p>
                        
                        <h4 class="text-2xl font-bold border-l-4 border-gold pl-4 mb-4">Misi</h4>
                        <ul class="space-y-3 text-slate-400">
                            <li class="flex items-start gap-3"><i data-lucide="check-circle-2" class="w-5 h-5 text-sky-500 flex-shrink-0 mt-0.5"></i> Menyediakan pendampingan teknis dan administratif yang akurat berbasis hukum aktual.</li>
                            <li class="flex items-start gap-3"><i data-lucide="check-circle-2" class="w-5 h-5 text-sky-500 flex-shrink-0 mt-0.5"></i> Menjaga standar transparansi anggaran dan efisiensi birokrasi bagi para klien.</li>
                            <li class="flex items-start gap-3"><i data-lucide="check-circle-2" class="w-5 h-5 text-sky-500 flex-shrink-0 mt-0.5"></i> Mendukung percepatan kelayakan edar produk pangan, kosmetik, alkes, dan farmasi yang aman di pasar.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Section Layanan & Portofolio (Grid Dinamis dengan Filter Kategori & Modal Detail) -->
    <section id="layanan" class="py-24 bg-slate-50 relative">
        <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <h3 class="text-sky-600 font-bold tracking-wide uppercase text-sm mb-3">Katalog Layanan Resmi</h3>
                <h2 class="text-3xl md:text-4xl font-extrabold text-corporate mb-4">Layanan Perizinan &amp; Sertifikasi</h2>
                <p class="text-slate-600 text-lg">Pilihan pendampingan spesialis terakreditasi, estimasi biaya transparan, jangka waktu terukur, dan kepastian pemenuhan berkas perizinan &amp; sertifikasi bisnis Anda.</p>
            </div>

            <!-- Dynamic Services Grid Layout (Ukuran Card Lebih Ringkas & Presisi) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5" id="servicesGrid">
                @forelse($services->take(8) as $svc)
                <div class="service-card bg-white rounded-2xl shadow-sm border border-slate-200/90 flex flex-col overflow-hidden hover:shadow-lg hover:border-sky-300 transition-all duration-300 group"
                     data-category="{{ $svc->category ? $svc->category->slug : 'umum' }}"
                     data-id="{{ $svc->id }}"
                     data-title="{{ $svc->name }}"
                     data-category-name="{{ $svc->category ? $svc->category->name : 'Perizinan & Sertifikasi' }}"
                     data-time="{{ $svc->timeline_estimation ?: ($svc->estimation_time ?: '1-3 Bulan') }}"
                     data-price="{{ $svc->formatted_price ?: ('Rp ' . number_format($svc->price ?: $svc->cost ?: 0, 0, ',', '.')) }}"
                     data-desc="{{ $svc->description ?: 'Layanan konsultasi dan pendampingan menyeluruh dari tim ahli kami untuk memastikan kelancaran perizinan dan sertifikasi usaha Anda.' }}"
                     data-docs="{{ $svc->requirements_info ?: $svc->requirements_summary }}"
                >
                    <div class="p-4 sm:p-5 flex-1 flex flex-col">
                        <!-- Card Header & Badge -->
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-100">
                                <i data-lucide="tag" class="w-3 h-3 text-sky-600"></i>
                                {{ $svc->category ? $svc->category->name : 'Perizinan' }}
                            </span>
                            <div class="w-8 h-8 bg-slate-50 text-slate-700 group-hover:bg-sky-600 group-hover:text-white rounded-lg flex items-center justify-center border border-slate-100 group-hover:border-sky-600 transition-all shrink-0">
                                <i data-lucide="award" class="w-4 h-4"></i>
                            </div>
                        </div>

                        <!-- Service Title -->
                        <h3 class="text-sm sm:text-base font-bold text-corporate leading-snug mb-3 line-clamp-2 min-h-[2.5rem] group-hover:text-sky-700 transition-colors">
                            {{ $svc->name }}
                        </h3>
                        
                        <!-- Meta Stats (Timeline & Price) -->
                        <div class="grid grid-cols-2 gap-2 mb-3.5 bg-slate-50/80 p-2.5 rounded-xl border border-slate-100">
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Estimasi Waktu</span>
                                <div class="flex items-center gap-1 text-xs font-semibold text-slate-800">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-sky-600 shrink-0"></i> 
                                    <span class="truncate">{{ $svc->timeline_estimation ?: ($svc->estimation_time ?: '1-3 Bulan') }}</span>
                                </div>
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Estimasi Biaya</span>
                                <div class="flex items-center gap-1 text-xs font-bold text-sky-700">
                                    <i data-lucide="wallet" class="w-3.5 h-3.5 text-gold shrink-0"></i> 
                                    <span class="truncate">{{ $svc->formatted_price ?: ('Rp ' . number_format($svc->price ?: $svc->cost ?: 0, 0, ',', '.')) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Termasuk / Hal yang Diperoleh (Fasilitas & Output Klien) -->
                        <div class="flex-1 mb-4">
                            <span class="text-[10px] font-bold text-corporate uppercase tracking-wider block mb-2 flex items-center gap-1.5">
                                <i data-lucide="badge-check" class="w-3.5 h-3.5 text-sky-600"></i> Termasuk / Diperoleh:
                            </span>
                            <div class="h-28 overflow-y-auto card-scrollbar pr-1.5">
                                @php
                                    $svcNameLower = strtolower($svc->name);
                                    $catSlugLower = strtolower($svc->category ? $svc->category->slug : '');
                                    
                                    // Poin fasilitas / output resmi yang diperoleh klien
                                    $facilities = [
                                        'Checklist Uji Kepatuhan Regulasi',
                                        'Penyusunan Draf Dokumen Teknis',
                                        'Pendampingan Verifikasi & Evaluasi',
                                        'Sertifikat / Izin Resmi Terbit',
                                    ];

                                    if (str_contains($svcNameLower, 'halal') || str_contains($catSlugLower, 'halal')) {
                                        $facilities = [
                                            'Audit Sistem Jaminan Produk Halal (SJPH)',
                                            'Penyusunan Manual Halal & Pelatihan Penyelia',
                                            'Pendampingan Pemeriksaan LPH & Sidang Fatwa MUI',
                                            'Sertifikat Halal Resmi BPJPH RI Terbit',
                                        ];
                                    } elseif (str_contains($svcNameLower, 'bpom') || str_contains($svcNameLower, 'kosmetik') || str_contains($svcNameLower, 'smkpo') || str_contains($catSlugLower, 'bpom')) {
                                        $facilities = [
                                            'Checklist Uji Kepatuhan CPPOB / CPKB / BPOM',
                                            'Penyusunan Draf Dokumen Teknis & Formula Produk',
                                            'Pendampingan Verifikasi Akun & Notifikasi',
                                            'Nomor Izin Edar (NIE) / Sertifikat Resmi Terbit',
                                        ];
                                    } elseif (str_contains($svcNameLower, 'kemenkes') || str_contains($svcNameLower, 'alkes') || str_contains($svcNameLower, 'pkrt') || str_contains($catSlugLower, 'kemenkes')) {
                                        $facilities = [
                                            'Gap Analysis & Uji Kepatuhan Permenkes RI',
                                            'Penyusunan Dokumen Mutu Standar & Berkas Teknis',
                                            'Pendampingan Evaluasi Petugas Kemenkes RI',
                                            'Sertifikat Standar / Izin Edar Alkes Terbit',
                                        ];
                                    } elseif (str_contains($svcNameLower, 'cdob') || str_contains($svcNameLower, 'pbf') || str_contains($catSlugLower, 'cdob')) {
                                        $facilities = [
                                            'Checklist Kepatuhan & Penyusunan SOP Standar CDOB',
                                            'Pemetaan Suhu (Temperature Mapping) Gudang',
                                            'Pendampingan Audit CAPA Petugas Balai BPOM',
                                            'Sertifikat CDOB Resmi dari BPOM RI Terbit',
                                        ];
                                    } elseif (str_contains($svcNameLower, 'idak') || str_contains($svcNameLower, 'cdakb') || str_contains($catSlugLower, 'idak')) {
                                        $facilities = [
                                            'Penyusunan Manual Mutu CDAKB & Struktur Organisasi',
                                            'Validasi Denah Kelayakan Gudang Distribusi',
                                            'Pendampingan Audit Verifikasi Lapangan Kemenkes',
                                            'Izin Distributor Alat Kesehatan (IDAK) Terbit',
                                        ];
                                    } elseif (str_contains($svcNameLower, 'sni') || str_contains($catSlugLower, 'sni')) {
                                        $facilities = [
                                            'Uji Kepatuhan Parameter Standar Mutu SNI',
                                            'Penyusunan Dokumen SPM (Sistem Penjaminan Mutu)',
                                            'Pendampingan Audit Pabrik & Pengambilan Sampel',
                                            'Sertifikat SPPT SNI Resmi Terbit',
                                        ];
                                    }
                                @endphp
                                <ul class="space-y-1.5">
                                    @foreach($facilities as $facility)
                                    <li class="flex items-start gap-2 text-[11px] text-slate-600">
                                        <div class="w-3.5 h-3.5 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center shrink-0 mt-0.5">
                                            <i data-lucide="check" class="w-2 h-2 stroke-[3]"></i>
                                        </div>
                                        <span class="leading-normal font-medium">{{ $facility }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <!-- CTA Action: 1 Tombol Utama Detail Layanan -->
                        <div class="pt-3 border-t border-slate-100">
                            <a 
                                href="{{ route('services.show', $svc->slug) }}"
                                class="w-full py-2.5 px-3 bg-sky-600 hover:bg-sky-500 active:bg-sky-700 text-white font-bold text-xs rounded-xl shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-1.5 group-hover:scale-[1.01]"
                            >
                                <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                <span>Detail &amp; Alur Lengkap</span>
                                <i data-lucide="arrow-right" class="w-3 h-3"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-slate-200">
                    <i data-lucide="folder-search" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                    <p class="font-bold text-corporate text-base">Belum ada data layanan perizinan.</p>
                    <p class="text-xs text-slate-400 mt-1">Silakan masuk ke portal Admin untuk menambahkan master layanan.</p>
                </div>
                @endforelse
            </div>

            <!-- Tombol Masuk ke Halaman Katalog Layanan Lengkap -->
            <div class="mt-12 text-center">
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-3 px-8 py-4 bg-sky-600 hover:bg-sky-500 active:bg-sky-700 text-white font-bold text-sm sm:text-base rounded-2xl shadow-lg shadow-sky-600/25 hover:shadow-sky-600/40 hover:-translate-y-0.5 transition-all duration-300 group">
                    <i data-lucide="layout-grid" class="w-5 h-5"></i>
                    <span>Lihat Seluruh Katalog Layanan</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1"></i>
                </a>
                <p class="text-xs text-slate-500 mt-2.5 font-medium">
                    Jelajahi {{ $services->count() }}+ pilihan layanan perizinan &amp; sertifikasi resmi kami di katalog lengkap.
                </p>
            </div>

            <!-- Empty Filter Result Placeholder (Hidden by default) -->
            <div id="noResults" class="hidden py-16 text-center bg-white rounded-3xl border border-slate-200 mt-4 max-w-xl mx-auto">
                <i data-lucide="search-x" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                <p class="font-bold text-corporate text-base">Tidak ada layanan pada kategori ini.</p>
                <p class="text-xs text-slate-400 mt-1">Silakan pilih kategori perizinan lainnya atau hubungi kami langsung via WhatsApp.</p>
            </div>
        </div>
    </section>

    <!-- Modal Dialog Detail Layanan (Interactive Detail Popup) -->
    <div id="serviceDetailModal" class="fixed inset-0 z-[120] hidden items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm transition-all duration-300">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden transform scale-95 transition-transform" id="modalCard">
            <!-- Modal Header -->
            <div class="p-6 md:p-8 border-b border-slate-100 flex items-start justify-between gap-4 bg-slate-50/60">
                <div>
                    <span id="modalCategory" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-sky-100 text-sky-800 mb-2">
                        Kategori Layanan
                    </span>
                    <h3 id="modalTitle" class="text-xl md:text-2xl font-extrabold text-corporate leading-tight">
                        Judul Layanan
                    </h3>
                </div>
                <button type="button" onclick="closeServiceDetailModal()" class="w-9 h-9 rounded-full bg-slate-200/80 hover:bg-slate-300 text-slate-600 flex items-center justify-center transition-colors shrink-0">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-6 md:p-8 overflow-y-auto card-scrollbar space-y-6 flex-1">
                <!-- Meta Info Grid -->
                <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Estimasi Durasi</span>
                        <div class="flex items-center gap-2 text-sm font-bold text-slate-800">
                            <i data-lucide="clock" class="w-4 h-4 text-sky-600"></i>
                            <span id="modalTime">-</span>
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Estimasi Anggaran</span>
                        <div class="flex items-center gap-2 text-sm font-bold text-sky-700">
                            <i data-lucide="wallet" class="w-4 h-4 text-gold"></i>
                            <span id="modalPrice">-</span>
                        </div>
                    </div>
                </div>

                <!-- Deskripsi Layanan -->
                <div>
                    <h4 class="text-xs font-bold text-corporate uppercase tracking-wider mb-2 flex items-center gap-2">
                        <i data-lucide="file-text" class="w-4 h-4 text-sky-600"></i> Deskripsi &amp; Cakupan Layanan
                    </h4>
                    <p id="modalDesc" class="text-sm text-slate-600 leading-relaxed bg-white p-4 rounded-2xl border border-slate-100">
                        -
                    </p>
                </div>

                <!-- Dokumen Persyaratan -->
                <div>
                    <h4 class="text-xs font-bold text-corporate uppercase tracking-wider mb-2 flex items-center gap-2">
                        <i data-lucide="folder-check" class="w-4 h-4 text-sky-600"></i> Checklist Dokumen Persyaratan
                    </h4>
                    <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                        <ul id="modalDocsList" class="space-y-2 text-xs text-slate-700">
                            <!-- Populated by JS -->
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-slate-50/50">
                <a href="{{ route('register') }}" class="w-full sm:w-auto px-5 py-3 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-white transition-all text-center flex items-center justify-center gap-2">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    Daftar Akun Klien
                </a>
                
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <button type="button" onclick="closeServiceDetailModal()" class="w-full sm:w-auto px-5 py-3 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition-colors">
                        Tutup
                    </button>
                    <a id="modalWhatsAppBtn" href="#" target="_blank" class="w-full sm:w-auto px-6 py-3 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs rounded-xl shadow-md shadow-sky-600/20 transition-all text-center flex items-center justify-center gap-2">
                        <i data-lucide="message-circle" class="w-4 h-4"></i>
                        Konsultasi via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>


    <!-- 7. Section Penilaian & Testimoni Klien -->
    <section id="testimoni" class="py-24 bg-white relative">
        <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h3 class="text-sky-600 font-bold tracking-wide uppercase text-sm mb-3">Testimoni Klien</h3>
                <h2 class="text-3xl md:text-4xl font-extrabold text-corporate mb-4">Kepercayaan Pelanggan Kami</h2>
                <p class="text-slate-600 text-lg">Mendukung ratusan perusahaan dari berbagai sektor industri di seluruh Indonesia dalam meraih sertifikasi dan izin operasional yang cepat serta transparan.</p>
            </div>

            <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-6">
                @if(isset($publishedReviews) && $publishedReviews->isNotEmpty())
                    @foreach($publishedReviews as $pubRev)
                        <div class="bg-slate-50 p-8 rounded-3xl shadow-sm border border-sky-100 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-1 mb-4 text-gold">
                                    @for($s = 1; $s <= 5; $s++)
                                        <i data-lucide="star" class="w-4 h-4 {{ $s <= $pubRev->rating ? 'fill-current' : 'text-slate-300' }}"></i>
                                    @endfor
                                </div>
                                <p class="text-slate-600 text-sm mb-6 italic leading-relaxed">"{{ $pubRev->comment }}"</p>
                            </div>
                            <div class="border-t border-slate-200 pt-4">
                                <h4 class="font-bold text-corporate text-sm">{{ $pubRev->user?->name ?? 'Klien Mitra' }}</h4>
                                <p class="text-xs text-slate-500">{{ $pubRev->user?->company_name ?? ($pubRev->service?->name ?? 'Mitra IzinYou') }}</p>
                            </div>
                        </div>
                    @endforeach
                @endif
                <!-- Testimoni 1 -->
                <div class="bg-slate-50 p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1 mb-4 text-gold">
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        </div>
                        <p class="text-slate-600 text-sm mb-6 italic leading-relaxed">"Sangat terbantu oleh tim IzinYou. Pengurusan Izin Edar Alkes Kemenkes berjalan jauh lebih cepat dari ekspektasi. Biayanya sangat transparan dari awal tanpa hidden fee."</p>
                    </div>
                    <div class="border-t border-slate-200 pt-4">
                        <h4 class="font-bold text-corporate text-sm">Direktur Operasional</h4>
                        <p class="text-xs text-slate-500">PT Distributor Alkes Nasional</p>
                    </div>
                </div>
                <!-- Testimoni 2 -->
                <div class="bg-slate-50 p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1 mb-4 text-gold">
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        </div>
                        <p class="text-slate-600 text-sm mb-6 italic leading-relaxed">"Konsultasi yang diberikan sangat solutif. Mengurus Izin Edar Kosmetik BPOM Import jadi sangat mudah karena pendampingan profesional dari awal hingga sertifikat terbit."</p>
                    </div>
                    <div class="border-t border-slate-200 pt-4">
                        <h4 class="font-bold text-corporate text-sm">Manajer Regulatory Affairs</h4>
                        <p class="text-xs text-slate-500">CV Importir Kosmetik Global</p>
                    </div>
                </div>
                <!-- Testimoni 3 -->
                <div class="bg-slate-50 p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1 mb-4 text-gold">
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        </div>
                        <p class="text-slate-600 text-sm mb-6 italic leading-relaxed">"Mulai dari sertifikasi Halal hingga penerapan SMKPO, semuanya diurus dengan sangat teliti. Ahli di bidang perizinan dan sertifikasi. Sangat direkomendasikan!"</p>
                    </div>
                    <div class="border-t border-slate-200 pt-4">
                        <h4 class="font-bold text-corporate text-sm">Quality Assurance</h4>
                        <p class="text-xs text-slate-500">Pabrik Pangan Olahan</p>
                    </div>
                </div>
                <!-- Testimoni 4 -->
                <div class="bg-slate-50 p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1 mb-4 text-gold">
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        </div>
                        <p class="text-slate-600 text-sm mb-6 italic leading-relaxed">"Proses CDAKB yang terkenal rumit ternyata bisa diselesaikan dengan lancar berkat bimbingan tim IzinYou. Audit fasilitas berjalan sukses."</p>
                    </div>
                    <div class="border-t border-slate-200 pt-4">
                        <h4 class="font-bold text-corporate text-sm">Penanggung Jawab Teknis</h4>
                        <p class="text-xs text-slate-500">Distributor Alkes Jawa Barat</p>
                    </div>
                </div>
                <!-- Testimoni 5 -->
                <div class="bg-slate-50 p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1 mb-4 text-gold">
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        </div>
                        <p class="text-slate-600 text-sm mb-6 italic leading-relaxed">"Kami mengurus PBF dan CDOB secara paralel. Pendampingan dokumen dan pemantauan progres dari IzinYou sangat rapi. Izin edar dan sertifikat operasional kami kini terbit lancar."</p>
                    </div>
                    <div class="border-t border-slate-200 pt-4">
                        <h4 class="font-bold text-corporate text-sm">Direktur Farmasi</h4>
                        <p class="text-xs text-slate-500">PBF Skala Nasional</p>
                    </div>
                </div>
                <!-- Testimoni 6 -->
                <div class="bg-slate-50 p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1 mb-4 text-gold">
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        </div>
                        <p class="text-slate-600 text-sm mb-6 italic leading-relaxed">"Pendaftaran Merek untuk produk baru kami selesai dengan aman. Penjelasannya mudah dipahami bagi pelaku usaha baru seperti kami. Terima kasih."</p>
                    </div>
                    <div class="border-t border-slate-200 pt-4">
                        <h4 class="font-bold text-corporate text-sm">Owner Startup</h4>
                        <p class="text-xs text-slate-500">Brand Kosmetik Lokal</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

<!-- ================= SECTION KONTAK (Hub Komunikasi & Kantor Operasional) ================= -->
<section id="kontak" class="py-20 bg-slate-50 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Header Section -->
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-sky-700 font-bold text-xs tracking-wider uppercase bg-sky-100/70 px-3.5 py-1 rounded-full border border-sky-200">
                Hub Komunikasi &amp; Kantor Resmi
            </span>
            <h2 class="text-3xl font-extrabold text-corporate mt-3 sm:text-4xl">
                Hubungi Konsultan IzinYou
            </h2>
            <p class="text-slate-600 mt-3 text-base">
                Diskusikan langsung kebutuhan sertifikasi, persyaratan teknis, dan timeline perizinan usaha Anda melalui saluran resmi kami di bawah ini.
            </p>
        </div>

        <!-- 3 Modern Corporate Channels Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Card 1: WhatsApp Hotline Langsung -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl hover:border-sky-300 transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-600 group-hover:bg-sky-600 group-hover:text-white transition-all duration-300 shadow-sm">
                            <i data-lucide="message-square" class="w-7 h-7"></i>
                        </div>
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-sky-50 text-sky-700 border border-sky-200">
                            Respon Cepat
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-corporate mb-2">WhatsApp Resmi</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mb-6">
                        Saluran komunikasi langsung dengan Tim Regulatory Affairs kami untuk konsultasi persyaratan dokumen dan panduan awal.
                    </p>

                    <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 mb-6">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Nomor Resmi WA</span>
                        <p class="text-base font-extrabold text-corporate">+62 821-2876-5874</p>
                    </div>
                </div>

                <a href="https://wa.me/6282128765874?text=Halo%20Tim%20IzinYou,%20saya%20ingin%20berkonsultasi%20mengenai%20perizinan/sertifikasi..." target="_blank" class="w-full py-3.5 px-4 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs rounded-xl shadow-md shadow-sky-600/20 hover:shadow-lg transition-all flex items-center justify-center gap-2">
                    <i data-lucide="phone-forwarded" class="w-4 h-4"></i>
                    <span>Chat WhatsApp Langsung</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <!-- Card 2: Email Korporat Resmi -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl hover:border-sky-300 transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 shadow-sm">
                            <i data-lucide="mail" class="w-7 h-7"></i>
                        </div>
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">
                            Korespondensi Dokumen
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-corporate mb-2">Email Korporat</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mb-6">
                        Gunakan email resmi untuk pengiriman draf berkas teknis, penawaran resmi B2B, perjanjian kerahasiaan (NDA), dan faktur kerja sama.
                    </p>

                    <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 mb-6">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Alamat Email Resmi</span>
                        <p class="text-xs sm:text-sm font-bold text-corporate truncate" title="prastasolusi.indonesia@gmail.com">prastasolusi.indonesia@gmail.com</p>
                    </div>
                </div>

                <a href="mailto:prastasolusi.indonesia@gmail.com?subject=Permohonan%20Konsultasi%20Perizinan%20IzinYou" class="w-full py-3.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center justify-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>Kirim Email Resmi</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <!-- Card 3: Kantor Operasional & Entitas Resmi Badan Usaha -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl hover:border-sky-300 transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-all duration-300 shadow-sm">
                            <i data-lucide="building-2" class="w-7 h-7"></i>
                        </div>
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                            Entitas Resmi
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-corporate mb-2">Kantor Operasional</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mb-6">
                        Berbadan hukum resmi di bawah naungan <strong>CV Prasta Solusi Indonesia</strong>. Layanan konsultasi terjadwal dan koordinasi dokumen perizinan &amp; sertifikasi.
                    </p>

                    <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 mb-6 space-y-2">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lokasi Kantor:</span>
                            <p class="text-xs font-semibold text-corporate leading-snug">Cluster New Liverpool Blok P18A No 46, Babelan, Kab. Bekasi, Jawa Barat 17610</p>
                        </div>
                        <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Jam Operasional:</span>
                            <strong class="text-corporate">08.30 - 17.00 WIB</strong>
                        </div>
                    </div>
                </div>

                <a href="https://maps.google.com/?q=Babelan+Kabupaten+Bekasi" target="_blank" class="w-full py-3.5 px-4 bg-slate-100 hover:bg-slate-200 text-corporate font-bold text-xs rounded-xl transition-all flex items-center justify-center gap-2 border border-slate-200">
                    <i data-lucide="map-pin" class="w-4 h-4 text-sky-600"></i>
                    <span>Buka Peta Lokasi</span>
                    <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

        </div>

        <!-- Trust Guarantee Bar -->
        <div class="mt-10 p-6 rounded-3xl bg-slate-900 text-white flex items-center gap-4 sm:gap-5 shadow-xl border border-slate-800">
            <div class="w-12 h-12 rounded-2xl bg-sky-500/20 text-sky-400 flex items-center justify-center shrink-0">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <div>
                <h4 class="text-sm sm:text-base font-bold text-white">Jaminan Kerahasiaan Dokumen &amp; Proses Akuntabel</h4>
                <p class="text-xs sm:text-sm text-slate-400 mt-0.5">Seluruh informasi formula produk, berkas teknis, dan data korporasi Anda dilindungi sepenuhnya dengan standar kerahasiaan Non-Disclosure Agreement (NDA).</p>
            </div>
        </div>

    </div>
</section>

<!-- FOOTER -->
<footer class="bg-slate-950 text-slate-300 relative border-t border-slate-800 pt-16 pb-12 overflow-hidden">
    <!-- Efek Glow Background Aksen -->
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-sky-500/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-amber-500/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-10 lg:gap-12 pb-12 border-b border-slate-800/80">
            
            <!-- Kolom 1: Branding & Info Perusahaan (Span 5) -->
            <div class="md:col-span-5 space-y-4">
                <div class="flex items-center space-x-3 mb-2">
                    <img src="{{ asset('images/logo-horizontal-darkmode.png') }}" alt="CV Prasta Solusi Indonesia" class="h-10 sm:h-11 w-auto object-contain">
                </div>
                <p class="text-xs text-slate-400">Platform digital resmi perizinan &amp; sertifikasi oleh <strong class="text-slate-200">CV Prasta Solusi Indonesia</strong> (IzinYou)</p>

                <p class="text-sm text-slate-400 leading-relaxed pr-4">
                    Platform digital terdepan yang memprioritaskan kepastian hukum, kecepatan proses, dan transparansi biaya dalam pengurusan perizinan serta sertifikasi bisnis di Indonesia.
                </p>

                <div class="pt-2 flex items-center space-x-3 text-xs text-slate-400">
                    <span class="flex items-center gap-1.5 bg-slate-900 border border-slate-800 px-3 py-1.5 rounded-lg">
                        <span class="w-2 h-2 rounded-full bg-sky-400"></span> Izin &amp; Sertifikat Terverifikasi
                    </span>
                </div>
            </div>

            <!-- Kolom 2: Navigasi Cepat (Span 3) -->
            <div class="md:col-span-3 space-y-4">
                <h4 class="text-white font-bold text-sm tracking-wider uppercase">Navigasi Utama</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#beranda" class="text-slate-400 hover:text-sky-400 transition-colors flex items-center gap-2"><span class="text-sky-500">&rsaquo;</span> Beranda Utama</a></li>
                    <li><a href="#tentang" class="text-slate-400 hover:text-sky-400 transition-colors flex items-center gap-2"><span class="text-sky-500">&rsaquo;</span> Tentang IzinYou</a></li>
                    <li><a href="{{ route('catalog.index') }}" class="text-slate-400 hover:text-sky-400 transition-colors flex items-center gap-2"><span class="text-sky-500">&rsaquo;</span> Katalog Layanan</a></li>
                    <li><a href="#testimoni" class="text-slate-400 hover:text-sky-400 transition-colors flex items-center gap-2"><span class="text-sky-500">&rsaquo;</span> Penilaian Klien</a></li>
                    <li><a href="#kontak" class="text-slate-400 hover:text-sky-400 transition-colors flex items-center gap-2"><span class="text-sky-500">&rsaquo;</span> Hubungi Kami</a></li>
                </ul>
            </div>

            <!-- Kolom 3: Portal Sistem & Layanan Khusus (Span 4) -->
            <div class="md:col-span-4 space-y-4">
                <h4 class="text-white font-bold text-sm tracking-wider uppercase">Akses Portal & Staf</h4>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Masuk ke dalam sistem manajemen terpusat untuk memantau progres perizinan, verifikasi dokumen, dan pengelolaan keuangan.
                </p>
                <div class="pt-1">
                    <a href="/login" class="inline-flex items-center justify-center space-x-2 w-full py-3 px-4 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-sky-600/20 transition-all duration-200">
                        <span>Login Sistem</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
                <div class="pt-2 text-[11px] text-slate-500">
                    Didukung pengamanan Verifikasi 2 Langkah (2FA OTP WhatsApp) &amp; enkripsi data tingkat lanjut.
                </div>
            </div>

        </div>

        <!-- Bagian Hak Cipta Bawah -->
        <div class="pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} <strong>IzinYou.com</strong>. Hak Cipta Dilindungi Undang-Undang. Badan Usaha <strong class="text-slate-400">CV Prasta Solusi Indonesia</strong>.</p>
            <div class="flex items-center space-x-6">
                <span class="hover:text-slate-400 transition-colors">Kebijakan Privasi</span>
                <span>&bull;</span>
                <span class="hover:text-slate-400 transition-colors">Syarat &amp; Ketentuan Layanan</span>
            </div>
        </div>
    </div>
</footer>
<!--FOOTER -->

    <!-- ================================================================ -->
    <!-- JAVASCRIPT: Inisialisasi ikon, interaksi UI, filter & modal      -->
    <!-- ================================================================ -->
    <script>
        // Inisialisasi semua ikon Lucide yang ada di halaman
        lucide.createIcons();

        // ── Toggle Mobile Menu (hamburger) ──
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu    = document.getElementById('mobile-menu');
        const mobileLinks   = document.querySelectorAll('.mobile-link');

        // Tampilkan / sembunyikan menu mobile saat tombol hamburger diklik
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            mobileMenu.classList.toggle('flex');
        });

        // Tutup menu mobile otomatis saat salah satu tautan diklik
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                mobileMenu.classList.remove('flex');
            });
        });

        // ── Efek Navbar & Tombol Scroll to Top saat halaman di-scroll ──
        const scrollToTopBtn = document.getElementById('scrollToTopBtn');
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            // Tambah shadow saat scroll > 20px; kembalikan glass effect di atas
            if (window.scrollY > 20) {
                nav.classList.add('shadow-md');
                nav.classList.replace('glass-nav', 'bg-white/95');
            } else {
                nav.classList.remove('shadow-md');
                nav.classList.replace('bg-white/95', 'glass-nav');
            }
            
            // Tampilkan tombol scroll-to-top setelah scroll 150px ke bawah agar muncul lebih awal dan lebih lama
            if (window.scrollY > 150) {
                scrollToTopBtn.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
                scrollToTopBtn.classList.add('opacity-100', 'translate-y-0');
            } else {
                scrollToTopBtn.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
                scrollToTopBtn.classList.remove('opacity-100', 'translate-y-0');
            }
        });

        // Gulir halaman ke atas dengan animasi smooth
        scrollToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // ── Filter Kartu Layanan Berdasarkan Kategori ──
        function filterServices(category, btn) {
            document.querySelectorAll('.filter-pill').forEach(b => {
                b.classList.remove('bg-sky-600', 'text-white', 'shadow-sky-600/20');
                b.classList.add('bg-white', 'text-slate-700', 'border', 'border-slate-200');
            });
            if (btn) {
                btn.classList.remove('bg-white', 'text-slate-700', 'border', 'border-slate-200');
                btn.classList.add('bg-sky-600', 'text-white', 'shadow-sky-600/20');
            }

            const cards = document.querySelectorAll('.service-card');
            let visibleCount = 0;
            cards.forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            const noResults = document.getElementById('noResults');
            if (noResults) {
                if (visibleCount === 0) {
                    noResults.classList.remove('hidden');
                } else {
                    noResults.classList.add('hidden');
                }
            }
        }

        // ── Buka Modal Detail Layanan ──
        function openServiceDetailModal(btn) {
            const card = btn.closest('.service-card');
            if (!card) return;

            const modalCat = document.getElementById('modalCategory');
            const modalTitle = document.getElementById('modalTitle');
            const modalTime = document.getElementById('modalTime');
            const modalPrice = document.getElementById('modalPrice');
            const modalDesc = document.getElementById('modalDesc');

            if (modalCat) modalCat.textContent = card.dataset.categoryName || 'Perizinan & Sertifikasi';
            if (modalTitle) modalTitle.textContent = card.dataset.title || '';
            if (modalTime) modalTime.textContent = card.dataset.time || '-';
            if (modalPrice) modalPrice.textContent = card.dataset.price || '-';
            if (modalDesc) modalDesc.textContent = card.dataset.desc || '-';

            const docsList = document.getElementById('modalDocsList');
            if (docsList) {
                docsList.innerHTML = '';
                const rawDocs = card.dataset.docs || '';
                const lines = rawDocs.split('\n').map(l => l.replace(/^[•\-\s]+/, '').trim()).filter(l => l.length > 0);
                
                if (lines.length > 0) {
                    lines.forEach(doc => {
                        const li = document.createElement('li');
                        li.className = 'flex items-start gap-2';
                        li.innerHTML = '<i data-lucide="check" class="w-4 h-4 text-sky-500 mt-0.5 shrink-0"></i><span>' + doc + '</span>';
                        docsList.appendChild(li);
                    });
                } else {
                    docsList.innerHTML = '<li class="text-slate-400 italic">Hubungi tim ahli kami untuk rincian berkas teknis.</li>';
                }
            }

            const waUrl = 'https://wa.me/6282128765874?text=' + encodeURIComponent('Halo IzinYou, saya ingin berkonsultasi gratis mengenai: ' + card.dataset.title);
            const waBtn = document.getElementById('modalWhatsAppBtn');
            if (waBtn) waBtn.href = waUrl;

            const modal = document.getElementById('serviceDetailModal');
            const modalCard = document.getElementById('modalCard');
            if (modal && modalCard) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    modalCard.classList.remove('scale-95');
                    modalCard.classList.add('scale-100');
                }, 10);
            }

            if (window.lucide) {
                lucide.createIcons();
            }
        }

        // ── Tutup Modal Detail Layanan ──
        function closeServiceDetailModal() {
            const modal = document.getElementById('serviceDetailModal');
            const modalCard = document.getElementById('modalCard');
            if (!modal) return;
            
            if (modalCard) {
                modalCard.classList.remove('scale-100');
                modalCard.classList.add('scale-95');
            }
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 150);
        }

        document.getElementById('serviceDetailModal')?.addEventListener('click', (e) => {
            if (e.target.id === 'serviceDetailModal') {
                closeServiceDetailModal();
            }
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeServiceDetailModal();
            }
        });

        function selectCategoryFilter(slug) {
            const targetSection = document.getElementById('layanan');
            if (targetSection) {
                targetSection.scrollIntoView({ behavior: 'smooth' });
            }

            setTimeout(() => {
                const pills = Array.from(document.querySelectorAll('.filter-pill'));
                let targetBtn = null;
                if (slug === 'all') {
                    targetBtn = pills[0];
                } else {
                    targetBtn = pills.find(b => {
                        const attr = b.getAttribute('onclick') || '';
                        return attr.toLowerCase().includes(slug.toLowerCase());
                    });
                }

                if (targetBtn) {
                    targetBtn.click();
                } else {
                    filterServices(slug, pills[0] || null);
                }
            }, 300);
        }

        // ── Interactive Hero Showcase Card Tab Switcher ──
        const heroCardData = [
            {
                title: 'Sertifikasi Halal & Standar SNI',
                desc: 'Audit Sistem Jaminan Produk Halal (SJPH), sertifikasi Halal BPJPH RI dan uji kelayakan standar mutu SNI resmi.',
                price: 'Rp 8.5juta'
            },
            {
                title: 'Izin Edar Alkes & BPOM',
                desc: 'Pendampingan pengurusan perizinan dan sertifikasi lengkap dari audit berkas teknis hingga sertifikat resmi kementerian terbit.',
                price: 'Rp 7juta'
            },
            {
                title: 'Izin Distribusi IDAK & CDOB',
                desc: 'Pendampingan sertifikasi kelayakan sarana distribusi, CDOB BPOM Farmasi dan perizinan distributor alat kesehatan (IDAK) Kemenkes.',
                price: 'Rp 15juta'
            }
        ];

        let currentHeroTabIndex = 1;

        function selectHeroTab(index) {
            currentHeroTabIndex = index;
            const titleEl = document.getElementById('heroCardTitle');
            const descEl  = document.getElementById('heroCardDesc');
            const priceEl = document.getElementById('heroCardPrice');

            if (titleEl && descEl && priceEl) {
                titleEl.textContent = heroCardData[index].title;
                descEl.textContent  = heroCardData[index].desc;
                priceEl.textContent = heroCardData[index].price;
            }

            for (let i = 0; i < 3; i++) {
                const tabEl = document.getElementById('heroTab' + i);
                if (tabEl) {
                    if (i === index) {
                        tabEl.classList.add('bg-sky-50/80', 'border-2', 'border-sky-400', 'ring-2', 'ring-sky-200/50', 'shadow-sm');
                        tabEl.classList.remove('border-transparent', 'hover:bg-slate-50');
                    } else {
                        tabEl.classList.remove('bg-sky-50/80', 'border-2', 'border-sky-400', 'ring-2', 'ring-sky-200/50', 'shadow-sm');
                        tabEl.classList.add('border-transparent', 'hover:bg-slate-50');
                    }
                }
            }
        }

        function nextHeroTab() {
            const nextIdx = (currentHeroTabIndex + 1) % heroCardData.length;
            selectHeroTab(nextIdx);
        }

        // ── Animasi Teks Rotasi Kata pada Hero ──
        (function initHeroWordAnimation() {
            // Daftar kata yang akan berotasi secara dinamis (dimulai dari Akuntabel)
            const words = ['Akuntabel.', 'Profesional.', 'Terbaik.', 'Terpercaya.', 'Resmi & Cepat.'];
            let currentIndex = 0;
            const targetEl = document.getElementById('heroAnimatedWord');
            if (!targetEl) return;

            // Timer rotasi setiap 2.6 detik
            setInterval(() => {
                // Tahap 1: Efek transisi keluar (fade out dan geser ke atas)
                targetEl.style.opacity = '0';
                targetEl.style.transform = 'translateY(-12px) scale(0.95)';

                setTimeout(() => {
                    // Ganti teks dengan kata berikutnya
                    currentIndex = (currentIndex + 1) % words.length;
                    targetEl.textContent = words[currentIndex];

                    // Tempatkan elemen di bawah sebelum muncul kembali
                    targetEl.style.transform = 'translateY(12px) scale(0.95)';

                    setTimeout(() => {
                        // Tahap 2: Efek transisi masuk (fade in kembali ke posisi normal)
                        targetEl.style.opacity = '1';
                        targetEl.style.transform = 'translateY(0) scale(1)';
                    }, 50);
                }, 300);
            }, 2600);
        })();
    </script>
</body>
</html>
