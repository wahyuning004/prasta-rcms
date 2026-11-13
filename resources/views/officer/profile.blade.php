<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Document &amp; Licensing Officer - IzinYou</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon-192.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .text-corporate { color: #0f172a; }
        .bg-corporate { background-color: #0f172a; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-teal-600 selection:text-white flex h-screen overflow-hidden">

    <!-- Dark Sidebar Officer -->
    @include('officer.partials.sidebar')

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/60">
        
        <!-- Header -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0 relative z-10">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-corporate">Profil &amp; Keamanan Akun Officer</h2>
                    <span class="bg-teal-100 text-teal-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full">Akun Internal</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Kelola informasi identitas staf Document &amp; Licensing Officer dan kredensial keamanan login.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('officer.dashboard') }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold flex items-center gap-1.5 transition">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Kembali ke Dasbor</span>
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        @include('officer.partials.alerts')

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8 space-y-8 sidebar-scroll max-w-5xl mx-auto w-full">

            <!-- Card Profil Staf Header -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-center gap-6">
                <div class="w-20 h-20 rounded-2xl bg-teal-900/80 text-teal-300 border-2 border-teal-500/30 flex items-center justify-center font-extrabold text-2xl shadow-inner shrink-0">
                    {{ strtoupper(substr($user->name ?? 'O', 0, 1)) }}
                </div>
                <div class="flex-1 text-center sm:text-left">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <h3 class="text-lg font-bold text-corporate">{{ $user->name }}</h3>
                        <span class="bg-teal-50 text-teal-700 text-xs font-bold px-2.5 py-0.5 rounded-full border border-teal-200">
                            {{ $user->getRoleLabel() }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">{{ $user->email }} &bull; {{ $user->phone ?? 'Belum ada nomor HP' }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $user->company_name ?? 'CV Prasta Solusi Indonesia' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Form 1: Ubah Data Profil -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                        <i data-lucide="user" class="w-5 h-5 text-teal-600"></i>
                        <h4 class="font-bold text-corporate text-sm">Informasi Data Akun</h4>
                    </div>

                    <form action="{{ route('officer.profile.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Alamat Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>

                        <div>
                            <label for="pic_position" class="block text-xs font-bold text-slate-700 mb-1">Jabatan / Posisi</label>
                            <input type="text" id="pic_position" name="pic_position" value="{{ old('pic_position', $user->pic_position ?? 'Document & Licensing Officer') }}" class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>

                        <div>
                            <label for="company_name" class="block text-xs font-bold text-slate-700 mb-1">Nama Perusahaan / Divisi</label>
                            <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $user->company_name ?? 'CV Prasta Solusi Indonesia') }}" class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>

                        <div>
                            <label for="address" class="block text-xs font-bold text-slate-700 mb-1">Alamat Kantor</label>
                            <textarea id="address" name="address" rows="2" class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-teal-500">{{ old('address', $user->address) }}</textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                Simpan Perubahan Profil
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Form 2: Ubah Kata Sandi -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                        <i data-lucide="key-round" class="w-5 h-5 text-teal-600"></i>
                        <h4 class="font-bold text-corporate text-sm">Keamanan &amp; Kata Sandi</h4>
                    </div>

                    <form action="{{ route('officer.profile.password') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="current_password" class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi Saat Ini</label>
                            <input type="password" id="current_password" name="current_password" required placeholder="Masukkan kata sandi saat ini" class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>

                        <div>
                            <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi Baru</label>
                            <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter kombinasi huruf & angka" class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi kata sandi baru" class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>

                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/60 text-[11px] text-slate-500 space-y-1">
                            <p class="font-bold text-slate-700">Persyaratan Keamanan:</p>
                            <ul class="list-disc list-inside space-y-0.5">
                                <li>Panjang minimal 8 karakter</li>
                                <li>Wajib memuat kombinasi huruf dan angka</li>
                            </ul>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                Perbarui Kata Sandi
                            </button>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>

