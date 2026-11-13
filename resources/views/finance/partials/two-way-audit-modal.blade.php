<!-- MODAL DETAIL AUDIT KEASLIAN DUA ARAH (TWO-WAY VERIFICATION MODAL) -->
<div id="twoWayAuditModal" class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-3xl w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative max-h-[92vh] overflow-y-auto sidebar-scroll">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-slate-900 text-amber-400 flex items-center justify-center font-bold shadow-md">
                    <i data-lucide="shield-check" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-extrabold text-corporate text-lg">Audit Validasi Bukti Pembayaran Dua Arah</h3>
                        <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">Two-Way Forensics</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5" id="auditModalSubtitle">Pesanan &amp; Verifikasi Keaslian</p>
                </div>
            </div>
            <button type="button" onclick="closeTwoWayAuditModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Skor Keseluruhan & Rekomendasi Sistem -->
        <div id="auditVerdictBanner" class="p-5 rounded-2xl mb-6 border transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div id="auditScoreCircle" class="w-14 h-14 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner shrink-0">
                    --%
                </div>
                <div>
                    <h4 class="font-bold text-sm" id="auditVerdictLabel">Memuat Hasil Analisis...</h4>
                    <p class="text-xs mt-0.5 opacity-90" id="auditRecommendation">Sistem sedang memeriksa integritas dokumen bukti transfer.</p>
                </div>
            </div>
            <div class="shrink-0 flex items-center gap-2">
                <span id="auditBadgeStatus" class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-xs">
                    Audit Selesai
                </span>
            </div>
        </div>

        <!-- Grid Dua Arah (Two-Way Checks) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
            
            <!-- KOLOM 1: Pengecekan Arah Pertama (Sistem ke Dokumen) -->
            <div class="bg-slate-50/90 rounded-2xl p-5 border border-slate-200/80 space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-200">
                    <i data-lucide="database" class="w-4 h-4 text-sky-600"></i>
                    <h4 class="font-bold text-corporate text-xs uppercase tracking-wider">Arah 1: Sistem ke Dokumen</h4>
                </div>

                <!-- Item 1: Nominal -->
                <div class="p-3 bg-white rounded-xl border border-slate-200/60 shadow-xs">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-xs font-bold text-slate-700">1. Kesesuaian Nominal Tagihan</span>
                        <span id="chkAmountBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full">--</span>
                    </div>
                    <p class="text-[11px] text-slate-500" id="chkAmountMsg">Memeriksa nominal...</p>
                    <div class="mt-2 text-[11px] font-mono flex justify-between text-slate-600 bg-slate-50 p-2 rounded-lg">
                        <span>Tagihan: <strong id="chkAmountExpected">Rp 0</strong></span>
                        <span>Transfer: <strong id="chkAmountActual">Rp 0</strong></span>
                    </div>
                </div>

                <!-- Item 2: Nomor Referensi -->
                <div class="p-3 bg-white rounded-xl border border-slate-200/60 shadow-xs">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-xs font-bold text-slate-700">2. No. Referensi &amp; Pesanan</span>
                        <span id="chkRefBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full">--</span>
                    </div>
                    <p class="text-[11px] text-slate-500" id="chkRefMsg">Memeriksa referensi pesanan...</p>
                </div>

                <!-- Item 3: Rekening Tujuan Resmi -->
                <div class="p-3 bg-white rounded-xl border border-slate-200/60 shadow-xs">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-xs font-bold text-slate-700">3. Rekening / Kanal Resmi</span>
                        <span id="chkDestBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full">--</span>
                    </div>
                    <p class="text-[11px] text-slate-500" id="chkDestMsg">Memeriksa rekening tujuan...</p>
                </div>

                <!-- Item 4: Waktu Transaksi -->
                <div class="p-3 bg-white rounded-xl border border-slate-200/60 shadow-xs">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-xs font-bold text-slate-700">4. Validitas Waktu Transaksi</span>
                        <span id="chkTimingBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full">--</span>
                    </div>
                    <p class="text-[11px] text-slate-500" id="chkTimingMsg">Memeriksa rentang waktu pembayaran...</p>
                </div>
            </div>

            <!-- KOLOM 2: Pengecekan Arah Kedua (Validasi Internal / Format & Forensik) -->
            <div class="bg-slate-50/90 rounded-2xl p-5 border border-slate-200/80 space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-200">
                    <i data-lucide="binary" class="w-4 h-4 text-amber-600"></i>
                    <h4 class="font-bold text-corporate text-xs uppercase tracking-wider">Arah 2: Format &amp; Forensik File</h4>
                </div>

                <!-- Item 1: Deteksi Editor Manipulation -->
                <div class="p-3 bg-white rounded-xl border border-slate-200/60 shadow-xs">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-xs font-bold text-slate-700">1. Forensik Software Editor</span>
                        <span id="chkManipBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full">--</span>
                    </div>
                    <p class="text-[11px] text-slate-500" id="chkManipMsg">Memeriksa jejak software editing...</p>
                </div>

                <!-- Item 2: Format Cetak Bank Resmi -->
                <div class="p-3 bg-white rounded-xl border border-slate-200/60 shadow-xs">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-xs font-bold text-slate-700">2. Template Cetak Bank Resmi</span>
                        <span id="chkBankSigBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full">--</span>
                    </div>
                    <p class="text-[11px] text-slate-500" id="chkBankSigMsg">Memeriksa karakteristik bank slip...</p>
                </div>

                <!-- Item 3: Integritas File & Magic Bytes -->
                <div class="p-3 bg-white rounded-xl border border-slate-200/60 shadow-xs">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-xs font-bold text-slate-700">3. Integritas Header Biner</span>
                        <span id="chkIntegrityBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full">--</span>
                    </div>
                    <p class="text-[11px] text-slate-500" id="chkIntegrityMsg">Memvalidasi magic bytes file...</p>
                </div>

                <!-- Item 4: Anti-Duplikasi Replay Protection -->
                <div class="p-3 bg-white rounded-xl border border-slate-200/60 shadow-xs">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-xs font-bold text-slate-700">4. Anti-Duplikasi Hash Bukti</span>
                        <span id="chkDupBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full">--</span>
                    </div>
                    <p class="text-[11px] text-slate-500" id="chkDupMsg">Mendeteksi penggunaan ulang berkas...</p>
                </div>
            </div>

        </div>

        <!-- Tombol Aksi Langsung Modal -->
        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <a id="auditProofLink" href="#" target="_blank" class="text-xs font-bold text-blue-600 hover:underline flex items-center gap-1.5">
                <i data-lucide="external-link" class="w-4 h-4"></i>
                <span>Buka File Bukti Asli di Tab Baru</span>
            </a>

            <div class="flex items-center gap-2">
                <form id="modalApproveForm" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="status" value="success">
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        <span>Verifikasi 'Lunas'</span>
                    </button>
                </form>

                <button type="button" id="modalRejectBtn" onclick="triggerRejectFromAudit()" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5">
                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                    <span>Tolak Pembayaran</span>
                </button>
            </div>
        </div>

    </div>
</div>

<!-- MODAL TOLAK PEMBAYARAN -->
<div id="rejectPaymentModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 relative">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <div>
                <h3 class="font-bold text-corporate text-base">Tolak Bukti Pembayaran</h3>
                <p class="text-xs text-slate-400" id="rejectOrderLabel">Order</p>
            </div>
            <button type="button" onclick="document.getElementById('rejectPaymentModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="rejectPaymentForm" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="status" value="failed">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Penolakan Pembayaran *</label>
                <textarea id="rejectionReasonInput" name="rejection_reason" rows="3" required placeholder="Cth: Nominal transfer tidak sesuai tagihan atau gambar bukti buram." class="w-full text-xs p-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-rose-500"></textarea>
                <p class="text-[11px] text-slate-400 mt-1">Alasan penolakan ini otomatis tersimpan ke catatan audit dan disampaikan ke portal pelanggan.</p>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('rejectPaymentModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-500">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-xl shadow-md">Kirim Penolakan</button>
            </div>
        </form>
    </div>
</div>

<script>
    window.paymentAudits = window.paymentAudits || {};

    let currentActivePaymentId = null;
    let currentSuggestedRejection = '';
    let currentOrderNumber = '';

    function openTwoWayAuditModal(paymentId, orderNumber) {
        currentActivePaymentId = paymentId;
        currentOrderNumber = orderNumber || '';
        const data = window.paymentAudits[paymentId];

        if (!data) {
            fetch('/finance/payment/' + paymentId + '/audit')
                .then(res => res.json())
                .then(json => {
                    window.paymentAudits[paymentId] = json;
                    populateAuditModal(paymentId, json);
                })
                .catch(() => alert('Gagal memuat data audit verifikasi dua arah.'));
            return;
        }

        populateAuditModal(paymentId, data);
    }

    function populateAuditModal(paymentId, data) {
        currentSuggestedRejection = data.suggested_rejection || '';

        document.getElementById('auditModalSubtitle').innerText = 'ID Pembayaran #' + paymentId + ' • Hasil Verifikasi Forensik 2-Arah';
        document.getElementById('auditProofLink').href = '/pembayaran/' + paymentId + '/bukti';
        document.getElementById('modalApproveForm').action = '/finance/payment/' + paymentId + '/verify';

        // Overall Score & Banner Styling
        const scoreCircle = document.getElementById('auditScoreCircle');
        const banner = document.getElementById('auditVerdictBanner');
        const verdictLabel = document.getElementById('auditVerdictLabel');
        const recommendation = document.getElementById('auditRecommendation');
        const badgeStatus = document.getElementById('auditBadgeStatus');

        scoreCircle.innerText = data.overall_score + '%';
        verdictLabel.innerText = data.verdict_label;
        recommendation.innerText = data.recommendation;

        if (data.badge_color === 'emerald') {
            banner.className = 'p-5 rounded-2xl mb-6 border bg-emerald-50/90 border-emerald-200 text-emerald-950 flex flex-col sm:flex-row sm:items-center justify-between gap-4';
            scoreCircle.className = 'w-14 h-14 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner shrink-0 bg-emerald-600 text-white';
            badgeStatus.className = 'px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-600 text-white';
            badgeStatus.innerText = 'Lulus Otentik';
        } else if (data.badge_color === 'amber') {
            banner.className = 'p-5 rounded-2xl mb-6 border bg-amber-50/90 border-amber-200 text-amber-950 flex flex-col sm:flex-row sm:items-center justify-between gap-4';
            scoreCircle.className = 'w-14 h-14 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner shrink-0 bg-amber-600 text-white';
            badgeStatus.className = 'px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-600 text-white';
            badgeStatus.innerText = 'Selisih Tagihan';
        } else {
            banner.className = 'p-5 rounded-2xl mb-6 border bg-rose-50/90 border-rose-200 text-rose-950 flex flex-col sm:flex-row sm:items-center justify-between gap-4';
            scoreCircle.className = 'w-14 h-14 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner shrink-0 bg-rose-600 text-white';
            badgeStatus.className = 'px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-600 text-white';
            badgeStatus.innerText = 'Peringatan Anomali';
        }

        // Direction 1 Fields
        const d1 = data.direction_1;
        document.getElementById('chkAmountMsg').innerText = d1.amount.message;
        document.getElementById('chkAmountExpected').innerText = 'Rp ' + Number(d1.amount.expected).toLocaleString('id-ID');
        document.getElementById('chkAmountActual').innerText = 'Rp ' + Number(d1.amount.actual).toLocaleString('id-ID');
        setBadge('chkAmountBadge', d1.amount.status === 'valid', d1.amount.status.toUpperCase());

        document.getElementById('chkRefMsg').innerText = d1.reference.message;
        setBadge('chkRefBadge', d1.reference.status === 'matched', d1.reference.status.toUpperCase());

        document.getElementById('chkDestMsg').innerText = d1.destination.message;
        setBadge('chkDestBadge', d1.destination.status === 'valid', d1.destination.status.toUpperCase());

        document.getElementById('chkTimingMsg').innerText = d1.timing.message;
        setBadge('chkTimingBadge', d1.timing.status === 'valid', d1.timing.status.toUpperCase());

        // Direction 2 Fields
        const d2 = data.direction_2;
        document.getElementById('chkManipMsg').innerText = d2.manipulation.message;
        setBadge('chkManipBadge', !d2.manipulation.detected, d2.manipulation.detected ? 'TERDETEKSI' : 'BERSIH');

        document.getElementById('chkBankSigMsg').innerText = d2.bank_signature.message;
        setBadge('chkBankSigBadge', d2.bank_signature.matched, d2.bank_signature.matched ? 'COCOK' : 'STANDAR');

        document.getElementById('chkIntegrityMsg').innerText = d2.integrity.message;
        setBadge('chkIntegrityBadge', d2.integrity.valid, d2.integrity.valid ? 'VALID' : 'RUSAK');

        document.getElementById('chkDupMsg').innerText = d2.anti_replay.message;
        setBadge('chkDupBadge', !d2.anti_replay.is_duplicate, d2.anti_replay.is_duplicate ? 'DUPLIKAT' : 'UNIK');

        document.getElementById('twoWayAuditModal').classList.remove('hidden');
        if (window.lucide) {
            lucide.createIcons();
        }
    }

    function setBadge(elementId, isSuccess, text) {
        const el = document.getElementById(elementId);
        if (!el) return;
        el.innerText = text;
        if (isSuccess) {
            el.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800';
        } else {
            el.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800';
        }
    }

    function closeTwoWayAuditModal() {
        document.getElementById('twoWayAuditModal').classList.add('hidden');
    }

    function triggerRejectFromAudit() {
        closeTwoWayAuditModal();
        openRejectPaymentModal(currentActivePaymentId, currentOrderNumber, currentSuggestedRejection);
    }

    function openRejectPaymentModal(paymentId, orderNumber, defaultReason) {
        currentActivePaymentId = paymentId;
        currentOrderNumber = orderNumber || '';
        document.getElementById('rejectOrderLabel').innerText = 'Nomor Pesanan: ' + (orderNumber || '-');
        document.getElementById('rejectPaymentForm').action = '/finance/payment/' + paymentId + '/verify';
        
        const reasonInput = document.getElementById('rejectionReasonInput');
        if (defaultReason && defaultReason.trim() !== '') {
            reasonInput.value = defaultReason;
        } else {
            reasonInput.value = '';
        }

        document.getElementById('rejectPaymentModal').classList.remove('hidden');
    }
</script>

