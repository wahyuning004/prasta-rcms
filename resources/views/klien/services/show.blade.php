<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $service->name }} - Detail Layanan Portal Klien</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon-192.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
        <!-- Top Header Portal Klien -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-8 shrink-0 relative z-10">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" onclick="toggleKlienSidebar()" class="lg:hidden p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 shrink-0">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h2 class="text-base sm:text-xl font-bold text-corporate truncate">Detail Layanan Perizinan</h2>
                        <span class="hidden sm:inline-block bg-sky-100 text-sky-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full shrink-0">
                            {{ $service->category?->name ?? 'Perizinan & Regulasi' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5 truncate">{{ $service->name }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('klien.services.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Kembali ke Katalog</span>
                </a>
                <a href="{{ route('klien.checkout.show', $service->slug) }}" class="px-3.5 sm:px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all">
                    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                    <span>Checkout / Pesan Layanan</span>
                </a>
            </div>
        </header>

        @include('klien.partials.alerts')

        @php
            $effectivePrice = $service->getEffectivePrice();
            $jutaVal = round($effectivePrice / 1000000, 1);
            $kajianVal = round($jutaVal * 0.75, 1);
            $fullTuntasVal = round($jutaVal * 1.4, 1);
            $estimationLabel = $service->timeline_estimation ?: ($service->estimation_time ?: '1-3 Bulan');
            $additionalReqInfo = trim((string) ($service->requirements_info ?: $service->requirements_summary));
        @endphp

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto scroll-smooth p-4 sm:p-8 pt-5">
            <div class="max-w-7xl mx-auto space-y-6">

                <!-- Breadcrumb Internal Portal Klien -->
                <nav class="flex items-center gap-2 text-xs text-slate-500">
                    <a href="{{ route('klien.dashboard') }}" class="hover:text-sky-600 font-medium transition-colors">Dashboard</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                    <a href="{{ route('klien.services.index') }}" class="hover:text-sky-600 font-medium transition-colors">Katalog Layanan</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span class="text-sky-700 font-bold truncate">{{ $service->name }}</span>
                </nav>

                <!-- Ringkasan Utama Layanan & CTA Checkout -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    <!-- Kiri: Informasi Utama & Tahapan -->
                    <div class="lg:col-span-8 bg-corporate text-white rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                                {{ $service->category?->name ?? 'Perizinan & Regulasi' }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-800 text-slate-200 border border-slate-700">
                                <i data-lucide="clock" class="w-3.5 h-3.5 text-sky-400"></i>
                                Estimasi: {{ $estimationLabel }}
                            </span>
                            @if($publishedReviews->isNotEmpty())
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-400/30">
                                    ★ {{ number_format((float) $avgRating, 1) }} ({{ $publishedReviews->count() }} Ulasan)
                                </span>
                            @endif
                        </div>

                        <div>
                            <h1 class="text-2xl sm:text-3xl font-black text-white leading-tight tracking-tight mb-3">
                                {{ $service->name }}
                            </h1>
                            <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                                {{ $service->description ?: 'Layanan konsultasi dan pendampingan menyeluruh oleh tim Regulatory Specialist CV Prasta Solusi Indonesia mulai dari penyiapan berkas teknis hingga izin resmi terbit.' }}
                            </p>
                        </div>

                        <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-4 sm:p-5">
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-3 text-xs font-bold text-slate-300">
                                <span class="flex items-center gap-1.5 uppercase tracking-wider text-sky-400 text-[11px]">
                                    <i data-lucide="git-commit" class="w-4 h-4"></i>
                                    Alur Tahapan Pengerjaan Layanan
                                </span>
                                <span class="text-slate-400">Estimasi Selesai: <strong class="text-white">{{ $estimationLabel }}</strong></span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-center">
                                <div class="bg-slate-900/90 rounded-xl p-3 border border-slate-700">
                                    <span class="text-[10px] font-bold text-sky-400 block mb-0.5">TAHAP 1</span>
                                    <p class="text-xs font-semibold text-white">Audit Berkas</p>
                                </div>
                                <div class="bg-slate-900/90 rounded-xl p-3 border border-slate-700">
                                    <span class="text-[10px] font-bold text-sky-400 block mb-0.5">TAHAP 2</span>
                                    <p class="text-xs font-semibold text-white">Penyiapan SOP</p>
                                </div>
                                <div class="bg-slate-900/90 rounded-xl p-3 border border-slate-700">
                                    <span class="text-[10px] font-bold text-sky-400 block mb-0.5">TAHAP 3</span>
                                    <p class="text-xs font-semibold text-white">Submit &amp; Evaluasi</p>
                                </div>
                                <div class="bg-sky-950/50 rounded-xl p-3 border border-sky-500/40">
                                    <span class="text-[10px] font-bold text-sky-300 block mb-0.5">TAHAP 4</span>
                                    <p class="text-xs font-bold text-sky-300">Sertifikat Terbit</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kanan: Estimasi Biaya & Tombol Aksi -->
                    <div class="lg:col-span-4 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-5">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Biaya Layanan</span>
                            <h3 class="text-3xl font-black text-corporate tracking-tight">
                                {{ $service->formatted_price }}
                            </h3>
                            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                                Termasuk pendampingan teknis, pemeriksaan berkas persyaratan, serta pemantauan progres proyek di Portal Klien.
                            </p>
                        </div>

                        <div class="grid grid-cols-3 gap-2 text-center p-2.5 rounded-2xl bg-slate-50 border border-slate-200/70">
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase block">Kajian</span>
                                <strong class="text-xs text-corporate">{{ $kajianVal }} Jt</strong>
                            </div>
                            <div class="border-x border-slate-200 px-1">
                                <span class="text-[9px] font-bold text-sky-600 uppercase block">Pendampingan</span>
                                <strong class="text-xs text-sky-700">{{ $jutaVal }} Jt</strong>
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-emerald-600 uppercase block">Full Tuntas</span>
                                <strong class="text-xs text-emerald-700">{{ $fullTuntasVal }} Jt</strong>
                            </div>
                        </div>

                        <div class="space-y-2.5">
                            <a href="{{ route('klien.checkout.show', $service->slug) }}"
                               class="w-full py-3 px-4 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-sky-600/20 flex items-center justify-center gap-2 transition">
                                <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                                <span>Checkout / Pesan Layanan</span>
                            </a>

                            <a href="{{ route('klien.services.index') }}"
                               class="w-full py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-corporate font-bold text-xs rounded-xl flex items-center justify-center gap-2 transition">
                                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                                <span>Kembali ke Katalog</span>
                            </a>

                            <a href="{{ route('klien.consultations.index') }}"
                               class="w-full py-2.5 px-4 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 font-bold text-xs rounded-xl flex items-center justify-center gap-2 transition">
                                <i data-lucide="message-square-plus" class="w-4 h-4 text-amber-600"></i>
                                <span>Ajukan Konsultasi Terkait</span>
                            </a>
                        </div>

                        <div class="pt-4 border-t border-slate-100 space-y-2 text-xs text-slate-600">
                            <div class="flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4 text-sky-600 shrink-0"></i>
                                <span>Perjanjian Kerjasama (SPK) &amp; Invoice Resmi</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4 text-sky-600 shrink-0"></i>
                                <span>Garansi Pendampingan hingga Izin Terbit</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4 text-sky-600 shrink-0"></i>
                                <span>Kerahasiaan Dokumen Terjamin (NDA)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Persyaratan Dokumen & Informasi Tambahan dari Database -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100">
                        <div>
                            <h3 class="text-lg font-bold text-corporate flex items-center gap-2">
                                <i data-lucide="folder-check" class="w-5 h-5 text-sky-600"></i>
                                Persyaratan Dokumen &amp; Informasi Teknis
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Daftar berkas legalitas dan dokumen teknis yang disiapkan saat pengajuan layanan.</p>
                        </div>
                        <span class="text-xs font-bold bg-sky-50 text-sky-700 px-3 py-1 rounded-full w-fit">
                            {{ $mandatoryRequirements->count() + $optionalRequirements->count() }} Item Persyaratan
                        </span>
                    </div>

                    @if($additionalReqInfo !== '')
                        <div class="p-4 rounded-2xl bg-sky-50/70 border border-sky-200/80 text-xs text-slate-700 leading-relaxed space-y-1">
                            <p class="font-bold text-sky-900 uppercase tracking-wider text-[10px]">Ringkasan Persyaratan &amp; Catatan Tambahan</p>
                            <p class="whitespace-pre-line">{{ $additionalReqInfo }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Wajib -->
                        <div class="space-y-3">
                            <h4 class="text-xs font-extrabold uppercase tracking-wider text-corporate flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] bg-rose-100 text-rose-700">Wajib</span>
                                <span>Dokumen Persyaratan Utama ({{ $mandatoryRequirements->count() }})</span>
                            </h4>

                            @forelse($mandatoryRequirements as $req)
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-xs font-bold text-corporate">{{ $req->document_name }}</p>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-white border border-slate-200 text-slate-600 shrink-0">
                                            {{ $req->document_type ?: 'Wajib' }}
                                        </span>
                                    </div>
                                    @if(!empty($req->description))
                                        <p class="text-[11px] text-slate-500 leading-relaxed">{{ $req->description }}</p>
                                    @endif
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 italic">Persyaratan dokumen utama akan diinformasikan setelah checkout.</p>
                            @endforelse
                        </div>

                        <!-- Opsional -->
                        <div class="space-y-3">
                            <h4 class="text-xs font-extrabold uppercase tracking-wider text-corporate flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] bg-slate-200 text-slate-700">Opsional</span>
                                <span>Dokumen Pendukung ({{ $optionalRequirements->count() }})</span>
                            </h4>

                            @forelse($optionalRequirements as $req)
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-xs font-bold text-corporate">{{ $req->document_name }}</p>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-white border border-slate-200 text-slate-600 shrink-0">
                                            {{ $req->document_type ?: 'Opsional' }}
                                        </span>
                                    </div>
                                    @if(!empty($req->description))
                                        <p class="text-[11px] text-slate-500 leading-relaxed">{{ $req->description }}</p>
                                    @endif
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 italic">Tidak ada dokumen opsional tambahan yang diwajibkan.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Ulasan Klien Terpublikasi -->
                @if($publishedReviews->isNotEmpty())
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="text-base font-bold text-corporate flex items-center gap-2">
                                <i data-lucide="star" class="w-5 h-5 text-amber-500"></i>
                                Ulasan Klien Untuk Layanan Ini ({{ $publishedReviews->count() }})
                            </h3>
                            <span class="text-xs font-bold text-amber-700 bg-amber-50 px-3 py-1 rounded-full">
                                ★ {{ number_format((float) $avgRating, 1) }} / 5.0
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($publishedReviews as $rev)
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2 text-xs">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-bold text-corporate">{{ $rev->user->name ?? 'Klien IzinYou' }}</p>
                                            @if(!empty($rev->user->company_name))
                                                <p class="text-[11px] text-slate-500">{{ $rev->user->company_name }}</p>
                                            @endif
                                        </div>
                                        <span class="text-amber-500 font-bold">★ {{ $rev->rating }}/5</span>
                                    </div>
                                    <p class="text-slate-600 leading-relaxed">{{ $rev->comment }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Layanan Terkait Lainnya di Dalam Portal Klien -->
                @if($relatedServices->isNotEmpty())
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-4">
                        <h3 class="text-base font-bold text-corporate">Layanan Terkait Lainnya</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($relatedServices as $rel)
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 flex flex-col justify-between space-y-3">
                                    <div>
                                        <span class="text-[10px] font-bold text-sky-700 bg-sky-100 px-2 py-0.5 rounded-full">
                                            {{ $rel->category?->name ?? 'Perizinan' }}
                                        </span>
                                        <h4 class="text-xs font-bold text-corporate mt-2 line-clamp-2">{{ $rel->name }}</h4>
                                        <p class="text-xs font-extrabold text-sky-700 mt-1">{{ $rel->formatted_price }}</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <a href="{{ route('klien.services.show', $rel->id) }}" class="py-2 px-3 bg-white hover:bg-slate-100 text-corporate border border-slate-200 rounded-xl text-[11px] font-bold text-center transition">
                                            Detail
                                        </a>
                                        <a href="{{ route('klien.checkout.show', $rel->slug) }}" class="py-2 px-3 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-[11px] font-bold text-center transition">
                                            Checkout
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>

