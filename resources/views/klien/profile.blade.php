<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pelanggan & Pengaturan Akun - IzinYou</title>
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
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-sky-500 selection:text-white flex h-screen overflow-hidden">

    @include('klien.partials.sidebar')

    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/60">

        <!-- Header -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-8 shrink-0 relative z-10">
            <div class="flex items-center gap-3">
                <button type="button" onclick="toggleKlienSidebar()" class="lg:hidden p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg sm:text-xl font-bold text-corporate">Profil Pelanggan &amp; Pengaturan Akun</h2>
                        <span class="bg-sky-100 text-sky-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full">Sistem IZINYOU</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola identitas akun pelanggan, data PIC, legalitas perusahaan (NIB &amp; NPWP), serta keamanan kata sandi Anda.</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('klien.dashboard') }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all shadow-sm">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Kembali ke Dashboard</span>
                </a>
            </div>
        </header>

        @include('klien.partials.alerts')

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-8 pt-4">
            <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Ringkasan Profil Card & Ganti Password -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm text-center">
                        <div class="w-20 h-20 rounded-2xl bg-sky-100 text-sky-700 font-black text-2xl flex items-center justify-center mx-auto mb-4 border border-sky-200">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <h3 class="font-bold text-corporate text-base">{{ $user->name }}</h3>
                        <p class="text-xs text-sky-600 font-semibold mt-0.5">{{ $user->pic_position ?: 'Penanggung Jawab (PIC)' }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $user->company_name ?: 'Nama Perusahaan Belum Diisi' }}</p>

                        <div class="mt-3">
                            <span class="inline-block px-3 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                {{ $user->getRoleLabel() }}
                            </span>
                        </div>

                        <div class="mt-5 pt-5 border-t border-slate-100 text-left space-y-2.5 text-xs">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-slate-400">Email Akun:</span>
                                <span class="font-semibold text-corporate truncate" title="{{ $user->email }}">{{ $user->email }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-slate-400">No. HP / WA:</span>
                                <span class="font-mono font-semibold text-corporate">{{ $user->phone ?: '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-slate-400">Jabatan PIC:</span>
                                <span class="font-semibold text-corporate">{{ $user->pic_position ?: 'Penanggung Jawab' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-slate-400">Badan Usaha:</span>
                                <span class="font-semibold text-corporate truncate">{{ $user->company_name ?: '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-slate-400">Bidang Usaha:</span>
                                <span class="font-semibold text-corporate truncate">{{ $user->business_field ?: '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-slate-400">NIB OSS-RBA:</span>
                                <span class="font-mono font-semibold text-corporate">{{ $user->nib ?: 'Belum diisi' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-slate-400">NPWP:</span>
                                <span class="font-mono font-semibold text-corporate">{{ $user->npwp ?: 'Belum diisi' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-slate-400">Role Sistem:</span>
                                <span class="font-mono font-bold text-sky-700">{{ strtoupper($user->role) }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-slate-400">Status Akun:</span>
                                @if($user->email_verified_at)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Terverifikasi
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        Belum Verifikasi
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-slate-400">Terdaftar Sejak:</span>
                                <span class="font-semibold text-corporate">
                                    {{ $user->created_at ? $user->created_at->copy()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y') : '-' }}
                                </span>
                            </div>
                        </div>

                        <a href="#edit-profil-form" onclick="focusKlienEditProfile()"
                           class="mt-5 w-full py-2.5 px-4 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold inline-flex items-center justify-center gap-1.5 shadow-sm transition">
                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                            <span>Edit Profil</span>
                        </a>
                    </div>

                    <!-- Form Ganti Password -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                        <div class="flex items-center gap-2 mb-4">
                            <i data-lucide="lock" class="w-5 h-5 text-sky-600"></i>
                            <h3 class="font-bold text-corporate text-sm">Keamanan &amp; Kata Sandi</h3>
                        </div>

                        <form action="{{ route('klien.profile.password') }}" method="POST" class="space-y-3.5">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Saat Ini *</label>
                                <input type="password" name="current_password" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 focus:bg-white @error('current_password') border-rose-400 bg-rose-50 @enderror">
                                @error('current_password')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Baru *</label>
                                <input type="password" name="password" required minlength="8" placeholder="Min. 8 karakter (huruf &amp; angka)" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 focus:bg-white @error('password') border-rose-400 bg-rose-50 @enderror">
                                @error('password')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Kata Sandi Baru *</label>
                                <input type="password" name="password_confirmation" required minlength="8" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 focus:bg-white">
                            </div>

                            <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-sm">
                                Perbarui Kata Sandi
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Form Edit Profil Pelanggan -->
                <div class="lg:col-span-2">
                    <div id="edit-profil-form" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm transition-all">
                        <div class="flex items-center gap-2.5 pb-4 mb-6 border-b border-slate-100">
                            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                                <i data-lucide="user-check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-corporate">Informasi Pribadi &amp; Legalitas Usaha Pelanggan</h3>
                                <p class="text-xs text-slate-500">Perbarui nama PIC, jabatan, kontak resmi, legalitas NIB &amp; NPWP, serta alamat domisili usaha Anda.</p>
                            </div>
                        </div>

                        <form action="{{ route('klien.profile.update') }}" method="POST" id="profileForm" class="space-y-5">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="name" class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap (PIC) *</label>
                                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="100"
                                           class="w-full px-4 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 focus:bg-white @error('name') border-rose-400 bg-rose-50 @enderror">
                                    <p id="profileNameWarn" class="text-[11px] text-rose-600 mt-1 hidden"></p>
                                    @error('name')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="pic_position" class="block text-xs font-bold text-slate-700 mb-1.5">Jabatan PIC</label>
                                    <input type="text" id="pic_position" name="pic_position" value="{{ old('pic_position', $user->pic_position) }}" maxlength="100"
                                           placeholder="Cth: Direktur / Regulatory Affairs / APJ"
                                           class="w-full px-4 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 focus:bg-white @error('pic_position') border-rose-400 bg-rose-50 @enderror">
                                    @error('pic_position')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email *</label>
                                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="255"
                                           class="w-full px-4 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 focus:bg-white @error('email') border-rose-400 bg-rose-50 @enderror">
                                    @error('email')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-xs font-bold text-slate-700 mb-1.5">Nomor HP / WhatsApp *</label>
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required inputmode="numeric" maxlength="15"
                                           placeholder="Cth: 081234567890"
                                           class="w-full px-4 py-2.5 text-xs sm:text-sm font-mono bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 focus:bg-white @error('phone') border-rose-400 bg-rose-50 @enderror">
                                    <p id="profilePhoneWarn" class="text-[11px] text-rose-600 mt-1 hidden"></p>
                                    @error('phone')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="company_name" class="block text-xs font-bold text-slate-700 mb-1.5">Nama Perusahaan / Badan Usaha *</label>
                                    <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $user->company_name) }}" required maxlength="150"
                                           placeholder="Cth: PT Prasta Solusi Indonesia"
                                           class="w-full px-4 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 focus:bg-white @error('company_name') border-rose-400 bg-rose-50 @enderror">
                                    @error('company_name')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="business_field" class="block text-xs font-bold text-slate-700 mb-1.5">Bidang Usaha / Sektor Industri</label>
                                    <input type="text" id="business_field" name="business_field" value="{{ old('business_field', $user->business_field) }}" maxlength="150"
                                           placeholder="Cth: Alat Kesehatan, Kosmetik, Pangan Olahan, Farmasi"
                                           class="w-full px-4 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 focus:bg-white @error('business_field') border-rose-400 bg-rose-50 @enderror">
                                    @error('business_field')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="nib" class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Induk Berusaha (NIB OSS-RBA)</label>
                                    <input type="text" id="nib" name="nib" value="{{ old('nib', $user->nib) }}" inputmode="numeric" maxlength="13"
                                           placeholder="13 digit angka NIB OSS-RBA"
                                           class="w-full px-4 py-2.5 text-xs sm:text-sm font-mono bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 focus:bg-white @error('nib') border-rose-400 bg-rose-50 @enderror">
                                    <p id="profileNibWarn" class="text-[11px] text-rose-600 mt-1 hidden"></p>
                                    @error('nib')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="npwp" class="block text-xs font-bold text-slate-700 mb-1.5">NPWP Perusahaan / Pemohon</label>
                                    <input type="text" id="npwp" name="npwp" value="{{ old('npwp', $user->npwp) }}" maxlength="30"
                                           placeholder="Cth: 94.812.334.2-407.000 atau 16 digit angka"
                                           class="w-full px-4 py-2.5 text-xs sm:text-sm font-mono bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 focus:bg-white @error('npwp') border-rose-400 bg-rose-50 @enderror">
                                    @error('npwp')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div>
                                <label for="address" class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Lengkap Perusahaan / Domisili *</label>
                                <textarea id="address" name="address" rows="3" required maxlength="500"
                                          placeholder="Jalan, No./Gedung, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten, Kode Pos"
                                          class="w-full px-4 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 focus:bg-white @error('address') border-rose-400 bg-rose-50 @enderror">{{ old('address', $user->address) }}</textarea>
                                @error('address')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <!-- Informasi Role & Hak Akses Read-Only -->
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
                                <div>
                                    <p class="font-bold text-corporate">Role &amp; Hak Akses Akun: {{ $user->getRoleLabel() }} ({{ strtoupper($user->role) }})</p>
                                    <p class="text-slate-500 mt-0.5">Role, permission, dan status verifikasi akun hanya dapat dikelola oleh tim Administrator resmi.</p>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700 w-fit shrink-0">Read-Only</span>
                            </div>

                            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                                <button type="submit" id="saveProfileBtn" class="px-6 py-3 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-sky-600/20 flex items-center gap-2 transition">
                                    <i data-lucide="save" class="w-4 h-4"></i>
                                    <span>Simpan Perubahan Profil</span>
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

        const nameEl = document.getElementById('name');
        const nameWarn = document.getElementById('profileNameWarn');
        const phoneEl = document.getElementById('phone');
        const phoneWarn = document.getElementById('profilePhoneWarn');
        const nibEl = document.getElementById('nib');
        const nibWarn = document.getElementById('profileNibWarn');
        const formEl = document.getElementById('profileForm');
        const editCardEl = document.getElementById('edit-profil-form');

        function focusKlienEditProfile() {
            if (editCardEl) {
                editCardEl.classList.add('ring-2', 'ring-sky-300');
                setTimeout(() => editCardEl.classList.remove('ring-2', 'ring-sky-300'), 1400);
            }
            if (nameEl) {
                nameEl.focus();
            }
        }

        if (nameEl) {
            nameEl.addEventListener('input', () => {
                if (/[0-9]/.test(nameEl.value)) {
                    nameWarn.textContent = 'Nama lengkap tidak boleh mengandung angka.';
                    nameWarn.classList.remove('hidden');
                } else {
                    nameWarn.classList.add('hidden');
                }
            });
        }

        if (phoneEl) {
            phoneEl.addEventListener('input', () => {
                const raw = phoneEl.value;
                const cleaned = raw.replace(/(?!^\+)[^0-9]/g, '');
                if (cleaned !== raw) {
                    phoneEl.value = cleaned;
                    phoneWarn.textContent = 'Nomor HP hanya boleh berisi angka.';
                    phoneWarn.classList.remove('hidden');
                } else {
                    phoneWarn.classList.add('hidden');
                }
            });
        }

        if (nibEl) {
            nibEl.addEventListener('input', () => {
                const raw = nibEl.value;
                const cleaned = raw.replace(/[^0-9]/g, '').slice(0, 13);
                if (cleaned !== raw) {
                    nibEl.value = cleaned;
                }
                if (cleaned.length > 0 && cleaned.length < 13) {
                    nibWarn.textContent = 'NIB harus tepat 13 digit angka (' + cleaned.length + '/13 digit).';
                    nibWarn.classList.remove('hidden');
                } else {
                    nibWarn.classList.add('hidden');
                }
            });
        }

        if (formEl) {
            formEl.addEventListener('submit', (e) => {
                const phoneVal = phoneEl.value.trim();
                if (!/^(?:\+62|62|0)8[1-9][0-9]{7,11}$/.test(phoneVal)) {
                    e.preventDefault();
                    phoneWarn.textContent = 'Format nomor HP tidak valid (harus 08xx / 628xx / +628xx, 10–14 digit).';
                    phoneWarn.classList.remove('hidden');
                    phoneEl.focus();
                    return false;
                }
                if (/[0-9]/.test(nameEl.value)) {
                    e.preventDefault();
                    nameWarn.textContent = 'Nama lengkap tidak boleh mengandung angka.';
                    nameWarn.classList.remove('hidden');
                    nameEl.focus();
                    return false;
                }
                if (nibEl && nibEl.value.trim() !== '') {
                    const nibVal = nibEl.value.trim();
                    if (!/^[0-9]{13}$/.test(nibVal)) {
                        e.preventDefault();
                        nibWarn.textContent = 'NIB harus tepat 13 digit angka.';
                        nibWarn.classList.remove('hidden');
                        nibEl.focus();
                        return false;
                    }
                }
            });
        }
    </script>
</body>
</html>
