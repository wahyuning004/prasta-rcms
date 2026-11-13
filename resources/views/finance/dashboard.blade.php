<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Keuangan &amp; Administrasi - IzinYou</title>
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
                    <h2 class="text-xl font-bold text-corporate">Dasbor Finance &amp; Administration</h2>
                    <span class="bg-amber-100 text-amber-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full">Modul Keuangan</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Pantau transaksi perizinan, antrean verifikasi bukti transfer, dan status pembayaran pelanggan.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('finance.payments') }}" class="px-3.5 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all">
                    <i data-lucide="wallet" class="w-4 h-4"></i>
                    <span>Antrean Verifikasi ({{ $stats['pendingPayments'] }})</span>
                </a>
                <a href="{{ route('finance.export.xlsx') }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all" title="Unduh Rekapitulasi Excel (.xlsx)">
                    <i data-lucide="sheet" class="w-4 h-4"></i>
                    <span>Ekspor Excel (.xlsx)</span>
                </a>
                <a href="{{ route('finance.export.pdf') }}" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all" title="Unduh Rekapitulasi PDF (.pdf)">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span>Ekspor PDF (.pdf)</span>
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        @include('finance.partials.alerts')

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8 pt-4 space-y-8" id="overview">

            <!-- 1. Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1: Antrean Verifikasi -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Menunggu Verifikasi</p>
                            <h3 class="text-3xl font-extrabold text-amber-600">{{ $stats['pendingPayments'] }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="wallet" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-amber-700 font-medium">Bukti transfer masuk</span>
                        <a href="{{ route('finance.payments', ['status' => 'waiting_confirmation']) }}" class="text-amber-600 font-bold hover:underline">Periksa &rarr;</a>
                    </div>
                </div>

                <!-- Card 2: Pembayaran Terverifikasi -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Terverifikasi (Lunas)</p>
                            <h3 class="text-3xl font-extrabold text-emerald-600">{{ $stats['verifiedPayments'] }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-emerald-700 font-medium">Invoice terbit otomatis</span>
                        <a href="{{ route('finance.payments', ['status' => 'success']) }}" class="text-emerald-600 font-bold hover:underline">Lihat &rarr;</a>
                    </div>
                </div>

                <!-- Card 3: Total Mutasi Keuangan -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Total Mutasi Keuangan</p>
                            <h3 class="text-2xl font-extrabold text-corporate">Rp {{ number_format($stats['totalRevenue'], 0, ',', '.') }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="receipt" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">Dari {{ $stats['totalOrders'] }} total pesanan</span>
                        <a href="{{ route('finance.transactions') }}" class="text-purple-600 font-bold hover:underline">Rincian &rarr;</a>
                    </div>
                </div>

                <!-- Card 4: Tagihan Belum Terbayar -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Tagihan Belum Lunas</p>
                            <h3 class="text-2xl font-extrabold text-blue-600">Rp {{ number_format($stats['unpaidRevenue'], 0, ',', '.') }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="clock" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">Menunggu pembayaran klien</span>
                        <a href="{{ route('finance.transactions', ['payment_status' => 'unpaid']) }}" class="text-blue-600 font-bold hover:underline">Filter &rarr;</a>
                    </div>
                </div>
            </div>

            @if(($stats['cancelledOrders'] ?? 0) > 0)
            <!-- Notifikasi Pesanan Pelanggan Dibatalkan -->
            <div class="bg-rose-50/80 border border-rose-200 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-xs font-bold text-rose-900">Perhatian: Terdapat {{ $stats['cancelledOrders'] }} Pesanan Pelanggan Dibatalkan</h4>
                            <span class="bg-rose-100 text-rose-800 text-[10px] font-extrabold px-2 py-0.5 rounded-full">Informasi Pembatalan</span>
                        </div>
                        <p class="text-[11px] text-rose-700 mt-0.5">Total nominal transaksi yang dibatalkan sebesar Rp {{ number_format($stats['cancelledOrdersVolume'] ?? 0, 0, ',', '.') }}. Keterangan alasan pembatalan dapat ditinjau untuk penyesuaian buku kas.</p>
                    </div>
                </div>
                <a href="{{ route('finance.transactions', ['payment_status' => 'cancelled']) }}" class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold transition-all shadow-sm shrink-0 flex items-center gap-1 self-start sm:self-auto">
                    <span>Lihat Rincian Batal</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
            @endif

            <!-- 2. Section: Antrean Verifikasi Pembayaran Masuk (Perlu Tindakan Segera) -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm" id="antrean-verifikasi">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="wallet" class="w-6 h-6 text-amber-600"></i>
                            <h3 class="text-lg font-bold text-corporate">Antrean Verifikasi Bukti Pembayaran Digital / Transfer</h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Daftar konfirmasi pembayaran dari pelanggan yang memerlukan verifikasi kelayakan dana masuk.</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('finance.payments') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                            <span>Buka Antrean Lengkap</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-4 px-6">No. Pesanan</th>
                                <th class="py-4 px-6">Perusahaan Klien</th>
                                <th class="py-4 px-6">Metode &amp; Bukti</th>
                                <th class="py-4 px-6">Nominal Bayar</th>
                                <th class="py-4 px-6">Status</th>
                                <th class="py-4 px-6 text-right">Aksi Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($pendingPayments as $pay)
                                @php
                                    $v = $pay->verification ?? null;
                                @endphp
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-4 px-6 font-mono text-xs font-bold text-corporate">
                                        {{ $pay->order_number ?? '-' }}
                                        <p class="text-[11px] text-slate-400 font-sans font-normal">{{ date('d M Y, H:i', strtotime($pay->created_at)) }}</p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="font-bold text-corporate text-xs">{{ $pay->company_name ?? '-' }}</p>
                                        <p class="text-[11px] text-slate-500">PIC: {{ $pay->client_name ?? '-' }}</p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="text-xs font-semibold text-slate-700">{{ $pay->payment_method ?? 'Transfer Bank' }}</p>
                                        @if($pay->payment_proof)
                                            <a href="{{ route('payments.proof', $pay->id) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:underline font-semibold mt-0.5">
                                                <i data-lucide="image" class="w-3 h-3"></i> Buka Bukti Asli
                                            </a>
                                        @else
                                            <span class="text-[11px] text-slate-400">Tanpa lampiran</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 font-bold text-corporate text-xs">
                                        Rp {{ number_format($pay->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="mb-1.5">
                                            <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                                <i data-lucide="clock" class="w-3.5 h-3.5"></i> Menunggu Verifikasi
                                            </span>
                                        </div>

                                        <!-- Badge Hasil Validasi Forensik Dua Arah -->
                                        @if($v)
                                            <button type="button" onclick="openTwoWayAuditModal({{ $pay->id }}, '{{ addslashes($pay->order_number ?? '') }}')" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all shadow-xs
                                                {{ $v['badge_color'] === 'emerald' ? 'bg-emerald-100/80 text-emerald-800 hover:bg-emerald-200 border border-emerald-300/80' : ($v['badge_color'] === 'amber' ? 'bg-amber-100/80 text-amber-900 hover:bg-amber-200 border border-amber-300/80' : 'bg-rose-100/80 text-rose-900 hover:bg-rose-200 border border-rose-300/80') }}" title="Klik untuk rincian audit 2-arah">
                                                <i data-lucide="shield-check" class="w-3 h-3"></i>
                                                <span>Audit 2-Arah: {{ $v['overall_score'] }}% &bull; {{ $v['verdict_label'] }}</span>
                                            </button>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        @if($pay->order?->isCancelled())
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-500 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-100">
                                                <i data-lucide="slash" class="w-3 h-3"></i> Pesanan Dibatalkan
                                            </span>
                                        @else
                                            <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                                <!-- Tombol Detail Audit 2-Arah -->
                                                <button type="button" onclick="openTwoWayAuditModal({{ $pay->id }}, '{{ addslashes($pay->order_number ?? '') }}')" class="px-2.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-amber-400 rounded-lg text-xs font-bold flex items-center gap-1 shadow-sm transition-all" title="Periksa Validasi Forensik Dua Arah">
                                                    <i data-lucide="scan" class="w-3.5 h-3.5"></i> Audit 2-Arah
                                                </button>

                                                <!-- Tombol Verifikasi Lunas -->
                                                <form action="{{ route('finance.payment.verify', $pay->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="success">
                                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold flex items-center gap-1 shadow-sm transition-all" title="Konfirmasi Lunas">
                                                        <i data-lucide="check" class="w-3.5 h-3.5"></i> Verifikasi Lunas
                                                    </button>
                                                </form>

                                                <!-- Tombol Tolak Pembayaran -->
                                                <button type="button" onclick="openRejectPaymentModal({{ $pay->id }}, '{{ addslashes($pay->order_number ?? '') }}', '{{ addslashes($v['suggested_rejection'] ?? '') }}')" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-bold flex items-center gap-1 transition-all" title="Tolak Bukti">
                                                    <i data-lucide="x" class="w-3.5 h-3.5"></i> Tolak
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                        <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                                            <i data-lucide="check-check" class="w-6 h-6"></i>
                                        </div>
                                        <p class="font-bold text-slate-600">Semua Pembayaran Telah Terverifikasi</p>
                                        <p class="text-xs text-slate-400 mt-1">Tidak ada antrean bukti transfer yang menunggu konfirmasi saat ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. Section: Monitoring Status Pembayaran Pelanggan Terkini -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm" id="status-pelanggan">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="credit-card" class="w-6 h-6 text-sky-600"></i>
                            <h3 class="text-lg font-bold text-corporate">Monitoring Transaksi &amp; Status Pembayaran Pelanggan</h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Lacak status penyelesaian invoice, riwayat transaksi, dan kepatuhan pembayaran pelanggan mitra.</p>
                    </div>

                    <a href="{{ route('finance.transactions') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1">
                        <span>Lihat Semua Transaksi</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-4 px-6">Pesanan &amp; Invoice</th>
                                <th class="py-4 px-6">Perusahaan / PIC</th>
                                <th class="py-4 px-6">Layanan Perizinan</th>
                                <th class="py-4 px-6">Total Tagihan</th>
                                <th class="py-4 px-6">Status Pembayaran Pelanggan</th>
                                <th class="py-4 px-6 text-right">Dokumen Invoice</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($recentTransactions as $ord)
                                @php
                                    $paymentMeta = $ord->paymentStatusMeta();
                                    $paymentCode = $paymentMeta['code'];
                                    $isCancelled = $ord->isCancelled() || $paymentCode === 'cancelled';
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
                                        <p class="text-[11px] text-slate-400 font-sans">
                                            {{ $ord->invoice ? $ord->invoice->invoice_number : 'Inv Pending' }} &bull; {{ date('d M Y', strtotime($ord->created_at)) }}
                                        </p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="font-bold text-corporate text-xs">{{ $ord->user->company_name ?? $ord->user->name }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $ord->user->name }} ({{ $ord->user->phone ?? '-' }})</p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="text-xs font-medium text-slate-700">{{ $ord->service->name ?? 'Layanan Perizinan' }}</p>
                                        @if($ord->promo_code)
                                            <span class="inline-block text-[10px] bg-indigo-50 text-indigo-700 font-semibold px-2 py-0.5 rounded-full mt-0.5">
                                                Kupon: {{ $ord->promo_code }} (-Rp {{ number_format($ord->discount_amount, 0, ',', '.') }})
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 font-bold text-corporate text-xs">
                                        Rp {{ number_format($ord->total_cost, 0, ',', '.') }}
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($isCancelled)
                                            <div>
                                                <span class="inline-flex items-center gap-1 bg-rose-100 text-rose-800 border border-rose-300 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                                    <i data-lucide="x-circle" class="w-3.5 h-3.5 text-rose-600"></i> Dibatalkan
                                                </span>
                                                <div class="mt-1.5 p-2 bg-rose-50 border border-rose-200/80 rounded-lg text-xs text-rose-950 max-w-xs">
                                                    <p class="text-[10px] text-rose-700 font-bold uppercase tracking-wider flex items-center gap-1">
                                                        <i data-lucide="info" class="w-3 h-3 text-rose-500"></i> Keterangan Pembatalan:
                                                    </p>
                                                    <p class="text-[11px] text-slate-700 font-sans mt-0.5 leading-snug">
                                                        {{ Str::limit($ord->cancellationReason() ?: 'Pesanan dibatalkan pelanggan.', 140) }}
                                                    </p>
                                                </div>
                                            </div>
                                        @elseif($paymentCode === 'paid')
                                            <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Lunas
                                            </span>
                                        @elseif($paymentCode === 'waiting_verification')
                                            <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                                <i data-lucide="clock" class="w-3.5 h-3.5"></i> Menunggu Verifikasi
                                            </span>
                                        @elseif($paymentCode === 'rejected')
                                            <span class="inline-flex items-center gap-1 bg-rose-50 text-rose-700 border border-rose-200 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                                <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Pembayaran Ditolak
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-600 border border-slate-200 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> Belum Bayar
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        @if($isCancelled)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-500 bg-rose-50 px-2 py-1 rounded-lg border border-rose-100">
                                                <i data-lucide="slash" class="w-3 h-3"></i> Akses Dinonaktifkan
                                            </span>
                                        @else
                                            <div class="flex items-center justify-end gap-1.5">
                                                <a href="{{ route('finance.invoice.generate', $ord->id) }}" target="_blank" class="px-2.5 py-1.5 bg-corporate hover:bg-slate-800 text-white rounded-lg text-xs font-bold flex items-center gap-1 transition-all" title="Cetak / Buka Invoice">
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
                                    <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                        Belum ada transaksi atau pesanan masuk.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4. Section: Distribusi Metode Pembayaran & Saluran Masuk Kas -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm lg:col-span-2">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div class="flex items-center gap-2">
                            <i data-lucide="pie-chart" class="w-5 h-5 text-amber-600"></i>
                            <h3 class="font-bold text-corporate text-base">Metode Pembayaran Pelanggan</h3>
                        </div>
                        <span class="text-xs text-slate-400">Akumulasi seluruh transaksi</span>
                    </div>

                    <div class="space-y-4">
                        @forelse($paymentMethods as $pm)
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center font-bold text-amber-600 shadow-sm">
                                        <i data-lucide="credit-card" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-corporate text-sm">{{ $pm->payment_method ?? 'Transfer Bank' }}</h5>
                                        <p class="text-xs text-slate-400">{{ $pm->count }} transaksi pembayaran</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-extrabold text-corporate text-sm">Rp {{ number_format((float) $pm->total_amount, 0, ',', '.') }}</p>
                                    <span class="text-[11px] text-emerald-600 font-semibold">Tercatat</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-6 text-center">Belum ada mutasi metode pembayaran.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-gradient-to-br from-corporate to-slate-800 rounded-3xl p-6 text-white shadow-xl flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center mb-4 border border-amber-500/30">
                            <i data-lucide="shield-check" class="w-6 h-6"></i>
                        </div>
                        <h4 class="font-bold text-base text-white mb-2">Integritas Keuangan &amp; Audit</h4>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Seluruh mutasi dana pelanggan terikat dengan invoice resmi, nomor pesanan unik, dan catatan log verifikasi petugas untuk kepatuhan tata kelola keuangan perizinan.
                        </p>
                    </div>

                    <div class="pt-6 border-t border-slate-700/80">
                        <div class="flex justify-between items-center text-xs text-slate-300 mb-3">
                            <span>Kepatuhan Transaksi:</span>
                            <strong class="text-emerald-400 font-bold">100% Tercatat</strong>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('finance.export.xlsx') }}" class="py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold text-center shadow-sm transition-all flex items-center justify-center gap-1">
                                <i data-lucide="sheet" class="w-3.5 h-3.5"></i>
                                <span>Excel (.xlsx)</span>
                            </a>
                            <a href="{{ route('finance.export.pdf') }}" class="py-2.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold text-center shadow-sm transition-all flex items-center justify-center gap-1">
                                <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                <span>PDF (.pdf)</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- MODAL DETAIL AUDIT KEASLIAN DUA ARAH & TOLAK PEMBAYARAN -->
    @include('finance.partials.two-way-audit-modal')

    <!-- Injeksi Data Verifikasi Dua Arah -->
    <script>
        window.paymentAudits = {
            @foreach($pendingPayments as $p)
                @if(isset($p->verification))
                    "{{ $p->id }}": @json($p->verification),
                @endif
            @endforeach
        };
        lucide.createIcons();
    </script>
</body>
</html>
