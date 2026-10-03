<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Klien - Prasta Solusi Indonesia</title>
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
        .bg-pattern {
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-emerald-500 selection:text-white relative min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-slate-200 via-slate-50 to-slate-50 z-0"></div>
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-[400px] h-[400px] bg-emerald-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-[300px] h-[300px] bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
    
    <div class="relative sm:mx-auto sm:w-full sm:max-w-2xl z-10">
        <a href="/" class="flex justify-center items-center gap-3 mb-6 group cursor-pointer hover:scale-105 transition-transform">
            <div class="w-12 h-12 rounded-xl bg-corporate flex items-center justify-center text-white shadow-lg">
                <i data-lucide="shield-check" class="w-7 h-7 text-gold"></i>
            </div>
            <h1 class="font-extrabold text-2xl text-corporate tracking-tight">Prasta Solusi</h1>
        </a>
        <h2 class="mt-2 text-center text-3xl font-extrabold text-corporate">
            Buat Akun Klien Baru
        </h2>
        <p class="mt-2 text-center text-sm text-slate-600 mb-8">
            Daftarkan perusahaan Anda untuk mendapatkan akses ke layanan kepatuhan kami.
        </p>

        <div class="bg-white py-10 px-6 shadow-2xl rounded-3xl sm:px-10 border border-slate-100">
            <!-- Simulated Form Submission using JS -->
            <form class="space-y-6" action="/verify-email" method="GET" onsubmit="alert('Simulasi Registrasi: Akun berhasil dibuat! Silakan verifikasi email Anda.'); return true;">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700">Nama Lengkap (PIC)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="user" class="h-5 w-5 text-slate-400"></i>
                            </div>
                            <input id="name" name="name" type="text" required class="focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 sm:text-sm border-slate-300 rounded-xl py-3 bg-slate-50 border transition-colors" placeholder="Cth: Budi Santoso">
                        </div>
                    </div>

                    <div>
                        <label for="company" class="block text-sm font-semibold text-slate-700">Nama Perusahaan</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="building" class="h-5 w-5 text-slate-400"></i>
                            </div>
                            <input id="company" name="company" type="text" required class="focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 sm:text-sm border-slate-300 rounded-xl py-3 bg-slate-50 border transition-colors" placeholder="Cth: PT Makmur Jaya">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700">Alamat Email</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="mail" class="h-5 w-5 text-slate-400"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required class="focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 sm:text-sm border-slate-300 rounded-xl py-3 bg-slate-50 border transition-colors" placeholder="email@perusahaan.com">
                        </div>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-semibold text-slate-700">Nomor Telepon / WhatsApp</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="phone" class="h-5 w-5 text-slate-400"></i>
                            </div>
                            <input id="phone" name="phone" type="tel" required class="focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 sm:text-sm border-slate-300 rounded-xl py-3 bg-slate-50 border transition-colors" placeholder="0812XXXXXX">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="lock" class="h-5 w-5 text-slate-400"></i>
                            </div>
                            <input id="password" name="password" type="password" required class="focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 sm:text-sm border-slate-300 rounded-xl py-3 bg-slate-50 border transition-colors" placeholder="••••••••">
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">Konfirmasi Password</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="shield-check" class="h-5 w-5 text-slate-400"></i>
                            </div>
                            <input id="password_confirmation" name="password_confirmation" type="password" required class="focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 sm:text-sm border-slate-300 rounded-xl py-3 bg-slate-50 border transition-colors" placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="terms" name="terms" type="checkbox" required class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded cursor-pointer">
                    <label for="terms" class="ml-2 block text-sm text-slate-700 cursor-pointer">
                        Saya menyetujui <a href="#" class="text-emerald-600 font-bold hover:underline">Syarat & Ketentuan</a> serta <a href="#" class="text-emerald-600 font-bold hover:underline">Kebijakan Privasi</a>.
                    </label>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-300">
                        Daftar Akun Baru
                    </button>
                </div>
            </form>
            
            <div class="mt-6 pt-6 border-t border-slate-200">
                <p class="text-center text-sm text-slate-600">
                    Sudah memiliki akun?
                    <a href="/login" class="font-bold text-corporate hover:text-emerald-600 transition-colors ml-1">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
