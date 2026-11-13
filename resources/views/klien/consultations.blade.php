<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konsultasi Perizinan - Portal Klien IzinYou</title>
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
        <!-- Top Header -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-8 shrink-0 relative z-10">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" onclick="toggleKlienSidebar()" class="lg:hidden p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 shrink-0">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h2 class="text-base sm:text-xl font-bold text-corporate truncate">Konsultasi Perizinan &amp; Sertifikasi</h2>
                        <span class="hidden sm:inline-block bg-sky-100 text-sky-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full truncate max-w-[200px]">{{ $user->company_name ?? 'Klien Mitra IzinYou' }}</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5 hidden sm:block">Ajukan pertanyaan baru, pantau status konsultasi, dan lihat tanggapan resmi tim IzinYou.</p>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <a href="{{ route('klien.dashboard') }}" class="px-3 sm:px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Kembali ke Dashboard</span>
                </a>
            </div>
        </header>

        @include('klien.partials.alerts')

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto scroll-smooth p-4 sm:p-8 pt-4">
            <div class="max-w-7xl mx-auto space-y-6">

                <!-- Ringkasan Status Konsultasi -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Total Sesi Konsultasi</p>
                        <h3 class="text-2xl font-extrabold text-corporate">{{ $consultationStats['total'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-white rounded-2xl p-5 border border-amber-100 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wider text-amber-600 mb-1">Menunggu Jawaban</p>
                        <h3 class="text-2xl font-extrabold text-amber-700">{{ $consultationStats['pending'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-white rounded-2xl p-5 border border-sky-100 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wider text-sky-600 mb-1">Sudah Dijawab</p>
                        <h3 class="text-2xl font-extrabold text-sky-700">{{ $consultationStats['responded'] ?? 0 }}</h3>
                    </div>
                </div>

                <!-- Form & Riwayat Konsultasi Perizinan -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" id="konsultasi">
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm lg:col-span-1 h-fit">
                        <div class="flex items-center gap-2 mb-3">
                            <i data-lucide="help-circle" class="w-5 h-5 text-sky-600"></i>
                            <h3 class="text-base font-bold text-corporate">Ajukan Pertanyaan Perizinan &amp; Sertifikasi</h3>
                        </div>
                        <p class="text-xs text-slate-500 mb-5 leading-relaxed">
                            Punya kendala mengenai izin edar, sertifikasi halal, atau uji lab? Tanyakan langsung ke tim konsultan teknis kami.
                        </p>

                        <form action="{{ route('klien.consultation.submit') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="subject" class="block text-xs font-semibold text-slate-700 mb-1.5">Topik / Subjek Pertanyaan</label>
                                <input id="subject" type="text" name="subject" value="{{ old('subject') }}" required placeholder="Contoh: Syarat Izin Edar Alkes Elektromedis" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-sky-500 focus:bg-white transition-colors">
                                @error('subject')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="message" class="block text-xs font-semibold text-slate-700 mb-1.5">Detail Pertanyaan</label>
                                <textarea id="message" name="message" rows="5" required placeholder="Jelaskan jenis produk, fasilitas, atau kendala perizinan yang sedang dihadapi..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:outline-none focus:border-sky-500 focus:bg-white transition-colors">{{ old('message') }}</textarea>
                                @error('message')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <button type="submit" class="w-full py-2.5 px-4 bg-corporate hover:bg-slate-800 text-white font-bold rounded-xl text-xs flex items-center justify-center gap-2 transition-all shadow-md">
                                <i data-lucide="send" class="w-4 h-4 text-sky-400"></i>
                                <span>Kirim Pertanyaan Konsultasi</span>
                            </button>
                        </form>
                    </div>

                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm lg:col-span-2">
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-2">
                                <i data-lucide="messages-square" class="w-5 h-5 text-sky-600"></i>
                                <h3 class="text-base font-bold text-corporate">Riwayat Tanya-Jawab Konsultasi</h3>
                            </div>
                            <span class="text-xs text-slate-400">Total: {{ $myConsultations->count() }} Sesi</span>
                        </div>

                        <div class="space-y-4">
                            @forelse ($myConsultations as $item)
                                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-100 space-y-3">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-slate-200/60 pb-2">
                                        <h4 class="font-bold text-corporate text-xs">{{ $item->subject }}</h4>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[11px] text-slate-400">
                                                {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }}
                                            </span>
                                            @if($item->status === 'responded')
                                                <span class="bg-sky-100 text-sky-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Dijawab</span>
                                            @else
                                                <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Menunggu</span>
                                            @endif
                                        </div>
                                    </div>

                                    <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line">{{ $item->message }}</p>

                                    @if($item->response)
                                        <div class="bg-sky-50/70 p-3 rounded-xl border border-sky-100 text-xs text-sky-900 leading-relaxed">
                                            <div class="flex items-center gap-1.5 font-bold text-sky-800 mb-1">
                                                <i data-lucide="badge-check" class="w-4 h-4 text-sky-600"></i>
                                                <span>Tanggapan Tim Konsultan IzinYou:</span>
                                            </div>
                                            <p class="text-sky-950 whitespace-pre-line">{{ $item->response }}</p>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-10 text-slate-400 text-sm">
                                    Belum ada pertanyaan konsultasi yang Anda ajukan.
                                </div>
                            @endforelse
                        </div>
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

