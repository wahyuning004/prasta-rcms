<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Semua Layanan &amp; Voucher Promo | PRASTA SOLUSI INDONESIA (IzinYou)</title>
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
                    <button id="mobile-menu-btn" type="button" class="text-slate-600 hover:text-corporate p-2">
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

        <!-- Header Page -->
        <div class="pt-4 pb-8 border-b border-slate-200 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">
                        Katalog Semua Layanan &amp; Voucher Promo
                    </h1>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200">
                        <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                        {{ $services->count() }} Layanan Perizinan &amp; Sertifikasi
                    </span>
                </div>
            </div>
            <p class="text-sm sm:text-base text-slate-500 mt-2 font-normal">
                Pilih layanan perizinan dan sertifikasi resmi berdasarkan kategori, estimasi biaya, waktu pengerjaan, dan persyaratan dokumen dari database kami.
            </p>
        </div>

        <!-- 2-Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- SIDEBAR KIRI -->
            <div class="lg:col-span-4 xl:col-span-3 space-y-6">

                <!-- CARD 1: VOUCHER PROMO DARI DATABASE -->
                @if($featuredPromo)
                @php
                    $remaining = $featuredPromo->remainingQuota();
                    $discountBadge = (float) $featuredPromo->discount_amount > 0
                        ? 'Rp ' . number_format((float) $featuredPromo->discount_amount / 1000, 0, ',', '.') . 'K'
                        : ((float) $featuredPromo->discount_percentage) . '%';
                @endphp
                <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm relative overflow-hidden">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">PROMO AKTIF</span>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 leading-snug">{{ $featuredPromo->title }}</h3>

                    <div class="mt-4 border-2 border-dashed border-sky-300 rounded-2xl p-4 bg-sky-50/50 relative">
                        <div class="w-3.5 h-3.5 bg-white rounded-full absolute -left-2 top-1/2 -translate-y-1/2 border-r border-sky-300"></div>
                        <div class="w-3.5 h-3.5 bg-white rounded-full absolute -right-2 top-1/2 -translate-y-1/2 border-l border-sky-300"></div>

                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="text-[9px] font-bold text-sky-800 tracking-wider uppercase block">IZINYOU.COM</span>
                                <h4 class="font-black text-sm text-slate-900 tracking-tight leading-none mt-0.5">VOUCHER</h4>
                                <p class="text-[10px] text-slate-500 leading-tight mt-1">{{ $featuredPromo->terms }}</p>
                                @if($featuredPromo->expired_date)
                                    <span class="inline-block bg-slate-900 text-white text-[8.5px] font-bold px-2 py-0.5 rounded font-mono mt-2">
                                        EXP: {{ $featuredPromo->expired_date->format('d M Y') }}
                                    </span>
                                @endif
                            </div>
                            <div class="bg-white border border-sky-200 rounded-xl p-2 text-center shadow-sm shrink-0">
                                <span class="text-sm font-black text-sky-700 block leading-tight">{{ $discountBadge }}</span>
                                <span class="text-[8.5px] font-bold text-slate-400 uppercase block">OFF</span>
                            </div>
                        </div>

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
                            </div>
                            <span class="font-mono text-[10px] font-bold tracking-widest text-slate-600 uppercase">{{ $featuredPromo->code }}</span>
                        </div>

                        <div class="mt-3 bg-amber-50 border border-amber-200 rounded-xl p-2.5 text-[10px]">
                            <div class="flex items-center justify-between font-bold text-slate-800 mb-0.5">
                                <span class="flex items-center gap-1 text-amber-700">
                                    <i data-lucide="zap" class="w-3 h-3 text-amber-500 fill-amber-500"></i> {{ $featuredPromo->badge_text ?: 'Kuota Terbatas' }}
                                </span>
                                <span class="text-slate-500">{{ $featuredPromo->used_count }}/{{ $featuredPromo->quota }}</span>
                            </div>
                            <p class="text-[9.5px] text-amber-800 font-semibold leading-tight">
                                Tersisa <strong class="text-rose-600">{{ $remaining }} kuota</strong> lagi sebelum habis!
                            </p>
                        </div>
                    </div>

                    <button type="button" onclick="openVoucherModal('{{ $featuredPromo->code }}', '{{ $featuredPromo->title }}')" class="w-full mt-4 py-3 px-4 bg-sky-600 hover:bg-sky-500 active:bg-sky-700 text-white font-bold text-xs rounded-xl shadow-md shadow-sky-600/25 flex items-center justify-center gap-2 transition-all">
                        <i data-lucide="ticket" class="w-4 h-4"></i>
                        <span>Lihat &amp; Salin Kode Voucher</span>
                    </button>
                </div>
                @endif

                <!-- CARD 2: PENCARIAN & FILTER -->
                <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
                    <form action="{{ route('catalog.index') }}" method="GET" id="catalogFilterForm" class="space-y-5">
                        <!-- Input Search -->
                        <div class="relative">
                            <input
                                type="text"
                                name="search"
                                id="catalogSearch"
                                value="{{ $search }}"
                                placeholder="Cari layanan, sertifikasi, syarat..."
                                class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-sky-500 focus:bg-white bg-slate-50 transition-colors"
                                oninput="filterCatalog()"
                            >
                            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute right-3.5 top-3 pointer-events-none"></i>
                        </div>

                        <!-- Filter Kategori -->
                        <div class="pb-5 border-b border-slate-100">
                            <label for="categoryFilter" class="block text-xs font-bold text-slate-900 uppercase tracking-wider mb-2">
                                Kategori Perizinan
                            </label>
                            <select name="category" id="categoryFilter" onchange="filterCatalog()" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs text-slate-700 bg-slate-50 focus:outline-none focus:border-sky-500 font-medium">
                                <option value="all" {{ $selectedCategory === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->slug }}" {{ $selectedCategory === $cat->slug ? 'selected' : '' }}>
                                        {{ $cat->name }} ({{ $cat->services_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Ratings -->
                        <div>
                            <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">
                                Filter Rating Ulasan
                            </h4>
                            <div class="space-y-2">
                                @for($star = 5; $star >= 1; $star--)
                                    <label class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 cursor-pointer group transition-colors">
                                        <div class="flex items-center gap-2.5">
                                            <input type="radio" name="rating" value="{{ $star }}" {{ (string) $selectedRating === (string) $star ? 'checked' : '' }} onchange="filterCatalog()" class="w-4 h-4 text-sky-600 focus:ring-sky-500 border-slate-300">
                                            <div class="flex gap-0.5">
                                                @for($s = 1; $s <= 5; $s++)
                                                    <i data-lucide="star" class="w-3.5 h-3.5 {{ $s <= $star ? 'text-amber-400 fill-current' : 'text-slate-200' }}"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        <span class="text-[11px] font-semibold text-slate-500">{{ $star }} Bintang</span>
                                    </label>
                                @endfor

                                <label class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 cursor-pointer group transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <input type="radio" name="rating" value="all" {{ $selectedRating === 'all' ? 'checked' : '' }} onchange="filterCatalog()" class="w-4 h-4 text-sky-600 focus:ring-sky-500 border-slate-300">
                                        <span class="text-xs font-bold text-slate-700">Semua Layanan</span>
                                    </div>
                                    <span class="text-xs font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-full border border-sky-100">{{ $services->count() }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="pt-2 flex gap-2">
                            <button type="submit" class="flex-1 py-2.5 px-3 bg-corporate hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition">
                                Terapkan Filter
                            </button>
                            <a href="{{ route('catalog.index') }}" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition text-center">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

            </div>

            <!-- MAIN GRID LAYANAN KANAN -->
            <div class="lg:col-span-8 xl:col-span-9">

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5" id="catalogCardsContainer">
                    @forelse($services as $svc)
                    @php
                        $rawPrice = $svc->getEffectivePrice();
                        $jutaVal = round($rawPrice / 1000000, 1);
                        $kajianVal = round($jutaVal * 0.75, 1);
                        $pendampinganVal = $jutaVal;
                        $fullTuntasVal = round($jutaVal * 1.4, 1);

                        $avgRating = $svc->published_reviews_avg_rating ? round((float) $svc->published_reviews_avg_rating, 1) : 0;
                        $reviewCount = (int) ($svc->published_reviews_count ?? 0);
                        $roundedRating = $reviewCount > 0 ? (int) round($avgRating) : 0;
                    @endphp
                    <div class="catalog-card bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-xl hover:border-sky-300 transition-all duration-300 group"
                         data-name="{{ strtolower($svc->name . ' ' . ($svc->description ?? '') . ' ' . ($svc->requirements_info ?? '')) }}"
                         data-category="{{ $svc->category ? $svc->category->slug : 'umum' }}"
                         data-rating="{{ $roundedRating }}"
                    >
                        <!-- Top Half -->
                        <div class="bg-gradient-to-br from-sky-600 via-sky-700 to-blue-800 p-3.5 sm:p-4 text-white text-center relative overflow-hidden">
                            <span class="text-[9.5px] font-semibold text-sky-100/90 block mb-1 tracking-wide">
                                {{ $svc->category?->name ?? 'IzinYou.com' }}
                            </span>

                            <h4 class="font-black text-xs uppercase tracking-tight text-white line-clamp-1 mb-0.5" title="{{ $svc->name }}">
                                {{ $svc->name }}
                            </h4>

                            <span class="text-[8px] font-bold tracking-widest text-sky-200 uppercase block mb-2.5">
                                ESTIMASI: {{ $svc->timeline_estimation ?: ($svc->estimation_time ?: '1-3 Bulan') }}
                            </span>

                            <!-- 3 Comparison Columns Inside -->
                            <div class="bg-white rounded-xl p-1.5 grid grid-cols-3 gap-1 text-slate-800 shadow-md border border-white/30 text-center">
                                <div class="p-1 rounded-lg">
                                    <span class="text-[7.5px] font-black text-slate-700 block uppercase leading-none">KAJIAN BERKAS</span>
                                    <span class="text-[6.5px] text-slate-400 block mt-0.5 leading-none">Audit Syarat</span>
                                    <div class="mt-2 pt-1 border-t border-slate-100">
                                        <span class="text-xs font-black text-sky-700 block leading-tight">{{ $kajianVal }}</span>
                                        <span class="text-[7px] font-bold text-slate-500 uppercase block">JUTA</span>
                                    </div>
                                </div>

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
                        </div>

                        <!-- Bottom Half -->
                        <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                            <div>
                                <h3 class="font-extrabold text-sm text-slate-900 leading-snug line-clamp-2 min-h-[2.5rem] group-hover:text-sky-700 transition-colors mb-1.5">
                                    {{ $svc->name }}
                                </h3>
                                <p class="text-[11px] text-slate-500 line-clamp-2 mb-2.5">{{ $svc->description }}</p>

                                <!-- Real Star Ratings & Review Count -->
                                <div class="flex items-center gap-1 text-xs">
                                    @if($reviewCount > 0)
                                        <div class="flex text-amber-400 gap-0.5">
                                            @for($s = 1; $s <= 5; $s++)
                                                <i data-lucide="star" class="w-3.5 h-3.5 {{ $s <= $roundedRating ? 'fill-current' : 'text-slate-200' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="font-bold text-slate-600 text-[11px] ml-1">{{ $avgRating }} ({{ $reviewCount }} Ulasan)</span>
                                    @else
                                        <span class="text-[11px] text-slate-400 font-medium">Belum ada ulasan</span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <div class="mb-3 pt-2 border-t border-slate-100 flex items-end justify-between">
                                    <div>
                                        <span class="text-[9px] text-slate-400 uppercase font-semibold block leading-none mb-0.5">estimasi biaya</span>
                                        <p class="text-base font-black text-slate-900 leading-tight">
                                            {{ $svc->formatted_price }}
                                        </p>
                                    </div>
                                    <span class="text-[10px] font-semibold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-full">
                                        {{ $svc->timeline_estimation ?: ($svc->estimation_time ?: '-') }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <a href="{{ route('services.show', $svc->slug) }}" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-corporate font-bold text-xs rounded-xl transition-all flex items-center justify-center gap-1">
                                        <span>Detail</span>
                                    </a>
                                    @auth
                                        @if(auth()->user()->isKlien())
                                            <a href="{{ route('klien.checkout.show', $svc->slug) }}" class="py-2.5 px-3 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs rounded-xl shadow-sm transition-all flex items-center justify-center gap-1">
                                                <span>Pesan</span>
                                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('services.show', $svc->slug) }}" class="py-2.5 px-3 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs rounded-xl shadow-sm transition-all flex items-center justify-center gap-1">
                                                <span>Lihat</span>
                                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="py-2.5 px-3 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs rounded-xl shadow-sm transition-all flex items-center justify-center gap-1">
                                            <span>Pesan</span>
                                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-slate-200">
                        <i data-lucide="folder-search" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                        <p class="font-bold text-corporate text-base">Belum ada layanan yang sesuai dengan pencarian Anda.</p>
                        <p class="text-xs text-slate-400 mt-1">Coba kata kunci lain atau reset filter kategori.</p>
                        <a href="{{ route('catalog.index') }}" class="inline-block mt-4 px-4 py-2 bg-sky-50 text-sky-700 font-bold text-xs rounded-xl border border-sky-200">Reset Filter</a>
                    </div>
                    @endforelse
                </div>

                <!-- Empty State JS Filter -->
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

    <!-- Modal Voucher Promo -->
    <div id="voucherModal" class="fixed inset-0 z-[120] hidden items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm transition-all duration-300">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-md w-full p-6 text-center relative overflow-hidden transform scale-95 transition-transform" id="voucherCard">
            <button type="button" onclick="closeVoucherModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center absolute top-4 right-4 transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>

            <div class="w-14 h-14 rounded-2xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="ticket" class="w-7 h-7 text-sky-600"></i>
            </div>

            <h3 class="text-xl font-black text-slate-900" id="voucherModalTitle">Voucher Promo IzinYou</h3>
            <p class="text-xs text-slate-500 mt-1">Masukkan kode promo resmi ini pada halaman checkout untuk mendapatkan potongan biaya.</p>

            <div class="my-5 p-4 bg-slate-50 border-2 border-dashed border-sky-300 rounded-2xl flex items-center justify-between">
                <div>
                    <span class="text-[9px] font-bold text-slate-400 block uppercase tracking-wider text-left">KODE VOUCHER</span>
                    <span class="font-mono text-xl font-black text-sky-700 tracking-widest" id="voucherCodeText">-</span>
                </div>
                <button type="button" onclick="copyVoucherCode()" id="copyCodeBtn" class="px-4 py-2 bg-white border border-sky-200 hover:bg-sky-50 text-sky-700 text-xs font-bold rounded-xl shadow-sm transition">
                    Salin Kode
                </button>
            </div>

            <button type="button" onclick="closeVoucherModal()" class="w-full py-2.5 text-xs font-semibold text-slate-500 hover:text-slate-700">
                Tutup
            </button>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-slate-950 text-slate-300 relative border-t border-slate-800 pt-16 pb-12 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10 lg:gap-12 pb-12 border-b border-slate-800/80">
                <div class="md:col-span-5 space-y-4">
                    <div class="flex items-center space-x-3 mb-2">
                        <img src="{{ asset('images/logo-horizontal-darkmode.png') }}" alt="CV Prasta Solusi Indonesia" class="h-10 sm:h-11 w-auto object-contain">
                    </div>
                    <p class="text-xs text-slate-400">Platform digital resmi perizinan &amp; sertifikasi oleh <strong class="text-slate-200">CV Prasta Solusi Indonesia</strong> (IzinYou)</p>
                </div>
                <div class="md:col-span-3 space-y-4">
                    <h4 class="text-white font-bold text-sm tracking-wider uppercase">Navigasi Utama</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('home') }}#beranda" class="text-slate-400 hover:text-sky-400 transition-colors">Beranda Utama</a></li>
                        <li><a href="{{ route('catalog.index') }}" class="text-sky-400 font-semibold">Katalog Layanan</a></li>
                        <li><a href="{{ route('home') }}#kontak" class="text-slate-400 hover:text-sky-400 transition-colors">Hubungi Kami</a></li>
                    </ul>
                </div>
                <div class="md:col-span-4 space-y-4">
                    <h4 class="text-white font-bold text-sm tracking-wider uppercase">Akses Portal</h4>
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center space-x-2 w-full py-3 px-4 bg-sky-600 hover:bg-sky-500 text-white text-sm font-semibold rounded-xl shadow-lg">
                        <span>Login Sistem</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
            <div class="pt-8 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} <strong>IzinYou.com</strong>. CV Prasta Solusi Indonesia.
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();

        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu    = document.getElementById('mobile-menu');
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        const voucherModal = document.getElementById('voucherModal');
        const voucherCard  = document.getElementById('voucherCard');

        function openVoucherModal(code, title) {
            if (code) document.getElementById('voucherCodeText').innerText = code;
            if (title) document.getElementById('voucherModalTitle').innerText = title;
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
            navigator.clipboard?.writeText(code).then(() => {
                const btn = document.getElementById('copyCodeBtn');
                btn.innerText = 'Tersalin!';
                setTimeout(() => { btn.innerText = 'Salin Kode'; }, 2000);
            });
        }

        function filterCatalog() {
            const query = (document.getElementById('catalogSearch').value || '').toLowerCase().trim();
            const category = document.getElementById('categoryFilter').value;
            const ratingRadio = document.querySelector('input[name="rating"]:checked');
            const rating = ratingRadio ? ratingRadio.value : 'all';

            const cards = document.querySelectorAll('.catalog-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const cardName = card.getAttribute('data-name') || '';
                const cardCategory = card.getAttribute('data-category') || '';
                const cardRating = card.getAttribute('data-rating') || '0';

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
                noResultsEl.classList.toggle('hidden', visibleCount > 0 || cards.length === 0);
            }
        }

        function resetFilters() {
            window.location.href = "{{ route('catalog.index') }}";
        }
    </script>
</body>
</html>
