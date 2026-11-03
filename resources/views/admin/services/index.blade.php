<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Layanan Perizinan & Sertifikasi - IzinYou Admin</title>
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
                    <span class="text-emerald-600 font-semibold">Master Layanan</span>
                </div>
                <h2 class="text-xl font-bold text-corporate">Master Layanan Perizinan &amp; Sertifikasi</h2>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.services.create') }}" class="flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm rounded-xl shadow-md shadow-emerald-600/20 transition-all">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    Tambah Layanan
                </a>
            </div>
        </header>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8">

            {{-- Alert Success --}}
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl shadow-sm text-sm font-medium">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            {{-- Alert Error --}}
            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl shadow-sm text-sm font-medium">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            <!-- Filter & Search Toolbar -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm mb-6 flex flex-col lg:flex-row items-center justify-between gap-4">
                <form action="{{ route('admin.services.index') }}" method="GET" class="w-full flex-1 flex flex-col sm:flex-row items-center gap-3">
                    
                    <!-- Search Input -->
                    <div class="relative flex-1 w-full sm:max-w-md">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 transform -translate-y-1/2"></i>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="Cari nama layanan atau persyaratan..." 
                            class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition-all"
                        >
                    </div>

                    <!-- Category Filter Dropdown -->
                    <div class="w-full sm:w-60">
                        <select name="category_id" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition-all">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Action -->
                    <div class="flex items-center gap-2 self-start sm:self-auto">
                        <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold rounded-xl transition-colors">
                            Terapkan
                        </button>
                        @if(request('search') || request('category_id'))
                            <a href="{{ route('admin.services.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>

                <div class="text-xs text-slate-500 font-medium shrink-0">
                    Total Layanan: <strong class="text-corporate">{{ $services->total() }}</strong>
                </div>
            </div>

            <!-- Services Data Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th scope="col" class="py-4 px-6 w-16">No</th>
                                <th scope="col" class="py-4 px-6">Nama Layanan</th>
                                <th scope="col" class="py-4 px-6">Kategori</th>
                                <th scope="col" class="py-4 px-6">Estimasi Waktu</th>
                                <th scope="col" class="py-4 px-6">Estimasi Biaya</th>
                                <th scope="col" class="py-4 px-6">Dokumen Persyaratan</th>
                                <th scope="col" class="py-4 px-6 text-right w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($services as $index => $srv)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6 font-semibold text-slate-400">
                                    {{ $services->firstItem() + $index }}
                                </td>
                                <td class="py-4 px-6 max-w-xs">
                                    <div class="font-bold text-corporate text-sm leading-snug">{{ $srv->name }}</div>
                                    <div class="font-mono text-[11px] text-slate-400 mt-0.5 truncate">{{ $srv->slug }}</div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                        <i data-lucide="tag" class="w-3 h-3"></i>
                                        {{ $srv->category ? $srv->category->name : 'Umum' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-700">
                                        <i data-lucide="clock" class="w-3.5 h-3.5 text-emerald-600"></i>
                                        {{ $srv->timeline_estimation ?: ($srv->estimation_time ?: '-') }}
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap font-bold text-emerald-700 text-sm">
                                    {{ $srv->formatted_price }}
                                </td>
                                <td class="py-4 px-6 max-w-xs">
                                    <div class="text-xs text-slate-500 line-clamp-2" title="{{ $srv->requirements_info ?: $srv->requirements_summary }}">
                                        {{ $srv->requirements_info ?: ($srv->requirements_summary ?: 'Belum ada rincian dokumen.') }}
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-right space-x-2 whitespace-nowrap">
                                    <a href="{{ route('admin.services.edit', $srv->id) }}" class="inline-flex items-center p-2 rounded-lg text-slate-600 hover:text-emerald-600 hover:bg-emerald-50 transition-colors" title="Edit Layanan">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.services.destroy', $srv->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan {{ $srv->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center p-2 rounded-lg text-slate-600 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus Layanan">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i data-lucide="award" class="w-12 h-12 text-slate-300 mb-3"></i>
                                        <p class="font-medium text-slate-600">Belum ada layanan perizinan yang ditemukan.</p>
                                        <p class="text-xs text-slate-400 mt-1">Gunakan tombol "Tambah Layanan" untuk mendaftarkan layanan baru.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($services->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $services->links() }}
                </div>
                @endif
            </div>

        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
