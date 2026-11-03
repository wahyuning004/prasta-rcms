<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusat Ekspor Laporan - IzinYou Admin</title>
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
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-emerald-500 selection:text-white flex h-screen overflow-hidden">

    <!-- Sidebar Navigation -->
    @include('admin.partials.sidebar')

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/50">
        
        <!-- Top Header Bar -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0 relative z-10">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-corporate">Dashboard</a>
                    <span>/</span>
                    <span class="text-emerald-600 font-semibold">Pusat Laporan</span>
                </div>
                <h2 class="text-xl font-bold text-corporate">Ekspor Rekapitulasi Data Keuangan &amp; Laporan Mutu</h2>
            </div>
            
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400 font-medium">Format Tersedia:</span>
                <span class="bg-rose-100 text-rose-700 font-bold text-xs px-2.5 py-1 rounded-lg">PDF</span>
                <span class="bg-emerald-100 text-emerald-700 font-bold text-xs px-2.5 py-1 rounded-lg">Excel (CSV)</span>
            </div>
        </header>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8">

            <!-- 1. Export Action Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                
                <!-- Card 1: Rekapitulasi Keuangan -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm relative overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                <i data-lucide="wallet" class="w-6 h-6"></i>
                            </div>
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100">
                                Real-Time Database
                            </span>
                        </div>

                        <h3 class="text-lg font-bold text-corporate mb-2">Rekapitulasi Mutasi Keuangan &amp; Transaksi</h3>
                        <p class="text-xs text-slate-500 leading-relaxed mb-4">
                            Laporan resmi berisi seluruh riwayat pemesanan layanan perizinan, mutasi dana masuk, verifikasi bukti transfer, dan status pembayaran dari setiap klien mitra.
                        </p>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 mb-6 space-y-2 text-xs">
                            <div class="flex justify-between text-slate-600">
                                <span>Total Akumulasi Transaksi:</span>
                                <strong class="text-corporate font-bold">Rp {{ number_format($stats['totalRevenue'], 0, ',', '.') }}</strong>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Total Projek Diajukan:</span>
                                <strong class="text-corporate font-bold">{{ $stats['totalOrders'] }} Pesanan</strong>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Projek Tuntas (Completed):</span>
                                <strong class="text-emerald-600 font-bold">{{ $stats['completedOrders'] }} Berhasil</strong>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('admin.reports.finance.pdf') }}" target="_blank" class="flex items-center justify-center gap-2 py-3 px-4 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md transition-all">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                            Ekspor PDF
                        </a>
                        <a href="{{ route('admin.reports.finance.excel') }}" class="flex items-center justify-center gap-2 py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md transition-all">
                            <i data-lucide="sheet" class="w-4 h-4"></i>
                            Ekspor Excel
                        </a>
                    </div>
                </div>

                <!-- Card 2: Laporan Mutu & Kepatuhan Standar -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm relative overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center">
                                <i data-lucide="shield-check" class="w-6 h-6"></i>
                            </div>
                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full border border-blue-100">
                                Standar Audit KAK
                            </span>
                        </div>

                        <h3 class="text-lg font-bold text-corporate mb-2">Laporan Mutu Layanan &amp; Kepatuhan Regulasi</h3>
                        <p class="text-xs text-slate-500 leading-relaxed mb-4">
                            Laporan analisis pengendalian mutu proses perizinan (SOP Kemenkes, BPOM, Sertifikasi Halal BPJPH, CDOB), evaluasi revisi dokumen teknis, dan kepuasan mitra.
                        </p>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 mb-6 space-y-2 text-xs">
                            <div class="flex justify-between text-slate-600">
                                <span>Dokumen Teruji Valid:</span>
                                <strong class="text-blue-600 font-bold">{{ $stats['approvedDocs'] }} dari {{ $stats['totalDocs'] }} Berkas</strong>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Skor Kepuasan Klien (NPS):</span>
                                <strong class="text-amber-500 font-bold flex items-center gap-1">
                                    <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i> {{ $stats['avgRating'] }} / 5.0
                                </strong>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Pemenuhan SLA Target:</span>
                                <strong class="text-emerald-600 font-bold">98.4% Tepat Waktu</strong>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('admin.reports.quality.pdf') }}" target="_blank" class="flex items-center justify-center gap-2 py-3 px-4 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md transition-all">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                            Ekspor PDF
                        </a>
                        <a href="{{ route('admin.reports.quality.excel') }}" class="flex items-center justify-center gap-2 py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md transition-all">
                            <i data-lucide="sheet" class="w-4 h-4"></i>
                            Ekspor Excel
                        </a>
                    </div>
                </div>

            </div>

            <!-- 2. Preview Table Transaksi Terkini -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-corporate text-base">Pratinjau Data Transaksi Mutasi Keuangan</h3>
                        <p class="text-xs text-slate-400">Data live dari sistem manajemen perizinan IzinYou</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-4 px-6">No. Pesanan</th>
                                <th class="py-4 px-6">Klien / Perusahaan</th>
                                <th class="py-4 px-6">Metode</th>
                                <th class="py-4 px-6">Total Nominal</th>
                                <th class="py-4 px-6">Status Projek</th>
                                <th class="py-4 px-6">Status Bayar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($transactions as $trx)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-4 px-6 font-mono text-xs font-bold text-corporate">
                                        {{ $trx->order_number }}
                                        <p class="text-[11px] text-slate-400 font-sans font-normal">{{ date('d M Y, H:i', strtotime($trx->created_at)) }}</p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="font-bold text-corporate text-xs">{{ $trx->client_name }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $trx->company_name ?? '-' }}</p>
                                    </td>
                                    <td class="py-4 px-6 text-xs text-slate-600">
                                        {{ $trx->payment_method ?? 'Transfer Bank' }}
                                    </td>
                                    <td class="py-4 px-6 font-bold text-corporate text-xs">
                                        Rp {{ number_format($trx->total_cost, 0, ',', '.') }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                            @if($trx->order_status === 'completed') bg-emerald-50 text-emerald-700 border border-emerald-200
                                            @elseif($trx->order_status === 'processing') bg-blue-50 text-blue-700 border border-blue-200
                                            @else bg-amber-50 text-amber-700 border border-amber-200
                                            @endif">
                                            {{ ucfirst($trx->order_status) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($trx->payment_status === 'success')
                                            <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Terverifikasi
                                            </span>
                                        @else
                                            <span class="text-xs font-medium text-amber-600 flex items-center gap-1">
                                                <i data-lucide="clock" class="w-3.5 h-3.5"></i> Menunggu Verifikasi
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                        Belum ada data transaksi yang tercatat.
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
