<!-- Modal Pusat Bantuan & Panduan Pengguna -->
<div id="helpCenterModal" onclick="if (event.target === this) closeHelpCenterModal()" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                    <i data-lucide="help-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-corporate text-base">Pusat Bantuan &amp; Panduan Pengguna</h3>
                    <p class="text-xs text-slate-400">FAQ, Alur Pemesanan, Voucher, Pembayaran, Dokumen &amp; Progres Proyek</p>
                </div>
            </div>
            <button type="button" onclick="closeHelpCenterModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="mb-4 shrink-0">
            <div class="relative">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" id="faqSearchInput" onkeyup="filterFaq()" placeholder="Cari pertanyaan, panduan pesanan, voucher, pembayaran, dokumen..." class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-sky-500">
            </div>
        </div>

        <div class="flex-1 overflow-y-auto space-y-3 pr-1 sidebar-scroll text-xs">
            <div class="p-4 rounded-2xl bg-sky-50 border border-sky-100 mb-2">
                <h4 class="font-bold text-sky-900 text-xs mb-2 flex items-center gap-1.5">
                    <i data-lucide="git-merge" class="w-4 h-4 text-sky-600"></i> Alur Pengajuan Layanan Perizinan:
                </h4>
                <ol class="list-decimal list-inside space-y-1 text-sky-800 text-[11px] leading-relaxed">
                    <li><strong>Pilih Layanan &amp; Checkout:</strong> Pilih layanan di katalog Dashboard, gunakan kode voucher promo, dan konfirmasi pesanan.</li>
                    <li><strong>Unduh Invoice &amp; Unggah Bukti Bayar:</strong> Lihat atau unduh invoice PDF resmi lalu unggah bukti transfer.</li>
                    <li><strong>Unggah Dokumen Persyaratan:</strong> Lengkapi dokumen wajib &amp; opsional (riwayat revisi tersimpan otomatis).</li>
                    <li><strong>Pelacakan Progres Real-Time:</strong> Pantau 6 tahapan pengerjaan hingga status Selesai.</li>
                    <li><strong>Rating &amp; Ulasan:</strong> Berikan rating 1–5 bintang dan ulasan setelah proyek selesai.</li>
                </ol>
            </div>

            @php
                $helpItems = [
                    [
                        'title' => 'Panduan Pemesanan Layanan Baru',
                        'content' => 'Buka menu "Katalog Layanan" di sidebar kiri atau klik "Pesan Layanan Baru" pada menu Pesanan & Pembayaran. Klik "Detail" untuk membaca persyaratan dan estimasi waktu, atau langsung klik "Pesan", lalu tekan "Konfirmasi & Buat Pesanan" untuk menerbitkan nomor pesanan dan invoice.',
                        'open' => true,
                    ],
                    [
                        'title' => 'Panduan Penggunaan Kode Voucher Promo',
                        'content' => 'Pada halaman Checkout, masukkan kode voucher promo yang tersedia lalu klik "Terapkan" sebelum konfirmasi pesanan. Sistem otomatis memvalidasi status aktif, masa berlaku, kuota penggunaan, dan minimal transaksi di sisi server sebelum memotong total tagihan.',
                        'open' => false,
                    ],
                    [
                        'title' => 'Panduan Pembayaran, Invoice PDF & Unggah Ulang',
                        'content' => 'Buka menu "Pesanan & Pembayaran" lalu klik "Invoice" untuk melihat rincian tagihan atau "Unduh PDF". Pada halaman detail pesanan, isi metode transfer, bank, nama pengirim, nominal minimal sesuai total tagihan, dan unggah bukti (JPG/PNG/PDF maks. 5 MB). Status akan berubah menjadi "Menunggu Verifikasi". Jika ditolak tim Finance, alasan penolakan akan ditampilkan dan Anda dapat langsung mengunggah ulang bukti transfer yang benar.',
                        'open' => false,
                    ],
                    [
                        'title' => 'Panduan Upload Dokumen & Riwayat Revisi (Versioning)',
                        'content' => 'Pada halaman detail pesanan bagian Dokumen Persyaratan, pilih persyaratan Wajib maupun Opsional dan unggah berkas (PDF/JPG/PNG/DOC/DOCX/XLS/XLSX maks. 10 MB). Apabila tim Legal menandai dokumen sebagai "Perlu Revisi", lihat catatan revisi lalu unggah versi terbaru disertai keterangan revisi. Seluruh versi sebelumnya (v1, v2, dst.) tetap tersimpan aman dan dapat diunduh kapan saja.',
                        'open' => false,
                    ],
                    [
                        'title' => 'Panduan Melihat Progres Perizinan (Tracking Proyek)',
                        'content' => 'Progres pengerjaan proyek dapat dipantau secara langsung melalui bagian "Pelacakan Progres Projek & Sertifikasi" di Dashboard (gunakan pemilih pesanan jika memiliki lebih dari satu proyek) maupun di halaman Detail Pesanan. Sistem menampilkan persentase progres dan 6 tahapan: Pesanan Diterima, Verifikasi Pembayaran, Pemeriksaan Dokumen, Proses Pengerjaan, Menunggu Persetujuan, dan Selesai.',
                        'open' => false,
                    ],
                    [
                        'title' => 'Panduan Mengajukan Konsultasi & Pertanyaan Teknis',
                        'content' => 'Klik tombol "Ajukan Konsultasi" di bagian kanan atas Dashboard atau tautan pada kartu ringkasan Sesi Konsultasi untuk membuka halaman konsultasi terpisah. Tuliskan topik serta kendala perizinan Anda, lalu pantau status dan jawaban tim konsultan kami pada panel Riwayat Tanya-Jawab Konsultasi.',
                        'open' => false,
                    ],
                    [
                        'title' => 'FAQ Umum: Keamanan Akun, Profil & Ulasan Layanan',
                        'content' => 'Pastikan data perusahaan (NIB, NPWP, alamat, PIC) melalui tombol "Profil" di bagian kanan atas Dashboard selalu akurat karena digunakan pada invoice dan pemberkasan izin. Setelah proyek berstatus "Selesai" (100%), kolom rating 1–5 bintang dan ulasan akan terbuka otomatis pada halaman detail pesanan.',
                        'open' => false,
                    ],
                ];
            @endphp

            @foreach($helpItems as $item)
                <div class="faq-item p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                    <button type="button" class="w-full font-bold text-left text-corporate flex justify-between items-center gap-2" onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <span>{{ $item['title'] }}</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 shrink-0"></i>
                    </button>
                    <p class="text-slate-600 mt-2 leading-relaxed {{ $item['open'] ? '' : 'hidden' }}">
                        {{ $item['content'] }}
                    </p>
                </div>
            @endforeach

            <!-- Bantuan Pelanggan -->
            <div class="faq-item p-4 rounded-2xl bg-amber-50/70 border border-amber-200/80 space-y-2">
                <h4 class="font-bold text-corporate text-xs flex items-center gap-1.5">
                    <i data-lucide="headphones" class="w-4 h-4 text-amber-600"></i>
                    Bantuan Pelanggan Resmi CV Prasta Solusi Indonesia (IzinYou)
                </h4>
                <p class="text-[11px] text-slate-600 leading-relaxed">
                    Membutuhkan bantuan langsung terkait transaksi, verifikasi berkas, atau kendala akun? Hubungi saluran layanan pelanggan resmi kami (Senin – Jumat, 08.30 – 17.00 WIB):
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                    <a href="https://wa.me/6282128765874?text={{ rawurlencode('Halo Tim IzinYou, saya membutuhkan bantuan pelanggan dari Portal Klien.') }}" target="_blank" class="px-3 py-2 bg-white hover:bg-sky-50 border border-slate-200 rounded-xl flex items-center gap-2 text-[11px] font-bold text-corporate transition">
                        <i data-lucide="message-circle" class="w-4 h-4 text-sky-600 shrink-0"></i>
                        <span>WhatsApp: +62 821-2876-5874</span>
                    </a>
                    <a href="mailto:prastasolusi.indonesia@gmail.com?subject=Bantuan%20Pelanggan%20Portal%20Klien%20IzinYou" class="px-3 py-2 bg-white hover:bg-sky-50 border border-slate-200 rounded-xl flex items-center gap-2 text-[11px] font-bold text-corporate transition">
                        <i data-lucide="mail" class="w-4 h-4 text-sky-600 shrink-0"></i>
                        <span class="truncate">prastasolusi.indonesia@gmail.com</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openHelpCenterModal() {
        const modal = document.getElementById('helpCenterModal');
        if (modal) {
            modal.classList.remove('hidden');
            const sidebar = document.getElementById('klienSidebar');
            const backdrop = document.getElementById('klienSidebarBackdrop');
            if (sidebar && backdrop && !backdrop.classList.contains('hidden')) {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    }

    function closeHelpCenterModal() {
        const modal = document.getElementById('helpCenterModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    function filterFaq() {
        const input = (document.getElementById('faqSearchInput')?.value || '').toLowerCase();
        document.querySelectorAll('#helpCenterModal .faq-item').forEach(item => {
            item.style.display = item.innerText.toLowerCase().includes(input) ? 'block' : 'none';
        });
    }
</script>

