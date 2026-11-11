<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Kategori Layanan - IzinYou Admin</title>
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
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-sky-500 selection:text-white flex h-screen overflow-hidden">

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
                    <span class="text-sky-600 font-semibold">Kategori Layanan</span>
                </div>
                <h2 class="text-xl font-bold text-corporate">Master Kategori Layanan Perizinan &amp; Sertifikasi</h2>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.categories.create') }}" class="flex items-center gap-2 px-4 py-2.5 bg-sky-600 hover:bg-sky-500 text-white font-semibold text-sm rounded-xl shadow-md shadow-sky-600/20 transition-all">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    Tambah Kategori
                </a>
            </div>
        </header>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8">

            {{-- Alert Success --}}
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-sky-50 border border-sky-200 text-sky-800 px-5 py-4 rounded-2xl shadow-sm text-sm font-medium">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-sky-600 shrink-0"></i>
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
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
                <form action="{{ route('admin.categories.index') }}" method="GET" class="w-full md:w-auto flex-1 flex items-center gap-3">
                    <div class="relative flex-1 max-w-md">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 transform -translate-y-1/2"></i>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="Cari nama kategori atau deskripsi..." 
                            class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 focus:bg-white transition-all"
                        >
                    </div>
                    <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold rounded-xl transition-colors">
                        Filter
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.categories.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800">
                            Reset
                        </a>
                    @endif
                </form>

                <div class="text-xs text-slate-500 font-medium">
                    Total Kategori: <strong class="text-corporate">{{ $categories->total() }}</strong>
                </div>
            </div>

            <!-- Categories Data Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th scope="col" class="py-4 px-6 w-16">No</th>
                                <th scope="col" class="py-4 px-6">Nama Kategori</th>
                                <th scope="col" class="py-4 px-6">Slug URL</th>
                                <th scope="col" class="py-4 px-6">Deskripsi Kategori</th>
                                <th scope="col" class="py-4 px-6 text-center">Jumlah Layanan</th>
                                <th scope="col" class="py-4 px-6 text-right w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($categories as $index => $cat)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6 font-semibold text-slate-400">
                                    {{ $categories->firstItem() + $index }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-bold text-corporate text-sm">{{ $cat->name }}</div>
                                </td>
                                <td class="py-4 px-6 font-mono text-xs text-slate-500">
                                    <span class="bg-slate-100 px-2 py-1 rounded-md">{{ $cat->slug }}</span>
                                </td>
                                <td class="py-4 px-6 text-xs text-slate-500 max-w-xs truncate">
                                    {{ $cat->description ?: '-' }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-100">
                                        <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                                        {{ $cat->services_count ?? 0 }} Layanan
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right space-x-2 whitespace-nowrap">
                                    <a href="{{ route('admin.categories.edit', $cat->id) }}" class="inline-flex items-center p-2 rounded-lg text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition-colors" title="Edit Kategori">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $cat->name }}? Seluruh layanan dalam kategori ini juga akan terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center p-2 rounded-lg text-slate-600 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus Kategori">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i data-lucide="tags" class="w-12 h-12 text-slate-300 mb-3"></i>
                                        <p class="font-medium text-slate-600">Belum ada kategori layanan.</p>
                                        <p class="text-xs text-slate-400 mt-1">Silakan klik tombol "Tambah Kategori" untuk membuat kategori perizinan baru.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($categories->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $categories->links() }}
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
