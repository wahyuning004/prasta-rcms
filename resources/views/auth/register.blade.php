<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Klien - IzinYou</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon-192.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .text-corporate { color: #0f172a; }
        .bg-corporate { background-color: #0f172a; }
        .text-gold { color: #d4af37; }
        .input-field {
            transition: all 0.2s ease;
        }
        .input-field:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-sky-500 selection:text-white relative min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-200 via-slate-50 to-slate-50 z-0"></div>
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-[450px] h-[450px] bg-sky-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-[350px] h-[350px] bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
    
    <div class="relative sm:mx-auto sm:w-full sm:max-w-2xl z-10 px-4 sm:px-0">
        <!-- Logo Brand (PRASTA SOLUSI INDONESIA) -->
        <a href="/" class="flex flex-col justify-center items-center gap-2 mb-6 group cursor-pointer hover:scale-105 transition-transform text-center">
            <div class="w-16 h-16 rounded-2xl bg-white shadow-xl border border-sky-100 flex items-center justify-center ring-4 ring-sky-50 group-hover:ring-sky-200 transition-all duration-300 p-2">
                <img src="{{ asset('images/logo-symbol-transparent.png') }}" alt="Prasta Solusi Indonesia" class="w-12 h-auto object-contain">
            </div>
            <div>
                <h1 class="font-black text-2xl text-slate-900 tracking-tight">
                    PRASTA <span class="text-sky-600">SOLUSI</span> INDONESIA
                </h1>
                <p class="text-[11px] text-slate-500 font-medium">Platform Resmi Perizinan &amp; Sertifikasi (IzinYou)</p>
            </div>
        </a>

        <h2 class="text-center text-3xl font-extrabold text-corporate tracking-tight">
            Buat Akun Klien IzinYou
        </h2>
        <p class="mt-2 text-center text-sm text-slate-600 mb-8 max-w-md mx-auto">
            Daftarkan perusahaan Anda untuk mendapatkan pendampingan layanan perizinan dan sertifikasi secara transparan.
        </p>

        <!-- Form Card -->
        <div class="bg-white py-10 px-6 shadow-2xl rounded-3xl sm:px-10 border border-slate-100">
            
            {{-- Alert Error --}}
            @if ($errors->any())
                <div class="mb-6 flex items-start gap-3 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3.5 rounded-2xl text-sm">
                    <i data-lucide="alert-circle" class="w-5 h-5 mt-0.5 flex-shrink-0 text-rose-500"></i>
                    <div>
                        <p class="font-semibold mb-1">Pendaftaran Belum Lengkap</p>
                        <ul class="list-disc list-inside text-xs space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form class="space-y-6" action="{{ route('register.post') }}" method="POST" id="registerForm">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Lengkap PIC -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5 flex items-center justify-between">
                            <span>Nama Lengkap (PIC) <span class="text-rose-500">*</span></span>
                            <span class="text-[10px] text-slate-400 font-normal">Hanya huruf (tanpa angka)</span>
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i data-lucide="user" class="h-5 w-5 text-slate-400"></i>
                            </div>
                            <input 
                                id="name" 
                                name="name" 
                                type="text" 
                                required 
                                maxlength="100"
                                autocomplete="name"
                                value="{{ old('name') }}"
                                class="input-field block w-full pl-11 pr-4 py-3 sm:text-sm border rounded-xl bg-slate-50 border-slate-200 focus:outline-none focus:border-sky-500 focus:bg-white transition-colors @error('name') border-rose-400 bg-rose-50 @enderror" 
                                placeholder="Cth: Budi Santoso"
                            >
                        </div>
                        <p id="nameWarning" class="text-xs text-rose-500 mt-1 font-medium hidden"></p>
                        @error('name')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Perusahaan -->
                    <div>
                        <label for="company" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Nama Perusahaan <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i data-lucide="building-2" class="h-5 w-5 text-slate-400"></i>
                            </div>
                            <input 
                                id="company" 
                                name="company" 
                                type="text" 
                                required 
                                maxlength="150"
                                value="{{ old('company') }}"
                                class="input-field block w-full pl-11 pr-4 py-3 sm:text-sm border rounded-xl bg-slate-50 border-slate-200 focus:outline-none focus:border-sky-500 focus:bg-white transition-colors @error('company') border-rose-400 bg-rose-50 @enderror" 
                                placeholder="Cth: PT Makmur Sentosa"
                            >
                        </div>
                        <p id="companyWarning" class="text-xs text-rose-500 mt-1 font-medium hidden"></p>
                        @error('company')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Alamat Email Perusahaan <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i data-lucide="mail" class="h-5 w-5 text-slate-400"></i>
                            </div>
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                autocomplete="email" 
                                required 
                                maxlength="255"
                                value="{{ old('email') }}"
                                class="input-field block w-full pl-11 pr-4 py-3 sm:text-sm border rounded-xl bg-slate-50 border-slate-200 focus:outline-none focus:border-sky-500 focus:bg-white transition-colors @error('email') border-rose-400 bg-rose-50 @enderror" 
                                placeholder="pic@perusahaan.com"
                            >
                        </div>
                        <p id="emailWarning" class="text-xs text-rose-500 mt-1 font-medium hidden"></p>
                        @error('email')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nomor WhatsApp (HP) - Strict Angka -->
                    <div>
                        <label for="phone" class="block text-sm font-semibold text-slate-700 mb-1.5 flex items-center justify-between">
                            <span>Nomor WhatsApp (HP) <span class="text-rose-500">*</span></span>
                            <span class="text-[10px] text-sky-700 font-bold bg-sky-100/70 px-2 py-0.5 rounded-full border border-sky-200">Wajib Angka (08xx)</span>
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i data-lucide="phone-call" class="h-5 w-5 text-sky-600"></i>
                            </div>
                            <input 
                                id="phone" 
                                name="phone" 
                                type="tel" 
                                inputmode="numeric"
                                pattern="^(?:\+62|62|0)8[1-9][0-9]{7,11}$"
                                maxlength="15"
                                autocomplete="tel"
                                required 
                                value="{{ old('phone') }}"
                                class="input-field block w-full pl-11 pr-4 py-3 sm:text-sm border rounded-xl bg-slate-50 border-slate-200 focus:outline-none focus:border-sky-500 focus:bg-white transition-colors @error('phone') border-rose-400 bg-rose-50 @enderror" 
                                placeholder="Cth: 081234567890"
                            >
                        </div>
                        {{-- Real-time validation warning message --}}
                        <p id="phoneWarning" class="text-xs text-rose-600 mt-1.5 font-semibold hidden flex items-center gap-1.5 bg-rose-50 border border-rose-200 px-2.5 py-1 rounded-lg">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0 text-rose-500"></i>
                            <span id="phoneWarningText">Hanya angka yang diperbolehkan! Huruf tidak dapat dimasukkan.</span>
                        </p>
                        <p class="text-[11px] text-slate-400 mt-1" id="phoneHint">Format: 081234567890 (10–14 digit angka). OTP akan dikirim via WhatsApp.</p>
                        @error('phone')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5 flex items-center justify-between">
                            <span>Password Akun <span class="text-rose-500">*</span></span>
                            <span class="text-[10px] text-slate-400 font-normal">Min. 8 karakter (huruf &amp; angka)</span>
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i data-lucide="lock" class="h-5 w-5 text-slate-400"></i>
                            </div>
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                autocomplete="new-password"
                                required 
                                class="input-field block w-full pl-11 pr-11 py-3 sm:text-sm border rounded-xl bg-slate-50 border-slate-200 focus:outline-none focus:border-sky-500 focus:bg-white transition-colors @error('password') border-rose-400 bg-rose-50 @enderror" 
                                placeholder="Minimal 8 karakter"
                            >
                            <button type="button" id="togglePasswordBtn" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                                <i data-lucide="eye" id="togglePasswordIcon" class="h-5 w-5"></i>
                            </button>
                        </div>
                        
                        <!-- Indikator Kekuatan Password -->
                        <div class="mt-2 space-y-1">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="text-slate-500">Kekuatan Password:</span>
                                <span id="passwordStrengthText" class="font-bold text-slate-400">-</span>
                            </div>
                            <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                <div id="passwordStrengthBar" class="h-full bg-slate-400 transition-all duration-300 w-0"></div>
                            </div>
                        </div>

                        @error('password')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Konfirmasi Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i data-lucide="shield-check" class="h-5 w-5 text-slate-400"></i>
                            </div>
                            <input 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                type="password" 
                                autocomplete="new-password"
                                required 
                                class="input-field block w-full pl-11 pr-11 py-3 sm:text-sm border rounded-xl bg-slate-50 border-slate-200 focus:outline-none focus:border-sky-500 focus:bg-white transition-colors" 
                                placeholder="Ulangi password"
                            >
                            <button type="button" id="toggleConfirmBtn" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                                <i data-lucide="eye" id="toggleConfirmIcon" class="h-5 w-5"></i>
                            </button>
                        </div>
                        <!-- Live Match Status -->
                        <p id="passwordMatchStatus" class="text-xs mt-1.5 font-medium hidden"></p>
                    </div>
                </div>

                <!-- Persetujuan Syarat & Ketentuan -->
                <div class="flex items-start gap-2.5 pt-1">
                    <input id="terms" name="terms" type="checkbox" required class="h-4 w-4 mt-0.5 text-sky-600 focus:ring-sky-500 border-slate-300 rounded cursor-pointer">
                    <label for="terms" class="text-xs sm:text-sm text-slate-600 cursor-pointer select-none">
                        Saya menyetujui seluruh <a href="#" class="text-sky-600 font-bold hover:underline">Syarat & Ketentuan</a> serta <a href="#" class="text-sky-600 font-bold hover:underline">Kebijakan Privasi</a> operasional portal IzinYou.
                    </label>
                </div>

                <!-- Google reCAPTCHA v2 Checkbox Widget -->
                <div class="pt-2 flex flex-col items-center justify-center">
                    <div class="g-recaptcha scale-95 sm:scale-100 origin-center" data-sitekey="{{ config('services.recaptcha.site_key', env('RECAPTCHA_SITE_KEY', '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI')) }}"></div>
                    @error('g-recaptcha-response')
                        <p class="text-xs text-rose-500 font-semibold mt-1 text-center">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Indikator Verifikasi 2 Langkah (OTP WhatsApp) -->
                <div class="p-3.5 rounded-2xl bg-sky-50 border border-sky-200/80 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-sky-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-sky-950">Proteksi Verifikasi 2-Langkah WhatsApp</p>
                            <p class="text-[11px] text-sky-700">Kode OTP 6-digit dikirimkan instan ke nomor WhatsApp untuk aktivasi akun baru.</p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('otpInfoModal').classList.remove('hidden')" class="text-xs font-bold text-sky-800 hover:text-sky-900 underline shrink-0">
                        Detail 2FA
                    </button>
                </div>

                <!-- Submit Button -->
                <div class="pt-1">
                    <button 
                        type="submit" 
                        id="registerBtn"
                        class="w-full flex justify-center items-center gap-2 py-3.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all duration-300 active:scale-[0.98]"
                    >
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        Daftar &amp; Kirim Kode OTP WhatsApp
                    </button>
                </div>
            </form>
            
            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <p class="text-sm text-slate-600">
                    Sudah memiliki akun terdaftar?
                    <a href="{{ route('login') }}" class="font-bold text-corporate hover:text-sky-600 transition-colors ml-1">Masuk di sini &rarr;</a>
                </p>
            </div>
        </div>

        <div class="text-center mt-6">
            <a href="/" class="text-sm text-slate-500 hover:text-corporate font-medium transition-colors inline-flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Beranda IzinYou
            </a>
        </div>
    </div>

    <!-- Modal Informasi Verifikasi 2 Langkah (OTP WhatsApp) -->
    <div id="otpInfoModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative text-center">
            <button onclick="document.getElementById('otpInfoModal').classList.add('hidden')" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>

            <div class="w-16 h-16 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center mx-auto mb-4 border border-sky-200 shadow-sm">
                <i data-lucide="message-square-code" class="w-8 h-8"></i>
            </div>

            <h3 class="text-lg font-bold text-corporate mb-1">Mekanisme Verifikasi 2-Langkah</h3>
            <p class="text-xs text-slate-500 mb-4">Keamanan Berlapis Standar Perizinan Industri</p>

            <div class="text-left text-xs text-slate-600 space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-200/60 mb-5">
                <div class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-sky-600 text-white font-bold flex items-center justify-center text-[10px] shrink-0 mt-0.5">1</span>
                    <p>Setelah form pendaftaran dikirimkan, sistem IzinYou meng-generate 6 digit kode token OTP acak dengan masa berlaku 10 menit.</p>
                </div>
                <div class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-sky-600 text-white font-bold flex items-center justify-center text-[10px] shrink-0 mt-0.5">2</span>
                    <p>Token OTP dikirimkan secara instan melalui WhatsApp Gateway resmi ke nomor HP yang Anda masukkan.</p>
                </div>
                <div class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-sky-600 text-white font-bold flex items-center justify-center text-[10px] shrink-0 mt-0.5">3</span>
                    <p>Masukkan token pada layar verifikasi untuk memvalidasi kepemilikan nomor dan mengaktifkan akun klien Anda.</p>
                </div>
            </div>

            <button onclick="document.getElementById('otpInfoModal').classList.add('hidden')" class="w-full py-2.5 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs rounded-xl shadow-md transition-all">
                Saya Mengerti
            </button>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // 1. Inisialisasi Elemen Form
        const registerForm = document.getElementById('registerForm');
        const registerBtn  = document.getElementById('registerBtn');
        const nameInput    = document.getElementById('name');
        const nameWarning  = document.getElementById('nameWarning');
        const companyInput = document.getElementById('company');
        const emailInput   = document.getElementById('email');
        const emailWarning = document.getElementById('emailWarning');
        const phoneInput   = document.getElementById('phone');
        const phoneWarning = document.getElementById('phoneWarning');
        const phoneWarningText = document.getElementById('phoneWarningText');
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirmation');
        const passwordMatchStatus  = document.getElementById('passwordMatchStatus');
        const passwordStrengthBar  = document.getElementById('passwordStrengthBar');
        const passwordStrengthText = document.getElementById('passwordStrengthText');

        // Toggle Password Show/Hide
        function setupPasswordToggle(btnId, inputId, iconId) {
            const btn = document.getElementById(btnId);
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (btn && input && icon) {
                btn.addEventListener('click', () => {
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.setAttribute('data-lucide', 'eye-off');
                    } else {
                        input.type = 'password';
                        icon.setAttribute('data-lucide', 'eye');
                    }
                    lucide.createIcons();
                });
            }
        }
        setupPasswordToggle('togglePasswordBtn', 'password', 'togglePasswordIcon');
        setupPasswordToggle('toggleConfirmBtn', 'password_confirmation', 'toggleConfirmIcon');

        // ============================================================
        // 2. LOGIKA KETAT: NOMOR WHATSAPP (MUTLAK HANYA ANGKA)
        // Mencegah input huruf apapun (misal 'a', 'b', dll)
        // ============================================================
        let warningTimeout = null;

        function showPhoneWarning(msg) {
            phoneWarningText.textContent = msg;
            phoneWarning.classList.remove('hidden');
            phoneInput.classList.add('border-rose-400', 'bg-rose-50');
            clearTimeout(warningTimeout);
            warningTimeout = setTimeout(() => {
                phoneWarning.classList.add('hidden');
                phoneInput.classList.remove('border-rose-400', 'bg-rose-50');
            }, 3500);
        }

        // Cegah penekanan tombol huruf atau karakter non-angka pada keyboard
        phoneInput.addEventListener('keydown', (e) => {
            // Izinkan tombol kontrol: Backspace, Tab, Enter, Delete, Arrow, Ctrl+A, Ctrl+C, Ctrl+V
            const allowedKeys = ['Backspace', 'Tab', 'Enter', 'Delete', 'ArrowLeft', 'ArrowRight', 'Home', 'End'];
            if (allowedKeys.includes(e.key) || (e.ctrlKey || e.metaKey)) {
                return;
            }

            // Jika tombol adalah huruf alfabet (a-z, A-Z)
            if (/^[a-zA-Z]$/.test(e.key)) {
                e.preventDefault();
                showPhoneWarning(`Karakter huruf "${e.key}" ditolak! Nomor WhatsApp hanya boleh berupa angka.`);
                return;
            }

            // Izinkan '+' hanya di posisi karakter pertama
            if (e.key === '+') {
                if (phoneInput.selectionStart === 0 && !phoneInput.value.includes('+')) {
                    return;
                } else {
                    e.preventDefault();
                    showPhoneWarning('Tanda "+" hanya dapat ditaruh di awal nomor.');
                    return;
                }
            }

            // Jika bukan angka 0-9
            if (!/^[0-9]$/.test(e.key)) {
                e.preventDefault();
                showPhoneWarning(`Karakter "${e.key}" tidak diizinkan. Harap hanya masukkan angka.`);
            }
        });

        // Filter paste & input events jika ada karakter huruf yang lolos
        phoneInput.addEventListener('input', () => {
            let originalVal = phoneInput.value;
            // Jika ada huruf di dalamnya
            if (/[a-zA-Z]/.test(originalVal)) {
                showPhoneWarning('Huruf terdeteksi dan otomatis dihapus. Nomor WhatsApp wajib angka!');
            }
            
            // Bersihkan: hanya izinkan leading '+' dan angka
            let cleanedVal = originalVal.replace(/(?!^\+)[^0-9]/g, '');
            if (cleanedVal !== originalVal) {
                phoneInput.value = cleanedVal;
            }
        });

        // Validasi format saat kehilangan fokus (blur)
        phoneInput.addEventListener('blur', () => {
            const val = phoneInput.value.trim();
            if (!val) return;

            const isValidIndonesian = /^(?:\+62|62|0)8[1-9][0-9]{7,11}$/.test(val);
            if (!isValidIndonesian) {
                showPhoneWarning('Format belum sesuai. Nomor seluler harus diawali 08, 628, atau +628 (panjang 10–14 digit).');
            } else {
                phoneWarning.classList.add('hidden');
                phoneInput.classList.remove('border-rose-400', 'bg-rose-50');
            }
        });

        // ============================================================
        // 3. LOGIKA KETAT: NAMA PIC (HANYA HURUF, TIDAK BOLEH ANGKA)
        // ============================================================
        nameInput.addEventListener('keydown', (e) => {
            const allowedKeys = ['Backspace', 'Tab', 'Enter', 'Delete', 'ArrowLeft', 'ArrowRight', 'Home', 'End'];
            if (allowedKeys.includes(e.key) || (e.ctrlKey || e.metaKey)) {
                return;
            }

            // Jika mengetik angka 0-9
            if (/^[0-9]$/.test(e.key)) {
                e.preventDefault();
                nameWarning.textContent = 'Nama lengkap PIC tidak boleh mengandung angka. Harap gunakan nama asli.';
                nameWarning.classList.remove('hidden');
                nameInput.classList.add('border-rose-400', 'bg-rose-50');
                setTimeout(() => {
                    nameWarning.classList.add('hidden');
                    nameInput.classList.remove('border-rose-400', 'bg-rose-50');
                }, 3000);
            }
        });

        nameInput.addEventListener('input', () => {
            if (/[0-9]/.test(nameInput.value)) {
                nameInput.value = nameInput.value.replace(/[0-9]/g, '');
                nameWarning.textContent = 'Angka dihapus. Nama PIC hanya boleh berisi huruf.';
                nameWarning.classList.remove('hidden');
                setTimeout(() => nameWarning.classList.add('hidden'), 2500);
            }
        });

        // ============================================================
        // 4. LOGIKA KEKUATAN PASSWORD & KECOCOKAN KONFIRMASI
        // ============================================================
        function checkPasswordStrength(pwd) {
            let score = 0;
            if (pwd.length >= 8) score++;
            if (pwd.length >= 10) score++;
            if (/[a-zA-Z]/.test(pwd) && /[0-9]/.test(pwd)) score++;
            if (/[^a-zA-Z0-9]/.test(pwd)) score++;

            if (pwd.length === 0) {
                passwordStrengthBar.style.width = '0%';
                passwordStrengthBar.className = 'h-full bg-slate-300 transition-all duration-300';
                passwordStrengthText.textContent = '-';
                passwordStrengthText.className = 'font-bold text-slate-400';
            } else if (score <= 1 || pwd.length < 8) {
                passwordStrengthBar.style.width = '30%';
                passwordStrengthBar.className = 'h-full bg-rose-500 transition-all duration-300';
                passwordStrengthText.textContent = 'Lemah (Min. 8 karakter & kombinasi angka)';
                passwordStrengthText.className = 'font-bold text-rose-500 text-[11px]';
            } else if (score === 2 || score === 3) {
                passwordStrengthBar.style.width = '70%';
                passwordStrengthBar.className = 'h-full bg-amber-500 transition-all duration-300';
                passwordStrengthText.textContent = 'Cukup Baik';
                passwordStrengthText.className = 'font-bold text-amber-500 text-[11px]';
            } else {
                passwordStrengthBar.style.width = '100%';
                passwordStrengthBar.className = 'h-full bg-sky-500 transition-all duration-300';
                passwordStrengthText.textContent = 'Sangat Kuat';
                passwordStrengthText.className = 'font-bold text-sky-600 text-[11px]';
            }
        }

        function checkPasswordMatch() {
            const pwd = passwordInput.value;
            const confirm = passwordConfirmInput.value;

            if (confirm.length === 0) {
                passwordMatchStatus.classList.add('hidden');
                return;
            }

            passwordMatchStatus.classList.remove('hidden');
            if (pwd === confirm) {
                passwordMatchStatus.textContent = '✓ Konfirmasi password cocok';
                passwordMatchStatus.className = 'text-xs mt-1.5 font-semibold text-sky-600';
                passwordConfirmInput.classList.remove('border-rose-400', 'bg-rose-50');
                passwordConfirmInput.classList.add('border-sky-400');
            } else {
                passwordMatchStatus.textContent = '✕ Konfirmasi password tidak cocok';
                passwordMatchStatus.className = 'text-xs mt-1.5 font-semibold text-rose-500';
                passwordConfirmInput.classList.remove('border-sky-400');
                passwordConfirmInput.classList.add('border-rose-400', 'bg-rose-50');
            }
        }

        passwordInput.addEventListener('input', () => {
            checkPasswordStrength(passwordInput.value);
            checkPasswordMatch();
        });

        passwordConfirmInput.addEventListener('input', checkPasswordMatch);

        // ============================================================
        // 5. VALIDASI SEBELUM SUBMIT FORM
        // Memastikan tidak ada data cacat yang terkirim ke server
        // ============================================================
        registerForm.addEventListener('submit', (e) => {
            const phone = phoneInput.value.trim();
            const name  = nameInput.value.trim();
            const pwd   = passwordInput.value;
            const pwdConf = passwordConfirmInput.value;

            // Cek Nomor HP mengandung huruf
            if (/[a-zA-Z]/.test(phone)) {
                e.preventDefault();
                showPhoneWarning('Pendaftaran ditolak: Nomor WhatsApp mengandung karakter huruf!');
                phoneInput.focus();
                return false;
            }

            // Cek Format Seluler Indonesia
            if (!/^(?:\+62|62|0)8[1-9][0-9]{7,11}$/.test(phone)) {
                e.preventDefault();
                showPhoneWarning('Pendaftaran ditolak: Nomor WhatsApp harus format nomor seluler Indonesia yang valid (10–14 digit).');
                phoneInput.focus();
                return false;
            }

            // Cek Nama mengandung angka
            if (/[0-9]/.test(name)) {
                e.preventDefault();
                alert('Nama PIC tidak boleh mengandung angka. Harap periksa kembali.');
                nameInput.focus();
                return false;
            }

            // Cek Password Match
            if (pwd !== pwdConf) {
                e.preventDefault();
                alert('Konfirmasi password tidak cocok dengan password yang dimasukkan.');
                passwordConfirmInput.focus();
                return false;
            }

            // Loading state saat lolos seluruh validasi
            registerBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 22 6.477 22 12h-4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Mendaftarkan & Mengirim OTP WhatsApp...';
            registerBtn.disabled = true;
        });
    </script>
</body>
</html>
