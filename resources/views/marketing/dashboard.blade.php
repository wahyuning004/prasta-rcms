<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Marketing &amp; Client Relations - IzinYou</title>
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
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-indigo-600 selection:text-white flex h-screen overflow-hidden">

    @include('marketing.partials.sidebar')

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/60">

        <!-- Header -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0 relative z-10">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-corporate">Dashboard Marketing &amp; Client Relations</h2>
                    <span class="bg-indigo-100 text-indigo-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full">Sistem IZINYOU</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Ringkasan operasional pertanyaan masuk, konversi calon pelanggan, promosi, dan moderasi ulasan.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('marketing.promos.index') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Kelola Kupon Promo</span>
                </a>
            </div>
        </header>

        @include('marketing.partials.alerts')

        <!-- Scrollable Content: Overview / Monitoring Only -->
        <div class="flex-1 overflow-y-auto p-8 pt-4 space-y-8" id="overview">

            <!-- 1. Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Pertanyaan Masuk</p>
                            <h3 class="text-3xl font-extrabold text-corporate">{{ $stats['totalConsultations'] }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="message-square-text" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-xs text-indigo-600 font-medium">{{ $stats['pendingConsultations'] }} belum ditangani &bull; {{ $stats['respondedConsultations'] }} selesai</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Konversi Pelanggan</p>
                            <h3 class="text-3xl font-extrabold text-corporate">{{ $stats['convertedCustomersCount'] }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="users" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-xs text-emerald-700 font-medium">Dari {{ $stats['registeredClientsCount'] }} akun terdaftar &bull; {{ $stats['totalProspects'] }} prospek</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Penggunaan Promosi</p>
                            <h3 class="text-3xl font-extrabold text-corporate">{{ $stats['totalPromosUsed'] }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="tag" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-xs text-sky-600 font-medium">{{ $stats['activePromos'] }} aktif &bull; {{ $stats['expiredPromos'] }} kedaluwarsa</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Ulasan &amp; Rating</p>
                            <h3 class="text-3xl font-extrabold text-corporate flex items-center gap-1.5">
                                {{ number_format((float) $stats['avgRating'], 1) }} <span class="text-amber-400 text-2xl">★</span>
                            </h3>
                        </div>
                        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="star" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">{{ $stats['pendingReviews'] }} pending &bull; {{ $stats['publishedReviews'] }} dari {{ $stats['totalReviews'] }} publik</p>
                </div>
            </div>

            <!-- 2. Ringkasan Pertanyaan Masuk & Konversi Calon Pelanggan -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="operasional">
                <!-- Panel 1: Ringkasan Tiket & Inquiry -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <i data-lucide="inbox" class="w-5 h-5 text-indigo-600"></i>
                                <h3 class="text-sm font-bold text-corporate">Tiket, Konsultasi &amp; Inquiry</h3>
                            </div>
                            <span class="text-[11px] font-bold bg-indigo-50 text-indigo-700 px-2.5 py-0.5 rounded-full">{{ $stats['totalConsultations'] }} Total</span>
                        </div>

                        <div class="space-y-2.5 text-xs">
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-600">Konsultasi Gratis (Umum / Akun Klien)</span>
                                <strong class="text-corporate">{{ $stats['directConsultationCount'] }}</strong>
                            </div>
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-600">Inquiry Formulir Layanan / Tamu</span>
                                <strong class="text-corporate">{{ $stats['serviceInquiryCount'] }}</strong>
                            </div>
                            <div class="grid grid-cols-2 gap-2.5 pt-1">
                                <div class="p-3 rounded-xl bg-amber-50/70 border border-amber-200/80">
                                    <span class="text-[10px] font-bold uppercase text-amber-700 block">Belum Ditangani</span>
                                    <strong class="text-lg font-extrabold text-amber-900">{{ $stats['pendingConsultations'] }}</strong>
                                </div>
                                <div class="p-3 rounded-xl bg-sky-50/70 border border-sky-200/80">
                                    <span class="text-[10px] font-bold uppercase text-sky-700 block">Sudah Ditangani</span>
                                    <strong class="text-lg font-extrabold text-sky-900">{{ $stats['respondedConsultations'] }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400">Kelola balasan &amp; status pesan masuk</span>
                        <a href="{{ route('marketing.tickets') }}" class="px-4 py-2 bg-corporate hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition-all inline-flex items-center gap-1.5">
                            <span>Buka Tiket &amp; Live Chat</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Panel 2: Konversi Calon Pelanggan -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between" id="konversi">
                    <div>
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <i data-lucide="trending-up" class="w-5 h-5 text-emerald-600"></i>
                                <h3 class="text-sm font-bold text-corporate">Konversi Calon Pelanggan</h3>
                            </div>
                            <span class="text-[11px] font-bold bg-emerald-50 text-emerald-700 px-2.5 py-0.5 rounded-full">{{ $stats['prospectToCustomerRate'] }}% Konversi</span>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="font-semibold text-slate-700">1. Calon Pelanggan (Prospek)</span>
                                    <strong class="text-corporate">{{ $stats['totalProspects'] }} Orang</strong>
                                </div>
                                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-slate-700 rounded-full" style="width: {{ $stats['totalProspects'] > 0 ? 100 : 0 }}%"></div>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $stats['guestLeadCount'] }} prospek tamu (konsultasi/klaim promo) &bull; {{ $stats['registeredClientsCount'] }} pendaftar</p>
                            </div>

                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="font-semibold text-slate-700">2. Sudah Pendaftaran Akun</span>
                                    <strong class="text-indigo-700">{{ $stats['registeredClientsCount'] }} Akun ({{ $stats['prospectToRegisteredRate'] }}%)</strong>
                                </div>
                                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-600 rounded-full" style="width: {{ min(100, $stats['prospectToRegisteredRate']) }}%"></div>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $stats['verifiedClientsCount'] }} terverifikasi OTP &bull; {{ $stats['unverifiedClientsCount'] }} menunggu OTP</p>
                            </div>

                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="font-semibold text-slate-700">3. Menjadi Pelanggan / Klien Aktif</span>
                                    <strong class="text-emerald-700">{{ $stats['convertedCustomersCount'] }} Klien ({{ $stats['registeredToCustomerRate'] }}% dr daftar)</strong>
                                </div>
                                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-600 rounded-full" style="width: {{ min(100, $stats['prospectToCustomerRate']) }}%"></div>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-0.5">Klien terdaftar yang telah melakukan pemesanan layanan.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Rasio Pendaftaran &rarr; Pelanggan:</span>
                        <strong class="text-emerald-700">{{ $stats['registeredToCustomerRate'] }}%</strong>
                    </div>
                </div>
            </div>

            <!-- 3. Ringkasan Kupon & Kuota Promo + Ringkasan Moderasi Ulasan Klien -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Panel 3: Ringkasan Kupon & Kuota Promo -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <i data-lucide="tags" class="w-5 h-5 text-indigo-600"></i>
                                <h3 class="text-sm font-bold text-corporate">Kupon &amp; Kuota Promo</h3>
                            </div>
                            <span class="text-[11px] font-bold bg-sky-50 text-sky-700 px-2.5 py-0.5 rounded-full">{{ $stats['totalPromos'] }} Kupon</span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-xs mb-4">
                            <div class="p-3.5 rounded-2xl bg-sky-50/70 border border-sky-100">
                                <span class="text-[11px] text-slate-500 block">Kupon Aktif</span>
                                <strong class="text-2xl font-extrabold text-sky-700">{{ $stats['activePromos'] }}</strong>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-indigo-50/70 border border-indigo-100">
                                <span class="text-[11px] text-slate-500 block">Penggunaan Bulan Ini</span>
                                <strong class="text-2xl font-extrabold text-indigo-700">{{ $stats['monthlyPromoOrdersCount'] }}</strong>
                            </div>
                        </div>

                        <div class="space-y-2 text-xs">
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-600">Total Penggunaan Seluruh Voucher</span>
                                <strong class="text-corporate">{{ $stats['totalPromosUsed'] }} Pemakaian</strong>
                            </div>
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-600">Voucher Paling Sering Digunakan</span>
                                @if($stats['topPromoCode'])
                                    <strong class="font-mono text-indigo-700">{{ $stats['topPromoCode'] }} ({{ $stats['topPromoUsedCount'] }}x)</strong>
                                @else
                                    <span class="text-slate-400">Belum ada voucher yang digunakan.</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-600">Kupon Kedaluwarsa / Non-Aktif</span>
                                <strong class="text-rose-600">{{ $stats['expiredPromos'] }} Kedaluwarsa &bull; {{ $stats['inactivePromos'] }} Non-Aktif</strong>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400">Tambah, ubah, &amp; atur kuota promo</span>
                        <a href="{{ route('marketing.promos.index') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl transition-all inline-flex items-center gap-1.5 shadow-sm">
                            <span>Kelola Kupon</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Panel 4: Ringkasan Moderasi Ulasan Klien -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <i data-lucide="star-half" class="w-5 h-5 text-amber-500"></i>
                                <h3 class="text-sm font-bold text-corporate">Moderasi Ulasan Klien</h3>
                            </div>
                            <span class="text-[11px] font-bold bg-amber-50 text-amber-700 px-2.5 py-0.5 rounded-full">★ {{ number_format((float) $stats['avgRating'], 1) }}</span>
                        </div>

                        <div class="grid grid-cols-3 gap-3 text-xs mb-4">
                            <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-200/80">
                                <span class="text-[11px] text-amber-700 font-semibold block">Review Pending</span>
                                <strong class="text-2xl font-extrabold text-amber-900">{{ $stats['pendingReviews'] }}</strong>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-sky-50/70 border border-sky-200/80">
                                <span class="text-[11px] text-sky-700 font-semibold block">Disetujui</span>
                                <strong class="text-2xl font-extrabold text-sky-900">{{ $stats['publishedReviews'] }}</strong>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-200/80">
                                <span class="text-[11px] text-rose-700 font-semibold block">Ditolak</span>
                                <strong class="text-2xl font-extrabold text-rose-900">{{ $stats['rejectedReviews'] }}</strong>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-600 leading-relaxed">
                            Total <strong class="text-corporate">{{ $stats['totalReviews'] }} ulasan</strong> pelanggan tercatat. Ulasan berstatus <strong>Disetujui</strong> tampil otomatis sebagai testimoni di halaman publik.
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400">Setujui atau tolak ulasan masuk</span>
                        <a href="{{ route('marketing.reviews.index') }}" class="px-4 py-2 bg-corporate hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition-all inline-flex items-center gap-1.5 shadow-sm">
                            <span>Lihat Review</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
