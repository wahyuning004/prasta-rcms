<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linimasa Stepper Projek - Finance IzinYou</title>
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
                    <h2 class="text-xl font-bold text-corporate">Linimasa Stepper Pengerjaan Projek</h2>
                    <span class="bg-blue-100 text-blue-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full">Milestone Tracking</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Perbarui kemajuan tahapan projek secara bertahap dan transparan untuk klien mitra.</p>
            </div>

            <div class="flex items-center gap-3">
                <button onclick="document.getElementById('addTrackingModal').classList.remove('hidden')" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Tambah Milestone</span>
                </button>
            </div>
        </header>

        <!-- Flash Messages -->
        @include('finance.partials.alerts')

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8 pt-4 space-y-6">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Form Tambah Linimasa Cepat -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm lg:col-span-1">
                    <div class="flex items-center gap-2 pb-4 border-b border-slate-100 mb-4">
                        <i data-lucide="edit-3" class="w-5 h-5 text-blue-600"></i>
                        <h4 class="font-bold text-corporate text-sm">Update Linimasa Cepat</h4>
                    </div>

                    <form action="{{ route('finance.tracking.add') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Projek Klien</label>
                            <select name="order_id" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-blue-500">
                                @foreach($orders as $ord)
                                    @if(($ord->status ?? '') !== 'cancelled')
                                        <option value="{{ $ord->id }}">{{ $ord->order_number }} - {{ $ord->company_name ?? $ord->client_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tahapan Proyek (Milestone)</label>
                            <select name="stage_name" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-blue-500">
                                @foreach($stages as $st)
                                    <option value="{{ $st['code'] }}">{{ $st['order'] }}. {{ $st['name'] }} ({{ $st['percentage'] }}%)</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Status Tahapan</label>
                            <select name="stage_status" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-blue-500">
                                <option value="in_progress">Sedang Berjalan (In Progress)</option>
                                <option value="completed" selected>Selesai (Completed)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Progres</label>
                            <textarea name="description" rows="3" required placeholder="Detail kemajuan yang dapat dipantau oleh klien mitra..." class="w-full text-xs p-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-blue-500"></textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-md transition-all">
                            Simpan Progres Linimasa
                        </button>
                    </form>
                </div>

                <!-- Riwayat Update Linimasa Terkini -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm lg:col-span-2">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                        <div class="flex items-center gap-2">
                            <i data-lucide="history" class="w-5 h-5 text-corporate"></i>
                            <h4 class="font-bold text-corporate text-sm">Riwayat Update Linimasa Stepper</h4>
                        </div>
                        <span class="text-xs text-slate-400 font-semibold">{{ count($projectTrackings) }} Log Tersimpan</span>
                    </div>

                    <div class="space-y-3">
                        @forelse($projectTrackings as $track)
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-start gap-3.5 hover:bg-slate-100/60 transition-colors">
                                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold shrink-0 mt-0.5">
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h5 class="font-bold text-corporate text-xs">{{ $track->stage_name }}</h5>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ date('d M Y, H:i', strtotime($track->date_recorded)) }}</span>
                                    </div>
                                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ $track->description }}</p>
                                    <p class="text-[10px] text-slate-400 mt-1.5 font-mono font-bold">
                                        {{ $track->order_number }} &bull; {{ $track->company_name ?? 'Klien Mitra' }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-10 text-center">Belum ada linimasa yang dicatat.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Modal Tambah Tahapan Linimasa Stepper -->
    <div id="addTrackingModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 relative">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                        <i data-lucide="git-commit" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-corporate text-base">Tambah Linimasa Projek</h3>
                        <p class="text-xs text-slate-400">Stepper real-time untuk portal klien</p>
                    </div>
                </div>
                <button onclick="document.getElementById('addTrackingModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('finance.tracking.add') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Pesanan Projek</label>
                    <select name="order_id" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-blue-500">
                        @foreach($orders as $ord)
                            @if(($ord->status ?? '') !== 'cancelled')
                                <option value="{{ $ord->id }}">{{ $ord->order_number }} - {{ $ord->company_name ?? $ord->client_name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tahapan Proyek (Milestone)</label>
                    <select name="stage_name" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-blue-500">
                        @foreach($stages as $st)
                            <option value="{{ $st['code'] }}">{{ $st['order'] }}. {{ $st['name'] }} ({{ $st['percentage'] }}%)</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Status Tahapan</label>
                    <select name="stage_status" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-blue-500">
                        <option value="in_progress">Sedang Berjalan (In Progress)</option>
                        <option value="completed" selected>Selesai (Completed)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Progres</label>
                    <textarea name="description" rows="3" required placeholder="Jelaskan kemajuan pengerjaan..." class="w-full text-xs p-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-blue-500"></textarea>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('addTrackingModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-500">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-md transition-all">
                        Simpan Milestone
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>

