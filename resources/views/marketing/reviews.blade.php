<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderasi Ulasan Klien - Marketing IzinYou</title>
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
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-indigo-600 selection:text-white flex h-screen overflow-hidden">

    @include('marketing.partials.sidebar')

    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/60">

        <!-- Header -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0 relative z-10">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-corporate">Moderasi Ulasan Klien</h2>
                    <span class="bg-indigo-100 text-indigo-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full">Sistem IZINYOU</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Tinjau ulasan layanan, setujui untuk tampil sebagai testimoni halaman utama, atau tolak ulasan yang tidak sesuai.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('marketing.dashboard') }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Ringkasan Dashboard</span>
                </a>
            </div>
        </header>

        @include('marketing.partials.alerts')

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8 pt-4 space-y-6" id="reviews">

            <!-- Filter Bar -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-1">Filter Status:</span>
                    <a href="{{ route('marketing.reviews.index') }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ empty($reviewStatusFilter) || $reviewStatusFilter === 'all' ? 'bg-corporate text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua ({{ $stats['totalReviews'] }})
                    </a>
                    <a href="{{ route('marketing.reviews.index', ['review_status' => 'pending']) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $reviewStatusFilter === 'pending' ? 'bg-amber-600 text-white shadow-sm' : 'bg-amber-50 text-amber-800 hover:bg-amber-100' }}">
                        Pending ({{ $stats['pendingReviews'] }})
                    </a>
                    <a href="{{ route('marketing.reviews.index', ['review_status' => 'published']) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ in_array($reviewStatusFilter, ['published', 'approved'], true) ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100' }}">
                        Disetujui ({{ $stats['publishedReviews'] }})
                    </a>
                    <a href="{{ route('marketing.reviews.index', ['review_status' => 'rejected']) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $reviewStatusFilter === 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'bg-rose-50 text-rose-800 hover:bg-rose-100' }}">
                        Ditolak ({{ $stats['rejectedReviews'] }})
                    </a>
                </div>

                <div class="text-xs font-bold text-amber-700 bg-amber-50 px-3 py-1.5 rounded-xl border border-amber-200/80">
                    Rata-rata Rating Publik: ★ {{ number_format((float) $stats['avgRating'], 1) }} / 5.0
                </div>
            </div>

            <!-- Reviews Table / List -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-4 px-6">Pelanggan &amp; Layanan</th>
                                <th class="py-4 px-4">Rating &amp; Tanggal</th>
                                <th class="py-4 px-4">Isi Ulasan</th>
                                <th class="py-4 px-4">Status</th>
                                <th class="py-4 px-6 text-right">Aksi Moderasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse ($reviews as $rev)
                                @php
                                    $serviceName = $rev->service->name
                                        ?? $rev->order?->service?->name
                                        ?? $rev->order?->items?->first()?->service?->name
                                        ?? 'Layanan Perizinan';
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition-colors align-top">
                                    <td class="py-4 px-6 min-w-[210px]">
                                        <p class="font-bold text-corporate">{{ $rev->user->name ?? 'Pelanggan' }}</p>
                                        @if(!empty($rev->user->company_name))
                                            <p class="text-xs text-slate-500">{{ $rev->user->company_name }}</p>
                                        @endif
                                        <p class="text-xs font-semibold text-indigo-700 mt-1">{{ $serviceName }}</p>
                                        @if(!empty($rev->order?->order_number))
                                            <p class="text-[11px] font-mono text-slate-400 mt-0.5">{{ $rev->order->order_number }}</p>
                                        @endif
                                    </td>

                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <div class="flex items-center gap-0.5 text-amber-500 font-bold text-sm">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="{{ $i <= (int) $rev->rating ? 'text-amber-500' : 'text-slate-200' }}">★</span>
                                            @endfor
                                            <span class="text-xs text-slate-600 ml-1">({{ $rev->rating }}/5)</span>
                                        </div>
                                        <p class="text-xs text-slate-400 mt-1">
                                            {{ $rev->created_at ? $rev->created_at->translatedFormat('d M Y') : '-' }}
                                        </p>
                                    </td>

                                    <td class="py-4 px-4 max-w-md">
                                        <p class="text-slate-700 text-xs leading-relaxed">{{ $rev->comment }}</p>
                                    </td>

                                    <td class="py-4 px-4 whitespace-nowrap">
                                        @if($rev->status === 'published')
                                            <span class="inline-block px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                                Disetujui (Publik)
                                            </span>
                                            @if($rev->is_featured)
                                                <span class="block w-fit mt-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800">
                                                    Testimoni Unggulan
                                                </span>
                                            @endif
                                        @elseif($rev->status === 'rejected')
                                            <span class="inline-block px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">
                                                Ditolak
                                            </span>
                                        @else
                                            <span class="inline-block px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                                Pending (Menunggu Review)
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <div class="inline-flex flex-wrap justify-end gap-1.5">
                                            @if($rev->status !== 'published')
                                                <form action="{{ route('marketing.reviews.moderate', $rev->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="published">
                                                    <input type="hidden" name="is_featured" value="0">
                                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all">
                                                        Setujui
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('marketing.reviews.moderate', $rev->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="published">
                                                    <input type="hidden" name="is_featured" value="{{ $rev->is_featured ? 0 : 1 }}">
                                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold transition-all">
                                                        {{ $rev->is_featured ? 'Lepas Unggulan' : 'Unggulkan' }}
                                                    </button>
                                                </form>

                                                <form action="{{ route('marketing.reviews.moderate', $rev->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="pending">
                                                    <input type="hidden" name="is_featured" value="0">
                                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-bold transition-all">
                                                        Pending
                                                    </button>
                                                </form>
                                            @endif

                                            @if($rev->status !== 'rejected')
                                                <form action="{{ route('marketing.reviews.moderate', $rev->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="rejected">
                                                    <input type="hidden" name="is_featured" value="0">
                                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition-all">
                                                        Tolak
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 px-6 text-center">
                                        <p class="text-sm font-bold text-corporate mb-1">Belum Ada Ulasan Klien</p>
                                        <p class="text-xs text-slate-500">Tidak ada ulasan pelanggan yang sesuai dengan filter status yang dipilih.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>

