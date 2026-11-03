<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Marketing &amp; Client Relations - IzinYou</title>
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

    <!-- Dark Sidebar -->
    <aside class="w-72 bg-corporate text-slate-300 flex flex-col h-full shrink-0 shadow-2xl relative z-20 border-r border-slate-800">
        <!-- Logo Header -->
        <div class="h-20 flex items-center px-6 border-b border-slate-800 gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center shadow-inner flex-shrink-0 border border-slate-700">
                <svg class="w-6 h-6 text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L3 7v6c0 5.5 3.8 10.7 9 12 5.2-1.3 9-6.5 9-12V7l-9-5z"/>
                    <path d="m9 12 2 2 4-4" stroke="#818cf8" stroke-width="2.5"/>
                </svg>
            </div>
            <div>
                <h1 class="font-bold text-white text-base leading-tight">IzinYou Marketing</h1>
                <p class="text-[10px] text-indigo-400 font-semibold tracking-widest uppercase">Client Relations Panel</p>
                <p class="text-[9px] text-slate-400">by CV Prasta Solusi Indonesia</p>
            </div>
        </div>

        <!-- Menu Navigation -->
        <div class="flex-1 overflow-y-auto sidebar-scroll py-6 px-4">
            <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Divisi Pemasaran</p>
            
            <nav class="space-y-1.5">
                <a href="#overview" class="flex items-center gap-3 px-3 py-2.5 bg-indigo-600/15 text-indigo-400 rounded-xl group transition-all font-medium text-sm">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Dashboard Marketing</span>
                </a>

                <a href="#konsultasi" class="flex items-center justify-between px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl group transition-all font-medium text-sm">
                    <div class="flex items-center gap-3">
                        <i data-lucide="message-square-text" class="w-5 h-5"></i>
                        <span>Tiket &amp; Live Chat</span>
                    </div>
                    @if($stats['pendingConsultations'] > 0)
                        <span class="bg-indigo-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $stats['pendingConsultations'] }} Baru</span>
                    @endif
                </a>

                <a href="#promos" class="flex items-center justify-between px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl group transition-all font-medium text-sm">
                    <div class="flex items-center gap-3">
                        <i data-lucide="tag" class="w-5 h-5"></i>
                        <span>Kupon &amp; Kuota Promo</span>
                    </div>
                    <span class="bg-slate-800 text-indigo-300 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $stats['activePromos'] }} Aktif</span>
                </a>

                <a href="#reviews" class="flex items-center justify-between px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl group transition-all font-medium text-sm">
                    <div class="flex items-center gap-3">
                        <i data-lucide="star" class="w-5 h-5"></i>
                        <span>Moderasi Ulasan Klien</span>
                    </div>
                    <span class="bg-amber-500/20 text-amber-300 text-[10px] font-bold px-2 py-0.5 rounded-full">★ {{ $stats['avgRating'] }}</span>
                </a>

                <div class="pt-6">
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700/60 text-center">
                        <div class="w-9 h-9 rounded-full bg-indigo-500/20 text-indigo-400 flex items-center justify-center mx-auto mb-2">
                            <i data-lucide="sparkles" class="w-5 h-5"></i>
                        </div>
                        <h4 class="text-xs font-bold text-white mb-1">Target Konversi Klien</h4>
                        <p class="text-[11px] text-slate-400 leading-tight mb-2">Respons cepat calon klien meningkatkan rasio closing layanan hingga 80%.</p>
                    </div>
                </div>
            </nav>
        </div>

        <!-- User Profile (Bottom) -->
        <div class="p-4 border-t border-slate-800">
            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-800/60 border border-slate-700/80">
                <div class="w-10 h-10 rounded-full bg-indigo-900/60 text-indigo-300 border border-indigo-500/30 flex items-center justify-center font-bold">
                    {{ strtoupper(substr($user->name ?? 'M', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate">{{ $user->name }}</p>
                    <p class="text-[11px] text-indigo-400 truncate flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
                        Marketing &amp; CR
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
                    <h2 class="text-xl font-bold text-corporate">Dashboard Marketing &amp; Client Relations</h2>
                    <span class="bg-indigo-100 text-indigo-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full">Sistem IZINYOU</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Kelola tiket konsultasi, kupon diskon &amp; kuota, serta ulasan testimoni klien.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <button onclick="document.getElementById('addPromoModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Terbitkan Kupon Baru</span>
                </button>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-8 pt-4">
            @if (session('success'))
                <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-sm font-medium shadow-sm">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
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
        <div class="flex-1 overflow-y-auto p-8 pt-4 space-y-8" id="overview">

            <!-- 1. Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Tiket Konsultasi Baru</p>
                            <h3 class="text-3xl font-extrabold text-corporate">{{ $stats['pendingConsultations'] }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="message-square-text" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-xs text-indigo-600 font-medium">Dari total {{ $stats['totalConsultations'] }} tiket masuk</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Kupon Promo Aktif</p>
                            <h3 class="text-3xl font-extrabold text-corporate">{{ $stats['activePromos'] }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="tag" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-xs text-emerald-600 font-medium">{{ $stats['totalPromosUsed'] }} transaksi terpakai</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Ulasan &amp; Rating</p>
                            <h3 class="text-3xl font-extrabold text-corporate flex items-center gap-1.5">
                                {{ $stats['avgRating'] }} <span class="text-amber-400 text-2xl">★</span>
                            </h3>
                        </div>
                        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="star" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">{{ $stats['publishedReviews'] }} dari {{ $stats['totalReviews'] }} dipublikasikan</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Status Keamanan</p>
                            <h3 class="text-xl font-extrabold text-emerald-600">RBAC Aktif</h3>
                        </div>
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="shield-check" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 font-medium">Panel Terisolasi Divisi Marketing</p>
                </div>
            </div>

            <!-- 2. Section A: Antrean Tiket Konsultasi Gratis & Live Chat -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm" id="konsultasi">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="messages-square" class="w-6 h-6 text-indigo-600"></i>
                            <h3 class="text-lg font-bold text-corporate">Antrean Tiket Konsultasi Gratis &amp; Live Chat</h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Respons pertanyaan calon klien mitra dan dampingi proses konsultasi awal.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('marketing.dashboard') }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ empty($statusFilter) || $statusFilter === 'all' ? 'bg-corporate text-white' : 'bg-slate-100 text-slate-600' }}">Semua</a>
                        <a href="{{ route('marketing.dashboard', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ $statusFilter === 'pending' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600' }}">Menunggu Respons</a>
                        <a href="{{ route('marketing.dashboard', ['status' => 'responded']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ $statusFilter === 'responded' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600' }}">Selesai Dijawab</a>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse ($consultations as $item)
                        <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/60 space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/60 pb-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-corporate text-sm">{{ $item->subject }}</h4>
                                        @if($item->status === 'pending')
                                            <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Butuh Respons</span>
                                        @else
                                            <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Telah Dijawab</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        Dari: <strong>{{ $item->client_name }}</strong> &bull; Perusahaan: <strong>{{ $item->company_name ?? '-' }}</strong> &bull; {{ date('d M Y, H:i', strtotime($item->created_at)) }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-2">
                                    @if($item->phone)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->phone) }}?text=Halo%20{{ urlencode($item->client_name) }},%20kami%20dari%20Tim%20Marketing%20IzinYou%20(CV%20Prasta%20Solusi%20Indonesia)%20menghubungi%20Anda%20terkait%20konsultasi%20{{ urlencode($item->subject) }}." target="_blank" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl flex items-center gap-1.5 transition-all shadow-sm">
                                            <i data-lucide="phone-forwarded" class="w-3.5 h-3.5"></i>
                                            Live Chat WA
                                        </a>
                                    @endif

                                    <button onclick="openReplyModal({{ json_encode($item) }})" class="px-3 py-1.5 bg-corporate hover:bg-slate-800 text-white text-xs font-bold rounded-xl flex items-center gap-1.5 transition-all">
                                        <i data-lucide="reply" class="w-3.5 h-3.5 text-indigo-400"></i>
                                        Jawab Tiket
                                    </button>
                                </div>
                            </div>

                            <div class="text-xs text-slate-700 leading-relaxed bg-white p-3.5 rounded-xl border border-slate-100">
                                <span class="font-bold text-slate-400 uppercase text-[10px] block mb-1">Pertanyaan Klien:</span>
                                {{ $item->message }}
                            </div>

                            @if($item->response)
                                <div class="bg-indigo-50/80 p-3.5 rounded-xl border border-indigo-100 text-xs text-indigo-950">
                                    <div class="flex items-center gap-1.5 font-bold text-indigo-800 mb-1">
                                        <i data-lucide="check-check" class="w-4 h-4 text-indigo-600"></i>
                                        <span>Tanggapan Tim Marketing IzinYou:</span>
                                    </div>
                                    <p class="leading-relaxed">{{ $item->response }}</p>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-12 text-slate-400 text-sm">
                            <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                            <p>Tidak ada tiket konsultasi pada antrean ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 3. Section B: Manajemen Kupon / Promo Diskon & Batas Kuota -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm" id="promos">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="tags" class="w-6 h-6 text-indigo-600"></i>
                            <h3 class="text-lg font-bold text-corporate">Manajemen Kode Kupon &amp; Batas Kuota Transaksi</h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Kendalikan promo harga, potongan persentase/nominal, dan limit transaksi klien.</p>
                    </div>

                    <button onclick="document.getElementById('addPromoModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-sm">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kupon Promo
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($promos as $promo)
                        <div class="p-5 rounded-2xl border {{ $promo->is_active ? 'border-indigo-100 bg-white shadow-sm' : 'border-slate-200 bg-slate-50 opacity-70' }} flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="font-mono font-extrabold text-base px-3 py-1 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        {{ $promo->code }}
                                    </span>
                                    @if($promo->is_active)
                                        <span class="text-[10px] font-bold bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-full">Aktif</span>
                                    @else
                                        <span class="text-[10px] font-bold bg-slate-200 text-slate-600 px-2.5 py-0.5 rounded-full">Non-Aktif</span>
                                    @endif
                                </div>

                                <div class="space-y-1 mb-4">
                                    <p class="text-xs text-slate-500">Nilai Diskon:</p>
                                    <h4 class="text-xl font-extrabold text-corporate">
                                        @if($promo->discount_amount > 0)
                                            Rp {{ number_format($promo->discount_amount, 0, ',', '.') }}
                                        @elseif($promo->discount_percentage > 0)
                                            {{ $promo->discount_percentage }}%
                                        @else
                                            Diskon Khusus
                                        @endif
                                    </h4>
                                </div>

                                <!-- Kuota Transaksi Progress -->
                                <div class="mb-4">
                                    <div class="flex justify-between text-xs text-slate-500 mb-1">
                                        <span>Kuota Terpakai:</span>
                                        <strong class="text-corporate">{{ $promo->used_count }} / {{ $promo->quota }} Kuota</strong>
                                    </div>
                                    @php
                                        $quotaPercent = $promo->quota > 0 ? min(100, round(($promo->used_count / $promo->quota) * 100)) : 0;
                                    @endphp
                                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full {{ $quotaPercent >= 90 ? 'bg-rose-500' : 'bg-indigo-600' }}" style="width: {{ $quotaPercent }}%"></div>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-1">Kedaluwarsa: {{ $promo->expired_date ? date('d M Y', strtotime($promo->expired_date)) : 'Tidak ada batas' }}</p>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                <form action="{{ route('marketing.promos.update', $promo->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="quota" value="{{ $promo->quota }}">
                                    <input type="hidden" name="is_active" value="{{ $promo->is_active ? 0 : 1 }}">
                                    <button type="submit" class="text-xs font-semibold {{ $promo->is_active ? 'text-amber-600 hover:text-amber-700' : 'text-emerald-600 hover:text-emerald-700' }}">
                                        {{ $promo->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>

                                <form action="{{ route('marketing.promos.destroy', $promo->id) }}" method="POST" onsubmit="return confirm('Hapus kupon {{ $promo->code }}?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-500 hover:text-rose-700">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-8 text-slate-400 text-sm">
                            Belum ada kode kupon yang dibuat.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 4. Section C: Moderasi & Publikasi Ulasan/Rating Bintang -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm" id="reviews">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="star-half" class="w-6 h-6 text-amber-500"></i>
                            <h3 class="text-lg font-bold text-corporate">Moderasi &amp; Publikasi Ulasan Mitra Klien</h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Pilih dan kurasi testimoni rating bintang untuk ditampilkan di Landing Page publik.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-4 px-6">Klien Mitra</th>
                                <th class="py-4 px-6">Rating Bintang</th>
                                <th class="py-4 px-6">Komentar Testimoni</th>
                                <th class="py-4 px-6">Status Publikasi</th>
                                <th class="py-4 px-6 text-right">Aksi Moderasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse ($reviews as $rev)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-4 px-6">
                                        <p class="font-bold text-corporate text-xs">{{ $rev->client_name }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $rev->company_name ?? '-' }}</p>
                                        <p class="text-[10px] text-slate-400 font-mono">{{ $rev->order_number ?? '' }}</p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center text-amber-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i data-lucide="star" class="w-4 h-4 {{ $i <= $rev->rating ? 'fill-current' : 'text-slate-200' }}"></i>
                                            @endfor
                                            <span class="text-xs font-bold text-slate-600 ml-1.5">{{ $rev->rating }}.0</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-xs text-slate-700 max-w-xs leading-relaxed">
                                        "{{ $rev->comment }}"
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($rev->status === 'published')
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                                <i data-lucide="eye" class="w-3 h-3"></i> Tampil di Beranda
                                            </span>
                                            @if($rev->is_featured)
                                                <span class="ml-1 text-[10px] font-bold bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full">Unggulan</span>
                                            @endif
                                        @elseif($rev->status === 'rejected')
                                            <span class="text-[11px] font-bold text-rose-700 bg-rose-50 px-2.5 py-0.5 rounded-full border border-rose-200">Ditolak</span>
                                        @else
                                            <span class="text-[11px] font-bold text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200">Menunggu Review</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('marketing.reviews.moderate', $rev->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="{{ $rev->status === 'published' ? 'pending' : 'published' }}">
                                                <input type="hidden" name="is_featured" value="{{ $rev->is_featured ? 0 : 1 }}">
                                                <button type="submit" class="px-2.5 py-1 text-xs font-bold rounded-lg {{ $rev->status === 'published' ? 'bg-slate-100 text-slate-600 hover:bg-slate-200' : 'bg-emerald-600 text-white hover:bg-emerald-500' }}">
                                                    {{ $rev->status === 'published' ? 'Tarik Publikasi' : 'Setujui & Publikasikan' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-slate-400 text-sm">
                                        Belum ada data ulasan dari klien.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <!-- Modal Jawab Tiket Konsultasi -->
    <div id="replyModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                        <i data-lucide="reply" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-corporate text-base" id="modalSubject">Tanggapan Konsultasi</h3>
                        <p class="text-xs text-slate-400" id="modalClientInfo">Klien Mitra IzinYou</p>
                    </div>
                </div>
                <button onclick="document.getElementById('replyModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="replyForm" method="POST" class="space-y-4">
                @csrf
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/60 text-xs text-slate-600" id="modalMessage">
                    <!-- Pertanyaan Klien -->
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggapan Resmi Marketing / Konsultan Teknis <span class="text-rose-500">*</span></label>
                    <textarea name="response" rows="5" required placeholder="Tuliskan jawaban yang jelas mengenai regulasi, estimasi biaya, atau jadwal tatap muka..." class="w-full text-xs p-3.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-indigo-500"></textarea>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('replyModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-500">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md transition-all">
                        Kirim Tanggapan ke Klien
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Kupon Promo -->
    <div id="addPromoModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                        <i data-lucide="tag" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-corporate text-base">Terbitkan Kupon Promo</h3>
                        <p class="text-xs text-slate-400">Tentukan batas kuota dan diskon harga</p>
                    </div>
                </div>
                <button onclick="document.getElementById('addPromoModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('marketing.promos.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kode Promo <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" required placeholder="Contoh: PRASMAS25" class="w-full uppercase font-mono text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Diskon</label>
                        <select name="discount_type" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-indigo-500">
                            <option value="nominal">Nominal (Rp)</option>
                            <option value="percentage">Persentase (%)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nilai Diskon <span class="text-rose-500">*</span></label>
                        <input type="number" name="discount_value" required min="1" placeholder="500000 / 10" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Batas Kuota Transaksi <span class="text-rose-500">*</span></label>
                        <input type="number" name="quota" required min="1" value="50" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Kedaluwarsa</label>
                        <input type="date" name="expired_date" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('addPromoModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-500">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md transition-all">
                        Terbitkan Kupon
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function openReplyModal(consultation) {
            document.getElementById('modalSubject').innerText = consultation.subject;
            document.getElementById('modalClientInfo').innerText = consultation.client_name + ' (' + (consultation.company_name || 'Mitra') + ')';
            document.getElementById('modalMessage').innerText = '"' + consultation.message + '"';
            document.getElementById('replyForm').action = "/marketing/consultation/" + consultation.id + "/reply";
            document.getElementById('replyModal').classList.remove('hidden');
        }
    </script>
</body>
</html>
