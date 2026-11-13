<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket &amp; Live Chat - Marketing IzinYou</title>
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
                    <h2 class="text-xl font-bold text-corporate">Tiket, Konsultasi &amp; Live Chat</h2>
                    <span class="bg-indigo-100 text-indigo-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full">Sistem IZINYOU</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Kelola pertanyaan konsultasi gratis, inquiry formulir layanan, live chat WhatsApp, dan tanggapan resmi.</p>
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
        <div class="flex-1 overflow-y-auto p-8 pt-4 space-y-6" id="konsultasi">

            <!-- Filter Bar -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-1">Sumber:</span>
                    <a href="{{ route('marketing.tickets', array_filter(['status' => $statusFilter])) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ empty($sourceFilter) || $sourceFilter === 'all' ? 'bg-corporate text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua ({{ $stats['totalConsultations'] }})
                    </a>
                    <a href="{{ route('marketing.tickets', array_filter(['source' => 'inquiry', 'status' => $statusFilter])) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $sourceFilter === 'inquiry' ? 'bg-sky-600 text-white shadow-sm' : 'bg-sky-50 text-sky-700 hover:bg-sky-100' }}">
                        Inquiry / Tamu ({{ $stats['serviceInquiryCount'] }})
                    </a>
                    <a href="{{ route('marketing.tickets', array_filter(['source' => 'consultation', 'status' => $statusFilter])) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $sourceFilter === 'consultation' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100' }}">
                        Konsultasi Klien ({{ $stats['directConsultationCount'] }})
                    </a>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-1">Status:</span>
                    <a href="{{ route('marketing.tickets', array_filter(['source' => $sourceFilter])) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ empty($statusFilter) || $statusFilter === 'all' ? 'bg-slate-800 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua Status
                    </a>
                    <a href="{{ route('marketing.tickets', array_filter(['source' => $sourceFilter, 'status' => 'pending'])) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $statusFilter === 'pending' ? 'bg-amber-600 text-white shadow-sm' : 'bg-amber-50 text-amber-800 hover:bg-amber-100' }}">
                        Menunggu ({{ $stats['pendingConsultations'] }})
                    </a>
                    <a href="{{ route('marketing.tickets', array_filter(['source' => $sourceFilter, 'status' => 'responded'])) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $statusFilter === 'responded' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100' }}">
                        Sudah Dijawab ({{ $stats['respondedConsultations'] }})
                    </a>
                </div>
            </div>

            <!-- Queue List -->
            <div class="space-y-4">
                @forelse ($consultations as $item)
                    @php
                        $isInquiry = $item->user_id === null || str_starts_with((string) $item->subject, 'Konsultasi Layanan:');
                        $isResponded = $item->status === 'responded';
                        $rawDigits = preg_replace('/[^0-9]/', '', (string) ($item->phone ?? '')) ?? '';
                        if (str_starts_with($rawDigits, '0')) {
                            $waDigits = '62'.substr($rawDigits, 1);
                        } else {
                            $waDigits = $rawDigits;
                        }
                        $createdLabel = $item->created_at
                            ? \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y, H:i')
                            : '-';
                    @endphp
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm hover:border-indigo-200 transition-all">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="space-y-2 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    @if($isInquiry)
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-sky-100 text-sky-800">Inquiry Formulir / Tamu</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-800">Konsultasi Klien</span>
                                    @endif

                                    @if($isResponded)
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Sudah Dijawab</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">Menunggu Tanggapan</span>
                                    @endif

                                    <span class="text-xs text-slate-400 font-medium">&bull; {{ $createdLabel }}</span>
                                </div>

                                <h3 class="text-base font-bold text-corporate">{{ $item->subject ?: 'Konsultasi Umum' }}</h3>

                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-600">
                                    <span class="font-semibold text-slate-800">
                                        {{ $item->client_name ?: 'Calon Pelanggan' }}
                                        @if(!empty($item->company_name))
                                            <span class="text-slate-400 font-normal">({{ $item->company_name }})</span>
                                        @endif
                                    </span>
                                    @if(!empty($item->email))
                                        <span class="inline-flex items-center gap-1">
                                            <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-400"></i>
                                            {{ $item->email }}
                                        </span>
                                    @endif
                                    @if(!empty($item->phone))
                                        <span class="inline-flex items-center gap-1">
                                            <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i>
                                            {{ $item->phone }}
                                        </span>
                                    @endif
                                </div>

                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $item->message }}</div>

                                @if(!empty($item->response))
                                    <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 text-xs text-emerald-900 space-y-1">
                                        <p class="font-bold uppercase tracking-wider text-[10px] text-emerald-700">Tanggapan Resmi Tim Marketing</p>
                                        <p class="text-sm text-emerald-950 whitespace-pre-line">{{ $item->response }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-wrap lg:flex-col items-stretch lg:items-end gap-2 shrink-0">
                                @if($waDigits !== '')
                                    <a href="https://wa.me/{{ $waDigits }}?text={{ urlencode('Halo '.($item->client_name ?: 'Bapak/Ibu').', kami dari tim Marketing & Client Relations IzinYou menindaklanjuti pertanyaan Anda terkait: '.($item->subject ?: 'Layanan Perizinan')) }}"
                                       target="_blank" rel="noopener noreferrer"
                                       class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold inline-flex items-center justify-center gap-1.5 shadow-sm transition-all">
                                        <i data-lucide="message-circle" class="w-4 h-4"></i>
                                        <span>Live Chat WA</span>
                                    </a>
                                @endif

                                <button type="button"
                                        data-id="{{ $item->id }}"
                                        data-client="{{ $item->client_name ?: 'Calon Pelanggan' }}"
                                        data-subject="{{ $item->subject ?: 'Konsultasi Umum' }}"
                                        data-status="{{ $item->status }}"
                                        data-response="{{ $item->response ?? '' }}"
                                        onclick="openReplyModal(this)"
                                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold inline-flex items-center justify-center gap-1.5 shadow-sm transition-all">
                                    <i data-lucide="reply" class="w-4 h-4"></i>
                                    <span>{{ $isResponded ? 'Edit Tanggapan / Status' : 'Jawab / Ubah Status' }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-3xl p-12 border border-slate-200/80 shadow-sm text-center">
                        <div class="w-14 h-14 bg-slate-100 text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="inbox" class="w-7 h-7"></i>
                        </div>
                        <h3 class="text-sm font-bold text-corporate mb-1">Belum Ada Tiket yang Cocok</h3>
                        <p class="text-xs text-slate-500">Belum ada pertanyaan konsultasi gratis maupun inquiry formulir pada filter ini.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </main>

    <!-- Reply & Status Modal -->
    <div id="replyModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex items-start justify-between pb-4 mb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-lg font-bold text-corporate">Jawab &amp; Perbarui Status Tiket</h3>
                    <p id="replyModalSubtitle" class="text-xs text-slate-500 mt-0.5">-</p>
                </div>
                <button type="button" onclick="closeReplyModal()" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="replyModalForm"
                  method="POST"
                  action="#"
                  data-action-template="{{ route('marketing.consultation.reply', ['id' => '__ID__']) }}"
                  class="space-y-4">
                @csrf

                <div>
                    <label for="replyStatus" class="block text-xs font-bold text-slate-700 mb-1.5">Status Penanganan</label>
                    <select id="replyStatus" name="status" onchange="syncReplyRequirement()"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                        <option value="responded">Sudah Dijawab (Selesai)</option>
                        <option value="pending">Menunggu / Diproses</option>
                    </select>
                </div>

                <div>
                    <label for="replyResponse" class="block text-xs font-bold text-slate-700 mb-1.5">Isi Tanggapan Resmi</label>
                    <textarea id="replyResponse" name="response" rows="5"
                              placeholder="Tuliskan penjelasan, estimasi persyaratan, atau arahan tindak lanjut untuk pelanggan..."
                              class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></textarea>
                    <p id="replyResponseHint" class="text-[11px] text-slate-500 mt-1">Tanggapan resmi wajib diisi ketika menandai tiket sudah dijawab.</p>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="closeReplyModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-sm">
                        Simpan Tanggapan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function syncReplyRequirement() {
            const statusSelect = document.getElementById('replyStatus');
            const responseField = document.getElementById('replyResponse');
            const hint = document.getElementById('replyResponseHint');
            if (!statusSelect || !responseField || !hint) {
                return;
            }

            if (statusSelect.value === 'responded') {
                responseField.required = true;
                hint.textContent = 'Wajib diisi ketika status dipilih Sudah Dijawab.';
            } else {
                responseField.required = false;
                hint.textContent = 'Opsional saat status dikembalikan ke Menunggu / Diproses.';
            }
        }

        function openReplyModal(button) {
            const modal = document.getElementById('replyModal');
            const form = document.getElementById('replyModalForm');
            const subtitle = document.getElementById('replyModalSubtitle');
            const statusSelect = document.getElementById('replyStatus');
            const responseField = document.getElementById('replyResponse');

            const id = button.dataset.id || '';
            const client = button.dataset.client || 'Calon Pelanggan';
            const subject = button.dataset.subject || 'Konsultasi';
            const currentStatus = button.dataset.status || 'pending';
            const currentResponse = button.dataset.response || '';

            const template = form.dataset.actionTemplate || '';
            form.action = template.replace('__ID__', id);
            subtitle.textContent = client + ' — ' + subject;
            statusSelect.value = currentStatus === 'responded' ? 'responded' : 'responded';
            if (currentStatus === 'pending' && currentResponse === '') {
                statusSelect.value = 'responded';
            } else {
                statusSelect.value = currentStatus;
            }
            responseField.value = currentResponse;

            syncReplyRequirement();
            modal.classList.remove('hidden');
        }

        function closeReplyModal() {
            document.getElementById('replyModal').classList.add('hidden');
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeReplyModal();
            }
        });
    </script>
</body>
</html>

