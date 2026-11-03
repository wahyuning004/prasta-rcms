<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP WhatsApp - IzinYou</title>
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
        .otp-input:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
            border-color: #10b981;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-emerald-500 selection:text-white relative min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    
    <!-- Background Gradient & Accents -->
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-200 via-slate-50 to-slate-50 z-0"></div>
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-[450px] h-[450px] bg-emerald-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-[350px] h-[350px] bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
    
    <div class="relative sm:mx-auto sm:w-full sm:max-w-md z-10 px-4 sm:px-0">
        
        <!-- Logo Brand -->
        <a href="/" class="flex flex-col justify-center items-center gap-2 mb-6 group cursor-pointer hover:scale-105 transition-transform text-center">
            <div class="w-16 h-16 rounded-2xl bg-corporate flex items-center justify-center shadow-xl border border-slate-700/60 ring-4 ring-emerald-100 group-hover:ring-emerald-300 transition-all duration-300">
                <svg class="w-9 h-9 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L3 7v6c0 5.5 3.8 10.7 9 12 5.2-1.3 9-6.5 9-12V7l-9-5z"/>
                    <path d="m9 12 2 2 4-4" stroke="#d4af37" stroke-width="2.5"/>
                </svg>
            </div>
            <div>
                <h1 class="font-extrabold text-2xl text-corporate tracking-tight">IzinYou</h1>
                <p class="text-[11px] text-slate-500 font-medium">Platform Resmi by CV Prasta Solusi Indonesia</p>
            </div>
        </a>

        <!-- Header Card -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-emerald-500/10 text-emerald-600 mb-3 border border-emerald-500/20">
                <i data-lucide="message-square-code" class="w-7 h-7"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-corporate">
                Verifikasi OTP WhatsApp
            </h2>
            <p class="mt-2 text-sm text-slate-600 leading-relaxed max-w-sm mx-auto">
                Kami telah mengirimkan 6 digit kode verifikasi ke nomor WhatsApp Anda:
                <strong class="text-emerald-700 block text-base mt-1 tracking-wide font-mono bg-emerald-50 py-1 px-3 rounded-lg border border-emerald-100 inline-block">
                    {{ $phone ?? (isset($user) ? $user->phone : 'Nomor WhatsApp') }}
                </strong>
            </p>
        </div>

        <!-- Form Box -->
        <div class="bg-white py-8 px-6 shadow-2xl rounded-3xl sm:px-10 border border-slate-100">
            
            {{-- Alert Success --}}
            @if (session('success'))
                <div class="mb-5 flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs sm:text-sm font-medium">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            {{-- Alert Warning --}}
            @if (session('warning'))
                <div class="mb-5 flex items-start gap-3 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-2xl text-xs sm:text-sm font-medium">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5"></i>
                    <div>{{ session('warning') }}</div>
                </div>
            @endif

            {{-- Alert Error --}}
            @if ($errors->any())
                <div class="mb-5 flex items-start gap-3 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl text-xs sm:text-sm">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 flex-shrink-0 mt-0.5"></i>
                    <div>
                        <p class="font-semibold mb-0.5">Verifikasi Gagal</p>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Mode Demo / Debug Helper jika dalam local development --}}
            @if (session('debug_otp'))
                <div class="mb-5 p-3 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 text-xs flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="info" class="w-4 h-4 text-blue-600"></i>
                        <span>Kode OTP (Simulasi Dev): <strong class="font-mono text-sm tracking-widest text-blue-700">{{ session('debug_otp') }}</strong></span>
                    </div>
                    <button type="button" onclick="autoFillOtp('{{ session('debug_otp') }}')" class="underline font-bold text-blue-600 hover:text-blue-800 text-[11px]">
                        Isi Otomatis
                    </button>
                </div>
            @endif

            <!-- Form Verifikasi OTP -->
            <form action="{{ route('otp.verify.post') }}" method="POST" id="otpForm" class="space-y-6">
                @csrf
                <input type="hidden" name="user_id" value="{{ session('pending_user_id', isset($user) ? $user->id : '') }}">
                <input type="hidden" name="otp" id="fullOtpInput">

                <div>
                    <label class="block text-sm font-bold text-slate-700 text-center mb-3">
                        Masukkan 6 Digit Kode OTP
                    </label>

                    <!-- 6 Digit Input Group -->
                    <div class="flex justify-center gap-2 sm:gap-3" id="otpContainer">
                        <input type="text" inputmode="numeric" maxlength="1" class="otp-input w-11 h-14 sm:w-12 sm:h-14 text-center text-2xl font-bold rounded-xl border-slate-300 bg-slate-50 border shadow-sm transition-all focus:bg-white focus:outline-none" autofocus required autocomplete="off">
                        <input type="text" inputmode="numeric" maxlength="1" class="otp-input w-11 h-14 sm:w-12 sm:h-14 text-center text-2xl font-bold rounded-xl border-slate-300 bg-slate-50 border shadow-sm transition-all focus:bg-white focus:outline-none" required autocomplete="off">
                        <input type="text" inputmode="numeric" maxlength="1" class="otp-input w-11 h-14 sm:w-12 sm:h-14 text-center text-2xl font-bold rounded-xl border-slate-300 bg-slate-50 border shadow-sm transition-all focus:bg-white focus:outline-none" required autocomplete="off">
                        <input type="text" inputmode="numeric" maxlength="1" class="otp-input w-11 h-14 sm:w-12 sm:h-14 text-center text-2xl font-bold rounded-xl border-slate-300 bg-slate-50 border shadow-sm transition-all focus:bg-white focus:outline-none" required autocomplete="off">
                        <input type="text" inputmode="numeric" maxlength="1" class="otp-input w-11 h-14 sm:w-12 sm:h-14 text-center text-2xl font-bold rounded-xl border-slate-300 bg-slate-50 border shadow-sm transition-all focus:bg-white focus:outline-none" required autocomplete="off">
                        <input type="text" inputmode="numeric" maxlength="1" class="otp-input w-11 h-14 sm:w-12 sm:h-14 text-center text-2xl font-bold rounded-xl border-slate-300 bg-slate-50 border shadow-sm transition-all focus:bg-white focus:outline-none" required autocomplete="off">
                    </div>
                    <p class="text-[11px] text-center text-slate-400 mt-2">Kode OTP aktif selama 10 menit sejak dikirimkan.</p>
                </div>

                <!-- Tombol Verifikasi -->
                <div>
                    <button 
                        type="submit" 
                        id="verifyBtn"
                        class="w-full flex justify-center items-center gap-2 py-3.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-300 active:scale-[0.98]"
                    >
                        <i data-lucide="check-check" class="w-4 h-4"></i>
                        Verifikasi &amp; Aktifkan Akun
                    </button>
                </div>
            </form>

            <!-- Resend OTP Section -->
            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500 mb-2">Tidak menerima pesan WhatsApp atau kode kadaluarsa?</p>
                
                <form action="{{ route('otp.resend') }}" method="POST" id="resendForm">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ session('pending_user_id', isset($user) ? $user->id : '') }}">
                    <button 
                        type="submit" 
                        id="resendBtn"
                        class="text-sm font-bold text-corporate hover:text-emerald-600 transition-colors inline-flex items-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                        <span id="resendText">Kirim Ulang Kode OTP</span>
                    </button>
                </form>
                <div id="countdownBox" class="text-xs text-slate-400 mt-1 hidden">
                    Kirim ulang dapat dilakukan dalam <span id="countdown" class="font-bold text-slate-600">60</span> detik
                </div>
            </div>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-400 hover:text-corporate flex items-center justify-center gap-1.5 transition-colors">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Halaman Login
                </a>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        const inputs = Array.from(document.querySelectorAll('.otp-input'));
        const fullOtpInput = document.getElementById('fullOtpInput');
        const otpForm = document.getElementById('otpForm');
        const verifyBtn = document.getElementById('verifyBtn');

        // Otomatis fokus ke input pertama
        if (inputs.length > 0) {
            inputs[0].focus();
        }

        // Logic Auto-Tab & Backspace untuk 6 kotak OTP
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                // Hanya izinkan angka
                input.value = input.value.replace(/[^0-9]/g, '');

                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                syncFullOtp();
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace') {
                    if (input.value === '' && index > 0) {
                        inputs[index - 1].focus();
                        inputs[index - 1].value = '';
                    }
                } else if (e.key === 'ArrowLeft' && index > 0) {
                    inputs[index - 1].focus();
                } else if (e.key === 'ArrowRight' && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            // Handle Paste seluruh 6 digit sekaligus
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').slice(0, 6);
                if (pastedData) {
                    for (let i = 0; i < pastedData.length; i++) {
                        if (inputs[i]) {
                            inputs[i].value = pastedData[i];
                        }
                    }
                    const nextIndex = Math.min(pastedData.length, inputs.length - 1);
                    inputs[nextIndex].focus();
                    syncFullOtp();
                }
            });
        });

        function syncFullOtp() {
            const combined = inputs.map(i => i.value).join('');
            fullOtpInput.value = combined;
        }

        function autoFillOtp(code) {
            if (!code || code.length !== 6) return;
            for (let i = 0; i < 6; i++) {
                if (inputs[i]) inputs[i].value = code[i];
            }
            syncFullOtp();
            inputs[5].focus();
        }

        // Form Submit Handler
        otpForm.addEventListener('submit', (e) => {
            syncFullOtp();
            if (fullOtpInput.value.length !== 6) {
                e.preventDefault();
                alert('Silakan masukkan 6 digit kode OTP secara lengkap.');
                return false;
            }
            verifyBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 22 6.477 22 12h-4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memverifikasi OTP...';
            verifyBtn.disabled = true;
        });

        // Countdown Timer Kirim Ulang OTP
        const resendBtn = document.getElementById('resendBtn');
        const resendText = document.getElementById('resendText');
        const countdownBox = document.getElementById('countdownBox');
        const countdownSpan = document.getElementById('countdown');

        let timeLeft = 60;
        let timer = null;

        function startCountdown() {
            resendBtn.disabled = true;
            countdownBox.classList.remove('hidden');
            timeLeft = 60;
            countdownSpan.textContent = timeLeft;

            timer = setInterval(() => {
                timeLeft--;
                countdownSpan.textContent = timeLeft;
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    resendBtn.disabled = false;
                    countdownBox.classList.add('hidden');
                    resendText.textContent = 'Kirim Ulang Kode OTP';
                }
            }, 1000);
        }

        // Mulai countdown jika baru saja kirim OTP
        startCountdown();
    </script>
</body>
</html>
