<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Pendapatan &amp; Arus Kas - Finance &amp; Administration</title>
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
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-amber-600 selection:text-white flex h-screen overflow-hidden">

    <!-- Dark Sidebar -->
    @include('finance.partials.sidebar')

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/60">
        
        <!-- Header -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0 relative z-10">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-corporate">Laporan Rekapitulasi Pendapatan &amp; Arus Kas</h2>
                    <span class="bg-emerald-100 text-emerald-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full">Cash Flow &amp; Revenue</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Analisis perputaran kas masuk riil, pendapatan bersih layanan perizinan, dan kepatuhan finansial perusahaan.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('finance.reports.export.xlsx', request()->query()) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all" title="Unduh Rekapitulasi Kas format Excel (.xlsx)">
                    <i data-lucide="sheet" class="w-4 h-4"></i>
                    <span>Ekspor Excel (.xlsx)</span>
                </a>
                <a href="{{ route('finance.reports.export.pdf', request()->query()) }}" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all" title="Unduh Rekapitulasi Kas format PDF (.pdf)">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span>Ekspor PDF (.pdf)</span>
                </a>
                <a href="{{ route('finance.reports.export', request()->query()) }}" class="px-3.5 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all" title="Unduh Format CSV">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>CSV</span>
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        @include('finance.partials.alerts')

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8 pt-4 space-y-8">

            <!-- 1. Filter Periode Laporan Keuangan -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                <form action="{{ route('finance.reports') }}" method="GET" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    
                    <div class="flex items-center gap-2 overflow-x-auto pb-2 lg:pb-0">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap">Periode:</span>
                        <a href="{{ route('finance.reports', ['period' => 'this_month']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ ($period === 'this_month' || !$period) ? 'bg-corporate text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Bulan Ini
                        </a>
                        <a href="{{ route('finance.reports', ['period' => 'last_month']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $period === 'last_month' ? 'bg-corporate text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Bulan Lalu
                        </a>
                        <a href="{{ route('finance.reports', ['period' => 'this_quarter']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $period === 'this_quarter' ? 'bg-corporate text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Kuartal Ini
                        </a>
                        <a href="{{ route('finance.reports', ['period' => 'this_year']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $period === 'this_year' ? 'bg-corporate text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Tahun Ini
                        </a>
                        <a href="{{ route('finance.reports', ['period' => 'all_time']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $period === 'all_time' ? 'bg-corporate text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Semua Waktu
                        </a>
                    </div>

                    <!-- Kustom Tanggal -->
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="period" value="custom">
                        <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="text-xs px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white">
                        <span class="text-xs text-slate-400">s/d</span>
                        <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="text-xs px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white">
                        <button type="submit" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold rounded-lg transition-all flex items-center gap-1 shadow-xs">
                            <i data-lucide="filter" class="w-3.5 h-3.5"></i> Filter
                        </button>
                    </div>

                </form>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Rentang Data Ditampilkan: <strong class="text-corporate">{{ $periodLabel }}</strong></span>
                    <span>Total Transaksi Periode Ini: <strong class="text-corporate">{{ $orders->count() }} Pesanan</strong></span>
                </div>
            </div>

            <!-- 2. Ringkasan Eksekutif Finansial (Executive Cash Flow & Revenue Metrics) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                
                <!-- Metric 1: Kas Masuk Riil (Actual Cash Inflow) -->
                <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-2xl p-5 text-white shadow-md relative overflow-hidden xl:col-span-2">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-100">Arus Kas Masuk Riil (Inflow)</p>
                            <h3 class="text-2xl font-black text-white mt-1">Rp {{ number_format($cashInflow, 0, ',', '.') }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-white/20 text-white rounded-xl flex items-center justify-center font-bold">
                            <i data-lucide="arrow-down-left" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs text-emerald-100 pt-2 border-t border-white/20">
                        <span>Dari {{ $payments->count() }} transaksi lunas</span>
                        <span class="font-bold">Kas di Bank (Settled)</span>
                    </div>
                </div>

                <!-- Metric 2: Pendapatan Bersih (Net Billed Revenue) -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm relative overflow-hidden group">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Pendapatan Bersih</p>
                    <h3 class="text-xl font-extrabold text-corporate">Rp {{ number_format($netRevenue, 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Setelah potongan promo</p>
                </div>

                <!-- Metric 3: Pendapatan Kotor (Gross Billed) -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm relative overflow-hidden group">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Pendapatan Kotor</p>
                    <h3 class="text-xl font-extrabold text-slate-700">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Total tagihan awal</p>
                </div>

                <!-- Metric 4: Piutang Berjalan (Receivables) -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm relative overflow-hidden group">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-blue-600 mb-1">Piutang Berjalan</p>
                    <h3 class="text-xl font-extrabold text-blue-600">Rp {{ number_format($receivables, 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-blue-700 mt-1">Menunggu pelunasan</p>
                </div>

                <!-- Metric 5: Tingkat Realisasi Kas (Collection Rate) -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm relative overflow-hidden group">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-amber-600 mb-1">Realisasi Kas Masuk</p>
                    <h3 class="text-2xl font-black text-amber-600">{{ $collectionRate }}%</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Kas / Total Tagihan</p>
                </div>

            </div>

            <!-- 3. Rincian Arus Kas: Buku Kas Harian & Komposisi Metode Pembayaran -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Kolom Kiri: Rekapitulasi Arus Kas Harian (Daily Cash Ledger) -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm lg:col-span-2">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div class="flex items-center gap-2">
                            <i data-lucide="book-open" class="w-5 h-5 text-emerald-600"></i>
                            <h3 class="font-bold text-corporate text-base">Buku Rekapitulasi Arus Kas Harian (Inflow Ledger)</h3>
                        </div>
                        <span class="text-xs text-slate-400">Mutasi kas riil terverifikasi</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/70 border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-3 px-4">Tanggal Masuk</th>
                                    <th class="py-3 px-4 text-center">Mutasi</th>
                                    <th class="py-3 px-4 text-right">Kas Masuk Riil</th>
                                    <th class="py-3 px-4 text-right">Saldo Kas Terakumulasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @forelse($dailyLedger as $row)
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="py-3 px-4 font-mono font-bold text-corporate">
                                            {{ date('d M Y', strtotime($row->tx_date)) }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-bold rounded-full text-[10px]">
                                                {{ $row->tx_count }} Transaksi
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-right font-bold text-emerald-600">
                                            +Rp {{ number_format((float) $row->daily_inflow, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-right font-mono font-extrabold text-corporate">
                                            Rp {{ number_format((float) $row->running_balance, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-slate-400">
                                            Belum ada arus kas masuk yang terverifikasi pada periode terpilih.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($dailyLedger->isNotEmpty())
                                <tfoot>
                                    <tr class="bg-slate-50/80 font-bold border-t border-slate-200 text-xs">
                                        <td class="py-3 px-4 text-corporate" colspan="2">TOTAL ARUS KAS MASUK PERIODE:</td>
                                        <td class="py-3 px-4 text-right text-emerald-700 font-extrabold">
                                            Rp {{ number_format($cashInflow, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-right font-extrabold text-corporate">
                                            Rp {{ number_format($cashInflow, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Kolom Kanan: Distribusi Kanal Perbankan & Kategori Layanan -->
                <div class="space-y-6">
                    
                    <!-- Kanal Rekening Bank Penerima Kas -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                            <div class="flex items-center gap-2">
                                <i data-lucide="building-2" class="w-4 h-4 text-amber-600"></i>
                                <h4 class="font-bold text-corporate text-sm">Kanal Bank Penerima Kas</h4>
                            </div>
                            <span class="text-[11px] text-slate-400">Settled Inflows</span>
                        </div>

                        <div class="space-y-3">
                            @forelse($channelBreakdown as $ch)
                                @php
                                    $pct = $cashInflow > 0 ? round(($ch->total_amount / $cashInflow) * 100, 1) : 0;
                                @endphp
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-xs font-bold text-corporate">{{ $ch->payment_method ?? 'Transfer Bank' }}</span>
                                        <span class="text-xs font-extrabold text-emerald-700">Rp {{ number_format((float) $ch->total_amount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-amber-600 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px] text-slate-400 mt-1">
                                        <span>{{ $ch->count }} transaksi</span>
                                        <span class="font-bold text-slate-600">{{ $pct }}%</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 py-4 text-center">Belum ada mutasi bank pada periode ini.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Distribusi Kategori Layanan -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                            <div class="flex items-center gap-2">
                                <i data-lucide="pie-chart" class="w-4 h-4 text-sky-600"></i>
                                <h4 class="font-bold text-corporate text-sm">Kontribusi Kategori Layanan</h4>
                            </div>
                            <span class="text-[11px] text-slate-400">Revenue Stream</span>
                        </div>

                        <div class="space-y-3">
                            @forelse($serviceBreakdown as $sb)
                                @php
                                    $spct = $netRevenue > 0 ? round(($sb->total_revenue / $netRevenue) * 100, 1) : 0;
                                @endphp
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-xs font-bold text-corporate">{{ $sb->category_name }}</span>
                                        <span class="text-xs font-extrabold text-corporate">Rp {{ number_format((float) $sb->total_revenue, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-sky-600 h-1.5 rounded-full" style="width: {{ $spct }}%"></div>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px] text-slate-400 mt-1">
                                        <span>{{ $sb->order_count }} pesanan</span>
                                        <span class="font-bold text-slate-600">{{ $spct }}%</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 py-4 text-center">Belum ada pesanan pada periode ini.</p>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>

            <!-- 4. Rincian Seluruh Transaksi & Tagihan pada Periode Terpilih -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <div class="flex items-center gap-2">
                        <i data-lucide="receipt" class="w-5 h-5 text-amber-600"></i>
                        <h3 class="font-bold text-corporate text-base">Rincian Transaksi Transaksional (Periode: {{ $periodLabel }})</h3>
                    </div>
                    <span class="text-xs text-slate-500 font-medium">{{ $orders->count() }} Data Transaksi</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-3 px-4">No. Order &amp; Invoice</th>
                                <th class="py-3 px-4">Perusahaan Klien</th>
                                <th class="py-3 px-4">Layanan</th>
                                <th class="py-3 px-4 text-right">Tagihan Bersih</th>
                                <th class="py-3 px-4">Status Pembayaran</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse($orders as $ord)
                                @php
                                    $meta = $ord->paymentStatusMeta();
                                @endphp
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3 px-4">
                                        <p class="font-mono font-bold text-corporate">{{ $ord->order_number }}</p>
                                        <p class="text-[11px] text-slate-400 font-mono">{{ $ord->invoice ? $ord->invoice->invoice_number : 'Inv Pending' }}</p>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="font-bold text-corporate">{{ $ord->user->company_name ?: $ord->user->name }}</p>
                                        <p class="text-[11px] text-slate-400">{{ $ord->user->name }}</p>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="font-medium text-slate-700">{{ $ord->service->name ?? 'Layanan' }}</p>
                                        @if($ord->promo_code)
                                            <span class="text-[10px] text-indigo-600 font-semibold">Promo: {{ $ord->promo_code }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right font-extrabold text-corporate">
                                        Rp {{ number_format($ord->total_cost, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($meta['code'] === 'paid')
                                            <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full text-[10px] font-bold">
                                                <i data-lucide="check-circle" class="w-3 h-3"></i> Lunas
                                            </span>
                                        @elseif($meta['code'] === 'waiting_verification')
                                            <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-full text-[10px] font-bold">
                                                <i data-lucide="clock" class="w-3 h-3"></i> Menunggu Verifikasi
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded-full text-[10px] font-bold">
                                                <i data-lucide="alert-circle" class="w-3 h-3"></i> Belum Bayar
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('finance.invoice.generate', $ord->id) }}" target="_blank" class="px-2 py-1 bg-corporate hover:bg-slate-800 text-white rounded-md text-[11px] font-bold inline-flex items-center gap-1">
                                            <i data-lucide="printer" class="w-3 h-3 text-gold"></i> Invoice
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400">
                                        Tidak ada transaksi pesanan tercatat pada rentang periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>

