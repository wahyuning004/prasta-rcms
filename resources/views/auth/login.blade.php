<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Prasta Solusi Indonesia</title>
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
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
        }
        .animate-float { animation: float 4s ease-in-out infinite; }
        .input-field {
            transition: all 0.2s ease;
        }
        .input-field:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-emerald-500 selection:text-white relative min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">

    <!-- Decorative Background -->
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-200 via-slate-50 to-slate-50 z-0"></div>
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-emerald-100 rounded-full mix-blend-multiply filter blur-3xl opacity-40 -translate-y-1/2 translate-x-1/3"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 translate-y-1/3 -translate-x-1/4"></div>

    <!-- Header: Logo + Judul -->
    <div class="relative sm:mx-auto sm:w-full sm:max-w-md z-10">
        <!-- Logo Bulat Besar -->
        <a href="/" class="flex flex-col items-center mb-8 group cursor-pointer">
            <div class="animate-float">
                <div class="w-28 h-28 rounded-full bg-white shadow-2xl border-4 border-white overflow-hidden ring-4 ring-emerald-100 group-hover:ring-emerald-300 transition-all duration-300">
                    <img 
                        src="/images/logo.png" 
                        alt="Logo Prasta Solusi Indonesia" 
                        class="w-full h-full object-contain p-2"
                        onerror="this.style.display='none'; this.parentElement.classList.add('flex','items-center','justify-center','bg-corporate'); this.insertAdjacentHTML('afterend', '<span class=\'text-3xl font-black text-white\'>P</span>');"
                    >
                </div>
            </div>
            <div class="mt-4 text-center">
                <h1 class="font-extrabold text-2xl text-corporate tracking-tight leading-tight">Prasta Solusi Indonesia</h1>
                <p class="text-xs text-gold font-semibold tracking-widest uppercase mt-1">Innovation · Technology · Integrity</p>
            </div>
        </a>

        <h2 class="text-center text-2xl font-extrabold text-corporate">
            Masuk ke Portal RCMS
        </h2>
        <p class="mt-2 text-center text-sm text-slate-500">
            Sistem Manajemen Regulasi & Sertifikasi
        </p>
    </div>

    <!-- Form Card -->
    <div class="relative mt-8 sm:mx-auto sm:w-full sm:max-w-md z-10">
        <div class="bg-white py-10 px-6 shadow-2xl rounded-3xl sm:px-10 border border-slate-100">

            {{-- Alert Error (jika login gagal) --}}
            @if ($errors->any())
                <div class="mb-6 flex items-start gap-3 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3.5 rounded-2xl text-sm">
                    <i data-lucide="alert-circle" class="w-5 h-5 mt-0.5 flex-shrink-0 text-rose-500"></i>
                    <div>
                        <p class="font-semibold mb-1">Login Gagal</p>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Alert Success --}}
            @if (session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl text-sm font-medium">
                    <i data-lucide="check-circle-2" class="w-5 h-5 flex-shrink-0"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form class="space-y-5" action="{{ route('login.post') }}" method="POST">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Alamat Email
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
                            value="{{ old('email') }}"
                            class="input-field block w-full pl-11 pr-4 py-3 sm:text-sm border rounded-xl bg-slate-50 border-slate-200 focus:outline-none focus:border-emerald-500 focus:bg-white transition-colors @error('email') border-rose-400 bg-rose-50 @enderror"
                            placeholder="email@perusahaan.com"
                        >
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Password
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="h-5 w-5 text-slate-400"></i>
                        </div>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            autocomplete="current-password" 
                            required 
                            class="input-field block w-full pl-11 pr-12 py-3 sm:text-sm border rounded-xl bg-slate-50 border-slate-200 focus:outline-none focus:border-emerald-500 focus:bg-white transition-colors"
                            placeholder="••••••••"
                        >
                        {{-- Toggle Show/Hide Password --}}
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 transition-colors">
                            <i data-lucide="eye" id="eyeIcon" class="h-5 w-5"></i>
                        </button>
                    </div>
                </div>

                {{-- Remember Me & Lupa Password --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded cursor-pointer">
                        <label for="remember-me" class="text-sm text-slate-600 cursor-pointer select-none">
                            Ingat Saya
                        </label>
                    </div>
                    <a href="#" class="text-sm font-semibold text-emerald-600 hover:text-emerald-500 transition-colors">
                        Lupa password?
                    </a>
                </div>

                {{-- Submit Button --}}
                <div class="pt-1">
                    <button 
                        type="submit" 
                        id="submitBtn"
                        class="w-full flex justify-center items-center gap-2 py-3.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-corporate hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-300 active:scale-[0.98]"
                    >
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        Masuk Sistem
                    </button>
                </div>
            </form>

            {{-- Register Link --}}
            <div class="mt-6 text-center text-sm">
                <span class="text-slate-500">Belum punya akun klien?</span>
                <a href="{{ route('register') }}" class="font-bold text-emerald-600 hover:text-emerald-500 ml-1 transition-colors">Daftar Sekarang</a>
            </div>

            {{-- Info Divider --}}
            <div class="mt-6 pt-6 border-t border-slate-100">
                <p class="text-center text-xs text-slate-400 leading-relaxed">
                    Masuk menggunakan email & password yang terdaftar di sistem RCMS.<br>
                    Hubungi admin jika mengalami kesulitan.
                </p>
            </div>
        </div>

        {{-- Back to Home --}}
        <div class="text-center mt-6">
            <a href="/" class="text-sm text-slate-500 hover:text-corporate font-medium transition-colors inline-flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Halaman Utama
            </a>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Toggle show/hide password
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                eyeIcon.setAttribute('data-lucide', isPassword ? 'eye-off' : 'eye');
                lucide.createIcons();
            });
        }

        // Loading state on submit
        const form = document.querySelector('form');
        const submitBtn = document.getElementById('submitBtn');
        if (form) {
            form.addEventListener('submit', () => {
                submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 22 6.477 22 12h-4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...';
                submitBtn.disabled = true;
            });
        }
    </script>
</body>
</html>
