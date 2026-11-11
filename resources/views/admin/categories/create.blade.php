<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori Layanan - IzinYou Admin</title>
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
                    <a href="{{ route('admin.categories.index') }}" class="hover:text-corporate">Kategori Layanan</a>
                    <span>/</span>
                    <span class="text-sky-600 font-semibold">Tambah Baru</span>
                </div>
                <h2 class="text-xl font-bold text-corporate">Tambah Kategori Layanan Baru</h2>
            </div>
            
            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Daftar
            </a>
        </header>

        <!-- Scrollable Form Content -->
        <div class="flex-1 overflow-y-auto p-8">
            <div class="max-w-2xl bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
                
                {{-- Error Alert --}}
                @if($errors->any())
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
                        <p class="font-bold mb-1">Terdapat kesalahan pengisian:</p>
                        <ul class="list-disc list-inside text-xs space-y-0.5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Nama Kategori -->
                    <div>
                        <label for="name" class="block text-sm font-bold text-slate-700 mb-2">
                            Nama Kategori Layanan <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            required 
                            value="{{ old('name') }}"
                            placeholder="Cth: Alat Kesehatan (Alkes)"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-sky-500 focus:bg-white transition-all @error('name') border-rose-400 bg-rose-50 @enderror"
                        >
                        <p class="text-[11px] text-slate-400 mt-1">Nama kelompok bidang perizinan atau sertifikasi.</p>
                    </div>

                    <!-- Slug URL (Opsional) -->
                    <div>
                        <label for="slug" class="block text-sm font-bold text-slate-700 mb-2">
                            Slug URL <span class="text-xs font-normal text-slate-400">(Opsional / Terisi Otomatis)</span>
                        </label>
                        <input 
                            type="text" 
                            name="slug" 
                            id="slug" 
                            value="{{ old('slug') }}"
                            placeholder="Cth: alat-kesehatan"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:outline-none focus:border-sky-500 focus:bg-white transition-all @error('slug') border-rose-400 bg-rose-50 @enderror"
                        >
                        <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk rute URL ramah SEO. Jika dikosongkan, akan dibuat otomatis dari nama kategori.</p>
                    </div>

                    <!-- Deskripsi Kategori -->
                    <div>
                        <label for="description" class="block text-sm font-bold text-slate-700 mb-2">
                            Deskripsi Kategori <span class="text-xs font-normal text-slate-400">(Opsional)</span>
                        </label>
                        <textarea 
                            name="description" 
                            id="description" 
                            rows="4" 
                            placeholder="Jelaskan cakupan perizinan dan sertifikasi pada kategori ini..."
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-sky-500 focus:bg-white transition-all"
                        >{{ old('description') }}</textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.categories.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold text-sm hover:bg-slate-50 transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-sky-600 hover:bg-sky-500 text-white font-bold text-sm rounded-xl shadow-md shadow-sky-600/20 transition-all flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Simpan Kategori
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();

        // Auto slug helper
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');

        nameInput.addEventListener('input', () => {
            if (!slugInput.dataset.touched) {
                slugInput.value = nameInput.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
        });

        slugInput.addEventListener('input', () => {
            slugInput.dataset.touched = 'true';
        });
    </script>
</body>
</html>
