<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kupon &amp; Kuota Promo - Marketing IzinYou</title>
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

    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/60">

        <!-- Header -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0 relative z-10">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-corporate">Kupon &amp; Kuota Promo</h2>
                    <span class="bg-indigo-100 text-indigo-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full">Sistem IZINYOU</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Kelola penerbitan kupon diskon, batas kuota penggunaan, masa berlaku, dan status aktif voucher.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('marketing.dashboard') }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Ringkasan</span>
                </a>
                <button type="button" onclick="document.getElementById('addPromoModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Terbitkan Kupon Baru</span>
                </button>
            </div>
        </header>

        @include('marketing.partials.alerts')

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8 pt-4 space-y-6" id="promos">

            <!-- Ringkasan Status Kupon -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
                    <p class="text-[11px] font-bold text-slate-400 uppercase">Total Kupon</p>
                    <p class="text-2xl font-extrabold text-corporate mt-1">{{ $stats['totalPromos'] }}</p>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-emerald-200/80 shadow-sm">
                    <p class="text-[11px] font-bold text-emerald-600 uppercase">Aktif</p>
                    <p class="text-2xl font-extrabold text-emerald-700 mt-1">{{ $stats['activePromos'] }}</p>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-amber-200/80 shadow-sm">
                    <p class="text-[11px] font-bold text-amber-600 uppercase">Kedaluwarsa</p>
                    <p class="text-2xl font-extrabold text-amber-700 mt-1">{{ $stats['expiredPromos'] }}</p>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-rose-200/80 shadow-sm">
                    <p class="text-[11px] font-bold text-rose-600 uppercase">Kuota Habis</p>
                    <p class="text-2xl font-extrabold text-rose-700 mt-1">{{ $stats['exhaustedPromos'] }}</p>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
                    <p class="text-[11px] font-bold text-slate-500 uppercase">Non-Aktif</p>
                    <p class="text-2xl font-extrabold text-slate-700 mt-1">{{ $stats['inactivePromos'] }}</p>
                </div>
            </div>

            <!-- Promo Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse ($promos as $promo)
                    @php
                        $isExpired = $promo->isExpired();
                        $isExhausted = $promo->quota > 0 && $promo->used_count >= $promo->quota;
                        $usagePct = $promo->quota > 0 ? min(100, round(($promo->used_count / $promo->quota) * 100)) : 0;
                        $isPercentage = $promo->discount_percentage !== null && (float) $promo->discount_percentage > 0;
                        $discountValue = $isPercentage ? (float) $promo->discount_percentage : (float) $promo->discount_amount;
                        $isFilteredPromo = ($historyFilters['promo_code'] ?? '') === strtoupper((string) $promo->code);
                    @endphp
                    <div class="bg-white rounded-3xl p-6 border shadow-sm flex flex-col justify-between transition-all {{ $isFilteredPromo ? 'border-indigo-500 ring-2 ring-indigo-100' : 'border-slate-200/80' }}">
                        <div>
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <div>
                                    <span class="inline-block font-mono text-sm font-extrabold tracking-wide px-3 py-1 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-200/80">
                                        {{ $promo->code }}
                                    </span>
                                    <h3 class="text-sm font-bold text-corporate mt-2">{{ $promo->title ?: 'Voucher Promo '.$promo->code }}</h3>
                                </div>

                                @if($isExpired)
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 shrink-0">Kedaluwarsa</span>
                                @elseif(!$promo->is_active)
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-200 text-slate-700 shrink-0">Non-Aktif</span>
                                @elseif($isExhausted)
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 shrink-0">Kuota Habis</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 shrink-0">Aktif</span>
                                @endif
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2 text-xs mb-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500">Nilai Potongan</span>
                                    <strong class="text-corporate text-sm">
                                        @if($isPercentage)
                                            Diskon {{ rtrim(rtrim(number_format($discountValue, 2, ',', '.'), '0'), ',') }}%
                                        @else
                                            Rp {{ number_format($discountValue, 0, ',', '.') }}
                                        @endif
                                    </strong>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500">Min. Transaksi</span>
                                    <span class="font-semibold text-slate-700">Rp {{ number_format((float) $promo->min_transaction, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500">Masa Berlaku</span>
                                    <span class="font-semibold text-slate-700">
                                        {{ $promo->expired_date ? $promo->expired_date->format('d M Y') : 'Tanpa Batas Waktu' }}
                                    </span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="flex items-center justify-between text-xs mb-1.5">
                                    <span class="text-slate-500 font-medium">Digunakan: <strong class="text-corporate">{{ $promo->used_count }} / {{ $promo->quota }}</strong></span>
                                    <span class="font-bold text-indigo-700">{{ $usagePct }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $isExhausted ? 'bg-rose-500' : 'bg-indigo-600' }}" style="width: {{ $usagePct }}%;"></div>
                                </div>

                                <a href="{{ route('marketing.promos.index', ['promo_code' => $promo->code]) }}#riwayat-penggunaan"
                                   class="mt-3 w-full px-3 py-2 rounded-xl text-xs font-bold flex items-center justify-between transition-all {{ $isFilteredPromo ? 'bg-indigo-600 text-white shadow-sm' : 'bg-indigo-50 hover:bg-indigo-100 text-indigo-700' }}">
                                    <span>Lihat Riwayat Penggunaan</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2">
                            <button type="button"
                                    data-id="{{ $promo->id }}"
                                    data-code="{{ $promo->code }}"
                                    data-title="{{ $promo->title }}"
                                    data-discount-type="{{ $isPercentage ? 'percentage' : 'nominal' }}"
                                    data-discount-value="{{ $discountValue }}"
                                    data-min-transaction="{{ (float) $promo->min_transaction }}"
                                    data-quota="{{ $promo->quota }}"
                                    data-expired-date="{{ $promo->expired_date ? $promo->expired_date->format('Y-m-d') : '' }}"
                                    data-is-active="{{ $promo->is_active ? '1' : '0' }}"
                                    onclick="openEditPromoModal(this)"
                                    class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold inline-flex items-center gap-1 transition-all">
                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                <span>Edit</span>
                            </button>

                            <form action="{{ route('marketing.promos.update', $promo->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="quota" value="{{ $promo->quota }}">
                                <input type="hidden" name="is_active" value="{{ $promo->is_active ? 0 : 1 }}">
                                <button type="submit"
                                        class="px-3 py-1.5 rounded-xl text-xs font-bold inline-flex items-center gap-1 transition-all {{ $promo->is_active ? 'bg-amber-50 hover:bg-amber-100 text-amber-800' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-800' }}">
                                    <i data-lucide="{{ $promo->is_active ? 'pause-circle' : 'play-circle' }}" class="w-3.5 h-3.5"></i>
                                    <span>{{ $promo->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</span>
                                </button>
                            </form>

                            <form action="{{ route('marketing.promos.destroy', $promo->id) }}" method="POST" class="inline ml-auto"
                                  onsubmit="return confirm('Yakin ingin menghapus kupon promo {{ $promo->code }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold inline-flex items-center gap-1 transition-all">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-3xl p-12 border border-slate-200/80 shadow-sm text-center">
                        <div class="w-14 h-14 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="ticket" class="w-7 h-7"></i>
                        </div>
                        <h3 class="text-sm font-bold text-corporate mb-1">Belum Ada Kupon Promo</h3>
                        <p class="text-xs text-slate-500 mb-4">Terbitkan kode voucher promosi pertama untuk mendukung kampanye akuisisi klien.</p>
                        <button type="button" onclick="document.getElementById('addPromoModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold inline-flex items-center gap-1.5">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            <span>Terbitkan Kupon Baru</span>
                        </button>
                    </div>
                @endforelse
            </div>

            <!-- Riwayat Penggunaan Kupon (Sumber Data Asli: Order, OrderItem, Service, User, Invoice, Payment) -->
            @php
                $hasActiveHistoryFilter = ($historyFilters['promo_code'] ?? '') !== ''
                    || ($historyFilters['client'] ?? '') !== ''
                    || ($historyFilters['company'] ?? '') !== ''
                    || ($historyFilters['service_id'] ?? null) !== null
                    || ($historyFilters['date_from'] ?? '') !== ''
                    || ($historyFilters['date_to'] ?? '') !== ''
                    || ($historyFilters['usage_status'] ?? '') !== '';
            @endphp
            <section id="riwayat-penggunaan" class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <i data-lucide="history" class="w-5 h-5 text-indigo-600"></i>
                            <h3 class="text-base font-bold text-corporate">Riwayat Penggunaan Kupon</h3>
                            @if(($historyFilters['promo_code'] ?? '') !== '')
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-800">
                                    Kode: {{ $historyFilters['promo_code'] }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Rekam penggunaan voucher berdasarkan pesanan layanan nyata, klien, perusahaan/PT, invoice, dan nominal diskon.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <div class="px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200/80">
                            <span class="text-slate-500">Total Transaksi:</span>
                            <strong class="text-corporate ml-1">{{ $historySummary['totalCount'] }}</strong>
                        </div>
                        <div class="px-3.5 py-2 rounded-xl bg-indigo-50/70 border border-indigo-100">
                            <span class="text-indigo-700">Total Diskon:</span>
                            <strong class="text-indigo-900 ml-1">Rp {{ number_format((float) $historySummary['totalDiscount'], 0, ',', '.') }}</strong>
                        </div>
                        <div class="px-3.5 py-2 rounded-xl bg-emerald-50/70 border border-emerald-100">
                            <span class="text-emerald-700">Total Bayar (Aktif):</span>
                            <strong class="text-emerald-900 ml-1">Rp {{ number_format((float) $historySummary['totalNetAmount'], 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Filter Riwayat Penggunaan Kupon -->
                <form action="{{ route('marketing.promos.index') }}#riwayat-penggunaan" method="GET" class="p-6 bg-slate-50/70 border-b border-slate-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="filterPromoCode" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Kode Voucher</label>
                            <select id="filterPromoCode" name="promo_code"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                                <option value="">Semua Kode Kupon</option>
                                @foreach($availablePromoCodes as $codeOption)
                                    <option value="{{ $codeOption }}" @selected(($historyFilters['promo_code'] ?? '') === $codeOption)>{{ $codeOption }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="filterClient" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Nama / Email Klien</label>
                            <input id="filterClient" type="text" name="client" value="{{ $historyFilters['client'] ?? '' }}"
                                   placeholder="Contoh: Eka Wahyuning"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                        </div>

                        <div>
                            <label for="filterCompany" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">PT / Perusahaan</label>
                            <input id="filterCompany" type="text" name="company" value="{{ $historyFilters['company'] ?? '' }}"
                                   placeholder="Contoh: PT Cinta Sejati"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                        </div>

                        <div>
                            <label for="filterService" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Layanan</label>
                            <select id="filterService" name="service_id"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                                <option value="">Semua Layanan</option>
                                @foreach($services as $serviceOption)
                                    <option value="{{ $serviceOption->id }}" @selected(($historyFilters['service_id'] ?? null) === $serviceOption->id)>{{ $serviceOption->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="filterDateFrom" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Dari Tanggal</label>
                            <input id="filterDateFrom" type="date" name="date_from" value="{{ $historyFilters['date_from'] ?? '' }}"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                        </div>

                        <div>
                            <label for="filterDateTo" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Sampai Tanggal</label>
                            <input id="filterDateTo" type="date" name="date_to" value="{{ $historyFilters['date_to'] ?? '' }}"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                        </div>

                        <div>
                            <label for="filterUsageStatus" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Status Penggunaan</label>
                            <select id="filterUsageStatus" name="usage_status"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                                <option value="">Semua Status</option>
                                <option value="berhasil" @selected(($historyFilters['usage_status'] ?? '') === 'berhasil')>Berhasil ({{ $historySummary['successCount'] }})</option>
                                <option value="batal" @selected(($historyFilters['usage_status'] ?? '') === 'batal')>Batal ({{ $historySummary['cancelledCount'] }})</option>
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <button type="submit" class="px-4 py-2 bg-corporate hover:bg-slate-800 text-white rounded-xl text-xs font-bold inline-flex items-center gap-1.5 transition-all">
                                <i data-lucide="search" class="w-3.5 h-3.5"></i>
                                <span>Terapkan Filter</span>
                            </button>

                            @if($hasActiveHistoryFilter)
                                <a href="{{ route('marketing.promos.index') }}#riwayat-penggunaan" class="px-3.5 py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold transition-all">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <!-- Tabel Riwayat Penggunaan Kupon -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-3.5 px-5">Kode Kupon</th>
                                <th class="py-3.5 px-4">Klien &amp; PT / Perusahaan</th>
                                <th class="py-3.5 px-4">Layanan</th>
                                <th class="py-3.5 px-4">Order &amp; Invoice</th>
                                <th class="py-3.5 px-4 text-right">Nilai Transaksi</th>
                                <th class="py-3.5 px-4 text-right">Diskon</th>
                                <th class="py-3.5 px-4 text-right">Total Bayar</th>
                                <th class="py-3.5 px-4">Tanggal</th>
                                <th class="py-3.5 px-5">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse($promoUsages as $usage)
                                <tr class="hover:bg-slate-50/70 transition-colors align-top">
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        <span class="inline-block font-mono text-xs font-extrabold px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200/80">
                                            {{ $usage->promo_code }}
                                        </span>
                                        @if($usage->promo_title !== '')
                                            <p class="text-[11px] text-slate-400 mt-1 max-w-[160px] truncate" title="{{ $usage->promo_title }}">{{ $usage->promo_title }}</p>
                                        @endif
                                    </td>

                                    <td class="py-4 px-4 min-w-[180px]">
                                        <p class="font-bold text-corporate">{{ $usage->client_name }}</p>
                                        <p class="font-semibold text-indigo-700 mt-0.5">{{ $usage->company_name }}</p>
                                        @if($usage->client_email !== '')
                                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $usage->client_email }}</p>
                                        @endif
                                    </td>

                                    <td class="py-4 px-4 min-w-[180px] max-w-xs">
                                        <p class="font-semibold text-slate-800 leading-snug">{{ $usage->service_name }}</p>
                                    </td>

                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <p class="font-mono font-bold text-corporate">{{ $usage->order_number }}</p>
                                        <p class="font-mono text-[11px] text-slate-500 mt-0.5">
                                            {{ $usage->invoice_number !== '-' ? $usage->invoice_number : 'Belum ada invoice' }}
                                        </p>
                                    </td>

                                    <td class="py-4 px-4 text-right whitespace-nowrap text-slate-600 font-medium">
                                        Rp {{ number_format((float) $usage->transaction_amount, 0, ',', '.') }}
                                    </td>

                                    <td class="py-4 px-4 text-right whitespace-nowrap font-bold text-indigo-700">
                                        - Rp {{ number_format((float) $usage->discount_amount, 0, ',', '.') }}
                                    </td>

                                    <td class="py-4 px-4 text-right whitespace-nowrap font-extrabold text-corporate">
                                        Rp {{ number_format((float) $usage->final_amount, 0, ',', '.') }}
                                    </td>

                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <p class="font-semibold text-slate-700">{{ $usage->date_label }}</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $usage->time_label }}</p>
                                    </td>

                                    <td class="py-4 px-5 whitespace-nowrap">
                                        @if($usage->is_cancelled)
                                            <span class="inline-block px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">
                                                {{ $usage->usage_status_label }}
                                            </span>
                                        @else
                                            <span class="inline-block px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                                {{ $usage->usage_status_label }}
                                            </span>
                                        @endif
                                        <p class="text-[10px] text-slate-400 mt-1">
                                            {{ $usage->order_status_label }} &bull; {{ $usage->payment_status_label }}
                                        </p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 px-6 text-center">
                                        <p class="text-sm font-bold text-corporate mb-1">Belum Ada Riwayat Penggunaan Kupon</p>
                                        <p class="text-xs text-slate-500">
                                            Belum terdapat transaksi pesanan pelanggan yang tercatat memakai kode kupon sesuai filter yang dipilih.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($promoUsages->hasPages())
                    <div class="p-5 border-t border-slate-100 bg-slate-50/40">
                        {{ $promoUsages->links() }}
                    </div>
                @endif
            </section>

        </div>
    </main>

    <!-- Modal: Tambah Kupon Promo -->
    <div id="addPromoModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                <h3 class="text-lg font-bold text-corporate">Terbitkan Kupon Promo Baru</h3>
                <button type="button" onclick="document.getElementById('addPromoModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('marketing.promos.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kode Kupon</label>
                        <input type="text" name="code" required placeholder="Contoh: IZINHEMAT20"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm uppercase font-mono focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kuota Transaksi</label>
                        <input type="number" name="quota" required min="1" value="50"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul / Nama Promo</label>
                    <input type="text" name="title" placeholder="Contoh: Promo Pendirian PT & NIB"
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tipe Diskon</label>
                        <select name="discount_type" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                            <option value="percentage">Persentase (%)</option>
                            <option value="nominal">Nominal (Rp)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nilai Diskon</label>
                        <input type="number" name="discount_value" required min="1" step="any" placeholder="15 atau 250000"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Minimal Transaksi (Rp)</label>
                        <input type="number" name="min_transaction" min="0" value="0"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Kedaluwarsa</label>
                        <input type="date" name="expired_date"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" id="addPromoIsActive" name="is_active" value="1" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="addPromoIsActive" class="text-xs font-semibold text-slate-700">Langsung aktifkan kupon setelah diterbitkan</label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('addPromoModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-sm">
                        Terbitkan Kupon
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Kupon Promo -->
    <div id="editPromoModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                <h3 class="text-lg font-bold text-corporate">Edit Detail Kupon Promo</h3>
                <button type="button" onclick="document.getElementById('editPromoModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="editPromoForm"
                  method="POST"
                  action="#"
                  data-action-template="{{ route('marketing.promos.update', ['id' => '__ID__']) }}"
                  class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kode Kupon</label>
                        <input id="editPromoCode" type="text" name="code" required
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm uppercase font-mono focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kuota Transaksi</label>
                        <input id="editPromoQuota" type="number" name="quota" required min="1"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul / Nama Promo</label>
                    <input id="editPromoTitle" type="text" name="title"
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tipe Diskon</label>
                        <select id="editPromoDiscountType" name="discount_type"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                            <option value="percentage">Persentase (%)</option>
                            <option value="nominal">Nominal (Rp)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nilai Diskon</label>
                        <input id="editPromoDiscountValue" type="number" name="discount_value" required min="1" step="any"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Minimal Transaksi (Rp)</label>
                        <input id="editPromoMinTransaction" type="number" name="min_transaction" min="0"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Kedaluwarsa</label>
                        <input id="editPromoExpiredDate" type="date" name="expired_date"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="hidden" name="is_active" value="0">
                    <input id="editPromoIsActive" type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="editPromoIsActive" class="text-xs font-semibold text-slate-700">Kupon dalam status Aktif</label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('editPromoModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function openEditPromoModal(button) {
            const modal = document.getElementById('editPromoModal');
            const form = document.getElementById('editPromoForm');

            const id = button.dataset.id || '';
            const template = form.dataset.actionTemplate || '';
            form.action = template.replace('__ID__', id);

            document.getElementById('editPromoCode').value = button.dataset.code || '';
            document.getElementById('editPromoTitle').value = button.dataset.title || '';
            document.getElementById('editPromoDiscountType').value = button.dataset.discountType || 'percentage';
            document.getElementById('editPromoDiscountValue').value = button.dataset.discountValue || '';
            document.getElementById('editPromoMinTransaction').value = button.dataset.minTransaction || '0';
            document.getElementById('editPromoQuota').value = button.dataset.quota || '1';
            document.getElementById('editPromoExpiredDate').value = button.dataset.expiredDate || '';
            document.getElementById('editPromoIsActive').checked = button.dataset.isActive === '1';

            modal.classList.remove('hidden');
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                document.getElementById('addPromoModal').classList.add('hidden');
                document.getElementById('editPromoModal').classList.add('hidden');
            }
        });
    </script>
</body>
</html>

