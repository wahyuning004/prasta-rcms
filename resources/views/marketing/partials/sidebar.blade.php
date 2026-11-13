<!-- Dark Sidebar -->
<aside class="w-72 bg-corporate text-slate-300 flex flex-col h-full shrink-0 shadow-2xl relative z-20 border-r border-slate-800">
    <!-- Logo Header (PRASTA SOLUSI INDONESIA) -->
    <div class="h-20 flex items-center px-6 border-b border-slate-800 gap-3">
        <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center shadow-inner flex-shrink-0 border border-slate-700 p-1">
            <img src="{{ asset('images/logo-symbol-transparent.png') }}" alt="Prasta Solusi" class="w-8 h-auto object-contain">
        </div>
        <div>
            <h1 class="font-extrabold text-white text-base leading-tight">PRASTA SOLUSI</h1>
            <p class="text-[10px] text-sky-400 font-semibold tracking-widest uppercase">Client Relations Panel</p>
        </div>
    </div>

    <!-- Menu Navigation -->
    <div class="flex-1 overflow-y-auto sidebar-scroll py-6 px-4">
        <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Divisi Pemasaran</p>

        <nav class="space-y-1.5">
            <a href="{{ route('marketing.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl group transition-all font-medium text-sm {{ request()->routeIs('marketing.dashboard') ? 'bg-indigo-600/15 text-indigo-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span>Dashboard Marketing</span>
            </a>

            <a href="{{ route('marketing.tickets') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl group transition-all font-medium text-sm {{ request()->routeIs('marketing.tickets') ? 'bg-indigo-600/15 text-indigo-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="message-square-text" class="w-5 h-5"></i>
                    <span>Tiket &amp; Live Chat</span>
                </div>
                @if($stats['pendingConsultations'] > 0)
                    <span class="bg-indigo-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $stats['pendingConsultations'] }} Baru</span>
                @endif
            </a>

            <a href="{{ route('marketing.promos.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl group transition-all font-medium text-sm {{ request()->routeIs('marketing.promos.*') ? 'bg-indigo-600/15 text-indigo-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="tag" class="w-5 h-5"></i>
                    <span>Kupon &amp; Kuota Promo</span>
                </div>
                <span class="bg-slate-800 text-indigo-300 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $stats['activePromos'] }} Aktif</span>
            </a>

            <a href="{{ route('marketing.reviews.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl group transition-all font-medium text-sm {{ request()->routeIs('marketing.reviews.*') ? 'bg-indigo-600/15 text-indigo-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="star" class="w-5 h-5"></i>
                    <span>Moderasi Ulasan Klien</span>
                </div>
                <span class="bg-amber-500/20 text-amber-300 text-[10px] font-bold px-2 py-0.5 rounded-full">★ {{ number_format((float) $stats['avgRating'], 1) }}</span>
            </a>

            <a href="{{ route('marketing.profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl group transition-all font-medium text-sm {{ request()->routeIs('marketing.profile.*') ? 'bg-indigo-600/15 text-indigo-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="user-circle" class="w-5 h-5"></i>
                <span>Profil</span>
            </a>
        </nav>
    </div>

    <!-- User Profile (Bottom) -->
    <div class="p-4 border-t border-slate-800">
        <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-800/60 border border-slate-700/80">
            <a href="{{ route('marketing.profile.edit') }}" class="flex items-center gap-3 flex-1 min-w-0 group">
                <div class="w-10 h-10 rounded-full bg-indigo-900/60 text-indigo-300 border border-indigo-500/30 flex items-center justify-center font-bold shrink-0">
                    {{ strtoupper(substr($user->name ?? 'M', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate group-hover:text-indigo-300 transition-colors">{{ $user->name }}</p>
                    <p class="text-[11px] text-indigo-400 truncate flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-400 inline-block"></span>
                        Marketing &amp; CR
                    </p>
                </div>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" title="Keluar / Logout" class="text-slate-400 hover:text-rose-400 p-1.5 rounded-lg hover:bg-slate-700 transition-colors">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

