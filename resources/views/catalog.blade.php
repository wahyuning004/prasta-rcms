<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Semua Layanan & Voucher Promo | PRASTA SOLUSI INDONESIA (IzinYou)</title>
    <meta name="description" content="Katalog lengkap layanan perizinan dan sertifikasi resmi (BPOM, Kemenkes, Halal, SNI, IDAK, CDOB) dan voucher promo diskon dari CV Prasta Solusi Indonesia.">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon-192.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
        
        /* Barcode lines */
        .barcode-line {
            display: inline-block;
            height: 28px;
            background-color: #334155;
            margin-right: 2px;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-sky-500 selection:text-white">

    <!-- 1. Top Bar & Navbar -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo & Name (IzinYou dengan Logo Hexagon Resmi) -->
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-3 cursor-pointer group">
                    <img src="{{ asset('images/logo-symbol-transparent.png') }}" alt="IzinYou" class="h-10 sm:h-11 w-auto object-contain transition-transform duration-300 group-hover:scale-105">
                    <div>
                        <h1 class="font-black text-2xl leading-none text-slate-900 tracking-tight flex items-center gap-1.5">
                            Izin<span class="text-sky-600">You</span>
                            <span class="text-[10px] bg-sky-100 text-sky-800 font-bold px-2 py-0.5 rounded-full">.com</span>
                        </h1>
                        <p class="text-[10px] text-slate-500 font-medium leading-none mt-1">by CV Prasta Solusi Indonesia</p>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex space-x-7 items-center">
                    <a href="{{ route('home') }}#beranda" class="text-sm font-semibold text-slate-600 hover:text-sky-600 transition-colors">Beranda</a>
                    <a href="{{ route('home') }}#tentang" class="text-sm font-semibold text-slate-600 hover:text-sky-600 transition-colors">Tentang Kami</a>
                    <a href="{{ route('catalog.index') }}" class="text-sm font-bold text-sky-600 transition-colors flex items-center gap-1.5 py-1 px-3 bg-sky-50 rounded-full border border-sky-200">
                        <span>Katalog Layanan</span>
                        <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                    </a>
                    <a href="{{ route('home') }}#testimoni" class="text-sm font-semibold text-slate-600 hover:text-sky-600 transition-colors">Penilaian Klien</a>
                    <a href="{{ route('home') }}#kontak" class="text-sm font-semibold text-slate-600 hover:text-sky-600 transition-colors">Kontak</a>
                    
                    <div class="h-6 w-px bg-slate-300"></div>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-bold text-white bg-sky-600 hover:bg-sky-500 px-5 py-2.5 rounded-full transition-colors flex items-center gap-2 shadow-md shadow-sky-600/20">
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
        </div>

        <!-- Mobile Menu Dropdown -->
        <div id="mobile-menu" class="hidden lg:hidden bg-white border-b border-slate-200 px-4 pt-2 pb-6 space-y-3 shadow-xl">
            <a href="{{ route('home') }}#beranda" class="block text-sm font-semibold text-slate-900 hover:text-sky-600 py-2">Beranda</a>
            <a href="{{ route('home') }}#tentang" class="block text-sm font-semibold text-slate-600 hover:text-sky-600 py-2">Tentang Kami</a>
            <a href="{{ route('catalog.index') }}" class="block text-sm font-bold text-sky-600 py-2">Katalog Layanan &amp; Promo</a>
            <a href="{{ route('home') }}#testimoni" class="block text-sm font-semibold text-slate-600 hover:text-sky-600 py-2">Penilaian Klien</a>
            <a href="{{ route('home') }}#kontak" class="block text-sm font-semibold text-slate-600 hover:text-sky-600 py-2">Kontak</a>
            <div class="pt-2 border-t border-slate-100">
                @auth
                    <a href="{{ route('dashboard') }}" class="block w-full text-center text-sm font-bold text-white bg-sky-600 px-5 py-3 rounded-xl shadow-md">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="block w-full text-center text-sm font-bold text-white bg-corporate px-5 py-3 rounded-xl shadow-md">Login Sistem</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="pt-28 pb-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Page (Sesuai Gambar User) -->
        <div class="pt-4 pb-8 border-b border-slate-200 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">
                        Katalog Semua Layanan &amp; Voucher Promo
                    </h1>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200">
                        <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                        {{ $services->count() }}+ Layanan Perizinan &amp; Sertifikasi
                    </span>
                </div>
            </div>
            <p class="text-sm sm:text-base text-slate-500 mt-2 font-normal">
                Memberikan Pelayanan Perizinan &amp; Sertifikasi Bisnis <strong class="text-slate-800 font-bold">Profesional &amp; Terpercaya.</strong>
            </p>
        </div>

        <!-- 2-Column Layout (Sidebar Kiri: Promo & Filter | Kanan: Grid Layanan) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- SIDEBAR KIRI (Span 4 / Span 3.5) -->
            <div class="lg:col-span-4 xl:col-span-3 space-y-6">
                
                <!-- CARD 1: VOUCHER PROMO (Sesuai Gambar User) -->
                <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm relative overflow-hidden">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">PROMO</span>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 leading-snug">Voucher Diskon 10%</h3>

                    <!-- Ticket Voucher Box -->
                    <div class="mt-4 border-2 border-dashed border-sky-300 rounded-2xl p-4 bg-sky-50/50 relative">
                        <!-- Cutout circles on borders -->
                        <div class="w-3.5 h-3.5 bg-white rounded-full absolute -left-2 top-1/2 -translate-y-1/2 border-r border-sky-300"></div>
                        <div class="w-3.5 h-3.5 bg-white rounded-full absolute -right-2 top-1/2 -translate-y-1/2 border-l border-sky-300"></div>

                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="text-[9px] font-bold text-sky-800 tracking-wider uppercase block">IZINYOU.COM</span>
                                <h4 class="font-black text-sm text-slate-900 tracking-tight leading-none mt-0.5">VOUCHER</h4>
                                <p class="text-[10px] text-slate-500 leading-tight mt-1">Berlaku untuk seluruh layanan legalitas &amp; perizinan</p>
                                <span class="inline-block bg-slate-900 text-white text-[8.5px] font-bold px-2 py-0.5 rounded font-mono mt-2">
                                    EXP: 24 Nov 2026
                                </span>
                            </div>
                            <div class="bg-white border border-sky-200 rounded-xl p-2 text-center shadow-sm shrink-0">
                                <span class="text-sm font-black text-sky-700 block leading-tight">Rp 250K</span>
                                <span class="text-[8.5px] font-bold text-slate-400 uppercase block">OFF</span>
                            </div>
                        </div>

                        <!-- Barcode Section -->
                        <div class="mt-4 pt-3 border-t border-dashed border-sky-200 flex items-center justify-between">
                            <div class="flex items-center py-0.5" title="Barcode Voucher">
                                <span class="barcode-line w-0.5"></span>
                                <span class="barcode-line w-1"></span>
                                <span class="barcode-line w-0.5"></span>
                                <span class="barcode-line w-1.5"></span>
                                <span class="barcode-line w-0.5"></span>
                                <span class="barcode-line w-1"></span>
                                <span class="barcode-line w-0.5"></span>
                                <span class="barcode-line w-1"></span>
                                <span class="barcode-line w-0.5"></span>
                                <span class="barcode-line w-1.5"></span>
                                <span class="barcode-line w-0.5"></span>
                                <span class="barcode-line w-1"></span>
                            </div>
                            <span class="font-mono text-[10px] font-bold tracking-widest text-slate-400 uppercase">LEGALCEPAT</span>
                        </div>

                        <!-- Banner Kuota Terbatas -->
                        <div class="mt-3 bg-amber-50 border border-amber-200 rounded-xl p-2.5 text-[10px]">
                            <div class="flex items-center justify-between font-bold text-slate-800 mb-0.5">
                                <span class="flex items-center gap-1 text-amber-700">
                                    <i data-lucide="zap" class="w-3 h-3 text-amber-500 fill-amber-500"></i> Khusus 5 Orang Tercepat
                                </span>
                                <span class="text-slate-500">4/20 Klaim</span>
                            </div>
                            <p class="text-[9.5px] text-amber-800 font-semibold leading-tight">
                                Tersisa <strong class="text-rose-600">16 kuota</strong> lagi sebelum hangus!
                            </p>
                        </div>
                    </div>

                    <!-- Tombol Klaim Voucher -->
                    <button type="button" onclick="openVoucherModal()" class="w-full mt-4 py-3 px-4 bg-sky-600 hover:bg-sky-500 active:bg-sky-700 text-white font-bold text-xs rounded-xl shadow-md shadow-sky-600/25 flex items-center justify-center gap-2 transition-all">
                        <i data-lucide="ticket" class="w-4 h-4"></i>
                        <span>Klaim Voucher Sekarang</span>
                    </button>
                </div>

                <!-- CARD 2: PENCARIAN & FILTER RATINGS (Sesuai Gambar User) -->
                <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
                    <!-- Input Search -->
                    <div class="relative mb-6">
                        <input 
                            type="text" 
                            id="catalogSearch" 
                            placeholder="Cari layanan..." 
                            class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-sky-500 focus:bg-white bg-slate-50 transition-colors"
                            oninput="filterCatalog()"
                        >
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute right-3.5 top-3 pointer-events-none"></i>
                    </div>

                    <!-- Filter Kategori Ringkas (Dropdown / Pills) -->
                    <div class="mb-6 pb-5 border-b border-slate-100">
                        <label for="categoryFilter" class="block text-xs font-bold text-slate-900 uppercase tracking-wider mb-2">
                            Kategori Perizinan
                        </label>
                        <select id="categoryFilter" onchange="filterCatalog()" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs text-slate-700 bg-slate-50 focus:outline-none focus:border-sky-500 font-medium">
                            <option value="all">Semua Kategori ({{ $services->count() }})</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}">{{ $cat->name }} ({{ $cat->services_count }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Ratings -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">
                            Ratings
                        </h4>
                        <div class="space-y-2.5">
                            <!-- 5 Stars -->
                            <label class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 cursor-pointer group transition-colors">
                                <div class="flex items-center gap-2.5">
                                    <input type="radio" name="ratingFilter" value="5" onchange="filterCatalog()" class="w-4 h-4 text-sky-600 focus:ring-sky-500 border-slate-300">
                                    <div class="flex text-amber-400 gap-0.5">
                                        <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-slate-400 group-hover:text-slate-600">12</span>
                            </label>

                            <!-- 4 Stars -->
                            <label class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 cursor-pointer group transition-colors">
                                <div class="flex items-center gap-2.5">
                                    <input type="radio" name="ratingFilter" value="4" onchange="filterCatalog()" class="w-4 h-4 text-sky-600 focus:ring-sky-500 border-slate-300">
                                    <div class="flex gap-0.5">
                                        <i data-lucide="star" class="w-3.5 h-3.5 text-amber-400 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 text-amber-400 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 text-amber-400 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 text-amber-400 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 text-slate-200"></i>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-slate-400 group-hover:text-slate-600">5</span>
                            </label>

                            <!-- 3 Stars -->
                            <label class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 cursor-pointer group transition-colors">
                                <div class="flex items-center gap-2.5">
                                    <input type="radio" name="ratingFilter" value="3" onchange="filterCatalog()" class="w-4 h-4 text-sky-600 focus:ring-sky-500 border-slate-300">
                                    <div class="flex gap-0.5">
                                        <i data-lucide="star" class="w-3.5 h-3.5 text-amber-400 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 text-amber-400 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 text-amber-400 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 text-slate-200"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 text-slate-200"></i>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-slate-400 group-hover:text-slate-600">2</span>
                            </label>

                            <!-- 2 Stars -->
                            <label class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 cursor-pointer group transition-colors">
                                <div class="flex items-center gap-2.5">
                                    <input type="radio" name="ratingFilter" value="2" onchange="filterCatalog()" class="w-4 h-4 text-sky-600 focus:ring-sky-500 border-slate-300">
                                    <div class="flex gap-0.5">
                                        <i data-lucide="star" class="w-3.5 h-3.5 text-amber-400 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 text-amber-400 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 text-slate-200"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 text-slate-200"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 text-slate-200"></i>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-slate-400 group-hover:text-slate-600">0</span>
                            </label>

                            <!-- 1 Star -->
                            <label class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 cursor-pointer group transition-colors">
                                <div class="flex items-center gap-2.5">
                                    <input type="radio" name="ratingFilter" value="1" onchange="filterCatalog()" class="w-4 h-4 text-sky-600 focus:ring-sky-500 border-slate-300">
                                    <div class="flex gap-0.5">
                                        <i data-lucide="star" class="w-3.5 h-3.5 text-amber-400 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 text-slate-200"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 text-slate-200"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 text-slate-200"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 text-slate-200"></i>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-slate-400 group-hover:text-slate-600">0</span>
                            </label>

                            <!-- Semua (Default) -->
                            <label class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 cursor-pointer group transition-colors">
                                <div class="flex items-center gap-2.5">
                                    <input type="radio" name="ratingFilter" value="all" checked onchange="filterCatalog()" class="w-4 h-4 text-sky-600 focus:ring-sky-500 border-slate-300">
                                    <span class="text-xs font-bold text-slate-700">Semua Layanan</span>
                                </div>
                                <span class="text-xs font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-full border border-sky-100">{{ $services->count() }}</span>
                            </label>
                        </div>
                    </div>
                </div>

            </div>

            <!-- MAIN GRID LAYANAN KANAN (Span 8 / Span 9) -->
            <div class="lg:col-span-8 xl:col-span-9">
                
                <!-- Services Cards Grid (3 Kolom per Baris di Desktop, Sesuai Gambar User) -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5" id="catalogCardsContainer">
                    @forelse($services as $svc)
                    @php
                        $rawPrice = $svc->price ?: $svc->cost ?: 10000000;
                        $jutaVal = round($rawPrice / 1000000, 1);
                        $kajianVal = round($jutaVal * 0.75, 1);
                        $pendampinganVal = $jutaVal;
                        $fullTuntasVal = round($jutaVal * 1.4, 1);

                        // Mock rating score & reviews for realistic fidelity
                        $ratingScore = 5;
                        $reviewCount = ($svc->id % 2 === 0) ? 130 : (($svc->id % 3 === 0) ? 74 : 60);
                    @endphp
                    <div class="catalog-card bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-xl hover:border-sky-300 transition-all duration-300 group"
                         data-name="{{ strtolower($svc->name) }}"
                         data-category="{{ $svc->category ? $svc->category->slug : 'umum' }}"
                         data-rating="{{ $ratingScore }}"
                    >
                        <!-- Top Half: Deep Ocean Blue Header -->
                        <div class="bg-gradient-to-br from-sky-600 via-sky-700 to-blue-800 p-3.5 sm:p-4 text-white text-center relative overflow-hidden">
                            <span class="text-[9.5px] font-semibold text-sky-100/90 block mb-1 tracking-wide">
                                IzinYou.com
                            </span>
                            
                            <h4 class="font-black text-xs uppercase tracking-tight text-white line-clamp-1 mb-0.5" title="{{ $svc->name }}">
                                {{ $svc->name }}
                            </h4>
                            
                            <span class="text-[8px] font-bold tracking-widest text-sky-200 uppercase block mb-2.5">
                                SKEMA LAYANAN
                            </span>

                            <!-- 3 Comparison Columns Inside -->
                            <div class="bg-white rounded-xl p-1.5 grid grid-cols-3 gap-1 text-slate-800 shadow-md border border-white/30 text-center">
                                
                                <!-- Col 1: Kajian Berkas -->
                                <div class="p-1 rounded-lg">
                                    <span class="text-[7.5px] font-black text-slate-700 block uppercase leading-none">KAJIAN BERKAS</span>
                                    <span class="text-[6.5px] text-slate-400 block mt-0.5 leading-none">Audit Syarat</span>
                                    <div class="mt-2 pt-1 border-t border-slate-100">
                                        <span class="text-xs font-black text-sky-700 block leading-tight">{{ $kajianVal }}</span>
                                        <span class="text-[7px] font-bold text-slate-500 uppercase block">JUTA</span>
                                    </div>
                                </div>

                                <!-- Col 2: Pendampingan (POPULER) -->
                                <div class="relative p-1 bg-sky-50/70 rounded-lg border border-sky-300 ring-1 ring-sky-200">
                                    <span class="absolute -top-2 left-1/2 -translate-x-1/2 bg-amber-400 text-slate-900 text-[6px] font-black px-1 rounded uppercase tracking-wider shadow-sm">
                                        POPULER
                                    </span>
                                    <span class="text-[7.5px] font-black text-sky-900 block uppercase leading-none">PENDAMPINGAN</span>
                                    <span class="text-[6.5px] text-sky-600 block mt-0.5 leading-none font-medium">Mitra Resmi</span>
                                    <div class="mt-2 pt-1 border-t border-sky-200">
                                        <span class="text-xs font-black text-sky-700 block leading-tight">{{ $pendampinganVal }}</span>
                                        <span class="text-[7px] font-bold text-sky-800 uppercase block">JUTA</span>
                                    </div>
                                </div>

                                <!-- Col 3: Full Tuntas (GARANSI RESMI) -->
                                <div class="relative p-1 rounded-lg">
                                    <span class="absolute -top-2 left-1/2 -translate-x-1/2 bg-emerald-500 text-white text-[5.5px] font-black px-1 rounded uppercase tracking-wider shadow-sm">
                                        GARANSI RESMI
                                    </span>
                                    <span class="text-[7.5px] font-black text-slate-700 block uppercase leading-none">FULL TUNTAS</span>
                                    <span class="text-[6.5px] text-emerald-600 block mt-0.5 leading-none font-medium">Garansi Sah</span>
                                    <div class="mt-2 pt-1 border-t border-slate-100">
                                        <span class="text-xs font-black text-sky-700 block leading-tight">{{ $fullTuntasVal }}</span>
                                        <span class="text-[7px] font-bold text-slate-500 uppercase block">JUTA</span>
                                    </div>
                                </div>

                            </div>

                            <!-- Gold Banner Underneath (Sesuai Gambar) -->
                            <div class="bg-amber-400 text-slate-900 text-[8px] font-bold py-1 px-2 rounded-md mt-2 flex items-center justify-center gap-1 shadow-sm">
                                <span class="truncate">Termasuk: Konsultasi Teknis, Review Dokumen...</span>
                            </div>

                            <p class="text-[7.5px] text-sky-100/90 mt-1.5 leading-tight">
                                Pengurusan resmi terkoordinasi dengan kementerian/instansi terkait
                            </p>
                        </div>

                        <!-- Bottom Half: White Details Card -->
                        <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                            <div>
                                <!-- Service Title -->
                                <h3 class="font-extrabold text-sm text-slate-900 leading-snug line-clamp-2 min-h-[2.5rem] group-hover:text-sky-700 transition-colors mb-2">
                                    {{ $svc->name }}
                                </h3>

                                <!-- Star Ratings & Review Count (Sesuai Gambar) -->
                                <div class="flex items-center gap-1 text-xs">
                                    <div class="flex text-amber-400 gap-0.5">
                                        <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                        <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                    </div>
                                    <span class="font-bold text-slate-500 text-[11px] ml-1">{{ $reviewCount }} Review</span>
                                </div>
                            </div>

                            <div>
                                <!-- Price -->
                                <div class="mb-3">
                                    <span class="text-[9px] text-slate-400 uppercase font-semibold block leading-none mb-0.5">mulai dari</span>
                                    <p class="text-base font-black text-slate-900 leading-tight">
                                        {{ $svc->formatted_price }}
                                    </p>
                                </div>

                                <!-- Detail Action Button (Sesuai Gambar User: Solid Blue Pill) -->
                                <a href="{{ route('services.show', $svc->slug) }}" class="w-full py-2.5 px-4 bg-sky-600 hover:bg-sky-500 active:bg-sky-700 text-white font-bold text-xs rounded-xl shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-1.5 group-hover:scale-[1.01]">
                                    <span>Detail</span>
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-slate-200">
                        <i data-lucide="folder-search" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                        <p class="font-bold text-corporate text-base">Belum ada katalog layanan perizinan.</p>
                        <p class="text-xs text-slate-400 mt-1">Silakan hubungi tim kami melalui WhatsApp untuk informasi layanan perizinan khusus.</p>
                    </div>
                    @endforelse
                </div>

                <!-- Empty State Filter Not Found -->
                <div id="catalogNoResults" class="hidden py-16 text-center bg-white rounded-3xl border border-slate-200 mt-6">
                    <i data-lucide="search-x" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                    <h3 class="font-bold text-slate-900 text-base">Tidak ada layanan yang sesuai dengan pencarian Anda.</h3>
                    <p class="text-xs text-slate-500 mt-1">Coba kata kunci pencarian yang lain atau reset filter kategori &amp; rating.</p>
                    <button type="button" onclick="resetFilters()" class="mt-4 px-4 py-2 bg-sky-50 text-sky-700 font-bold text-xs rounded-xl border border-sky-200 hover:bg-sky-100 transition">
                        Reset Filter
                    </button>
                </div>

            </div>

        </div>
    </main>

    <!-- Modal Voucher Klaim Promo -->
    <div id="voucherModal" class="fixed inset-0 z-[120] hidden items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm transition-all duration-300">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-md w-full p-6 text-center relative overflow-hidden transform scale-95 transition-transform" id="voucherCard">
            <button type="button" onclick="closeVoucherModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center absolute top-4 right-4 transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>

            <div class="w-14 h-14 rounded-2xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="ticket" class="w-7 h-7 text-sky-600"></i>
            </div>

            <h3 class="text-xl font-black text-slate-900">Voucher Diskon Rp 250.000</h3>
            <p class="text-xs text-slate-500 mt-1">Gunakan kode promo resmi di bawah ini saat berkonsultasi dengan konsultan perizinan kami.</p>

            <div class="my-5 p-4 bg-slate-50 border-2 border-dashed border-sky-300 rounded-2xl flex items-center justify-between">
                <div>
                    <span class="text-[9px] font-bold text-slate-400 block uppercase tracking-wider text-left">KODE VOUCHER</span>
                    <span class="font-mono text-xl font-black text-sky-700 tracking-widest" id="voucherCodeText">LEGALCEPAT</span>
                </div>
                <button type="button" onclick="copyVoucherCode()" id="copyCodeBtn" class="px-4 py-2 bg-white border border-sky-200 hover:bg-sky-50 text-sky-700 text-xs font-bold rounded-xl shadow-sm transition">
                    Salin Kode
                </button>
            </div>

            <div class="space-y-2">
                <a 
                    id="voucherWaBtn" 
                    href="https://wa.me/6282128765874?text=Halo%20Tim%20IzinYou,%20saya%20mengklaim%20Voucher%20Promo%20LEGALCEPAT%20(Diskon%20Rp%20250.000)%20untuk%20layanan%20perizinan..." 
                    target="_blank" 
                    class="w-full py-3.5 px-4 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs rounded-xl shadow-md shadow-sky-600/25 flex items-center justify-center gap-2 transition"
                >
                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                    <span>Pakai Voucher via WhatsApp</span>
                </a>
                <button type="button" onclick="closeVoucherModal()" class="w-full py-2.5 text-xs font-semibold text-slate-500 hover:text-slate-700">
                    Nanti Saja
                </button>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-slate-950 text-slate-300 relative border-t border-slate-800 pt-16 pb-12 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10 lg:gap-12 pb-12 border-b border-slate-800/80">
                
                <!-- Kolom 1: Branding -->
                <div class="md:col-span-5 space-y-4">
                    <div class="flex items-center space-x-3 mb-2">
                        <img src="{{ asset('images/logo-horizontal-darkmode.png') }}" alt="CV Prasta Solusi Indonesia" class="h-10 sm:h-11 w-auto object-contain">
                    </div>
                    <p class="text-xs text-slate-400">Platform digital resmi perizinan &amp; sertifikasi oleh <strong class="text-slate-200">CV Prasta Solusi Indonesia</strong> (IzinYou)</p>
                    <p class="text-sm text-slate-400 leading-relaxed pr-4">
                        Platform digital terdepan yang memprioritaskan kepastian hukum, kecepatan proses, dan transparansi biaya dalam pengurusan perizinan serta sertifikasi bisnis di Indonesia.
                    </p>
                </div>

                <!-- Kolom 2: Navigasi Cepat -->
                <div class="md:col-span-3 space-y-4">
                    <h4 class="text-white font-bold text-sm tracking-wider uppercase">Navigasi Utama</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('home') }}#beranda" class="text-slate-400 hover:text-sky-400 transition-colors flex items-center gap-2"><span class="text-sky-500">&rsaquo;</span> Beranda Utama</a></li>
                        <li><a href="{{ route('home') }}#tentang" class="text-slate-400 hover:text-sky-400 transition-colors flex items-center gap-2"><span class="text-sky-500">&rsaquo;</span> Tentang Kami</a></li>
                        <li><a href="{{ route('catalog.index') }}" class="text-sky-400 font-semibold flex items-center gap-2"><span class="text-sky-500">&rsaquo;</span> Katalog Layanan</a></li>
                        <li><a href="{{ route('home') }}#testimoni" class="text-slate-400 hover:text-sky-400 transition-colors flex items-center gap-2"><span class="text-sky-500">&rsaquo;</span> Penilaian Klien</a></li>
                        <li><a href="{{ route('home') }}#kontak" class="text-slate-400 hover:text-sky-400 transition-colors flex items-center gap-2"><span class="text-sky-500">&rsaquo;</span> Hubungi Kami</a></li>
                    </ul>
                </div>

                <!-- Kolom 3: Portal Sistem -->
                <div class="md:col-span-4 space-y-4">
                    <h4 class="text-white font-bold text-sm tracking-wider uppercase">Akses Portal &amp; Staf</h4>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Masuk ke dalam sistem manajemen terpusat untuk memantau progres perizinan, verifikasi dokumen, dan pengelolaan keuangan.
                    </p>
                    <div class="pt-1">
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center space-x-2 w-full py-3 px-4 bg-sky-600 hover:bg-sky-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-sky-600/20 transition-all duration-200">
                            <span>Login Sistem</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Copyright -->
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

    <!-- JavaScript Interactivity -->
    <script>
        lucide.createIcons();

        // Toggle Mobile Menu
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu    = document.getElementById('mobile-menu');
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Voucher Modal
        const voucherModal = document.getElementById('voucherModal');
        const voucherCard  = document.getElementById('voucherCard');

        function openVoucherModal() {
            if (voucherModal && voucherCard) {
                voucherModal.classList.remove('hidden');
                voucherModal.classList.add('flex');
                setTimeout(() => {
                    voucherCard.classList.remove('scale-95');
                    voucherCard.classList.add('scale-100');
                }, 10);
            }
        }

        function closeVoucherModal() {
            if (voucherModal && voucherCard) {
                voucherCard.classList.remove('scale-100');
                voucherCard.classList.add('scale-95');
                setTimeout(() => {
                    voucherModal.classList.add('hidden');
                    voucherModal.classList.remove('flex');
                }, 150);
            }
        }

        function copyVoucherCode() {
            const code = document.getElementById('voucherCodeText').innerText;
            navigator.clipboard.writeText(code).then(() => {
                const btn = document.getElementById('copyCodeBtn');
                btn.innerText = 'Tersalin!';
                btn.classList.add('bg-emerald-600', 'text-white', 'border-emerald-600');
                setTimeout(() => {
                    btn.innerText = 'Salin Kode';
                    btn.classList.remove('bg-emerald-600', 'text-white', 'border-emerald-600');
                }, 2000);
            });
        }

        // Filter Catalog by Keyword, Category & Rating
        function filterCatalog() {
            const query = (document.getElementById('catalogSearch').value || '').toLowerCase().trim();
            const category = document.getElementById('categoryFilter').value;
            const ratingRadio = document.querySelector('input[name="ratingFilter"]:checked');
            const rating = ratingRadio ? ratingRadio.value : 'all';

            const cards = document.querySelectorAll('.catalog-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const cardName = card.getAttribute('data-name') || '';
                const cardCategory = card.getAttribute('data-category') || '';
                const cardRating = card.getAttribute('data-rating') || '';

                const matchesQuery = query === '' || cardName.includes(query);
                const matchesCategory = category === 'all' || cardCategory === category;
                const matchesRating = rating === 'all' || cardRating === rating;

                if (matchesQuery && matchesCategory && matchesRating) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            const noResultsEl = document.getElementById('catalogNoResults');
            if (noResultsEl) {
                if (visibleCount === 0) {
                    noResultsEl.classList.remove('hidden');
                } else {
                    noResultsEl.classList.add('hidden');
                }
            }
        }

        function resetFilters() {
            document.getElementById('catalogSearch').value = '';
            document.getElementById('categoryFilter').value = 'all';
            const allRadio = document.querySelector('input[name="ratingFilter"][value="all"]');
            if (allRadio) allRadio.checked = true;
            filterCatalog();
        }
    </script>
</body>
</html>

