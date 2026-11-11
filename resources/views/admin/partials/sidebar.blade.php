@php
    $currentUser = Auth::user();
@endphp
<!-- Sidebar Admin -->
<aside class="w-72 bg-corporate text-slate-300 flex flex-col h-full shrink-0 shadow-2xl relative z-20 transition-transform duration-300 border-r border-slate-800">
    <!-- Logo PRASTA SOLUSI -->
    <a href="{{ route('admin.dashboard') }}" class="h-20 flex items-center px-6 border-b border-slate-800 gap-3 group">
        <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center shadow-inner flex-shrink-0 border border-slate-700 p-1 group-hover:border-sky-500/50 transition-colors">
            <img src="{{ asset('images/logo-symbol-transparent.png') }}" alt="Prasta Solusi" class="w-8 h-auto object-contain">
        </div>
        <div>
            <h1 class="font-extrabold text-white text-base leading-tight">PRASTA SOLUSI</h1>
            <p class="text-[10px] text-sky-400 font-semibold tracking-widest uppercase">Admin Portal &bull; RCMS</p>
        </div>
    </a>

    <!-- Menu Navigation -->
    <div class="flex-1 overflow-y-auto sidebar-scroll py-6 px-4">
        <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Navigasi Utama</p>
        
        <nav class="space-y-1">
            <!-- Dashboard Ringkasan -->
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg group transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-sky-600/10 text-sky-400 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-sky-400' : 'text-slate-400 group-hover:text-sky-400' }}"></i>
                <span class="font-medium text-sm">Dashboard</span>
            </a>

            <!-- Submenu Master Perizinan & Sertifikasi -->
            <p class="px-2 text-[11px] font-semibold text-slate-500 uppercase tracking-wider mt-6 mb-2">Master Layanan</p>
            
            <!-- Kategori Layanan -->
            <a href="{{ route('admin.categories.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg group transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-sky-600/10 text-sky-400 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="tags" class="w-5 h-5 {{ request()->routeIs('admin.categories.*') ? 'text-sky-400' : 'text-slate-400 group-hover:text-sky-400' }}"></i>
                <span class="font-medium text-sm">Kategori Layanan</span>
            </a>
            
            <!-- Master Layanan Perizinan & Sertifikasi -->
            <a href="{{ route('admin.services.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg group transition-colors {{ request()->routeIs('admin.services.*') ? 'bg-sky-600/10 text-sky-400 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="award" class="w-5 h-5 {{ request()->routeIs('admin.services.*') ? 'text-sky-400' : 'text-slate-400 group-hover:text-sky-400' }}"></i>
                <span class="font-medium text-sm">Layanan Perizinan</span>
            </a>

            <p class="px-2 text-[11px] font-semibold text-slate-500 uppercase tracking-wider mt-6 mb-2">Administrasi Staf</p>
            
            <!-- Manajemen Pengguna Staf -->
            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg group transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-sky-600/10 text-sky-400 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="user-cog" class="w-5 h-5 {{ request()->routeIs('admin.users.*') ? 'text-sky-400' : 'text-slate-400 group-hover:text-sky-400' }}"></i>
                <span class="font-medium text-sm">Manajemen Staf</span>
            </a>

            <p class="px-2 text-[11px] font-semibold text-slate-500 uppercase tracking-wider mt-6 mb-2">Operasional & Laporan</p>
            
            <!-- Data Klien -->
            <a href="{{ route('admin.dashboard') }}#klien" class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg group transition-colors">
                <i data-lucide="users" class="w-5 h-5"></i>
                <span class="font-medium text-sm">Data Klien</span>
            </a>
            
            <!-- Dokumen Persyaratan -->
            <a href="{{ route('admin.dashboard') }}#dokumen" class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg group transition-colors">
                <i data-lucide="folder-check" class="w-5 h-5"></i>
                <span class="font-medium text-sm">Dokumen Persyaratan</span>
            </a>
            
            <!-- Pelacakan Projek (Tracking) -->
            <a href="{{ route('admin.dashboard') }}#tracking" class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg group transition-colors flex justify-between">
                <div class="flex items-center gap-3">
                    <i data-lucide="git-merge" class="w-5 h-5"></i>
                    <span class="font-medium text-sm">Pelacakan Projek</span>
                </div>
                <span class="bg-sky-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">3 Aktif</span>
            </a>
            
            <!-- Transaksi Pembayaran -->
            <a href="{{ route('admin.dashboard') }}#transaksi" class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg group transition-colors">
                <i data-lucide="credit-card" class="w-5 h-5"></i>
                <span class="font-medium text-sm">Transaksi Pembayaran</span>
            </a>
            
            <!-- Ekspor Laporan PDF & Excel -->
            <a href="{{ route('admin.reports.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg group transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-sky-600/10 text-sky-400 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="file-bar-chart" class="w-5 h-5 {{ request()->routeIs('admin.reports.*') ? 'text-sky-400' : 'text-slate-400 group-hover:text-sky-400' }}"></i>
                <span class="font-medium text-sm">Ekspor Laporan</span>
            </a>
        </nav>
    </div>

    <!-- User Profile (Bottom) -->
    <div class="p-4 border-t border-slate-800">
        <div class="flex items-center gap-3 p-2 rounded-lg bg-slate-800/50 border border-slate-700">
            <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center text-sky-400 font-bold shrink-0">
                {{ strtoupper(substr($currentUser->name ?? 'A', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white truncate">{{ $currentUser->name ?? 'Admin Prasta' }}</p>
                <p class="text-[11px] text-sky-400 truncate">{{ $currentUser->getRoleLabel() }}</p>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" title="Keluar / Logout" class="text-slate-400 hover:text-rose-400 p-1.5 transition-colors">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>
</aside>
