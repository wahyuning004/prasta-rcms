<!-- Dark Sidebar Finance & Administration -->
<aside class="w-72 bg-corporate text-slate-300 flex flex-col h-full shrink-0 shadow-2xl relative z-20 border-r border-slate-800">
    <!-- Logo Header (PRASTA SOLUSI INDONESIA) -->
    <div class="h-20 flex items-center px-6 border-b border-slate-800 gap-3">
        <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center shadow-inner flex-shrink-0 border border-slate-700 p-1">
            <img src="{{ asset('images/logo-symbol-transparent.png') }}" alt="Prasta Solusi" class="w-8 h-auto object-contain">
        </div>
        <div>
            <h1 class="font-extrabold text-white text-base leading-tight">PRASTA SOLUSI</h1>
            <p class="text-[10px] text-amber-400 font-semibold tracking-widest uppercase">Finance &amp; Administration</p>
        </div>
    </div>

    <!-- Menu Navigation -->
    <div class="flex-1 overflow-y-auto sidebar-scroll py-6 px-4">
        <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Dasbor &amp; Keuangan</p>

        <nav class="space-y-1.5">
            <a href="{{ route('finance.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl group transition-all font-medium text-sm {{ request()->routeIs('finance.dashboard') ? 'bg-amber-600/15 text-amber-400 font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span>Dasbor Keuangan</span>
            </a>

            <a href="{{ route('finance.payments') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl group transition-all font-medium text-sm {{ request()->routeIs('finance.payments*') ? 'bg-amber-600/15 text-amber-400 font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                    <span>Verifikasi Pembayaran</span>
                </div>
                @if(isset($stats['pendingPayments']) && $stats['pendingPayments'] > 0)
                    <span class="bg-amber-500 text-slate-900 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $stats['pendingPayments'] }} Baru</span>
                @endif
            </a>

            <a href="{{ route('finance.invoices') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl group transition-all font-medium text-sm {{ request()->routeIs('finance.invoices*') ? 'bg-amber-600/15 text-amber-400 font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                    <span>Tagihan &amp; Invoice</span>
                </div>
                @if(isset($stats['unpaidInvoices']) && $stats['unpaidInvoices'] > 0)
                    <span class="bg-blue-500/20 text-blue-300 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $stats['unpaidInvoices'] }} Unpaid</span>
                @endif
            </a>

            <a href="{{ route('finance.transactions') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl group transition-all font-medium text-sm {{ request()->routeIs('finance.transactions*') ? 'bg-amber-600/15 text-amber-400 font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="receipt" class="w-5 h-5"></i>
                    <span>Transaksi &amp; Status Klien</span>
                </div>
            </a>

            <a href="{{ route('finance.reports') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl group transition-all font-medium text-sm {{ request()->routeIs('finance.reports*') ? 'bg-amber-600/15 text-amber-400 font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                    <span>Laporan &amp; Arus Kas</span>
                </div>
                <span class="bg-emerald-500/20 text-emerald-300 text-[10px] font-bold px-2 py-0.5 rounded-full">Cash Flow</span>
            </a>

            <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider pt-5 pb-2">Administrasi &amp; Operasional</p>

            <a href="{{ route('finance.tracking') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl group transition-all font-medium text-sm {{ request()->routeIs('finance.tracking*') ? 'bg-amber-600/15 text-amber-400 font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="git-merge" class="w-5 h-5"></i>
                    <span>Linimasa Stepper</span>
                </div>
                @if(isset($orders))
                    <span class="bg-blue-500/20 text-blue-300 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ is_countable($orders) ? count($orders) : $orders->count() }} Projek</span>
                @endif
            </a>

            <a href="{{ route('finance.archives') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl group transition-all font-medium text-sm {{ request()->routeIs('finance.archives*') ? 'bg-amber-600/15 text-amber-400 font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="server" class="w-5 h-5"></i>
                    <span>Arsip Privat Server</span>
                </div>
                @if(isset($stats['totalArchives']))
                    <span class="bg-slate-800 text-slate-300 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $stats['totalArchives'] }} File</span>
                @endif
            </a>

            <a href="{{ route('finance.profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl group transition-all font-medium text-sm {{ request()->routeIs('finance.profile.*') ? 'bg-amber-600/15 text-amber-400 font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="user-cog" class="w-5 h-5"></i>
                <span>Profil Staf Keuangan</span>
            </a>
        </nav>
    </div>

    <!-- User Profile (Bottom) -->
    <div class="p-4 border-t border-slate-800">
        <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-800/60 border border-slate-700/80">
            <a href="{{ route('finance.profile.edit') }}" class="flex items-center gap-3 flex-1 min-w-0 group">
                <div class="w-10 h-10 rounded-full bg-amber-900/60 text-amber-300 border border-amber-500/30 flex items-center justify-center font-bold shrink-0">
                    {{ strtoupper(substr($user->name ?? 'F', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate group-hover:text-amber-300 transition-colors">{{ $user->name }}</p>
                    <p class="text-[11px] text-amber-400 truncate flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 inline-block"></span>
                        Finance &amp; Admin
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

