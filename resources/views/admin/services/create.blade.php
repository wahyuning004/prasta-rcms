<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Layanan Perizinan & Sertifikasi - IzinYou Admin</title>
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
                    <a href="{{ route('admin.services.index') }}" class="hover:text-corporate">Layanan Perizinan</a>
                    <span>/</span>
                    <span class="text-emerald-600 font-semibold">Tambah Baru</span>
                </div>
                <h2 class="text-xl font-bold text-corporate">Tambah Layanan Perizinan &amp; Sertifikasi Baru</h2>
            </div>
            
            <a href="{{ route('admin.services.index') }}" class="flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Daftar
            </a>
        </header>

        <!-- Scrollable Form Content -->
        <div class="flex-1 overflow-y-auto p-8">
            <div class="max-w-3xl bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
                
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

                <form action="{{ route('admin.services.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kategori Layanan -->
                        <div>
                            <label for="category_id" class="block text-sm font-bold text-slate-700 mb-2">
                                Kategori Layanan <span class="text-rose-500">*</span>
                            </label>
                            <select 
                                name="category_id" 
                                id="category_id" 
                                required
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition-all @error('category_id') border-rose-400 bg-rose-50 @enderror"
                            >
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Estimasi Waktu -->
                        <div>
                            <label for="timeline_estimation" class="block text-sm font-bold text-slate-700 mb-2">
                                Estimasi Waktu Proses
                            </label>
                            <input 
                                type="text" 
                                name="timeline_estimation" 
                                id="timeline_estimation" 
                                value="{{ old('timeline_estimation') }}"
                                placeholder="Cth: 14 Hari Kerja / 1-2 Bulan"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition-all"
                            >
                        </div>
                    </div>

                    <!-- Nama Layanan -->
                    <div>
                        <label for="name" class="block text-sm font-bold text-slate-700 mb-2">
                            Nama Layanan Perizinan / Sertifikasi <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            required 
                            value="{{ old('name') }}"
                            placeholder="Cth: Pengurusan Izin Edar Alat Kesehatan Kemenkes"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition-all @error('name') border-rose-400 bg-rose-50 @enderror"
                        >
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Slug URL -->
                        <div>
                            <label for="slug" class="block text-sm font-bold text-slate-700 mb-2">
                                Slug URL <span class="text-xs font-normal text-slate-400">(Opsional)</span>
                            </label>
                            <input 
                                type="text" 
                                name="slug" 
                                id="slug" 
                                value="{{ old('slug') }}"
                                placeholder="izin-edar-alkes-kemenkes"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:outline-none focus:border-emerald-500 focus:bg-white transition-all"
                            >
                        </div>

                        <!-- Biaya Layanan -->
                        <div>
                            <label for="price" class="block text-sm font-bold text-slate-700 mb-2">
                                Estimasi Biaya (Rp) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-sm font-bold text-slate-400">Rp</span>
                                <input 
                                    type="number" 
                                    name="price" 
                                    id="price" 
                                    step="1000"
                                    min="0"
                                    required 
                                    value="{{ old('price', 0) }}"
                                    placeholder="3500000"
                                    class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-emerald-700 focus:outline-none focus:border-emerald-500 focus:bg-white transition-all @error('price') border-rose-400 bg-rose-50 @enderror"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen Persyaratan -->
                    <div>
                        <label for="requirements_info" class="block text-sm font-bold text-slate-700 mb-2 flex items-center justify-between">
                            <span>Informasi Dokumen Persyaratan</span>
                            <span class="text-xs text-slate-400 font-normal">Format poin / rincian berkas</span>
                        </label>
                        <textarea 
                            name="requirements_info" 
                            id="requirements_info" 
                            rows="4" 
                            placeholder="Cth: NIB & NPWP, Akta Perusahaan, Surat Penunjukan (LoA), Sertifikat Standar Mutu, Ijazah Penanggung Jawab Teknis..."
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition-all"
                        >{{ old('requirements_info') }}</textarea>
                    </div>

                    <!-- Deskripsi Layanan -->
                    <div>
                        <label for="description" class="block text-sm font-bold text-slate-700 mb-2">
                            Deskripsi Lengkap Layanan <span class="text-xs font-normal text-slate-400">(Opsional)</span>
                        </label>
                        <textarea 
                            name="description" 
                            id="description" 
                            rows="4" 
                            placeholder="Uraikan alur pengerjaan dan pendampingan izin/sertifikasi yang disediakan..."
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition-all"
                        >{{ old('description') }}</textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.services.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold text-sm hover:bg-slate-50 transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm rounded-xl shadow-md shadow-emerald-600/20 transition-all flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Simpan Layanan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();

        // Auto slug generator
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
