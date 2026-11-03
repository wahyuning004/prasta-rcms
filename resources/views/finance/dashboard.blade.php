<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Finance &amp; Licensing Officer - IzinYou</title>
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
    <aside class="w-72 bg-corporate text-slate-300 flex flex-col h-full shrink-0 shadow-2xl relative z-20 border-r border-slate-800">
        <!-- Logo Header -->
        <div class="h-20 flex items-center px-6 border-b border-slate-800 gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center shadow-inner flex-shrink-0 border border-slate-700">
                <svg class="w-6 h-6 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L3 7v6c0 5.5 3.8 10.7 9 12 5.2-1.3 9-6.5 9-12V7l-9-5z"/>
                    <path d="m9 12 2 2 4-4" stroke="#fbbf24" stroke-width="2.5"/>
                </svg>
            </div>
            <div>
                <h1 class="font-bold text-white text-base leading-tight">IzinYou Finance</h1>
                <p class="text-[10px] text-amber-400 font-semibold tracking-widest uppercase">&amp; Licensing Officer</p>
                <p class="text-[9px] text-slate-400">by CV Prasta Solusi Indonesia</p>
            </div>
        </div>

        <!-- Menu Navigation -->
        <div class="flex-1 overflow-y-auto sidebar-scroll py-6 px-4">
            <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Divisi Keuangan &amp; Operasional</p>
            
            <nav class="space-y-1.5">
                <a href="#overview" class="flex items-center gap-3 px-3 py-2.5 bg-amber-600/15 text-amber-400 rounded-xl group transition-all font-medium text-sm">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Dashboard Finance</span>
                </a>

                <a href="#pembayaran" class="flex items-center justify-between px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl group transition-all font-medium text-sm">
                    <div class="flex items-center gap-3">
                        <i data-lucide="wallet" class="w-5 h-5"></i>
                        <span>Verifikasi Pembayaran</span>
                    </div>
                    @if($stats['pendingPayments'] > 0)
                        <span class="bg-amber-500 text-slate-900 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $stats['pendingPayments'] }} Baru</span>
                    @endif
                </a>

                <a href="#tracking" class="flex items-center justify-between px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl group transition-all font-medium text-sm">
                    <div class="flex items-center gap-3">
                        <i data-lucide="git-merge" class="w-5 h-5"></i>
                        <span>Linimasa Stepper</span>
                    </div>
                    <span class="bg-blue-500/20 text-blue-300 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $orders->count() }} Projek</span>
                </a>

                <a href="#arsip" class="flex items-center justify-between px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl group transition-all font-medium text-sm">
                    <div class="flex items-center gap-3">
                        <i data-lucide="server" class="w-5 h-5"></i>
                        <span>Arsip Privat Server</span>
                    </div>
                    <span class="bg-slate-800 text-slate-300 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $stats['totalArchives'] }} File</span>
                </a>

                <div class="pt-6">
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700/60 text-center">
                        <div class="w-9 h-9 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center mx-auto mb-2">
                            <i data-lucide="download" class="w-5 h-5"></i>
                        </div>
                        <h4 class="text-xs font-bold text-white mb-1">Ekspor Data Transaksi</h4>
                        <p class="text-[11px] text-slate-400 leading-tight mb-3">Unduh data mutasi keuangan format Excel / CSV untuk rekonsiliasi kas.</p>
                        <a href="{{ route('finance.export.excel') }}" class="block w-full py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-lg text-xs font-bold transition-all shadow-md">
                            Ekspor Excel (CSV)
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- User Profile (Bottom) -->
        <div class="p-4 border-t border-slate-800">
            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-800/60 border border-slate-700/80">
                <div class="w-10 h-10 rounded-full bg-amber-900/60 text-amber-300 border border-amber-500/30 flex items-center justify-center font-bold">
                    {{ strtoupper(substr($user->name ?? 'F', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate">{{ $user->name }}</p>
                    <p class="text-[11px] text-amber-400 truncate flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
                        Finance &amp; Officer
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
                    <h2 class="text-xl font-bold text-corporate">Dashboard Finance &amp; Licensing Officer</h2>
                    <span class="bg-amber-100 text-amber-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full">Sistem IZINYOU</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Verifikasi pembayaran transfer, update progress tracking stepper, dan direktori arsip privat server.</p>
            </div>

            <div class="flex items-center gap-3">
                <button onclick="document.getElementById('uploadArchiveModal').classList.remove('hidden')" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all">
                    <i data-lucide="upload-cloud" class="w-4 h-4 text-emerald-400"></i>
                    <span>Arsip Server Baru</span>
                </button>
                <a href="{{ route('finance.export.excel') }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all">
                    <i data-lucide="sheet" class="w-4 h-4"></i>
                    <span>Ekspor Excel</span>
                </a>
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
        </div>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8 pt-4 space-y-8" id="overview">

            <!-- 1. Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Menunggu Verifikasi Bayar</p>
                            <h3 class="text-3xl font-extrabold text-amber-600">{{ $stats['pendingPayments'] }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="wallet" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-xs text-amber-700 font-medium">Bukti transfer baru diunggah</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Pembayaran Terverifikasi</p>
                            <h3 class="text-3xl font-extrabold text-emerald-600">{{ $stats['verifiedPayments'] }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-xs text-emerald-700 font-medium">Invoice otomatis terbit</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Total Mutasi Keuangan</p>
                            <h3 class="text-2xl font-extrabold text-corporate">Rp {{ number_format($stats['totalRevenue'], 0, ',', '.') }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="receipt" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">Akumulasi transaksi projek</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Dokumen Server Privat</p>
                            <h3 class="text-3xl font-extrabold text-blue-600">{{ $stats['totalArchives'] }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="server" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">Tersimpan aman &amp; terenkripsi</p>
                </div>
            </div>

            <!-- 2. Section A: Antrean Verifikasi Bukti Pembayaran Digital / Transfer & Penerbitan Invoice -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm" id="pembayaran">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="credit-card" class="w-6 h-6 text-amber-600"></i>
                            <h3 class="text-lg font-bold text-corporate">Antrean Verifikasi Bukti Pembayaran &amp; Penerbitan Invoice</h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Periksa mutasi dana masuk, verifikasi bukti transfer, dan cetak invoice resmi klien.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('finance.dashboard') }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ empty($filterStatus) || $filterStatus === 'all' ? 'bg-corporate text-white' : 'bg-slate-100 text-slate-600' }}">Semua</a>
                        <a href="{{ route('finance.dashboard', ['status' => 'waiting_confirmation']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ $filterStatus === 'waiting_confirmation' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600' }}">Menunggu Konfirmasi</a>
                        <a href="{{ route('finance.dashboard', ['status' => 'success']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ $filterStatus === 'success' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600' }}">Lunas (Terverifikasi)</a>
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
                                <th class="py-4 px-6">Status Verifikasi</th>
                                <th class="py-4 px-6 text-right">Aksi Keuangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($payments as $pay)
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
                                            <span class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:underline cursor-pointer" onclick="alert('Bukti Transfer Terlampir: {{ basename($pay->payment_proof) }} (Tervalidasi)')">
                                                <i data-lucide="image" class="w-3 h-3"></i> Lihat Bukti
                                            </span>
                                        @else
                                            <span class="text-[11px] text-slate-400">Tanpa lampiran</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 font-bold text-corporate text-xs">
                                        Rp {{ number_format($pay->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-4 px-6">
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
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($pay->status !== 'success')
                                                <form action="{{ route('finance.payment.verify', $pay->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="success">
                                                    <button type="submit" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold flex items-center gap-1 transition-all" title="Konfirmasi Lunas">
                                                        <i data-lucide="check" class="w-3.5 h-3.5"></i> Verifikasi Lunas
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- Tombol Penerbitan Invoice Resmi -->
                                            <a href="{{ route('finance.invoice.generate', $pay->order_id) }}" target="_blank" class="px-2.5 py-1.5 bg-corporate hover:bg-slate-800 text-white rounded-lg text-xs font-bold flex items-center gap-1 transition-all" title="Cetak Invoice">
                                                <i data-lucide="printer" class="w-3.5 h-3.5 text-gold"></i> Cetak Invoice
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                        Tidak ada data pembayaran pada antrean ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. Section B: Update Linimasa Progress Tracking Projek (Real-Time Stepper) -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm" id="tracking">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="git-branch" class="w-6 h-6 text-blue-600"></i>
                            <h3 class="text-lg font-bold text-corporate">Update Linimasa Pengerjaan Projek (Real-Time Stepper)</h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Perbarui progress milestone pengerjaan izin secara transparan kepada portal klien.</p>
                    </div>

                    <button onclick="document.getElementById('addTrackingModal').classList.remove('hidden')" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-sm">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Tahapan Linimasa
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Form Cepat Tambah Stepper -->
                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200/60 lg:col-span-1">
                        <h4 class="font-bold text-corporate text-xs uppercase tracking-wider mb-3">Update Linimasa Cepat</h4>
                        <form action="{{ route('finance.tracking.add') }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Projek Klien</label>
                                <select name="order_id" required class="w-full text-xs px-3 py-2 rounded-xl border border-slate-200 bg-white">
                                    @foreach($orders as $ord)
                                        <option value="{{ $ord->id }}">{{ $ord->order_number }} - {{ $ord->company_name ?? $ord->client_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Tahapan (Milestone)</label>
                                <input type="text" name="stage_name" required placeholder="Cth: Registrasi Akun Kemenkes RI" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-200 bg-white">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan Progres</label>
                                <textarea name="description" rows="3" required placeholder="Detail kemajuan yang dapat dipantau oleh klien mitra..." class="w-full text-xs p-2.5 rounded-xl border border-slate-200 bg-white"></textarea>
                            </div>

                            <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                                Update Progres Linimasa
                            </button>
                        </form>
                    </div>

                    <!-- Riwayat Linimasa Terkini -->
                    <div class="lg:col-span-2 space-y-3">
                        <h4 class="font-bold text-corporate text-xs uppercase tracking-wider mb-2">Riwayat Update Linimasa Terkini</h4>
                        @forelse($projectTrackings as $track)
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold shrink-0 mt-0.5">
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h5 class="font-bold text-corporate text-xs">{{ $track->stage_name }}</h5>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ date('d M Y, H:i', strtotime($track->date_recorded)) }}</span>
                                    </div>
                                    <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">{{ $track->description }}</p>
                                    <p class="text-[10px] text-slate-400 mt-1 font-mono font-bold">{{ $track->order_number }} &bull; {{ $track->company_name ?? 'Mitra' }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-6 text-center">Belum ada linimasa yang dicatat.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- 4. Section C: Manajemen Pengarsipan Direktori Privat Server Internal -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm" id="arsip">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="folder-lock" class="w-6 h-6 text-emerald-600"></i>
                            <h3 class="text-lg font-bold text-corporate">Manajemen Direktori Privat Server (Internal Archives)</h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Penyimpanan terisolasi untuk sertifikat resmi, SK Kementerian, dan dokumen konfidensial perusahaan.</p>
                    </div>

                    <button onclick="document.getElementById('uploadArchiveModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-sm">
                        <i data-lucide="upload" class="w-4 h-4"></i> Simpan Dokumen ke Server
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($archives as $arc)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/60 flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-corporate text-emerald-400 flex items-center justify-center font-bold shrink-0">
                                    <i data-lucide="file-check" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full uppercase">{{ $arc->category }}</span>
                                    <h5 class="font-bold text-corporate text-xs mt-1">{{ $arc->title }}</h5>
                                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $arc->notes }}</p>
                                    <p class="text-[10px] text-slate-400 font-mono mt-1">Ukuran: {{ $arc->file_size ?? '2.1 MB' }} &bull; Pengunggah: {{ $arc->uploaded_by ?? 'Finance' }}</p>
                                </div>
                            </div>

                            <form action="{{ route('finance.archives.destroy', $arc->id) }}" method="POST" onsubmit="return confirm('Hapus arsip server ini?')" class="shrink-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-8 text-slate-400 text-sm">
                            Belum ada dokumen privat yang diarsipkan.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </main>

    <!-- Modal Tambah Tahapan Linimasa Stepper -->
    <div id="addTrackingModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 relative">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                        <i data-lucide="git-commit" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-corporate text-base">Tambah Linimasa Projek</h3>
                        <p class="text-xs text-slate-400">Stepper real-time untuk portal klien</p>
                    </div>
                </div>
                <button onclick="document.getElementById('addTrackingModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('finance.tracking.add') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Pesanan Projek</label>
                    <select name="order_id" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-blue-500">
                        @foreach($orders as $ord)
                            <option value="{{ $ord->id }}">{{ $ord->order_number }} - {{ $ord->company_name ?? $ord->client_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Tahapan (Milestone)</label>
                    <input type="text" name="stage_name" required placeholder="Cth: Audit Lapangan & Pengujian Lab" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Progres</label>
                    <textarea name="description" rows="3" required placeholder="Jelaskan kemajuan pengerjaan..." class="w-full text-xs p-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-blue-500"></textarea>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('addTrackingModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-500">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-md transition-all">
                        Simpan Milestone
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Upload Arsip Privat Server -->
    <div id="uploadArchiveModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 relative">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        <i data-lucide="server" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-corporate text-base">Arsipkan Dokumen Server</h3>
                        <p class="text-xs text-slate-400">Penyimpanan privat server terenkripsi</p>
                    </div>
                </div>
                <button onclick="document.getElementById('uploadArchiveModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('finance.archives.upload') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Dokumen Resmi</label>
                    <input type="text" name="title" required placeholder="Cth: SK Penetapan Izin Edar Alkes 2026" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Dokumen</label>
                    <select name="category" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-emerald-500">
                        <option value="Sertifikat Resmi">Sertifikat Resmi Terakreditasi</option>
                        <option value="Legalitas Perusahaan">Legalitas &amp; SK Perusahaan</option>
                        <option value="Kajian Regulasi">Kajian &amp; Notulensi Audit Teknis</option>
                        <option value="Berita Acara">Berita Acara Kajian (BAK)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Dokumen</label>
                    <textarea name="notes" rows="2" placeholder="Catatan keabsahan dokumen..." class="w-full text-xs p-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-emerald-500"></textarea>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('uploadArchiveModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-500">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition-all">
                        Simpan ke Server
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
