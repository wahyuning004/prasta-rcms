<!-- Dark Sidebar Document & Licensing Officer -->
<aside class="w-72 bg-corporate text-slate-300 flex flex-col h-full shrink-0 shadow-2xl relative z-20 border-r border-slate-800">
    <!-- Logo Header (PRASTA SOLUSI INDONESIA) -->
    <div class="h-20 flex items-center px-6 border-b border-slate-800 gap-3">
        <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center shadow-inner flex-shrink-0 border border-slate-700 p-1">
            <img src="{{ asset('images/logo-symbol-transparent.png') }}" alt="Prasta Solusi" class="w-8 h-auto object-contain">
        </div>
        <div>
            <h1 class="font-extrabold text-white text-base leading-tight">PRASTA SOLUSI</h1>
            <p class="text-[10px] text-teal-400 font-semibold tracking-widest uppercase">Document &amp; Licensing</p>
        </div>
    </div>

    <!-- Menu Navigation -->
    <div class="flex-1 overflow-y-auto sidebar-scroll py-6 px-4">
        <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Operasional Perizinan</p>

        <nav class="space-y-1.5">
            <a href="{{ route('officer.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl group transition-all font-medium text-sm {{ request()->routeIs('officer.dashboard') ? 'bg-teal-600/15 text-teal-400 font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span>Dasbor Operasional</span>
            </a>

            <a href="{{ route('officer.documents') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl group transition-all font-medium text-sm {{ request()->routeIs('officer.documents*') ? 'bg-teal-600/15 text-teal-400 font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="file-check-2" class="w-5 h-5"></i>
                    <span>Antrean Dokumen</span>
                </div>
                @if(isset($stats['pendingDocs']) && $stats['pendingDocs'] > 0)
                    <span class="bg-amber-500 text-slate-900 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $stats['pendingDocs'] }} Periksa</span>
                @endif
            </a>

            <a href="{{ route('officer.projects') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl group transition-all font-medium text-sm {{ request()->routeIs('officer.projects*') ? 'bg-teal-600/15 text-teal-400 font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="timer" class="w-5 h-5"></i>
                    <span>Tenggat Waktu Proyek</span>
                </div>
                @php
                    $criticalCount = ($stats['criticalProjects'] ?? 0) + ($stats['overdueProjects'] ?? 0);
                @endphp
                @if($criticalCount > 0)
                    <span class="bg-rose-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $criticalCount }} SLA Kritis</span>
                @elseif(isset($stats['activeProjects']) && $stats['activeProjects'] > 0)
                    <span class="bg-teal-500/20 text-teal-300 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $stats['activeProjects'] }} Aktif</span>
                @endif
            </a>

            <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider pt-6 pb-2">Akun Staf</p>

            <a href="{{ route('officer.profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl group transition-all font-medium text-sm {{ request()->routeIs('officer.profile.*') ? 'bg-teal-600/15 text-teal-400 font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="user-cog" class="w-5 h-5"></i>
                <span>Profil Staf Officer</span>
            </a>
        </nav>
    </div>

    <!-- User Profile (Bottom) -->
    <div class="p-4 border-t border-slate-800">
        <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-800/60 border border-slate-700/80">
            <a href="{{ route('officer.profile.edit') }}" class="flex items-center gap-3 flex-1 min-w-0 group">
                <div class="w-10 h-10 rounded-full bg-teal-900/60 text-teal-300 border border-teal-500/30 flex items-center justify-center font-bold shrink-0">
                    {{ strtoupper(substr($user->name ?? 'O', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate group-hover:text-teal-300 transition-colors">{{ $user->name }}</p>
                    <p class="text-[11px] text-teal-400 truncate flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-teal-400 inline-block"></span>
                        Licensing Officer
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

