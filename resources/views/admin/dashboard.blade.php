<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - IzinYou</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon-192.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    <!-- Sidebar -->
    @include('admin.partials.sidebar')

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/50">
        <!-- Top Header -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0 relative z-10">
            <div>
                <h2 class="text-xl font-bold text-corporate">Dashboard Ringkasan</h2>
                <p class="text-sm text-slate-500">Selamat datang kembali, pantau kinerja sistem IZINYOU Anda hari ini.</p>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- Tombol Ekspor Laporan PDF & Excel -->
                <div class="relative group">
                    <button class="flex items-center gap-2 px-3.5 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span>Ekspor Laporan</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                    </button>
                    <div class="absolute right-0 mt-1 w-60 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 hidden group-hover:block z-50">
                        <p class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Rekapitulasi Keuangan</p>
                        <a href="{{ route('admin.reports.finance.pdf') }}" target="_blank" class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 hover:bg-rose-50 hover:text-rose-600 font-medium">
                            <i data-lucide="file-text" class="w-4 h-4 text-rose-500"></i> Mutasi Keuangan (PDF)
                        </a>
                        <a href="{{ route('admin.reports.finance.excel') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 hover:bg-sky-50 hover:text-sky-600 font-medium">
                            <i data-lucide="sheet" class="w-4 h-4 text-sky-500"></i> Mutasi Keuangan (Excel)
                        </a>
                        <div class="border-t border-slate-100 my-1"></div>
                        <p class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Laporan Pengendalian Mutu</p>
                        <a href="{{ route('admin.reports.quality.pdf') }}" target="_blank" class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 hover:bg-rose-50 hover:text-rose-600 font-medium">
                            <i data-lucide="file-text" class="w-4 h-4 text-rose-500"></i> Laporan Mutu &amp; KAK (PDF)
                        </a>
                        <a href="{{ route('admin.reports.quality.excel') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 hover:bg-sky-50 hover:text-sky-600 font-medium">
                            <i data-lucide="sheet" class="w-4 h-4 text-sky-500"></i> Laporan Mutu (Excel)
                        </a>
                        <div class="border-t border-slate-100 my-1"></div>
                        <a href="{{ route('admin.reports.index') }}" class="flex items-center justify-between px-3 py-2 text-xs text-sky-700 font-bold hover:bg-sky-50">
                            <span>Buka Pusat Laporan Lengkap</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>

                <!-- Search -->
                <div class="relative hidden md:block">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 transform -translate-y-1/2"></i>
                    <input type="text" placeholder="Cari klien, pesanan..." class="pl-10 pr-4 py-2 bg-slate-100 border-transparent rounded-full text-sm focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none w-56 transition-all">
                </div>
                
                <!-- Notifications -->
                <button class="relative text-slate-400 hover:text-corporate transition-colors">
                    <i data-lucide="bell" class="w-6 h-6"></i>
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-rose-500 rounded-full border-2 border-white flex items-center justify-center text-[9px] font-bold text-white">4</span>
                </button>
            </div>
        </header>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8">
            <div class="max-w-7xl mx-auto space-y-8">
                
                <!-- 1. Statistik Ringkasan (Cards) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Card 1 -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-sky-50 rounded-bl-full -mr-4 -mt-4 opacity-50 group-hover:scale-110 transition-transform"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-500 mb-1">Pengajuan & Projek</p>
                                <h3 class="text-3xl font-extrabold text-corporate">{{ $stats['totalOrders'] ?? 24 }}</h3>
                            </div>
                            <div class="w-12 h-12 bg-sky-100 text-sky-600 rounded-xl flex items-center justify-center">
                                <i data-lucide="activity" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 text-sm">
                            <span class="text-sky-600 font-medium flex items-center"><i data-lucide="trending-up" class="w-3 h-3 mr-1"></i> Aktif</span>
                            <span class="text-slate-400">di sistem IZINYOU</span>
                        </div>
                    </div>
                    
                    <!-- Card 2 -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 opacity-50 group-hover:scale-110 transition-transform"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-500 mb-1">Klien Terdaftar</p>
                                <h3 class="text-3xl font-extrabold text-corporate">{{ $stats['totalClients'] ?? 1 }}</h3>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                                <i data-lucide="users-2" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 text-sm">
                            <span class="text-blue-600 font-medium flex items-center"><i data-lucide="building-2" class="w-3 h-3 mr-1"></i> Perusahaan</span>
                            <span class="text-slate-400">terverifikasi</span>
                        </div>
                    </div>
                    
                    <!-- Card 3 -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-amber-50 rounded-bl-full -mr-4 -mt-4 opacity-50 group-hover:scale-110 transition-transform"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-500 mb-1">Menunggu Review</p>
                                <h3 class="text-3xl font-extrabold text-corporate">{{ ($stats['pendingDocs'] ?? 0) + ($stats['pendingConsultations'] ?? 0) }}</h3>
                            </div>
                            <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center">
                                <i data-lucide="clock" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 text-sm">
                            <span class="text-amber-600 font-medium">{{ $stats['pendingDocs'] ?? 0 }} Dokumen, {{ $stats['pendingConsultations'] ?? 0 }} Konsultasi</span>
                        </div>
                    </div>
                    
                    <!-- Card 4 -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-purple-50 rounded-bl-full -mr-4 -mt-4 opacity-50 group-hover:scale-110 transition-transform"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-500 mb-1">Estimasi Nilai Projek</p>
                                <h3 class="text-xl font-extrabold text-corporate">Rp {{ number_format($stats['totalRevenue'] ?? 3500000, 0, ',', '.') }}</h3>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center">
                                <i data-lucide="wallet" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 text-sm">
                            <span class="text-sky-600 font-medium flex items-center"><i data-lucide="check-circle" class="w-3 h-3 mr-1"></i> Terproses</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- 2. Grafik Statistik Layanan Populer -->
                    <div class="lg:col-span-1 bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-bold text-corporate text-lg">Layanan Populer</h3>
                            <button class="text-slate-400 hover:text-sky-600"><i data-lucide="more-horizontal" class="w-5 h-5"></i></button>
                        </div>
                        <div class="flex-1 relative min-h-[250px] w-full flex items-center justify-center">
                            <canvas id="popularServicesChart"></canvas>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-2 gap-4">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-[#10b981]"></div>
                                <span class="text-xs text-slate-500 font-medium">Alkes (45%)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-[#3b82f6]"></div>
                                <span class="text-xs text-slate-500 font-medium">BPOM (25%)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-[#d4af37]"></div>
                                <span class="text-xs text-slate-500 font-medium">Halal (15%)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-[#8b5cf6]"></div>
                                <span class="text-xs text-slate-500 font-medium">SNI (15%)</span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Tabel Pengajuan / Pesanan Terbaru -->
                    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
                        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-white">
                            <h3 class="font-bold text-corporate text-lg">Pesanan Terbaru</h3>
                            <a href="#" class="text-sm font-semibold text-sky-600 hover:text-sky-700 flex items-center gap-1">Lihat Semua <i data-lucide="chevron-right" class="w-4 h-4"></i></a>
                        </div>
                        
                        <div class="overflow-x-auto flex-1">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/50 border-b border-slate-100">
                                        <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">No. Pesanan</th>
                                        <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Klien / Perusahaan</th>
                                        <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Jenis Layanan</th>
                                        <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                        <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <!-- Row 1 -->
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="py-4 px-6">
                                            <span class="font-semibold text-corporate">#ORD-2026-081</span>
                                            <p class="text-xs text-slate-400 mt-0.5">Hari ini, 10:45</p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">MA</div>
                                                <div>
                                                    <p class="font-semibold text-corporate text-sm">PT Medika Asia</p>
                                                    <p class="text-xs text-slate-500">Budi Santoso</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="text-sm font-medium text-slate-700">Izin Edar Alkes</span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                                Menunggu Verifikasi
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <button class="p-1.5 bg-sky-50 text-sky-600 hover:bg-sky-100 rounded-lg transition-colors" title="Verifikasi">
                                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Row 2 -->
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="py-4 px-6">
                                            <span class="font-semibold text-corporate">#ORD-2026-080</span>
                                            <p class="text-xs text-slate-400 mt-0.5">Kemarin, 14:20</p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center font-bold text-xs">KF</div>
                                                <div>
                                                    <p class="font-semibold text-corporate text-sm">CV Kosmetik Famindo</p>
                                                    <p class="text-xs text-slate-500">Siti Rahma</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="text-sm font-medium text-slate-700">Izin Edar Kosmetik</span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                                Proses Analisis
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <button class="p-1.5 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-lg transition-colors" title="Detail">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Row 3 -->
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="py-4 px-6">
                                            <span class="font-semibold text-corporate">#ORD-2026-079</span>
                                            <p class="text-xs text-slate-400 mt-0.5">06 Okt 2026</p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-xs">FN</div>
                                                <div>
                                                    <p class="font-semibold text-corporate text-sm">PT Food Nusantara</p>
                                                    <p class="text-xs text-slate-500">Ahmad Zaki</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="text-sm font-medium text-slate-700">Sertifikasi Halal</span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-sky-100 text-sky-700">
                                                Selesai
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <button class="p-1.5 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-lg transition-colors" title="Detail">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Row 4 -->
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="py-4 px-6">
                                            <span class="font-semibold text-corporate">#ORD-2026-078</span>
                                            <p class="text-xs text-slate-400 mt-0.5">05 Okt 2026</p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-xs">DP</div>
                                                <div>
                                                    <p class="font-semibold text-corporate text-sm">Distributor Pharma</p>
                                                    <p class="text-xs text-slate-500">Linda Wijaya</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="text-sm font-medium text-slate-700">Sertifikat CDOB</span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                                Menunggu Pembayaran
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <button class="p-1.5 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-lg transition-colors" title="Detail">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            
            <!-- Footer Content -->
            <footer class="mt-12 text-center text-sm text-slate-500 pb-4">
                <p>&copy; {{ date('Y') }} IzinYou.com &bull; Platform by CV Prasta Solusi Indonesia.</p>
            </footer>
        </div>
    </main>

    <!-- Init Scripts -->
    <script>
        // Init Lucide Icons
        lucide.createIcons();

        // Chart.js Setup
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('popularServicesChart').getContext('2d');
            
            // Register Plugin for text in center
            const centerTextPlugin = {
                id: 'centerText',
                beforeDraw: function(chart) {
                    if (chart.config.type !== 'doughnut') return;
                    var width = chart.width,
                        height = chart.height,
                        ctx = chart.ctx;
            
                    ctx.restore();
                    var fontSize = (height / 114).toFixed(2);
                    ctx.font = "bold " + fontSize + "em Inter";
                    ctx.textBaseline = "middle";
                    ctx.fillStyle = "#0f172a";
            
                    var text = "450",
                        textX = Math.round((width - ctx.measureText(text).width) / 2),
                        textY = height / 2 - 5;
            
                    ctx.fillText(text, textX, textY);
                    
                    ctx.font = "500 " + (fontSize * 0.4).toFixed(2) + "em Inter";
                    ctx.fillStyle = "#64748b";
                    var text2 = "Total Ajuan";
                    var text2X = Math.round((width - ctx.measureText(text2).width) / 2);
                    ctx.fillText(text2, text2X, textY + 20);
                    
                    ctx.save();
                }
            };
            
            Chart.register(centerTextPlugin);

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Alkes', 'BPOM', 'Halal', 'SNI'],
                    datasets: [{
                        data: [45, 25, 15, 15],
                        backgroundColor: [
                            '#10b981', // Emerald
                            '#3b82f6', // Blue
                            '#d4af37', // Gold
                            '#8b5cf6'  // Purple
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.label + ': ' + context.raw + '%';
                                }
                            },
                            backgroundColor: '#0f172a',
                            titleFont: { family: 'Inter' },
                            bodyFont: { family: 'Inter' },
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: true
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
