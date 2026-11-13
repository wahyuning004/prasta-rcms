<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrean Pemeriksaan Dokumen - Document &amp; Licensing Officer</title>
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
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-teal-600 selection:text-white flex h-screen overflow-hidden">

    <!-- Dark Sidebar Officer -->
    @include('officer.partials.sidebar')

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/60">
        
        <!-- Header -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0 relative z-10">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-corporate">Antrean Pemeriksaan Kelengkapan Dokumen</h2>
                    <span class="bg-teal-100 text-teal-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full">Verifikasi Teknis</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Daftar seluruh dokumen berkas persyaratan yang masuk dari mitra dan klien.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('officer.dashboard') }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold flex items-center gap-1.5 transition">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Kembali ke Dasbor</span>
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        @include('officer.partials.alerts')

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8 space-y-6 sidebar-scroll">
            
            <!-- Filter Bar -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <form method="GET" action="{{ route('officer.documents') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <div class="relative flex-1 md:w-72">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="q" value="{{ $docSearch }}" placeholder="Cari no order, nama klien, atau berkas..." class="w-full pl-8 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-teal-500">
                    </div>

                    <select name="status" onchange="this.form.submit()" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:bg-white focus:outline-none focus:border-teal-500 font-medium">
                        <option value="all" {{ $docFilterStatus === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="uploaded" {{ $docFilterStatus === 'uploaded' ? 'selected' : '' }}>Menunggu Review</option>
                        <option value="approved" {{ $docFilterStatus === 'approved' ? 'selected' : '' }}>Disetujui / Lengkap</option>
                        <option value="rejected" {{ $docFilterStatus === 'rejected' ? 'selected' : '' }}>Perlu Revisi</option>
                    </select>

                    <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                        Cari &amp; Filter
                    </button>

                    @if($docSearch || $docFilterStatus !== 'all')
                        <a href="{{ route('officer.documents') }}" class="text-xs text-slate-500 hover:text-rose-600 font-semibold">
                            Reset Filter
                        </a>
                    @endif
                </form>

                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span>Menampilkan <strong>{{ $documents->total() }}</strong> total dokumen</span>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/75 text-slate-400 text-xs font-semibold uppercase tracking-wider">
                                <th class="py-4 px-6">No. Pesanan &amp; Klien</th>
                                <th class="py-4 px-6">Nama &amp; Jenis Dokumen</th>
                                <th class="py-4 px-6">Audit Integritas TTD &amp; Metadata</th>
                                <th class="py-4 px-6">Versi &amp; File</th>
                                <th class="py-4 px-6">Waktu Masuk</th>
                                <th class="py-4 px-6">Status Pemeriksaan</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($documents as $doc)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-xs font-bold bg-slate-900 text-white px-2 py-0.5 rounded-lg">
                                                {{ $doc->order_number ?? '-' }}
                                            </span>
                                            <span class="text-xs font-semibold text-slate-600 truncate max-w-[160px]">
                                                {{ $doc->order?->resolvedService()?->name ?? 'Layanan Perizinan' }}
                                            </span>
                                        </div>
                                        <div class="mt-1">
                                            <p class="font-bold text-corporate text-xs">{{ $doc->client_name ?? 'Klien' }}</p>
                                            <p class="text-[11px] text-slate-500">{{ $doc->company_name ?? '-' }} &bull; {{ $doc->client_phone ?? '-' }}</p>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-corporate text-xs">{{ $doc->document_name ?? $doc->req_document_name ?? 'Dokumen' }}</span>
                                            @if($doc->is_mandatory)
                                                <span class="bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold px-1.5 py-0.5 rounded">Wajib</span>
                                            @else
                                                <span class="bg-slate-100 text-slate-600 text-[10px] font-medium px-1.5 py-0.5 rounded">Opsional</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-slate-500 mt-0.5">
                                            Tipe: {{ $doc->req_document_type ?? $doc->document_type ?? 'Berkas Teknis' }}
                                        </p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-1.5">
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-md {{ $doc->authenticity['badge_class'] ?? 'bg-slate-100 text-slate-700' }}">
                                                @if(($doc->authenticity['verdict_code'] ?? '') === 'GENUINE')
                                                    <i data-lucide="shield-check" class="w-3 h-3 text-teal-600"></i>
                                                @elseif(($doc->authenticity['verdict_code'] ?? '') === 'TAMPERED')
                                                    <i data-lucide="shield-alert" class="w-3 h-3 text-rose-600"></i>
                                                @else
                                                    <i data-lucide="alert-triangle" class="w-3 h-3 text-amber-600"></i>
                                                @endif
                                                <span>{{ $doc->authenticity['overall_score'] ?? 0 }}% {{ ($doc->authenticity['verdict_code'] ?? '') === 'GENUINE' ? 'Asli' : (($doc->authenticity['verdict_code'] ?? '') === 'TAMPERED' ? 'Manipulasi' : 'Perlu Cek') }}</span>
                                            </span>
                                        </div>
                                        <p class="text-[10px] text-slate-500 mt-1 truncate max-w-[170px]" title="{{ $doc->authenticity['signature_audit']['signature_type'] ?? '' }}">
                                            {{ ($doc->authenticity['signature_audit']['has_digital_signature'] ?? false) ? 'TTD Digital / QR' : (($doc->authenticity['signature_audit']['stamp_detected'] ?? false) ? 'Stempel Cap' : 'TTD Manual') }} &bull; {{ ($doc->authenticity['metadata_audit']['status'] ?? '') === 'tampered' ? 'Metadata Diedit' : 'Metadata Bersih' }}
                                        </p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2">
                                            <span class="bg-teal-50 text-teal-700 font-mono text-[11px] font-bold px-2 py-0.5 rounded-md border border-teal-200">
                                                v{{ $doc->version ?? 1 }}
                                            </span>
                                            <span class="text-xs text-slate-500 truncate max-w-[130px]" title="{{ $doc->original_filename }}">
                                                {{ $doc->original_filename ?: 'Berkas' }}
                                            </span>
                                        </div>
                                        @if($doc->file_size)
                                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $doc->file_size }}</p>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-xs text-slate-600">
                                        {{ \Carbon\Carbon::parse($doc->created_at)->translatedFormat('d M Y') }}
                                        <p class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($doc->created_at)->format('H:i') }} WIB</p>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($doc->status === 'approved')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                                Disetujui
                                            </span>
                                        @elseif($doc->status === 'rejected')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                                                Perlu Revisi
                                            </span>
                                            @if($doc->notes)
                                                <p class="text-[11px] text-rose-600 mt-1 italic line-clamp-1 max-w-[200px]" title="{{ $doc->notes }}">
                                                    "{{ $doc->notes }}"
                                                </p>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                                Menunggu Review
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('officer.documents.show', $doc->id) }}" class="p-2 bg-teal-50 hover:bg-teal-100 text-teal-700 rounded-xl transition" title="Tinjau &amp; Audit Keaslian Lengkap">
                                                <i data-lucide="shield-check" class="w-4 h-4"></i>
                                            </a>
                                            <a href="{{ route('officer.documents.download', $doc->id) }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition" title="Unduh Berkas">
                                                <i data-lucide="download" class="w-4 h-4"></i>
                                            </a>
                                            <button type="button" onclick="openVerifyModal('{{ $doc->id }}', '{{ addslashes($doc->document_name ?? $doc->req_document_name ?? 'Dokumen') }}', '{{ $doc->status }}', '{{ addslashes($doc->notes ?? '') }}')" class="px-3 py-1.5 bg-teal-600 hover:bg-teal-500 text-white rounded-xl text-xs font-bold flex items-center gap-1 shadow-sm transition">
                                                <i data-lucide="clipboard-check" class="w-3.5 h-3.5"></i>
                                                <span>Kaji Berkas</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400">
                                        <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                                        <p class="text-sm font-semibold">Tidak ada dokumen yang ditemukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($documents->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $documents->links() }}
                    </div>
                @endif
            </div>

        </div>
    </main>

    <!-- MODAL VERIFIKASI DOKUMEN -->
    <div id="verifyModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto sidebar-scroll">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center">
                        <i data-lucide="clipboard-check" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-corporate text-sm">Verifikasi Pemeriksaan Berkas</h4>
                        <p id="modalDocName" class="text-xs text-slate-400 truncate max-w-[250px]">-</p>
                    </div>
                </div>
                <button type="button" onclick="closeVerifyModal()" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="verifyDocForm" method="POST" action="">
                @csrf
                <div class="space-y-4">
                    <!-- Live Audit Summary Box -->
                    <div id="modalAuditBox" class="p-3 bg-slate-50 border border-slate-200 rounded-2xl space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                <i data-lucide="shield-check" class="w-4 h-4 text-teal-600"></i>
                                <span>Audit Integritas TTD &amp; Metadata</span>
                            </span>
                            <span id="modalAuditScoreBadge" class="px-2 py-0.5 rounded-md font-bold text-[11px] bg-teal-50 text-teal-700 border border-teal-200">
                                Memuat...
                            </span>
                        </div>
                        <div class="space-y-1 text-[11px] text-slate-600">
                            <p id="modalAuditSigStatus" class="flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-teal-500 inline-block"></span>
                                <span class="font-semibold text-slate-700">TTD &amp; Cap:</span> Memindai stempel &amp; tanda tangan...
                            </p>
                            <p id="modalAuditMetaStatus" class="flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-teal-500 inline-block"></span>
                                <span class="font-semibold text-slate-700">Metadata:</span> Memeriksa software pembuat &amp; SHA-256...
                            </p>
                        </div>
                        <div class="pt-1 flex items-center justify-between border-t border-slate-200/60">
                            <span id="modalShaShort" class="font-mono text-[10px] text-slate-400 truncate max-w-[200px]">SHA-256: -</span>
                            <a id="modalFullReviewLink" href="#" target="_blank" class="text-[11px] text-teal-600 font-bold hover:underline flex items-center gap-0.5">
                                <span>Buka Audit Lengkap</span>
                                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Pilihan Keputusan Status -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Keputusan Hasil Pemeriksaan:</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-teal-50/50 has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50/30">
                                <input type="radio" name="status" value="approved" id="statusApproved" class="text-teal-600 focus:ring-teal-500">
                                <span class="text-xs font-bold text-teal-800">Lengkap &amp; Disetujui</span>
                            </label>

                            <label class="flex items-center gap-2 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-rose-50/50 has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50/30">
                                <input type="radio" name="status" value="rejected" id="statusRejected" class="text-rose-600 focus:ring-rose-500">
                                <span class="text-xs font-bold text-rose-800">Perlu Revisi</span>
                            </label>
                        </div>
                    </div>

                    <!-- Template Catatan Perbaikan Cepat -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Template Catatan Cepat:</label>
                        <div class="flex flex-wrap gap-1.5 text-[10px]">
                            <button type="button" onclick="insertDocNote('Stempel cap instansi buram/tidak terbaca. Mohon unggah hasil scan asli berwarna.')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                                Stempel Buram
                            </button>
                            <button type="button" onclick="insertDocNote('QR Code verifikasi tidak valid / tidak mengarah ke portal resmi instansi pemerintah.')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                                QR Anomali
                            </button>
                            <button type="button" onclick="insertDocNote('Terdeteksi modifikasi digital pada metadata file berkas. Mohon kirimkan berkas PDF asli.')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                                Metadata Modifikasi
                            </button>
                            <button type="button" onclick="insertDocNote('Lampiran persyaratan belum lengkap sesuai ketentuan teknis.')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                                Lampiran Kurang
                            </button>
                        </div>
                    </div>

                    <!-- Textarea Catatan -->
                    <div>
                        <label for="modalNotes" class="block text-xs font-bold text-slate-700 mb-1">
                            Catatan Pemeriksaan / Instruksi Revisi:
                        </label>
                        <textarea id="modalNotes" name="notes" rows="3" placeholder="Tulis catatan kelengkapan atau poin perbaikan untuk klien..." class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-teal-500"></textarea>
                        <p class="text-[11px] text-slate-400 mt-1">* Wajib diisi jika status dipilih "Perlu Revisi".</p>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 shrink-0">
                        <button type="button" onclick="closeVerifyModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold rounded-xl shadow-sm transition">
                            Simpan Hasil Verifikasi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function insertDocNote(text) {
            const textarea = document.getElementById('modalNotes');
            if (textarea.value) {
                textarea.value = textarea.value + '\n- ' + text;
            } else {
                textarea.value = '- ' + text;
            }
        }

        function openVerifyModal(docId, docName, status, notes) {
            const form = document.getElementById('verifyDocForm');
            form.action = '{{ url("/officer/dokumen") }}/' + docId + '/verifikasi';
            document.getElementById('modalDocName').textContent = docName;
            document.getElementById('modalNotes').value = notes || '';
            document.getElementById('modalFullReviewLink').href = '{{ url("/officer/dokumen") }}/' + docId;

            if (status === 'approved') {
                document.getElementById('statusApproved').checked = true;
            } else if (status === 'rejected') {
                document.getElementById('statusRejected').checked = true;
            } else {
                document.getElementById('statusApproved').checked = true;
            }

            // Fetch live audit data
            fetch('{{ url("/officer/dokumen") }}/' + docId + '/audit')
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.audit) {
                        const a = data.audit;
                        const badge = document.getElementById('modalAuditScoreBadge');
                        badge.className = 'px-2 py-0.5 rounded-md font-bold text-[11px] ' + (a.badge_class || '');
                        badge.textContent = a.overall_score + '% ' + a.verdict_label;

                        const sig = a.signature_audit || {};
                        document.getElementById('modalAuditSigStatus').innerHTML =
                            '<span class="w-1.5 h-1.5 rounded-full bg-teal-500 inline-block"></span>' +
                            '<span class="font-semibold text-slate-700">TTD & Cap:</span> ' + (sig.signature_type || 'Manual') +
                            (sig.stamp_detected ? ' (Stempel Terdeteksi)' : '');

                        const meta = a.metadata_audit || {};
                        document.getElementById('modalAuditMetaStatus').innerHTML =
                            '<span class="w-1.5 h-1.5 rounded-full bg-teal-500 inline-block"></span>' +
                            '<span class="font-semibold text-slate-700">Metadata:</span> ' +
                            (meta.tampering_detected ? '<strong class="text-rose-600">Terdeteksi ' + meta.detected_editor + '</strong>' : 'Bersih (' + (meta.producer || 'PDF') + ')');

                        if (data.audit.file_info && data.audit.file_info.sha256) {
                            document.getElementById('modalShaShort').textContent = 'SHA-256: ' + data.audit.file_info.sha256.substring(0, 16) + '...';
                        }
                    }
                })
                .catch(() => {});

            document.getElementById('verifyModal').classList.remove('hidden');
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        function closeVerifyModal() {
            document.getElementById('verifyModal').classList.add('hidden');
        }
    </script>
</body>
</html>

