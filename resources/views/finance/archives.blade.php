<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsip Privat Server - Finance IzinYou</title>
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
                    <h2 class="text-xl font-bold text-corporate">Manajemen Direktori Privat Server</h2>
                    <span class="bg-sky-100 text-sky-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full">Server Archives</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Penyimpanan terisolasi dan terenkripsi untuk dokumen resmi, SK Kementerian, dan berkas perusahaan.</p>
            </div>

            <div class="flex items-center gap-3">
                <button onclick="document.getElementById('uploadArchiveModal').classList.remove('hidden')" class="px-3.5 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all">
                    <i data-lucide="upload" class="w-4 h-4"></i>
                    <span>Simpan Dokumen ke Server</span>
                </button>
            </div>
        </header>

        <!-- Flash Messages -->
        @include('finance.partials.alerts')

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8 pt-4 space-y-6">

            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                    <div>
                        <h3 class="text-base font-bold text-corporate">Berkas Dokumen Privat Server</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Dikelola dan diarsipkan oleh tim Finance &amp; Administration</p>
                    </div>
                    <span class="text-xs text-slate-500 font-semibold bg-slate-100 px-3 py-1 rounded-full">
                        {{ count($archives) }} Dokumen Tersimpan
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($archives as $arc)
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/70 flex items-start justify-between gap-4 hover:shadow-sm transition-all">
                            <div class="flex items-start gap-3.5">
                                <div class="w-11 h-11 rounded-xl bg-corporate text-amber-400 flex items-center justify-center font-bold shrink-0 shadow-sm">
                                    <i data-lucide="file-check" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full uppercase tracking-wider">{{ $arc->category }}</span>
                                    <h5 class="font-bold text-corporate text-sm mt-1.5">{{ $arc->title }}</h5>
                                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $arc->notes }}</p>
                                    <p class="text-[10px] text-slate-400 font-mono mt-2 flex items-center gap-2">
                                        <span>Ukuran: {{ $arc->file_size ?? '2.1 MB' }}</span>
                                        <span>&bull;</span>
                                        <span>Pengunggah: {{ $arc->uploaded_by ?? 'Finance' }}</span>
                                        <span>&bull;</span>
                                        <span>{{ date('d M Y', strtotime($arc->created_at)) }}</span>
                                    </p>
                                </div>
                            </div>

                            <form action="{{ route('finance.archives.destroy', $arc->id) }}" method="POST" onsubmit="return confirm('Hapus arsip dokumen server ini?')" class="shrink-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" title="Hapus Arsip">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-12 text-slate-400 text-sm">
                            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                <i data-lucide="folder" class="w-6 h-6"></i>
                            </div>
                            <p class="font-bold text-slate-600">Belum ada dokumen privat yang diarsipkan</p>
                            <p class="text-xs text-slate-400 mt-1">Klik tombol 'Simpan Dokumen ke Server' di pojok kanan atas untuk menambah arsip.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </main>

    <!-- Modal Upload Arsip Privat Server -->
    <div id="uploadArchiveModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 relative">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold">
                        <i data-lucide="server" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-corporate text-base">Arsipkan Dokumen Server</h3>
                        <p class="text-xs text-slate-400">Penyimpanan privat server terenkripsi</p>
                    </div>
                </div>
                <button onclick="document.getElementById('uploadArchiveModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('finance.archives.upload') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Dokumen Resmi</label>
                    <input type="text" name="title" required placeholder="Cth: SK Penetapan Izin Edar Alkes 2026" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Dokumen</label>
                    <select name="category" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-500">
                        <option value="Sertifikat Resmi">Sertifikat Resmi Terakreditasi</option>
                        <option value="Legalitas Perusahaan">Legalitas &amp; SK Perusahaan</option>
                        <option value="Kajian Regulasi">Kajian &amp; Notulensi Audit Teknis</option>
                        <option value="Berita Acara">Berita Acara Kajian (BAK)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Dokumen</label>
                    <textarea name="notes" rows="2" placeholder="Catatan keabsahan dokumen..." class="w-full text-xs p-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-500"></textarea>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('uploadArchiveModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-500">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs rounded-xl shadow-md transition-all">
                        Simpan ke Server
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

