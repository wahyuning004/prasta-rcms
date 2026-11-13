<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Staf Keuangan - Finance IzinYou</title>
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
        .text-gold { color: #d4af37; }
        .bg-gold { background-color: #d4af37; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-amber-600 selection:text-white flex h-screen overflow-hidden">

    <!-- Dark Sidebar -->
    @include('finance.partials.sidebar')

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/60">
        
        <!-- Header -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0 relative z-10">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-corporate">Profil Staf Finance &amp; Administration</h2>
                    <span class="bg-amber-100 text-amber-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full">Kelola Akun</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Kelola identitas resmi staf keuangan, kontak WhatsApp, dan keamanan kata sandi akun.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('finance.dashboard') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Kembali ke Dasbor</span>
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        @include('finance.partials.alerts')

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8 pt-4 space-y-6">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Column: Identitas Akun & Status Keamanan -->
                <div class="space-y-6 lg:col-span-1">
                    <!-- Profile Card Summary -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm text-center">
                        <div class="w-20 h-20 rounded-3xl bg-amber-500/10 text-amber-600 border border-amber-200 flex items-center justify-center font-extrabold text-2xl mx-auto mb-4 shadow-inner">
                            {{ strtoupper(substr($user->name ?? 'F', 0, 1)) }}
                        </div>
                        <h3 class="font-extrabold text-corporate text-lg">{{ $user->name }}</h3>
                        <p class="text-xs text-amber-600 font-bold mt-0.5">{{ $user->getRoleLabel() }}</p>
                        <p class="text-xs text-slate-400 font-mono mt-1">{{ $user->email }}</p>

                        <div class="mt-6 pt-6 border-t border-slate-100 text-left space-y-3 text-xs">
                            <div class="flex items-center justify-between text-slate-600">
                                <span class="text-slate-400">Divisi Kerja:</span>
                                <strong class="text-corporate font-semibold">Keuangan &amp; Administrasi</strong>
                            </div>
                            <div class="flex items-center justify-between text-slate-600">
                                <span class="text-slate-400">Status Akun:</span>
                                <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full font-bold text-[10px]">
                                    Aktif Terverifikasi
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-slate-600">
                                <span class="text-slate-400">Perusahaan / Kantor:</span>
                                <strong class="text-corporate font-semibold">{{ $user->company_name ?? 'CV Prasta Solusi Indonesia' }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- 2FA / Keamanan Info -->
                    <div class="bg-gradient-to-br from-corporate to-slate-800 rounded-3xl p-6 text-white shadow-xl">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center mb-3">
                            <i data-lucide="shield-check" class="w-5 h-5"></i>
                        </div>
                        <h4 class="font-bold text-sm text-white mb-1">Verifikasi 2-Langkah Aktif</h4>
                        <p class="text-xs text-slate-300 leading-relaxed mb-4">
                            Setiap sesi masuk diverifikasi dengan kode OTP 6 digit yang dikirimkan langsung ke nomor WhatsApp terdaftar Anda.
                        </p>
                        <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 text-xs font-mono text-amber-300">
                            WhatsApp: {{ $user->phone ?? '08xxxxxxxx' }}
                        </div>
                    </div>
                </div>

                <!-- Right Column: Form Edit Profil & Ganti Kata Sandi -->
                <div class="space-y-6 lg:col-span-2">
                    
                    <!-- Form 1: Ubah Data Profil -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                        <div class="flex items-center gap-2 pb-4 border-b border-slate-100 mb-5">
                            <i data-lucide="user-edit" class="w-5 h-5 text-amber-600"></i>
                            <div>
                                <h4 class="font-bold text-corporate text-base">Perbarui Data Diri</h4>
                                <p class="text-xs text-slate-400">Informasi resmi penanggung jawab bagian keuangan</p>
                            </div>
                        </div>

                        <form action="{{ route('finance.profile.update') }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap *</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-amber-500">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan PIC</label>
                                    <input type="text" name="pic_position" value="{{ old('pic_position', $user->pic_position ?? 'Finance & Administration Officer') }}" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-amber-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Email *</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-amber-500">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp Aktif *</label>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required placeholder="08xxxxxxxxxx" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-amber-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Perusahaan / Divisi</label>
                                <input type="text" name="company_name" value="{{ old('company_name', $user->company_name ?? 'CV Prasta Solusi Indonesia') }}" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-amber-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Kantor / Domisili</label>
                                <textarea name="address" rows="2" class="w-full text-xs p-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-amber-500">{{ old('address', $user->address) }}</textarea>
                            </div>

                            <div class="pt-2 flex justify-end">
                                <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs rounded-xl shadow-md transition-all">
                                    Simpan Perubahan Profil
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Form 2: Ubah Kata Sandi Akun -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                        <div class="flex items-center gap-2 pb-4 border-b border-slate-100 mb-5">
                            <i data-lucide="key-round" class="w-5 h-5 text-corporate"></i>
                            <div>
                                <h4 class="font-bold text-corporate text-base">Ganti Kata Sandi</h4>
                                <p class="text-xs text-slate-400">Pastikan gunakan kata sandi kuat minimal 8 karakter kombinasi huruf dan angka</p>
                            </div>
                        </div>

                        <form action="{{ route('finance.profile.password') }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi Saat Ini *</label>
                                <input type="password" name="current_password" required placeholder="Masukkan sandi lama..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-corporate">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi Baru *</label>
                                    <input type="password" name="password" required placeholder="Minimal 8 karakter..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-corporate">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Konfirmasi Sandi Baru *</label>
                                    <input type="password" name="password_confirmation" required placeholder="Ulangi sandi baru..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-corporate">
                                </div>
                            </div>

                            <div class="pt-2 flex justify-end">
                                <button type="submit" class="px-5 py-2.5 bg-corporate hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-md transition-all">
                                    Perbarui Kata Sandi
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

            </div>

        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>

