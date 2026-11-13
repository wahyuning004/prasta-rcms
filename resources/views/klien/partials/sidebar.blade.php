@php
    $sidebarUser = Auth::user();
    $ordersTotal = \App\Models\Order::where('user_id', $sidebarUser->id)->count();
    $pendingPayCount = \App\Models\Order::where('user_id', $sidebarUser->id)->where('status', 'pending')->count();
@endphp

<!-- Mobile Backdrop -->
<div id="klienSidebarBackdrop" onclick="toggleKlienSidebar()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-30 hidden lg:hidden"></div>

<!-- Sidebar Klien -->
<aside id="klienSidebar" class="fixed inset-y-0 left-0 z-40 w-72 bg-corporate text-slate-300 flex flex-col h-full shrink-0 shadow-2xl border-r border-slate-800 transform -translate-x-full lg:translate-x-0 lg:static transition-transform duration-200">
    <!-- Logo Header -->
    <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center shadow-inner flex-shrink-0 border border-slate-700 p-1">
                <img src="{{ asset('images/logo-symbol-transparent.png') }}" alt="Prasta Solusi" class="w-8 h-auto object-contain">
            </div>
            <div>
                <h1 class="font-extrabold text-white text-base leading-tight">PRASTA SOLUSI</h1>
                <p class="text-[10px] text-sky-400 font-semibold tracking-widest uppercase">Klien Portal &bull; Mitra</p>
            </div>
        </a>
        <button type="button" onclick="toggleKlienSidebar()" class="lg:hidden text-slate-400 hover:text-white p-1">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>

    <!-- Menu Navigation -->
    <div class="flex-1 overflow-y-auto sidebar-scroll py-6 px-4">
        <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Menu Pelanggan</p>

        <nav class="space-y-1.5">
            <a href="{{ route('klien.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl group transition-all font-medium text-sm {{ request()->routeIs('klien.dashboard') ? 'bg-sky-600/15 text-sky-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span>Dashboard Saya</span>
            </a>

            <a href="{{ route('klien.orders.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl group transition-all font-medium text-sm {{ request()->routeIs('klien.orders.*') ? 'bg-sky-600/15 text-sky-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="folder-kanban" class="w-5 h-5"></i>
                    <span>Pesanan &amp; Pembayaran</span>
                </div>
                @if($ordersTotal > 0)
                    <span class="bg-sky-500/20 text-sky-300 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $ordersTotal }}</span>
                @endif
            </a>

            <a href="{{ route('klien.services.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl group transition-all font-medium text-sm {{ request()->routeIs('klien.services.*') ? 'bg-sky-600/15 text-sky-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="layers" class="w-5 h-5"></i>
                <span>Katalog Layanan</span>
            </a>

            <button type="button" onclick="openHelpCenterModal()" class="w-full flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl group transition-all font-medium text-sm text-left">
                <i data-lucide="help-circle" class="w-5 h-5"></i>
                <span>Pusat Bantuan</span>
            </button>

            <a href="{{ route('klien.profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl group transition-all font-medium text-sm {{ request()->routeIs('klien.profile.*') ? 'bg-sky-600/15 text-sky-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="user" class="w-5 h-5"></i>
                <span>Profil</span>
            </a>
        </nav>
    </div>

    <!-- User Profile (Bottom) -->
    <div class="p-4 border-t border-slate-800">
        <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-800/60 border border-slate-700/80">
            <div class="w-10 h-10 rounded-full bg-amber-900/50 text-amber-300 border border-amber-500/30 flex items-center justify-center font-bold shrink-0">
                {{ strtoupper(substr($sidebarUser->name ?? 'K', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white truncate">{{ $sidebarUser->name }}</p>
                <p class="text-[11px] text-amber-400 truncate">{{ $sidebarUser->company_name ?? 'Klien Perusahaan' }}</p>
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

@include('klien.partials.help-center-modal')

<script>
    function toggleKlienSidebar() {
        const sidebar = document.getElementById('klienSidebar');
        const backdrop = document.getElementById('klienSidebarBackdrop');
        if (sidebar && backdrop) {
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }
    }
</script>

