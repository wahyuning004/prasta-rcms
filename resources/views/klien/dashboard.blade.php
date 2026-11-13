<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Klien - IzinYou</title>
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

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/60">
        <!-- Top Header -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-8 shrink-0 relative z-10">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" onclick="toggleKlienSidebar()" class="lg:hidden p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 shrink-0">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h2 class="text-base sm:text-xl font-bold text-corporate truncate">Portal Klien Mitra</h2>
                        <span class="hidden sm:inline-block bg-sky-100 text-sky-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full truncate max-w-[200px]">{{ $user->company_name ?? 'Klien Mitra IzinYou' }}</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5 hidden sm:block">Pantau status izin edar, sertifikasi, pembayaran, dan dokumen kepatuhan industri Anda.</p>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <button type="button" onclick="document.getElementById('twoFactorModal').classList.remove('hidden')" class="hidden xl:flex items-center gap-1.5 px-3 py-1.5 bg-sky-50 hover:bg-sky-100 border border-sky-200 text-sky-800 rounded-xl text-xs font-semibold transition-all">
                    <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-sky-600"></i>
                    <span>2FA WhatsApp Aktif</span>
                </button>

                <a href="{{ route('klien.consultations.index') }}" class="px-3 sm:px-4 py-2 bg-corporate hover:bg-slate-800 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all">
                    <i data-lucide="message-square-plus" class="w-4 h-4 text-sky-400"></i>
                    <span class="hidden sm:inline">Ajukan Konsultasi</span>
                </a>
            </div>
        </header>

        @include('klien.partials.alerts')

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto scroll-smooth p-4 sm:p-8 pt-4">
            <div class="max-w-7xl mx-auto space-y-8" id="overview">

                <!-- 1. Statistik Ringkasan Klien -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-100 shadow-sm hover:border-sky-200 transition-all">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Total Pesanan / Projek</p>
                                <h3 class="text-3xl font-extrabold text-corporate">{{ $stats['ordersCount'] ?? 0 }}</h3>
                            </div>
                            <div class="w-11 h-11 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center">
                                <i data-lucide="folder-kanban" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <a href="{{ route('klien.orders.index') }}" class="text-xs text-sky-700 font-semibold hover:underline">Kelola pesanan &amp; pembayaran &rarr;</a>
                    </div>

                    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-100 shadow-sm hover:border-blue-200 transition-all">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Dokumen Diterima</p>
                                <h3 class="text-3xl font-extrabold text-corporate">{{ $stats['approvedDocsCount'] ?? 0 }}</h3>
                            </div>
                            <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                                <i data-lucide="shield-check" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <p class="text-xs text-blue-700 font-medium">Lolos pemeriksaan tim Legal</p>
                    </div>

                    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-100 shadow-sm hover:border-amber-200 transition-all">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Menunggu / Revisi</p>
                                <h3 class="text-3xl font-extrabold text-corporate">{{ ($stats['pendingDocsCount'] ?? 0) + ($stats['revisionDocsCount'] ?? 0) }}</h3>
                            </div>
                            <div class="w-11 h-11 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                                <i data-lucide="clock" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <p class="text-xs text-amber-700 font-medium">{{ $stats['pendingDocsCount'] ?? 0 }} diperiksa &bull; {{ $stats['revisionDocsCount'] ?? 0 }} perlu revisi</p>
                    </div>

                    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-100 shadow-sm hover:border-purple-200 transition-all">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Sesi Konsultasi</p>
                                <h3 class="text-3xl font-extrabold text-corporate">{{ $stats['consultationsCount'] ?? 0 }}</h3>
                            </div>
                            <div class="w-11 h-11 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                                <i data-lucide="messages-square" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500 mb-1">{{ $stats['consultationsPendingCount'] ?? 0 }} menunggu &bull; {{ $stats['consultationsRespondedCount'] ?? 0 }} dijawab</p>
                        <a href="{{ route('klien.consultations.index') }}" class="text-xs text-purple-700 font-semibold hover:underline">Lihat tanya-jawab konsultan &rarr;</a>
                    </div>
                </div>

                <!-- 2. Section Pelacakan Real-time Projek Saya (Dinamis dari Database) -->
                <div class="bg-white rounded-3xl p-6 lg:p-8 border border-slate-200/80 shadow-sm" id="tracking">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <i data-lucide="git-merge" class="w-6 h-6 text-sky-600"></i>
                                <h3 class="text-lg font-bold text-corporate">Pelacakan Progres Projek &amp; Sertifikasi</h3>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Status dan persentase tahapan pengerjaan layanan yang diperbarui secara real-time dari database.</p>
                        </div>

                        @if($activeOrder)
                            <div class="flex flex-wrap items-center gap-2.5">
                                @php
                                    $projectDropdownList = ($trackableOrders ?? $myOrders)->reject(function ($o) {
                                        return $o->status === 'cancelled'
                                            || ($o->paymentStatusMeta()['code'] ?? '') === 'cancelled'
                                            || strtolower((string) ($o->paymentStatusMeta()['label'] ?? '')) === 'dibatalkan'
                                            || ($o->invoice && strtolower((string) $o->invoice->payment_status) === 'cancelled');
                                    });
                                @endphp
                                @if($projectDropdownList->count() > 1)
                                    <form method="GET" action="{{ route('klien.dashboard') }}">
                                        <select name="order_id" onchange="this.form.submit()" class="px-3 py-1.5 text-xs font-bold bg-slate-50 border border-slate-200 rounded-xl text-corporate">
                                            @foreach($projectDropdownList as $ordOpt)
                                                <option value="{{ $ordOpt->id }}" {{ $ordOpt->id === $activeOrder->id ? 'selected' : '' }}>
                                                    {{ $ordOpt->order_number }} — {{ $ordOpt->resolvedService()?->name ?? 'Layanan' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                @endif
                                <span class="text-xs font-bold font-mono bg-slate-900 text-white px-3 py-1.5 rounded-lg">
                                    {{ $activeOrder->order_number }}
                                </span>
                                <a href="{{ route('klien.orders.show', $activeOrder->id) }}" class="px-3.5 py-1.5 bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold rounded-xl transition">
                                    Buka Detail &amp; Bayar
                                </a>
                            </div>
                        @endif
                    </div>

                    @if($activeOrder)
                        @php $paymentMeta = $activeOrder->paymentStatusMeta(); @endphp
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5 p-4 rounded-2xl bg-slate-50 border border-slate-200/70">
                            <div>
                                <p class="text-xs text-slate-500">Layanan: <strong class="text-corporate">{{ $activeOrder->resolvedService()?->name ?? 'Perizinan & Sertifikasi' }}</strong></p>
                                <div class="flex flex-wrap items-center gap-2 mt-1 text-xs">
                                    <span>Tahap Saat Ini: <strong class="text-sky-700">{{ $activeOrder->current_stage ?? 'Pesanan Diterima' }}</strong></span>
                                    <span>&bull;</span>
                                    <span>Pembayaran: <strong class="text-corporate">{{ $paymentMeta['label'] }}</strong></span>
                                </div>
                            </div>
                            <div class="w-full sm:w-56">
                                <div class="flex justify-between text-xs font-bold mb-1">
                                    <span class="text-slate-500">Progres Keseluruhan</span>
                                    <span class="text-sky-600">{{ (int) ($activeOrder->progress_percentage ?? 15) }}%</span>
                                </div>
                                <div class="w-full h-2.5 bg-slate-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-sky-600 rounded-full" style="width: {{ min(100, (int) ($activeOrder->progress_percentage ?? 15)) }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- 6-Stage Stepper Progress Bar -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 mb-6">
                            @foreach($timeline as $step)
                                <div class="p-3.5 rounded-2xl border
                                    @if($step['status'] === 'completed') bg-emerald-50/60 border-emerald-200
                                    @elseif($step['status'] === 'in_progress') bg-sky-50 border-sky-300
                                    @else bg-slate-50 border-slate-200/70 @endif">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold
                                            @if($step['status'] === 'completed') bg-emerald-600 text-white
                                            @elseif($step['status'] === 'in_progress') bg-sky-600 text-white
                                            @else bg-slate-200 text-slate-500 @endif">
                                            {{ $step['order'] }}
                                        </span>
                                        <span class="text-[10px] font-bold {{ $step['status'] === 'completed' ? 'text-emerald-700' : ($step['status'] === 'in_progress' ? 'text-sky-700' : 'text-slate-400') }}">
                                            {{ $step['status'] === 'completed' ? 'Selesai' : ($step['status'] === 'in_progress' ? 'Berjalan' : 'Menunggu') }}
                                        </span>
                                    </div>
                                    <p class="text-xs font-bold text-corporate leading-snug">{{ $step['name'] }}</p>
                                    <p class="text-[10px] text-slate-500 mt-1 line-clamp-2">{{ $step['description'] }}</p>
                                    @if($step['updated_at'])
                                        <p class="text-[10px] text-slate-400 mt-1.5">{{ $step['updated_at'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Riwayat Tahapan -->
                        <div class="space-y-2.5">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Riwayat Pembaruan Terbaru:</h4>
                            @forelse ($myTrackings->sortByDesc('id')->take(5) as $track)
                                <div class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <div class="w-7 h-7 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center shrink-0 mt-0.5">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-0.5">
                                            <span class="font-bold text-corporate text-xs">{{ $track->stage_name }}</span>
                                            <span class="text-[11px] text-slate-400 font-medium">
                                                {{ \Carbon\Carbon::parse($track->date_recorded)->format('d F Y, H:i') }} WIB
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 leading-relaxed">{{ $track->description }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 italic">Belum ada pembaruan tahapan untuk projek ini.</p>
                            @endforelse
                        </div>
                    @else
                        <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            <i data-lucide="inbox" class="w-10 h-10 text-slate-400 mx-auto mb-2"></i>
                            <h4 class="text-sm font-bold text-corporate">Belum Ada Projek Berjalan</h4>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Anda belum memiliki pesanan aktif. Silakan pilih layanan melalui menu <a href="{{ route('klien.services.index') }}" class="text-sky-600 font-semibold hover:underline">Katalog Layanan</a> untuk memulai pengajuan sertifikasi.</p>
                        </div>
                    @endif
                </div>

                <!-- 3. Section Dokumen Persyaratan Klien -->
                <div class="bg-white rounded-3xl p-6 lg:p-8 border border-slate-200/80 shadow-sm" id="dokumen">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <div class="flex items-center gap-2">
                                <i data-lucide="folder-check" class="w-6 h-6 text-sky-600"></i>
                                <h3 class="text-lg font-bold text-corporate">Dokumen &amp; Berkas Perizinan Anda</h3>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Ringkasan berkas persyaratan yang diunggah beserta versi dan status pemeriksaannya.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold bg-sky-50 text-sky-700 px-3 py-1.5 rounded-full">
                                {{ $myDocuments->count() }} Berkas Terunggah
                            </span>
                            @if($activeOrder)
                                <a href="{{ route('klien.orders.show', $activeOrder->id) }}#dokumen" class="px-3.5 py-1.5 bg-corporate hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                                    Unggah / Kelola Dokumen
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 text-xs font-semibold uppercase tracking-wider">
                                    <th class="py-3 px-4">Nama &amp; Jenis Dokumen</th>
                                    <th class="py-3 px-4">Nomor Pesanan</th>
                                    <th class="py-3 px-4">Versi &amp; Tanggal Unggah</th>
                                    <th class="py-3 px-4">Status Verifikasi</th>
                                    <th class="py-3 px-4">Catatan / Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @forelse ($myDocuments as $doc)
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="py-4 px-4 text-xs">
                                            <div class="font-bold text-corporate">{{ $doc->document_name ?: ($doc->requirement?->document_name ?? basename($doc->file_path)) }}</div>
                                            <div class="text-[11px] text-slate-500">{{ $doc->document_type ?: ($doc->requirement?->document_type ?? 'Persyaratan Teknis') }} &bull; {{ $doc->original_filename ?: basename($doc->file_path) }}</div>
                                        </td>
                                        <td class="py-4 px-4 font-mono text-xs text-slate-600">
                                            {{ $doc->order?->order_number ?? '-' }}
                                        </td>
                                        <td class="py-4 px-4 text-xs text-slate-500">
                                            <span class="font-mono font-bold text-sky-700">v{{ $doc->version ?? 1 }}</span> &bull;
                                            {{ $doc->updated_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($doc->status === 'approved')
                                                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-full text-xs font-semibold">
                                                    <i data-lucide="check" class="w-3.5 h-3.5"></i> Diterima
                                                </span>
                                            @elseif($doc->status === 'rejected')
                                                <span class="inline-flex items-center gap-1 bg-rose-50 text-rose-700 border border-rose-200 px-2.5 py-1 rounded-full text-xs font-semibold">
                                                    <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> Perlu Revisi
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-full text-xs font-semibold">
                                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i> Menunggu Pemeriksaan
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-xs">
                                            <p class="text-slate-600 max-w-[240px] truncate" title="{{ $doc->notes }}">{{ $doc->notes ?? '-' }}</p>
                                            <div class="flex items-center gap-3 mt-1">
                                                @if($doc->order && $doc->order->isCancelled())
                                                    <span class="text-rose-600 font-semibold text-[11px]">Pesanan Dibatalkan</span>
                                                @else
                                                    <a href="{{ route('documents.current.download', $doc->id) }}" class="text-sky-600 font-bold hover:underline">Unduh</a>
                                                    @if($doc->order_id)
                                                        <a href="{{ route('klien.orders.show', $doc->order_id) }}#dokumen" class="text-corporate font-bold hover:underline">Kelola Versi</a>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-8 text-slate-400 text-sm">
                                            Belum ada dokumen yang diunggah.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Modal Indikator Verifikasi 2 Langkah -->
    <div id="twoFactorModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative text-center">
            <button type="button" onclick="document.getElementById('twoFactorModal').classList.add('hidden')" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            <div class="w-16 h-16 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center mx-auto mb-4 border border-sky-200 shadow-sm">
                <i data-lucide="shield-check" class="w-8 h-8"></i>
            </div>
            <h3 class="text-lg font-bold text-corporate mb-1">Verifikasi 2-Langkah WhatsApp</h3>
            <p class="text-xs font-semibold text-sky-600 bg-sky-50 py-1 px-3 rounded-full inline-block mb-4 border border-sky-100">
                Status: Aktif &amp; Terlindungi
            </p>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/60 text-left text-xs space-y-2 mb-5">
                <div class="flex justify-between text-slate-600">
                    <span>Nomor WhatsApp Terhubung:</span>
                    <strong class="text-corporate font-mono">{{ $user->phone ?? '-' }}</strong>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Metode Otentikasi:</span>
                    <strong class="text-corporate">One-Time Password (OTP) 6 Digit</strong>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('twoFactorModal').classList.add('hidden')" class="w-full py-2.5 bg-corporate hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-md transition-all">
                Mengerti &amp; Tutup
            </button>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
