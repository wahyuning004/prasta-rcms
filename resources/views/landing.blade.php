<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prasta Solusi Indonesia | Spesialis Regulasi & Sertifikasi</title>
    <meta name="description" content="Spesialis Regulasi, Sertifikasi, & Kepatuhan Distribusi Bisnis. Menyediakan layanan pendampingan menyeluruh.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-nav { 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(12px); 
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }
        .text-corporate { color: #0f172a; }
        .bg-corporate { background-color: #0f172a; }
        .text-gold { color: #d4af37; }
        .bg-gold { background-color: #d4af37; }
        .bg-pattern {
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 24px 24px;
        }
        
        /* Float Animation for WhatsApp Button */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        .card-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .card-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .card-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-emerald-500 selection:text-white">

    <!-- 1. Top Bar & Navbar -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo & Name -->
                <div class="flex-shrink-0 flex items-center gap-3 cursor-pointer" onclick="window.scrollTo(0,0)">
                    <div class="w-10 h-10 rounded-lg bg-corporate flex items-center justify-center text-white shadow-lg">
                        <i data-lucide="shield-check" class="w-6 h-6 text-gold"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-lg md:text-xl leading-tight text-corporate tracking-tight">Prasta Solusi Indonesia</h1>
                    </div>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex space-x-8 items-center">
                    <a href="#beranda" class="text-sm font-semibold text-slate-900 hover:text-emerald-600 transition-colors">Beranda</a>
                    <a href="#layanan" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Layanan & Portofolio</a>
                    <a href="#tentang" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Tentang Kami</a>
                    <a href="#testimoni" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Penilaian Klien</a>
                    <div class="h-6 w-px bg-slate-300"></div>
                    <a href="/login" class="text-sm font-bold text-white bg-corporate hover:bg-emerald-600 px-5 py-2.5 rounded-full transition-colors flex items-center gap-2 shadow-md">
                        <i data-lucide="log-in" class="w-4 h-4"></i> Login Sistem
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="lg:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-slate-600 hover:text-corporate p-2">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu Dropdown -->
            <div id="mobile-menu" class="hidden lg:hidden bg-white border-t border-slate-100 shadow-lg absolute w-full left-0 top-20 flex-col py-4 px-6 space-y-4">
                <a href="#beranda" class="mobile-link block text-sm font-semibold text-slate-900 hover:text-emerald-600 transition-colors py-2">Beranda</a>
                <a href="#layanan" class="mobile-link block text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors py-2">Layanan & Portofolio</a>
                <a href="#tentang" class="mobile-link block text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors py-2">Tentang Kami</a>
                <a href="#testimoni" class="mobile-link block text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors py-2">Penilaian Klien</a>
                <div class="h-px w-full bg-slate-200 my-2"></div>
                <a href="/login" class="block w-full text-center text-sm font-bold text-white bg-corporate hover:bg-emerald-600 px-5 py-3 rounded-xl transition-colors shadow-md flex justify-center items-center gap-2">
                    <i data-lucide="log-in" class="w-4 h-4"></i> Login Sistem
                </a>
            </div>
        </div>
    </nav>

    <!-- Scroll to Top Button -->
    <button id="scrollToTopBtn" class="fixed bottom-28 right-8 z-[90] w-12 h-12 bg-corporate hover:bg-slate-800 text-white rounded-full shadow-lg flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300 transform translate-y-4">
        <i data-lucide="arrow-up" class="w-6 h-6 text-gold"></i>
    </button>

    <!-- Floating WhatsApp Button (Pusat Komunikasi Tunggal) -->
    <a href="https://wa.me/6282128765874?text=Halo%20Prasta%20Solusi%20Indonesia,%20saya%20ingin%20berkonsultasi%20mengenai%20perizinan/sertifikasi..." target="_blank" class="fixed bottom-8 right-8 z-[100] flex items-center gap-3 animate-float group">
        <div class="bg-white px-5 py-3 rounded-full shadow-2xl border border-emerald-100 text-sm font-black text-emerald-600 whitespace-nowrap">
            Konsultasi Gratis
        </div>
        <div class="w-16 h-16 bg-emerald-500 group-hover:bg-emerald-600 rounded-full text-white shadow-2xl flex items-center justify-center transition-all duration-300 border-4 border-white hover:scale-110 flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
        </div>
    </a>

    <!-- 2. Hero Section -->
    <section id="beranda" class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden bg-corporate">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-slate-800 via-corporate to-corporate"></div>
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-[600px] h-[600px] bg-emerald-900/30 rounded-full mix-blend-overlay filter blur-3xl opacity-70"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-[500px] h-[500px] bg-blue-900/30 rounded-full mix-blend-overlay filter blur-3xl opacity-70"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <div class="max-w-4xl mx-auto">
                <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-white/10 border border-white/20 mb-8 backdrop-blur-sm">
                    <span class="text-gold text-sm">✨</span>
                    <span class="text-sm font-semibold text-white tracking-wide">Mitra Terpercaya Kepatuhan & Perizinan Usaha Anda</span>
                </div>
                <h2 class="text-4xl lg:text-5xl xl:text-6xl font-extrabold text-white leading-tight mb-8">
                    Spesialis Regulasi, Sertifikasi, & <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-emerald-200">Kepatuhan Distribusi Bisnis</span>
                </h2>
                <p class="text-lg md:text-xl text-slate-300 mb-12 leading-relaxed max-w-3xl mx-auto">
                    Menyediakan layanan pendampingan menyeluruh mencakup persyaratan teknis, estimasi waktu, dan biaya secara transparan dan akuntabel demi kelancaran serta legalitas bisnis Anda.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#layanan" class="px-8 py-4 text-center text-base font-bold text-corporate bg-emerald-400 hover:bg-emerald-300 rounded-full shadow-lg transition-all duration-300">
                        Jelajahi Katalog Layanan
                    </a>
                    <a href="#testimoni" class="px-8 py-4 text-center text-base font-bold text-white bg-white/10 border-2 border-white/20 hover:bg-white/20 rounded-full transition-all duration-300">
                        Lihat Testimoni Klien
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Section Mengapa Memilih Prasta Solusi Indonesia -->
    <section class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h3 class="text-emerald-600 font-bold tracking-wide uppercase text-sm mb-3">Keunggulan Kami</h3>
                <h2 class="text-3xl md:text-4xl font-extrabold text-corporate mb-4">Mengapa Memilih Prasta Solusi Indonesia?</h2>
                <p class="text-slate-600 text-lg">Kami memprioritaskan kepastian hukum, kecepatan proses, dan kejelasan informasi bagi seluruh klien kami.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Poin 1 -->
                <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 text-center hover:shadow-xl transition-all group">
                    <div class="w-16 h-16 bg-white shadow-sm border border-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                        <i data-lucide="wallet" class="w-8 h-8 text-gold"></i>
                    </div>
                    <h4 class="text-lg font-bold text-corporate mb-3">Transparansi Biaya & Waktu</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">Seluruh estimasi biaya operasional dan timeline pengerjaan disampaikan secara rinci dan jujur sejak awal konsultasi.</p>
                </div>
                <!-- Poin 2 -->
                <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 text-center hover:shadow-xl transition-all group">
                    <div class="w-16 h-16 bg-white shadow-sm border border-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                        <i data-lucide="users-2" class="w-8 h-8 text-emerald-500"></i>
                    </div>
                    <h4 class="text-lg font-bold text-corporate mb-3">Tim Ahli Berpengalaman</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">Ditangani spesialis regulasi yang menguasai birokrasi Kemenkes, BPOM, MUI, Kemenperin, dan DJKI.</p>
                </div>
                <!-- Poin 3 -->
                <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 text-center hover:shadow-xl transition-all group">
                    <div class="w-16 h-16 bg-white shadow-sm border border-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                        <i data-lucide="file-check-2" class="w-8 h-8 text-blue-500"></i>
                    </div>
                    <h4 class="text-lg font-bold text-corporate mb-3">Pendampingan dari Nol</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">Kami membimbing penyusunan dokumen dari tahap persiapan awal hingga sertifikat / izin edar resmi diterbitkan.</p>
                </div>
                <!-- Poin 4 -->
                <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 text-center hover:shadow-xl transition-all group">
                    <div class="w-16 h-16 bg-white shadow-sm border border-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                        <i data-lucide="scale" class="w-8 h-8 text-rose-500"></i>
                    </div>
                    <h4 class="text-lg font-bold text-corporate mb-3">Legalitas Terjamin</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">Mendukung perlindungan dan kepastian hukum mutlak bagi operasional bisnis dan produk yang Anda edarkan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Section Tentang Kami (Background Mendalam) -->
    <section id="tentang" class="py-24 bg-slate-900 text-white relative">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-slate-800 via-slate-900 to-slate-900"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h3 class="text-gold font-bold tracking-wide uppercase text-sm mb-3">Tentang Kami</h3>
                    <h2 class="text-3xl md:text-4xl font-extrabold mb-6 leading-tight">Mitra Konsultan Spesialis Terdepan di Indonesia</h2>
                    <p class="text-slate-300 text-lg mb-6 leading-relaxed">
                        Prasta Solusi Indonesia didirikan dengan dedikasi penuh untuk memfasilitasi dan mengakselerasi kepatuhan regulasi bisnis di Tanah Air. Kami menyadari bahwa birokrasi dan persyaratan teknis perizinan seringkali menjadi tantangan terbesar bagi pelaku usaha.
                    </p>
                    <p class="text-slate-400 mb-8 leading-relaxed">
                        Sebagai konsultan spesialis terdepan, kami menjembatani perusahaan Anda dengan instansi pemerintah terkait (seperti BPOM, Kemenkes, Halal MUI/BPJPH, Kemenperin, dan DJKI). Kami tidak sekadar mengurus berkas, melainkan memberikan edukasi, audit internal, dan pendampingan menyeluruh agar sistem manajemen mutu perusahaan Anda terkalibrasi sesuai standar nasional dan internasional.
                    </p>
                    <div class="flex gap-8 border-t border-slate-700 pt-8">
                        <div>
                            <p class="text-4xl font-black text-emerald-400 mb-1">100%</p>
                            <p class="text-xs text-slate-400 uppercase tracking-widest font-semibold">Legal & Tervalidasi</p>
                        </div>
                        <div>
                            <p class="text-4xl font-black text-gold mb-1">Cepat</p>
                            <p class="text-xs text-slate-400 uppercase tracking-widest font-semibold">Proses Efisien</p>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="absolute inset-0 bg-emerald-500 rounded-3xl transform rotate-3 opacity-20"></div>
                    <div class="bg-slate-800 border border-slate-700 p-10 rounded-3xl relative z-10 shadow-2xl">
                        <h4 class="text-2xl font-bold border-l-4 border-emerald-500 pl-4 mb-4">Visi</h4>
                        <p class="text-slate-400 leading-relaxed mb-8">Menjadi pusat rujukan dan konsultan regulasi berskala nasional yang paling terpercaya, inovatif, dan menjadi tolak ukur kepatuhan legalitas bagi seluruh ekosistem industri.</p>
                        
                        <h4 class="text-2xl font-bold border-l-4 border-gold pl-4 mb-4">Misi</h4>
                        <ul class="space-y-3 text-slate-400">
                            <li class="flex items-start gap-3"><i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5"></i> Menyediakan pendampingan teknis dan administratif yang akurat berbasis hukum aktual.</li>
                            <li class="flex items-start gap-3"><i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5"></i> Menjaga standar transparansi anggaran dan efisiensi birokrasi bagi para klien.</li>
                            <li class="flex items-start gap-3"><i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5"></i> Mendukung percepatan kelayakan edar produk pangan, kosmetik, alkes, dan farmasi yang aman di pasar.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Section Layanan & Portofolio (Grid dengan Detail Dokumen) -->
    <section id="layanan" class="py-24 bg-slate-50 relative">
        <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h3 class="text-emerald-600 font-bold tracking-wide uppercase text-sm mb-3">Layanan Spesialis</h3>
                <h2 class="text-3xl md:text-4xl font-extrabold text-corporate mb-4">Daftar Layanan & Portofolio</h2>
                <p class="text-slate-600 text-lg">Spesifikasi layanan, estimasi biaya operasional, timeline waktu penerbitan, beserta dokumen prasyarat yang harus disiapkan.</p>
            </div>

            <!-- Grid Cards Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                @php
                    $services = [
                        [
                            'title' => 'Sertifikasi SNI', 
                            'time' => '6-12 Bulan', 
                            'cost' => 'Menyesuaikan Kategori Produk', 
                            'icon' => 'file-badge',
                            'docs' => ['Merek terdaftar', 'Spesifikasi produk', 'Foto produk', 'Akta Perusahaan', 'NIB & NPWP', 'Sertifikat ISO 9001', 'Daftar fasilitas & peralatan', 'Hasil uji (fisik/kimia/bakar/kelistrikan)']
                        ],
                        [
                            'title' => 'Sistem Manajemen Keamanan Pangan Olahan di Sarana Peredaran / SMKPO', 
                            'time' => '20 Hari Kerja', 
                            'cost' => 'Rp 13.000.000', 
                            'icon' => 'shield-alert',
                            'docs' => ['NIB & NPWP', 'Akta Perusahaan', 'KTP Direktur', 'Denah Bangunan', 'SOP Perusahaan', 'Dokumen Legalitas Sarana']
                        ],
                        [
                            'title' => 'Sertifikasi Halal', 
                            'time' => '2-4 Bulan', 
                            'cost' => 'Menyesuaikan Kategori Produk', 
                            'icon' => 'award',
                            'docs' => ['Legalitas Perusahaan (NIB)', 'Informasi Tipe Produk', 'Sertifikat Penyelia Halal', 'Manual Sistem Jaminan Halal (SJH)', 'Daftar Bahan & Komposisi', 'Matriks Produk']
                        ],
                        [
                            'title' => 'CPPKRTB (Cara Pembuatan yang Baik PKRT)', 
                            'time' => '6-12 Bulan', 
                            'cost' => 'Rp 35.000.000', 
                            'icon' => 'factory',
                            'docs' => ['NIB & NPWP', 'Akta Pendirian', 'Struktur Organisasi', 'Denah Fasilitas Produksi', 'SOP Produksi & QC', 'Data Personel Penanggung Jawab']
                        ],
                        [
                            'title' => 'CPB ALKES (Cara Pembuatan Alat Kesehatan)', 
                            'time' => '6-12 Bulan', 
                            'cost' => 'Rp 35.000.000', 
                            'icon' => 'activity',
                            'docs' => ['NIB & NPWP', 'SOP Pengendalian Dokumen', 'Data Penanggung Jawab Teknis', 'Denah Pabrik', 'Daftar Alat Produksi', 'Prosedur Uji Mutu']
                        ],
                        [
                            'title' => 'IDAK (Izin Distributor Alat Kesehatan)', 
                            'time' => '1-2 Bulan', 
                            'cost' => 'Rp 15.000.000', 
                            'icon' => 'truck',
                            'docs' => ['NIB & NPWP', 'Akta Perusahaan', 'KTP Direktur', 'Ijazah Penanggung Jawab Teknis (PJT)', 'Denah Gudang Penyimpanan', 'SOP Distribusi']
                        ],
                        [
                            'title' => 'CDAKB (Cara Distribusi Alat Kesehatan yang Baik)', 
                            'time' => '6-12 Bulan', 
                            'cost' => 'Rp 35.000.000', 
                            'icon' => 'package-check',
                            'docs' => ['Sertifikat IDAK', 'SOP Pengelolaan Produk', 'Catatan Distribusi (Batch Record)', 'Bukti Kalibrasi Suhu', 'Data Personel PJT']
                        ],
                        [
                            'title' => 'Izin Edar Alat Kesehatan (Alkes) Kemenkes', 
                            'time' => '1-3 Bulan', 
                            'cost' => 'Rp 10.000.000', 
                            'icon' => 'stethoscope',
                            'docs' => ['Sertifikat IDAK', 'Letter of Authorization (LoA) - Impor', 'Free Sale Certificate - Impor', 'Uji Lab & Uji Klinis', 'Manual Book & Label Kemasan']
                        ],
                        [
                            'title' => 'Izin Edar Produk PKRT Kemenkes', 
                            'time' => '1-3 Bulan', 
                            'cost' => 'Rp 7.000.000', 
                            'icon' => 'spray-can',
                            'docs' => ['Sertifikat Izin Produksi PKRT', 'Hasil Uji Laboratorium Terakreditasi', 'Formula & Komposisi', 'Desain Kemasan', 'Surat Perjanjian Maklon (Bila ada)']
                        ],
                        [
                            'title' => 'Izin Pedagang Besar Farmasi (PBF)', 
                            'time' => '1-2 Bulan', 
                            'cost' => 'Rp 15.000.000', 
                            'icon' => 'building-2',
                            'docs' => ['NIB & NPWP', 'Akta Perusahaan', 'STRA/SIPA Apoteker Penanggung Jawab', 'KTP & Ijazah Apoteker', 'Denah Gudang PBF', 'Daftar Kelengkapan Gudang']
                        ],
                        [
                            'title' => 'Izin Edar Kosmetik BPOM Import', 
                            'time' => '1 Bulan', 
                            'cost' => 'Rp 10.000.000', 
                            'icon' => 'sparkles',
                            'docs' => ['Letter of Authorization (LoA)', 'Certificate of Free Sale (CFS)', 'Sertifikat GMP / CPKB Pabrik Asal', 'Daftar Komposisi (Formula)', 'Data Uji Mutu']
                        ],
                        [
                            'title' => 'Rekomendasi Importir Obat Bahan Alam, Suplemen & Obat Kuasi', 
                            'time' => '2-3 Bulan', 
                            'cost' => 'Rp 35.000.000', 
                            'icon' => 'leaf',
                            'docs' => ['NIB & API', 'Akta Perusahaan', 'Perjanjian Kerja Sama Import (LoA)', 'Ijazah Penanggung Jawab Apoteker', 'Denah Sarana Penyimpanan']
                        ],
                        [
                            'title' => 'Izin Edar Obat Bahan Alam, Suplemen & Obat Kuasi', 
                            'time' => '3-6 Bulan', 
                            'cost' => 'Rp 50.000.000', 
                            'icon' => 'pill',
                            'docs' => ['Sertifikat CPOTB / CPB', 'Hasil Uji Preklinis/Klinis', 'Uji Stabilitas', 'Formula Kuantitatif', 'Desain Label & Kemasan']
                        ],
                        [
                            'title' => 'CDOB (Cara Distribusi Obat yang Baik) - Obat Kimia', 
                            'time' => 'To Be Confirmed', 
                            'cost' => 'Rp 35.000.000', 
                            'icon' => 'flask-conical',
                            'docs' => ['Izin PBF', 'SOP CDOB Lengkap', 'Catatan Suhu & Mapping Suhu', 'Struktur Organisasi & Jobdesc', 'Program Pelatihan Personel']
                        ],
                        [
                            'title' => 'Rekomendasi Importir Kosmetik', 
                            'time' => 'To Be Confirmed', 
                            'cost' => 'Rp 15.000.000', 
                            'icon' => 'plane',
                            'docs' => ['NIB & NPWP', 'Surat Penunjukan (LoA)', 'Sertifikat GMP Pabrik Luar Negeri', 'Data Sarana Gudang', 'SOP Penarikan Produk']
                        ],
                        [
                            'title' => 'Surat Keterangan Import (SKI) Produk BPOM', 
                            'time' => '10 Hari Kerja', 
                            'cost' => 'Rp 1.000.000', 
                            'icon' => 'file-text',
                            'docs' => ['Nomor Izin Edar (NIE) Terdaftar', 'Invoice Pembelian', 'Packing List', 'Bill of Lading (B/L) / Airway Bill', 'Certificate of Analysis (CoA)']
                        ],
                        [
                            'title' => 'Pendaftaran Merek', 
                            'time' => '1 Hari Kerja Formulir / 1-2 Tahun Sertifikat', 
                            'cost' => 'Rp 5.000.000', 
                            'icon' => 'copyright',
                            'docs' => ['Label/Logo Merek Resolusi Tinggi', 'KTP & NPWP Pemohon/Direktur', 'Akta Perusahaan (Jika Atas Nama Badan Usaha)', 'Tanda Tangan Pemohon', 'Spesifikasi Kelas Barang/Jasa']
                        ]
                    ];
                @endphp

                @foreach($services as $svc)
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 flex flex-col overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="p-6 md:p-8 flex-1">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-14 h-14 bg-slate-50 text-corporate rounded-2xl flex items-center justify-center border border-slate-100 flex-shrink-0">
                                <i data-lucide="{{ $svc['icon'] }}" class="w-7 h-7"></i>
                            </div>
                            <h3 class="text-xl font-bold text-corporate leading-tight pt-2">{{ $svc['title'] }}</h3>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-6 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Estimasi Waktu</span>
                                <div class="flex items-center gap-1.5 text-sm font-semibold text-slate-800">
                                    <i data-lucide="clock" class="w-4 h-4 text-emerald-600"></i> {{ $svc['time'] }}
                                </div>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Estimasi Biaya</span>
                                <div class="flex items-center gap-1.5 text-sm font-bold text-emerald-700">
                                    <i data-lucide="wallet" class="w-4 h-4 text-gold"></i> {{ $svc['cost'] }}
                                </div>
                            </div>
                        </div>

                        <div>
                            <span class="text-[11px] font-bold text-corporate uppercase tracking-widest block mb-3 flex items-center gap-2">
                                <i data-lucide="file-check-2" class="w-4 h-4"></i> Rincian Persyaratan Dokumen:
                            </span>
                            <div class="h-40 overflow-y-auto card-scrollbar pr-2">
                                <ul class="space-y-2">
                                    @foreach($svc['docs'] as $doc)
                                    <li class="flex items-start gap-2 text-sm text-slate-600">
                                        <i data-lucide="check" class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                        <span class="leading-relaxed">{{ $doc }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 6. Section Manajemen Perusahaan -->
    <section class="py-24 bg-corporate relative overflow-hidden">
        <div class="absolute inset-0 bg-pattern opacity-10"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h3 class="text-gold font-bold tracking-wide uppercase text-sm mb-3">Struktur Kepemimpinan</h3>
                <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Manajemen Prasta Solusi Indonesia</h2>
                <p class="text-slate-400 text-lg">Dikemudikan oleh para profesional yang berdedikasi tinggi terhadap legalitas dan kepatuhan hukum industri di Indonesia.</p>
            </div>

            <div class="flex flex-col md:flex-row justify-center gap-8 md:gap-16">
                <!-- Direktur Utama -->
                <div class="text-center max-w-xs w-full mx-auto">
                    <div class="w-32 h-32 bg-slate-800 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl border-4 border-slate-700">
                        <i data-lucide="user-check" class="w-12 h-12"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">ARI AGUSTA ANANDA</h3>
                    <div class="inline-block px-4 py-1.5 bg-white/10 border border-white/20 rounded-full">
                        <p class="text-emerald-400 font-bold text-xs uppercase tracking-widest">Direktur Utama</p>
                    </div>
                </div>

                <!-- Komisaris Utama -->
                <div class="text-center max-w-xs w-full mx-auto">
                    <div class="w-32 h-32 bg-slate-800 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl border-4 border-slate-700">
                        <i data-lucide="user" class="w-12 h-12"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">PRAYOGA RAHMAT</h3>
                    <div class="inline-block px-4 py-1.5 bg-white/10 border border-white/20 rounded-full">
                        <p class="text-gold font-bold text-xs uppercase tracking-widest">Komisaris Utama</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. Section Penilaian & Testimoni Klien -->
    <section id="testimoni" class="py-24 bg-white relative">
        <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h3 class="text-emerald-600 font-bold tracking-wide uppercase text-sm mb-3">Testimoni Klien</h3>
                <h2 class="text-3xl md:text-4xl font-extrabold text-corporate mb-4">Kepercayaan Pelanggan Kami</h2>
                <p class="text-slate-600 text-lg">Mendukung ratusan perusahaan dari berbagai sektor industri di seluruh Indonesia dalam meraih legalitas operasional yang cepat dan transparan.</p>
            </div>

            <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-6">
                <!-- Testimoni 1 -->
                <div class="bg-slate-50 p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1 mb-4 text-gold">
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        </div>
                        <p class="text-slate-600 text-sm mb-6 italic leading-relaxed">"Sangat terbantu oleh tim Prasta Solusi Indonesia. Pengurusan Izin Edar Alkes Kemenkes berjalan jauh lebih cepat dari ekspektasi. Biayanya sangat transparan dari awal tanpa hidden fee."</p>
                    </div>
                    <div class="border-t border-slate-200 pt-4">
                        <h4 class="font-bold text-corporate text-sm">Direktur Operasional</h4>
                        <p class="text-xs text-slate-500">PT Distributor Alkes Nasional</p>
                    </div>
                </div>
                <!-- Testimoni 2 -->
                <div class="bg-slate-50 p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1 mb-4 text-gold">
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        </div>
                        <p class="text-slate-600 text-sm mb-6 italic leading-relaxed">"Konsultasi yang diberikan sangat solutif. Mengurus Izin Edar Kosmetik BPOM Import jadi sangat mudah karena pendampingan profesional dari awal hingga sertifikat terbit."</p>
                    </div>
                    <div class="border-t border-slate-200 pt-4">
                        <h4 class="font-bold text-corporate text-sm">Manajer Reguler</h4>
                        <p class="text-xs text-slate-500">CV Importir Kosmetik Global</p>
                    </div>
                </div>
                <!-- Testimoni 3 -->
                <div class="bg-slate-50 p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1 mb-4 text-gold">
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        </div>
                        <p class="text-slate-600 text-sm mb-6 italic leading-relaxed">"Mulai dari sertifikasi Halal hingga penerapan SMKPO, semuanya diurus dengan sangat teliti. Ahli di bidang kepatuhan distribusi. Sangat direkomendasikan!"</p>
                    </div>
                    <div class="border-t border-slate-200 pt-4">
                        <h4 class="font-bold text-corporate text-sm">Quality Assurance</h4>
                        <p class="text-xs text-slate-500">Pabrik Pangan Olahan</p>
                    </div>
                </div>
                <!-- Testimoni 4 -->
                <div class="bg-slate-50 p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1 mb-4 text-gold">
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        </div>
                        <p class="text-slate-600 text-sm mb-6 italic leading-relaxed">"Proses CDAKB yang terkenal rumit ternyata bisa diselesaikan dengan lancar berkat bimbingan tim Prasta. Audit fasilitas berjalan sukses."</p>
                    </div>
                    <div class="border-t border-slate-200 pt-4">
                        <h4 class="font-bold text-corporate text-sm">Penanggung Jawab Teknis</h4>
                        <p class="text-xs text-slate-500">Distributor Alkes Jawa Barat</p>
                    </div>
                </div>
                <!-- Testimoni 5 -->
                <div class="bg-slate-50 p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1 mb-4 text-gold">
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        </div>
                        <p class="text-slate-600 text-sm mb-6 italic leading-relaxed">"Kami mengurus PBF dan CDOB secara paralel. Pendampingan dokumen dan pemantauan progres dari Prasta sangat rapi. Legalitas operasional kami kini terjamin."</p>
                    </div>
                    <div class="border-t border-slate-200 pt-4">
                        <h4 class="font-bold text-corporate text-sm">Direktur Farmasi</h4>
                        <p class="text-xs text-slate-500">PBF Skala Nasional</p>
                    </div>
                </div>
                <!-- Testimoni 6 -->
                <div class="bg-slate-50 p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1 mb-4 text-gold">
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i><i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        </div>
                        <p class="text-slate-600 text-sm mb-6 italic leading-relaxed">"Pendaftaran Merek untuk produk baru kami selesai dengan aman. Penjelasannya mudah dipahami bagi pelaku usaha baru seperti kami. Terima kasih."</p>
                    </div>
                    <div class="border-t border-slate-200 pt-4">
                        <h4 class="font-bold text-corporate text-sm">Owner Startup</h4>
                        <p class="text-xs text-slate-500">Brand Kosmetik Lokal</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. Section Lokasi Google Maps -->
    <section class="py-0 bg-slate-200 relative h-[400px]">
        <div class="absolute inset-0 w-full h-full">
            <!-- Iframe embed Babelan, Bekasi -->
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126938.86795898863!2d106.9405527633789!3d-6.151817899999998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e698a964fdfc5ab%3A0xb695eeb8b6bbdbec!2sKec.%20Babelan%2C%20Kabupaten%20Bekasi%2C%20Jawa%20Barat!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid" 
                width="100%" 
                height="100%" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
        <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 bg-white px-6 py-4 rounded-2xl shadow-xl flex items-center gap-4">
            <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center">
                <i data-lucide="map-pin" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="font-bold text-corporate text-sm">Lokasi Kantor Pusat</p>
                <p class="text-xs text-slate-500">Babelan, Bekasi, Jawa Barat</p>
            </div>
        </div>
    </section>

    <!-- 9. Footer -->
    <footer class="bg-corporate pt-16 pb-24 lg:pb-8 text-slate-300 relative border-t-8 border-gold">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-12 gap-8 mb-12">
                <!-- Branding & Address -->
                <div class="md:col-span-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center text-white">
                            <i data-lucide="shield-check" class="w-6 h-6 text-gold"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-xl text-white tracking-tight">Prasta Solusi Indonesia</h2>
                        </div>
                    </div>
                    <ul class="space-y-4 max-w-xl">
                        <li class="flex items-start gap-3">
                            <i data-lucide="map-pin" class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5"></i>
                            <span class="text-sm leading-relaxed">Cluster New Liverpool Blok P18A No 46, RT 02 / RW 017, Kelurahan Kedung Jaya, Kecamatan Babelan, Kabupaten Bekasi, Provinsi Jawa Barat.</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i data-lucide="mail" class="w-5 h-5 text-emerald-500 flex-shrink-0"></i>
                            <span class="text-sm">prastasolusi.indonesia@gmail.com</span>
                        </li>
                    </ul>
                </div>

                <!-- Tautan Cepat -->
                <div class="md:col-span-4">
                    <h4 class="text-white font-bold mb-6">Navigasi Utama</h4>
                    <ul class="space-y-3">
                        <li><a href="#beranda" class="hover:text-emerald-400 text-sm transition-colors">Beranda</a></li>
                        <li><a href="#layanan" class="hover:text-emerald-400 text-sm transition-colors">Daftar Layanan & Portofolio</a></li>
                        <li><a href="#tentang" class="hover:text-emerald-400 text-sm transition-colors">Tentang Perusahaan</a></li>
                        <li><a href="#testimoni" class="hover:text-emerald-400 text-sm transition-colors">Penilaian Klien</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs">
                <p>
                    &copy; {{ date('Y') }} Prasta Solusi Indonesia. Hak Cipta Dilindungi Undang-Undang.
                </p>
                <div class="flex items-center gap-2">
                    <span>Mitra Kepatuhan & Perizinan Bisnis Terdepan</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Initialize Lucide Icons & Scripts -->
    <script>
        lucide.createIcons();

        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileLinks = document.querySelectorAll('.mobile-link');

        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            mobileMenu.classList.toggle('flex');
        });

        // Close mobile menu when clicking a link
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                mobileMenu.classList.remove('flex');
            });
        });

        // Navbar Scroll Effect & Scroll to Top
        const scrollToTopBtn = document.getElementById('scrollToTopBtn');
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            if (window.scrollY > 20) {
                nav.classList.add('shadow-md');
                nav.classList.replace('glass-nav', 'bg-white/95');
            } else {
                nav.classList.remove('shadow-md');
                nav.classList.replace('bg-white/95', 'glass-nav');
            }
            
            // Show/Hide Scroll to Top Button
            if (window.scrollY > 300) {
                scrollToTopBtn.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
                scrollToTopBtn.classList.add('opacity-100', 'translate-y-0');
            } else {
                scrollToTopBtn.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
                scrollToTopBtn.classList.remove('opacity-100', 'translate-y-0');
            }
        });

        // Scroll to Top Action
        scrollToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
</body>
</html>
