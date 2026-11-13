<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrean Verifikasi Pembayaran - Finance IzinYou</title>
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
                    <h2 class="text-xl font-bold text-corporate">Antrean Verifikasi Pembayaran</h2>
                    <span class="bg-amber-100 text-amber-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full">Verifikasi Dua Arah</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Validasi transfer bank, audit forensik keaslian bukti bayar, konfirmasi 'Lunas', atau 'Tolak' pembayaran.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('finance.dashboard') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Kembali ke Dasbor</span>
                </a>
                <a href="{{ route('finance.export.xlsx') }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all" title="Unduh Antrean format Excel (.xlsx)">
                    <i data-lucide="sheet" class="w-4 h-4"></i>
                    <span>Ekspor Excel (.xlsx)</span>
                </a>
                <a href="{{ route('finance.export.pdf') }}" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all" title="Unduh Antrean format PDF (.pdf)">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span>Ekspor PDF (.pdf)</span>
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        @include('finance.partials.alerts')

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8 pt-4 space-y-6">

            <!-- Filter Tabs & Search Bar -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <!-- Status Tabs -->
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('finance.payments', ['status' => 'all', 'search' => $searchTerm]) }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ empty($filterStatus) || $filterStatus === 'all' ? 'bg-corporate text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua Status ({{ $stats['totalPayments'] }})
                    </a>
                    <a href="{{ route('finance.payments', ['status' => 'waiting_confirmation', 'search' => $searchTerm]) }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $filterStatus === 'waiting_confirmation' ? 'bg-amber-600 text-white shadow-sm' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                        Menunggu Verifikasi ({{ $stats['pendingPayments'] }})
                    </a>
                    <a href="{{ route('finance.payments', ['status' => 'success', 'search' => $searchTerm]) }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $filterStatus === 'success' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                        Lunas / Terverifikasi ({{ $stats['verifiedPayments'] }})
                    </a>
                    <a href="{{ route('finance.payments', ['status' => 'failed', 'search' => $searchTerm]) }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $filterStatus === 'failed' ? 'bg-rose-600 text-white shadow-sm' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                        Ditolak ({{ $stats['failedPayments'] }})
                    </a>
                </div>

                <!-- Search Input -->
                <form action="{{ route('finance.payments') }}" method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="status" value="{{ $filterStatus }}">
                    <div class="relative w-full sm:w-64">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="search" value="{{ $searchTerm }}" placeholder="Cari no. order, klien, PT..." class="w-full text-xs pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-amber-500">
                    </div>
                    @if($searchTerm)
                        <a href="{{ route('finance.payments', ['status' => $filterStatus]) }}" class="px-2.5 py-2 text-xs text-slate-500 hover:text-slate-800" title="Reset Pencarian">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        </a>
                    @endif
                    <button type="submit" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition-all">
                        Cari
                    </button>
                </form>
            </div>

            <!-- Payments Table -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-4 px-6">No. Pesanan &amp; Tanggal</th>
                                <th class="py-4 px-6">Perusahaan / PIC</th>
                                <th class="py-4 px-6">Metode &amp; Bukti Transfer</th>
                                <th class="py-4 px-6">Nominal Bayar</th>
                                <th class="py-4 px-6">Hasil Validasi 2-Arah &amp; Status</th>
                                <th class="py-4 px-6 text-right">Aksi Keuangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($payments as $pay)
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
                                        <p class="text-[10px] text-slate-400 font-mono">{{ $pay->client_phone ?? '-' }}</p>
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
                                        <!-- Status Pembayaran Saat Ini -->
                                        <div class="mb-1.5">
                                            @if($pay->status === 'success')
                                                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Lunas / Terverifikasi
                                                </span>
                                            @elseif($pay->status === 'failed')
                                                <span class="inline-flex items-center gap-1 bg-rose-50 text-rose-700 border border-rose-200 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                                    <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Ditolak
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i> Menunggu Verifikasi
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Badge Hasil Validasi Dua Arah -->
                                        @if($v)
                                            <button type="button" onclick="openTwoWayAuditModal({{ $pay->id }})" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all shadow-xs
                                                {{ $v['badge_color'] === 'emerald' ? 'bg-emerald-100/80 text-emerald-800 hover:bg-emerald-200 border border-emerald-300/80' : ($v['badge_color'] === 'amber' ? 'bg-amber-100/80 text-amber-900 hover:bg-amber-200 border border-amber-300/80' : 'bg-rose-100/80 text-rose-900 hover:bg-rose-200 border border-rose-300/80') }}" title="Klik untuk rincian audit 2-arah">
                                                <i data-lucide="shield-check" class="w-3 h-3"></i>
                                                <span>Audit 2-Arah: {{ $v['overall_score'] }}% &bull; {{ $v['verdict_label'] }}</span>
                                            </button>
                                        @endif

                                        @if(!empty($pay->rejection_reason))
                                            <div class="text-[11px] text-rose-600 bg-rose-50/70 p-1.5 rounded-lg border border-rose-100 mt-1 max-w-xs" title="{{ $pay->rejection_reason }}">
                                                Alasan: {{ $pay->rejection_reason }}
                                            </div>
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
                                                <button type="button" onclick="openTwoWayAuditModal({{ $pay->id }})" class="px-2.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-amber-400 rounded-lg text-xs font-bold flex items-center gap-1 shadow-sm transition-all" title="Periksa Validasi Dua Arah">
                                                    <i data-lucide="scan" class="w-3.5 h-3.5"></i> Audit 2-Arah
                                                </button>

                                                <!-- Tombol Verifikasi Lunas -->
                                                @if($pay->status !== 'success')
                                                    <form action="{{ route('finance.payment.verify', $pay->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="status" value="success">
                                                        <button type="submit" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold flex items-center gap-1 shadow-sm transition-all" title="Konfirmasi Lunas">
                                                            <i data-lucide="check" class="w-3.5 h-3.5"></i> Lunas
                                                        </button>
                                                    </form>
                                                    <!-- Tombol Tolak Pembayaran -->
                                                    <button type="button" onclick="openRejectPaymentModal({{ $pay->id }}, '{{ addslashes($pay->order_number ?? '') }}', '{{ addslashes($v['suggested_rejection'] ?? '') }}')" class="px-2.5 py-1.5 bg-rose-600 hover:bg-rose-500 text-white rounded-lg text-xs font-bold flex items-center gap-1 shadow-sm transition-all" title="Tolak Bukti">
                                                        <i data-lucide="x" class="w-3.5 h-3.5"></i> Tolak
                                                    </button>
                                                @endif

                                                <a href="{{ route('finance.invoice.generate', $pay->order_id) }}" target="_blank" class="px-2.5 py-1.5 bg-corporate hover:bg-slate-800 text-white rounded-lg text-xs font-bold flex items-center gap-1 transition-all" title="Buka Invoice">
                                                    <i data-lucide="printer" class="w-3.5 h-3.5 text-gold"></i> Invoice
                                                </a>
                                                <a href="{{ route('finance.invoice.pdf', $pay->order_id) }}" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-bold flex items-center gap-1 transition-all" title="Unduh PDF">
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
                                        <p class="font-bold text-slate-600">Tidak ada data pembayaran ditemukan</p>
                                        <p class="text-xs text-slate-400 mt-1">Coba ubah filter status atau kata kunci pencarian Anda.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <!-- MODAL DETAIL AUDIT KEASLIAN DUA ARAH & TOLAK PEMBAYARAN -->
    @include('finance.partials.two-way-audit-modal')

    <!-- Injeksi Data Verifikasi Dua Arah -->
    <script>
        window.paymentAudits = {
            @foreach($payments as $p)
                @if(isset($p->verification))
                    "{{ $p->id }}": @json($p->verification),
                @endif
            @endforeach
        };
        lucide.createIcons();
    </script>
</body>
</html>
