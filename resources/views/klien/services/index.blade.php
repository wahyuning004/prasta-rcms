<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Layanan - Portal Klien IzinYou</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon-192.png') }}">
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
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-sky-500 selection:text-white flex h-screen overflow-hidden">

    @include('klien.partials.sidebar')

    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/60">
        <!-- Top Header Portal Klien -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-8 shrink-0 relative z-10">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" onclick="toggleKlienSidebar()" class="lg:hidden p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 shrink-0">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h2 class="text-base sm:text-xl font-bold text-corporate truncate">Katalog Layanan &amp; Pemesanan Perizinan</h2>
                        <span class="hidden sm:inline-block bg-sky-100 text-sky-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full shrink-0">{{ $services->count() }} Layanan</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5 hidden sm:block">Cari layanan perizinan &amp; sertifikasi resmi, lihat persyaratan, lalu lanjutkan ke checkout.</p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('klien.dashboard') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Kembali ke Dashboard</span>
                </a>
            </div>
        </header>

        @include('klien.partials.alerts')

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto scroll-smooth p-4 sm:p-8 pt-5">
            <div class="max-w-7xl mx-auto space-y-6">

                <!-- Promo / Voucher Aktif untuk Checkout -->
                @if($promos->isNotEmpty())
                    <div class="p-4 sm:p-5 rounded-3xl bg-gradient-to-r from-amber-500/10 via-sky-500/10 to-indigo-500/10 border border-amber-200/80 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-bold shrink-0 shadow-sm">
                                <i data-lucide="ticket" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-corporate">Voucher Promo Aktif Tersedia!</h3>
                                <p class="text-[11px] text-slate-600">Klik kode voucher untuk menyalin, lalu tempel pada halaman Checkout pemesanan layanan.</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            @foreach ($promos as $promoItem)
                                <button type="button" onclick="copyKlienPromoCode('{{ $promoItem->code }}', this)"
                                        class="group flex items-center gap-2 px-3 py-2 bg-white border border-amber-300 rounded-xl shadow-sm hover:border-sky-500 transition-all text-left">
                                    <span class="font-mono text-xs font-extrabold text-corporate group-hover:text-sky-600">{{ $promoItem->code }}</span>
                                    <span class="text-[10px] font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-lg">
                                        {{ (float) $promoItem->discount_amount > 0 ? 'Rp ' . number_format((float) $promoItem->discount_amount, 0, ',', '.') : rtrim(rtrim(number_format((float) $promoItem->discount_percentage, 2, ',', '.'), '0'), ',') . '%' }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">({{ $promoItem->used_count }}/{{ $promoItem->quota }})</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Filter & Daftar Layanan -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                    <!-- Panel Filter Kiri -->
                    <div class="lg:col-span-4 xl:col-span-3 space-y-5">
                        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm">
                            <form action="{{ route('klien.services.index') }}" method="GET" id="catalogFilterForm" class="space-y-5">
                                <div>
                                    <label for="catalogSearch" class="block text-xs font-bold text-corporate uppercase tracking-wider mb-2">
                                        Cari Layanan
                                    </label>
                                    <div class="relative">
                                        <input type="text"
                                               name="search"
                                               id="catalogSearch"
                                               value="{{ $search }}"
                                               placeholder="Nama izin, sertifikasi, syarat..."
                                               oninput="filterCatalog()"
                                               class="w-full pl-3.5 pr-9 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-sky-500 focus:bg-white bg-slate-50 transition-colors">
                                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute right-3 top-2.5 pointer-events-none"></i>
                                    </div>
                                </div>

                                <div class="pb-4 border-b border-slate-100">
                                    <label for="categoryFilter" class="block text-xs font-bold text-corporate uppercase tracking-wider mb-2">
                                        Kategori Perizinan
                                    </label>
                                    <select name="category" id="categoryFilter" onchange="filterCatalog()"
                                            class="w-full p-2.5 rounded-xl border border-slate-200 text-xs text-slate-700 bg-slate-50 focus:outline-none focus:border-sky-500 font-medium">
                                        <option value="all" {{ $selectedCategory === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->slug }}" {{ $selectedCategory === $cat->slug ? 'selected' : '' }}>
                                                {{ $cat->name }} ({{ $cat->services_count }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <h4 class="text-xs font-bold text-corporate uppercase tracking-wider mb-2.5">
                                        Filter Rating Ulasan
                                    </h4>
                                    <div class="space-y-1.5">
                                        @for($star = 5; $star >= 1; $star--)
                                            <label class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors">
                                                <div class="flex items-center gap-2">
                                                    <input type="radio" name="rating" value="{{ $star }}" {{ (string) $selectedRating === (string) $star ? 'checked' : '' }} onchange="filterCatalog()" class="w-4 h-4 text-sky-600 focus:ring-sky-500 border-slate-300">
                                                    <div class="flex gap-0.5 text-amber-400">
                                                        @for($s = 1; $s <= 5; $s++)
                                                            <i data-lucide="star" class="w-3.5 h-3.5 {{ $s <= $star ? 'fill-current' : 'text-slate-200' }}"></i>
                                                        @endfor
                                                    </div>
                                                </div>
                                                <span class="text-[11px] font-semibold text-slate-500">{{ $star }} Bintang</span>
                                            </label>
                                        @endfor

                                        <label class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors">
                                            <div class="flex items-center gap-2">
                                                <input type="radio" name="rating" value="all" {{ $selectedRating === 'all' ? 'checked' : '' }} onchange="filterCatalog()" class="w-4 h-4 text-sky-600 focus:ring-sky-500 border-slate-300">
                                                <span class="text-xs font-bold text-slate-700">Semua Layanan</span>
                                            </div>
                                            <span class="text-xs font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-full border border-sky-100">{{ $services->count() }}</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="pt-1 flex gap-2">
                                    <button type="submit" class="flex-1 py-2.5 px-3 bg-corporate hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition">
                                        Terapkan Filter
                                    </button>
                                    <a href="{{ route('klien.services.index') }}" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition text-center">
                                        Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Grid Kartu Layanan -->
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

                                    $requirementsPreview = trim((string) ($svc->requirements_summary ?: $svc->requirements_info));
                                    $reqCount = $svc->requirements->count();
                                @endphp
                                <div class="catalog-card bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-lg hover:border-sky-300 transition-all duration-200 group"
                                     data-name="{{ strtolower($svc->name . ' ' . ($svc->description ?? '') . ' ' . $requirementsPreview) }}"
                                     data-category="{{ $svc->category ? $svc->category->slug : 'umum' }}"
                                     data-rating="{{ $roundedRating }}">

                                    <!-- Header Kartu -->
                                    <div class="bg-gradient-to-br from-sky-600 via-sky-700 to-blue-800 p-4 text-white text-center">
                                        <span class="text-[10px] font-semibold text-sky-100/90 block mb-1 tracking-wide">
                                            {{ $svc->category?->name ?? 'Perizinan Resmi' }}
                                        </span>
                                        <h4 class="font-black text-xs uppercase tracking-tight text-white line-clamp-1 mb-0.5" title="{{ $svc->name }}">
                                            {{ $svc->name }}
                                        </h4>
                                        <span class="text-[9px] font-bold tracking-widest text-sky-200 uppercase block mb-2.5">
                                            ESTIMASI: {{ $svc->timeline_estimation ?: ($svc->estimation_time ?: '1-3 Bulan') }}
                                        </span>

                                        <div class="bg-white rounded-xl p-1.5 grid grid-cols-3 gap-1 text-slate-800 shadow-sm text-center">
                                            <div class="p-1 rounded-lg">
                                                <span class="text-[7.5px] font-black text-slate-700 block uppercase leading-none">KAJIAN</span>
                                                <span class="text-[6.5px] text-slate-400 block mt-0.5 leading-none">Audit Syarat</span>
                                                <div class="mt-1.5 pt-1 border-t border-slate-100">
                                                    <span class="text-xs font-black text-sky-700 block leading-tight">{{ $kajianVal }}</span>
                                                    <span class="text-[7px] font-bold text-slate-500 uppercase block">JUTA</span>
                                                </div>
                                            </div>
                                            <div class="p-1 bg-sky-50/80 rounded-lg border border-sky-300">
                                                <span class="text-[7.5px] font-black text-sky-900 block uppercase leading-none">PENDAMPINGAN</span>
                                                <span class="text-[6.5px] text-sky-600 block mt-0.5 leading-none font-medium">Mitra Resmi</span>
                                                <div class="mt-1.5 pt-1 border-t border-sky-200">
                                                    <span class="text-xs font-black text-sky-700 block leading-tight">{{ $pendampinganVal }}</span>
                                                    <span class="text-[7px] font-bold text-sky-800 uppercase block">JUTA</span>
                                                </div>
                                            </div>
                                            <div class="p-1 rounded-lg">
                                                <span class="text-[7.5px] font-black text-slate-700 block uppercase leading-none">FULL TUNTAS</span>
                                                <span class="text-[6.5px] text-emerald-600 block mt-0.5 leading-none font-medium">Garansi Sah</span>
                                                <div class="mt-1.5 pt-1 border-t border-slate-100">
                                                    <span class="text-xs font-black text-sky-700 block leading-tight">{{ $fullTuntasVal }}</span>
                                                    <span class="text-[7px] font-bold text-slate-500 uppercase block">JUTA</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Konten & Aksi -->
                                    <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                                        <div class="space-y-2">
                                            <h3 class="font-extrabold text-sm text-corporate leading-snug line-clamp-2 group-hover:text-sky-700 transition-colors">
                                                {{ $svc->name }}
                                            </h3>
                                            <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">
                                                {{ $svc->description ?: 'Layanan pendampingan pengurusan perizinan & sertifikasi resmi bersama tim IzinYou.' }}
                                            </p>

                                            @if($requirementsPreview !== '' || $reqCount > 0)
                                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-600">
                                                    <p class="font-bold text-corporate text-[10px] uppercase tracking-wider mb-1">
                                                        Persyaratan {{ $reqCount > 0 ? '(' . $reqCount . ' Dokumen)' : '' }}
                                                    </p>
                                                    @if($requirementsPreview !== '')
                                                        <p class="line-clamp-2 whitespace-pre-line leading-snug">{{ $requirementsPreview }}</p>
                                                    @else
                                                        <p class="leading-snug">{{ $svc->requirements->take(3)->pluck('document_name')->implode(', ') }}</p>
                                                    @endif
                                                </div>
                                            @endif

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
                                            <div class="mb-3 pt-2.5 border-t border-slate-100 flex items-end justify-between">
                                                <div>
                                                    <span class="text-[9px] text-slate-400 uppercase font-semibold block leading-none mb-0.5">Estimasi Biaya</span>
                                                    <p class="text-base font-black text-corporate leading-tight">{{ $svc->formatted_price }}</p>
                                                </div>
                                                <span class="text-[10px] font-semibold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-full">
                                                    {{ $svc->timeline_estimation ?: ($svc->estimation_time ?: '-') }}
                                                </span>
                                            </div>

                                            <div class="grid grid-cols-2 gap-2">
                                                <a href="{{ route('klien.services.show', $svc->id) }}" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-corporate font-bold text-xs rounded-xl transition-all flex items-center justify-center gap-1">
                                                    <span>Detail</span>
                                                </a>
                                                <a href="{{ route('klien.checkout.show', $svc->slug) }}" class="py-2.5 px-3 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs rounded-xl shadow-sm transition-all flex items-center justify-center gap-1">
                                                    <span>Checkout</span>
                                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-slate-200">
                                    <i data-lucide="folder-search" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                                    <p class="font-bold text-corporate text-base">Belum ada layanan yang sesuai dengan pencarian Anda.</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba kata kunci lain atau reset filter kategori.</p>
                                    <a href="{{ route('klien.services.index') }}" class="inline-block mt-4 px-4 py-2 bg-sky-50 text-sky-700 font-bold text-xs rounded-xl border border-sky-200">Reset Filter</a>
                                </div>
                            @endforelse
                        </div>

                        <div id="catalogNoResults" class="hidden py-16 text-center bg-white rounded-3xl border border-slate-200 mt-5">
                            <i data-lucide="search-x" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                            <h3 class="font-bold text-corporate text-base">Tidak ada layanan yang sesuai dengan pencarian Anda.</h3>
                            <p class="text-xs text-slate-500 mt-1">Coba kata kunci pencarian lain atau reset filter kategori &amp; rating.</p>
                            <button type="button" onclick="resetFilters()" class="mt-4 px-4 py-2 bg-sky-50 text-sky-700 font-bold text-xs rounded-xl border border-sky-200 hover:bg-sky-100 transition">
                                Reset Filter
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();

        function copyKlienPromoCode(code, buttonEl) {
            navigator.clipboard?.writeText(code);
            if (buttonEl) {
                const orig = buttonEl.innerHTML;
                buttonEl.classList.add('border-emerald-400', 'bg-emerald-50');
                setTimeout(() => {
                    buttonEl.classList.remove('border-emerald-400', 'bg-emerald-50');
                }, 1200);
            }
            alert('Kode voucher ' + code + ' berhasil disalin. Silakan tempel pada halaman Checkout.');
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
            window.location.href = "{{ route('klien.services.index') }}";
        }
    </script>
</body>
</html>

