<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Transaksi &amp; Status Pelanggan - Finance IzinYou</title>
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
                    <h2 class="text-xl font-bold text-corporate">Monitoring Transaksi &amp; Status Pelanggan</h2>
                    <span class="bg-amber-100 text-amber-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full">Buku Besar Transaksi</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Pantau status tagihan invoice, kepatuhan pembayaran pelanggan, dan rincian transaksi per pesanan.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('finance.invoices') }}" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span>Kelola Invoice Tagihan</span>
                </a>
                <a href="{{ route('finance.reports') }}" class="px-3.5 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all">
                    <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                    <span>Laporan Kas</span>
                </a>
                <a href="{{ route('finance.export.xlsx') }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all" title="Unduh Transaksi format Excel (.xlsx)">
                    <i data-lucide="sheet" class="w-4 h-4"></i>
                    <span>Ekspor Excel (.xlsx)</span>
                </a>
                <a href="{{ route('finance.export.pdf') }}" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all" title="Unduh Transaksi format PDF (.pdf)">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span>Ekspor PDF (.pdf)</span>
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        @include('finance.partials.alerts')

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8 pt-4 space-y-6">

            <!-- 1. Statistik Ringkasan Transaksi Keuangan -->
            @if(isset($txSummary))
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Transaksi</p>
                    <h3 class="text-2xl font-black text-corporate mt-1">Rp {{ number_format($txSummary['totalVolume'], 0, ',', '.') }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $txSummary['totalCount'] }} total transaksi</p>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Lunas (Paid)</p>
                    <h3 class="text-2xl font-black text-emerald-600 mt-1">Rp {{ number_format($txSummary['paidVolume'], 0, ',', '.') }}</h3>
                    <p class="text-xs text-emerald-700 font-medium">{{ $txSummary['paidCount'] }} selesai bayar</p>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-blue-600">Belum Bayar (Unpaid)</p>
                    <h3 class="text-2xl font-black text-blue-600 mt-1">Rp {{ number_format($txSummary['unpaidVolume'], 0, ',', '.') }}</h3>
                    <p class="text-xs text-blue-700 font-medium">{{ $txSummary['unpaidCount'] }} menunggu bayar</p>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Verifikasi</p>
                    <h3 class="text-2xl font-black text-amber-600 mt-1">{{ $txSummary['pendingVerificationCount'] }}</h3>
                    <p class="text-xs text-amber-700 font-medium">Bukti transfer audit</p>
                </div>
                <div class="bg-white rounded-2xl p-5 border {{ ($txSummary['cancelledCount'] ?? 0) > 0 ? 'border-rose-200/90 bg-rose-50/20' : 'border-slate-200/80' }} shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-rose-600">Pesanan Dibatalkan</p>
                    <h3 class="text-2xl font-black text-rose-600 mt-1">{{ $txSummary['cancelledCount'] ?? 0 }}</h3>
                    <p class="text-xs text-rose-700 font-medium">Rp {{ number_format($txSummary['cancelledVolume'] ?? 0, 0, ',', '.') }} batal</p>
                </div>
            </div>
            @endif

            <!-- 2. Filter Tabs, Date Range & Search Bar -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                
                <form action="{{ route('finance.transactions') }}" method="GET" class="space-y-4">
                    
                    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
                        
                        <!-- Status Tabs -->
                        <div class="flex items-center gap-2 overflow-x-auto pb-2 lg:pb-0">
                            <a href="{{ route('finance.transactions', array_merge(request()->query(), ['payment_status' => 'all'])) }}" 
                               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ empty($filterPaymentStatus) || $filterPaymentStatus === 'all' ? 'bg-corporate text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                Semua Transaksi
                            </a>
                            <a href="{{ route('finance.transactions', array_merge(request()->query(), ['payment_status' => 'paid'])) }}" 
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $filterPaymentStatus === 'paid' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                Lunas
                            </a>
                            <a href="{{ route('finance.transactions', array_merge(request()->query(), ['payment_status' => 'waiting_verification'])) }}" 
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $filterPaymentStatus === 'waiting_verification' ? 'bg-amber-600 text-white shadow-sm' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                                Menunggu Verifikasi
                            </a>
                            <a href="{{ route('finance.transactions', array_merge(request()->query(), ['payment_status' => 'unpaid'])) }}" 
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $filterPaymentStatus === 'unpaid' ? 'bg-blue-600 text-white shadow-sm' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
                                Belum Bayar
                            </a>
                            <a href="{{ route('finance.transactions', array_merge(request()->query(), ['payment_status' => 'rejected'])) }}" 
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $filterPaymentStatus === 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                                Ditolak
                            </a>
                            <a href="{{ route('finance.transactions', array_merge(request()->query(), ['payment_status' => 'cancelled'])) }}" 
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $filterPaymentStatus === 'cancelled' ? 'bg-rose-700 text-white shadow-sm' : 'bg-rose-50 text-rose-800 hover:bg-rose-100' }}">
                                Dibatalkan ({{ $txSummary['cancelledCount'] ?? 0 }})
                            </a>
                        </div>

                        <!-- Search Input -->
                        <div class="relative w-full lg:w-72">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
                            <input type="text" name="search" value="{{ $searchTerm ?? '' }}" placeholder="Cari order, invoice, klien..." class="w-full text-xs pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-amber-500">
                        </div>

                    </div>

                    <!-- Filter Rentang Tanggal & Metode Pembayaran -->
                    <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center gap-3">
                        <span class="text-xs font-bold text-slate-500 flex items-center gap-1">
                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i> Tanggal:
                        </span>
                        <div class="flex items-center gap-2">
                            <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="text-xs px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white">
                            <span class="text-xs text-slate-400">s/d</span>
                            <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="text-xs px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white">
                        </div>

                        <select name="payment_method" class="text-xs px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white">
                            <option value="all">Semua Metode Pembayaran</option>
                            <option value="bca" {{ ($filterMethod ?? '') === 'bca' ? 'selected' : '' }}>Bank BCA</option>
                            <option value="mandiri" {{ ($filterMethod ?? '') === 'mandiri' ? 'selected' : '' }}>Bank Mandiri</option>
                            <option value="bri" {{ ($filterMethod ?? '') === 'bri' ? 'selected' : '' }}>Bank BRI</option>
                            <option value="bni" {{ ($filterMethod ?? '') === 'bni' ? 'selected' : '' }}>Bank BNI</option>
                            <option value="qris" {{ ($filterMethod ?? '') === 'qris' ? 'selected' : '' }}>QRIS Dinamis</option>
                        </select>

                        <button type="submit" class="px-3.5 py-1.5 bg-corporate hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition-all flex items-center gap-1">
                            <i data-lucide="filter" class="w-3.5 h-3.5"></i> Terapkan
                        </button>

                        @if(!empty($searchTerm) || !empty($startDate) || !empty($endDate) || (!empty($filterPaymentStatus) && $filterPaymentStatus !== 'all') || (!empty($filterMethod) && $filterMethod !== 'all'))
                            <a href="{{ route('finance.transactions') }}" class="text-xs text-rose-600 font-bold hover:underline flex items-center gap-1">
                                <i data-lucide="rotate-ccw" class="w-3 h-3"></i> Reset Filter
                            </a>
                        @endif
                    </div>

                </form>

                <!-- Transactions Table -->
                <div class="overflow-x-auto mt-6">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-4 px-6">No. Pesanan &amp; Invoice</th>
                                <th class="py-4 px-6">Perusahaan / PIC Klien</th>
                                <th class="py-4 px-6">Layanan Perizinan</th>
                                <th class="py-4 px-6">Rincian Biaya (IDR)</th>
                                <th class="py-4 px-6">Status Pembayaran</th>
                                <th class="py-4 px-6 text-right">Aksi Dokumen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($transactions as $ord)
                                @php
                                    $paymentMeta = $ord->paymentStatusMeta();
                                    $paymentCode = $paymentMeta['code'];
                                    $isCancelled = $ord->isCancelled() || $paymentCode === 'cancelled';
                                    $basePrice = $ord->base_price > 0 ? $ord->base_price : ($ord->total_cost + $ord->discount_amount);
                                @endphp
                                <tr class="{{ $isCancelled ? 'bg-rose-50/20 hover:bg-rose-50/40' : 'hover:bg-slate-50/60' }} transition-colors">
                                    <td class="py-4 px-6 font-mono text-xs">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <p class="font-bold text-corporate">{{ $ord->order_number }}</p>
                                            @if($isCancelled)
                                                <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-wider bg-rose-100 text-rose-700 border border-rose-200">
                                                    Batal
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-slate-500 font-sans mt-0.5">
                                            {{ $ord->invoice ? $ord->invoice->invoice_number : 'Invoice Belum Terbit' }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 font-sans">
                                            {{ date('d M Y, H:i', strtotime($ord->created_at)) }}
                                        </p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="font-bold text-corporate text-xs">{{ $ord->user->company_name ?? $ord->user->name }}</p>
                                        <p class="text-[11px] text-slate-500">PIC: {{ $ord->user->name }}</p>
                                        <p class="text-[10px] text-slate-400 font-mono">{{ $ord->user->email }} &bull; {{ $ord->user->phone ?? '-' }}</p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="text-xs font-semibold text-slate-700">{{ $ord->service->name ?? 'Layanan Perizinan' }}</p>
                                        <span class="inline-block text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full mt-0.5">
                                            {{ $ord->service->category->name ?? 'Umum' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-xs">
                                        <p class="font-bold text-corporate text-sm">Rp {{ number_format($ord->total_cost, 0, ',', '.') }}</p>
                                        @if($ord->promo_code)
                                            <span class="inline-block text-[10px] bg-indigo-50 text-indigo-700 font-bold px-2 py-0.5 rounded-full mt-0.5">
                                                Kupon: {{ $ord->promo_code }}
                                            </span>
                                        @endif
                                        @if($ord->discount_amount > 0)
                                            <p class="text-[10px] text-emerald-600 font-medium line-through">
                                                Rp {{ number_format($basePrice, 0, ',', '.') }}
                                            </p>
                                            <span class="text-[10px] bg-emerald-50 text-emerald-700 font-bold px-1.5 py-0.5 rounded">
                                                Diskon: -Rp {{ number_format($ord->discount_amount, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($isCancelled)
                                            <div>
                                                <span class="inline-flex items-center gap-1 bg-rose-100 text-rose-800 border border-rose-300 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                                    <i data-lucide="x-circle" class="w-3.5 h-3.5 text-rose-600"></i> Dibatalkan
                                                </span>
                                                <div class="mt-2 p-2.5 bg-rose-50 border border-rose-200/80 rounded-xl text-xs text-rose-950 shadow-xs max-w-xs">
                                                    <div class="flex items-center gap-1 text-rose-800 font-bold text-[11px] mb-1">
                                                        <i data-lucide="info" class="w-3.5 h-3.5 text-rose-600 shrink-0"></i>
                                                        <span>Keterangan Pembatalan:</span>
                                                    </div>
                                                    <p class="text-[11px] text-slate-700 leading-relaxed font-sans">
                                                        {{ $ord->cancellationReason() ?: 'Pesanan dibatalkan oleh pelanggan sebelum pembayaran diselesaikan.' }}
                                                    </p>
                                                    @if($ord->cancellationDate())
                                                        <p class="text-[10px] text-rose-600 font-medium mt-1 flex items-center gap-1">
                                                            <i data-lucide="calendar" class="w-3 h-3 text-rose-400"></i>
                                                            <span>Waktu Batal: {{ $ord->cancellationDate() }}</span>
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif($paymentCode === 'paid')
                                            <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Lunas
                                            </span>
                                            <p class="text-[10px] text-slate-400 mt-0.5">Metode: {{ $ord->latestPayment?->payment_method ?? 'Transfer Bank' }}</p>
                                        @elseif($paymentCode === 'waiting_verification')
                                            <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                                <i data-lucide="clock" class="w-3.5 h-3.5"></i> Menunggu Verifikasi
                                            </span>
                                            <p class="text-[10px] text-amber-600 mt-0.5">Bukti transfer terunggah</p>
                                        @elseif($paymentCode === 'rejected')
                                            <span class="inline-flex items-center gap-1 bg-rose-50 text-rose-700 border border-rose-200 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                                <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Ditolak
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-600 border border-slate-200 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> Belum Bayar
                                            </span>
                                            <p class="text-[10px] text-slate-400 mt-0.5">Menunggu transfer klien</p>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        @if($isCancelled)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-500 bg-rose-50 px-2 py-1 rounded-lg border border-rose-100">
                                                <i data-lucide="slash" class="w-3 h-3"></i> Akses Dinonaktifkan
                                            </span>
                                        @else
                                            <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                                <a href="{{ route('finance.invoice.generate', $ord->id) }}" target="_blank" class="px-2.5 py-1.5 bg-corporate hover:bg-slate-800 text-white rounded-lg text-xs font-bold flex items-center gap-1 transition-all" title="Buka Invoice HTML">
                                                    <i data-lucide="printer" class="w-3.5 h-3.5 text-gold"></i> Invoice
                                                </a>
                                                <a href="{{ route('finance.invoice.pdf', $ord->id) }}" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-bold flex items-center gap-1 transition-all" title="Unduh PDF Resmi">
                                                    <i data-lucide="download" class="w-3.5 h-3.5"></i> PDF
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400 text-sm">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                            <i data-lucide="inbox" class="w-6 h-6"></i>
                                        </div>
                                        <p class="font-bold text-slate-600">Tidak ada transaksi ditemukan</p>
                                        <p class="text-xs text-slate-400 mt-1">Gunakan filter pencarian lain untuk menemukan data transaksi.</p>
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
