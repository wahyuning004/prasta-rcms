<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - RCMS Prasta Solusi Indonesia</title>
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
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-emerald-500 selection:text-white flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-72 bg-corporate text-slate-300 flex flex-col h-full shrink-0 shadow-2xl relative z-20 transition-transform duration-300 border-r border-slate-800">
        <!-- Logo -->
        <div class="h-20 flex items-center px-6 border-b border-slate-800 gap-3">
            <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center overflow-hidden flex-shrink-0">
                <img src="/images/logo.png" alt="Logo Prasta" class="w-full h-full object-contain" onerror="this.src='https://ui-avatars.com/api/?name=PS&background=0f172a&color=fff'">
            </div>
            <div>
                <h1 class="font-bold text-white text-sm leading-tight">RCMS Admin</h1>
                <p class="text-[10px] text-gold tracking-widest uppercase">Prasta Solusi Indonesia</p>
            </div>
        </div>

        <!-- Menu Navigation -->
        <div class="flex-1 overflow-y-auto sidebar-scroll py-6 px-4">
            <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Navigasi Utama</p>
            
            <nav class="space-y-1">
                <!-- Dashboard (Active) -->
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 bg-emerald-600/10 text-emerald-400 rounded-lg group transition-colors">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span class="font-medium text-sm">Dashboard</span>
                </a>
                
                <!-- Layanan & Kategori -->
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg group transition-colors">
                    <i data-lucide="layers" class="w-5 h-5"></i>
                    <span class="font-medium text-sm">Layanan & Kategori</span>
                </a>
                
                <!-- Data Klien -->
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg group transition-colors">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    <span class="font-medium text-sm">Data Klien</span>
                </a>
                
                <!-- Dokumen Persyaratan -->
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg group transition-colors">
                    <i data-lucide="folder-check" class="w-5 h-5"></i>
                    <span class="font-medium text-sm">Dokumen Persyaratan</span>
                </a>
                
                <!-- Pelacakan Projek (Tracking) -->
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg group transition-colors flex justify-between">
                    <div class="flex items-center gap-3">
                        <i data-lucide="git-merge" class="w-5 h-5"></i>
                        <span class="font-medium text-sm">Pelacakan Projek</span>
                    </div>
                    <span class="bg-emerald-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">3 Aktif</span>
                </a>
                
                <!-- Transaksi Pembayaran -->
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg group transition-colors">
                    <i data-lucide="credit-card" class="w-5 h-5"></i>
                    <span class="font-medium text-sm">Transaksi Pembayaran</span>
                </a>
                
                <!-- Laporan -->
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg group transition-colors">
                    <i data-lucide="file-bar-chart" class="w-5 h-5"></i>
                    <span class="font-medium text-sm">Laporan Mutu</span>
                </a>
            </nav>
        </div>

        <!-- User Profile (Bottom) -->
        <div class="p-4 border-t border-slate-800">
            <div class="flex items-center gap-3 p-2 rounded-lg bg-slate-800/50 border border-slate-700">
                <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center text-emerald-400">
                    <i data-lucide="user" class="w-5 h-5"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate">Admin Prasta</p>
                    <p class="text-[11px] text-slate-400 truncate">admin@prastasolusi.com</p>
                </div>
                <button class="text-slate-400 hover:text-rose-400 p-1.5 transition-colors">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/50">
        <!-- Top Header -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0 relative z-10">
            <div>
                <h2 class="text-xl font-bold text-corporate">Dashboard Ringkasan</h2>
                <p class="text-sm text-slate-500">Selamat datang kembali, pantau kinerja sistem RCMS Anda hari ini.</p>
            </div>
            
            <div class="flex items-center gap-6">
                <!-- Search -->
                <div class="relative hidden md:block">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 transform -translate-y-1/2"></i>
                    <input type="text" placeholder="Cari klien, pesanan..." class="pl-10 pr-4 py-2 bg-slate-100 border-transparent rounded-full text-sm focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-64 transition-all">
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
                        <div class="absolute right-0 top-0 w-24 h-24 bg-emerald-50 rounded-bl-full -mr-4 -mt-4 opacity-50 group-hover:scale-110 transition-transform"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-500 mb-1">Pengajuan Aktif</p>
                                <h3 class="text-3xl font-extrabold text-corporate">24</h3>
                            </div>
                            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
                                <i data-lucide="activity" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 text-sm">
                            <span class="text-emerald-600 font-medium flex items-center"><i data-lucide="trending-up" class="w-3 h-3 mr-1"></i> +12%</span>
                            <span class="text-slate-400">bulan ini</span>
                        </div>
                    </div>
                    
                    <!-- Card 2 -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 opacity-50 group-hover:scale-110 transition-transform"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-500 mb-1">Klien Terdaftar</p>
                                <h3 class="text-3xl font-extrabold text-corporate">142</h3>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                                <i data-lucide="users-2" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 text-sm">
                            <span class="text-emerald-600 font-medium flex items-center"><i data-lucide="trending-up" class="w-3 h-3 mr-1"></i> +5</span>
                            <span class="text-slate-400">minggu ini</span>
                        </div>
                    </div>
                    
                    <!-- Card 3 -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-amber-50 rounded-bl-full -mr-4 -mt-4 opacity-50 group-hover:scale-110 transition-transform"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-500 mb-1">Pesanan Pending</p>
                                <h3 class="text-3xl font-extrabold text-corporate">8</h3>
                            </div>
                            <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center">
                                <i data-lucide="clock" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 text-sm">
                            <span class="text-amber-500 font-medium">Butuh verifikasi segera</span>
                        </div>
                    </div>
                    
                    <!-- Card 4 -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-purple-50 rounded-bl-full -mr-4 -mt-4 opacity-50 group-hover:scale-110 transition-transform"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-500 mb-1">Total Pendapatan</p>
                                <h3 class="text-xl font-extrabold text-corporate">Rp 128.5M</h3>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center">
                                <i data-lucide="wallet" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 text-sm">
                            <span class="text-emerald-600 font-medium flex items-center"><i data-lucide="trending-up" class="w-3 h-3 mr-1"></i> +8.4%</span>
                            <span class="text-slate-400">bulan ini</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- 2. Grafik Statistik Layanan Populer -->
                    <div class="lg:col-span-1 bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-bold text-corporate text-lg">Layanan Populer</h3>
                            <button class="text-slate-400 hover:text-emerald-600"><i data-lucide="more-horizontal" class="w-5 h-5"></i></button>
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
                            <a href="#" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">Lihat Semua <i data-lucide="chevron-right" class="w-4 h-4"></i></a>
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
                                            <button class="p-1.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-lg transition-colors" title="Verifikasi">
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
                                                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs">KF</div>
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
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
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
                <p>&copy; 2026 Prasta Solusi Indonesia. Regulatory & Certification Management System.</p>
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
