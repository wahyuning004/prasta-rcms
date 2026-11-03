<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Support\Str;

class ServiceCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kategori Perizinan & Sertifikasi
        $categoriesData = [
            [
                'name' => 'Kemenkes (Alkes & PKRT)',
                'slug' => 'kemenkes',
                'description' => 'Layanan izin edar alat kesehatan, PKRT, CPB Alkes, dan standar sarana Kementerian Kesehatan RI.',
            ],
            [
                'name' => 'BPOM & Kosmetik',
                'slug' => 'bpom',
                'description' => 'Notifikasi kosmetik lokal/impor, izin edar obat bahan alam, suplemen, pangan olahan, dan SKI BPOM.',
            ],
            [
                'name' => 'Halal BPJPH',
                'slug' => 'halal-bpjph',
                'description' => 'Sertifikasi Halal resmi Kementerian Agama (BPJPH) dan Majelis Ulama Indonesia untuk pangan, obat, dan kosmetik.',
            ],
            [
                'name' => 'Standar Nasional (SNI)',
                'slug' => 'sni',
                'description' => 'Sertifikasi Standar Nasional Indonesia untuk kelayakan mutu dan keamanan produk industri.',
            ],
            [
                'name' => 'CDOB & Farmasi',
                'slug' => 'cdob',
                'description' => 'Sertifikasi Cara Distribusi Obat yang Baik dan izin Pedagang Besar Farmasi (PBF).',
            ],
            [
                'name' => 'IDAK & Distribusi Alkes',
                'slug' => 'idak',
                'description' => 'Izin Distributor Alat Kesehatan (IDAK) dan Cara Distribusi Alat Kesehatan yang Baik (CDAKB).',
            ],
            [
                'name' => 'DJKI & Merek Usaha',
                'slug' => 'djki',
                'description' => 'Pendaftaran perlindungan merek dagang dan hak kekayaan intelektual Kementerian Hukum & HAM.',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $catData) {
            $categories[$catData['slug']] = Category::updateOrCreate(
                ['slug' => $catData['slug']],
                $catData
            );
        }

        // 2. Daftar Layanan Perizinan & Sertifikasi Lengkap
        $servicesData = [
            // Kategori: SNI
            [
                'category_slug' => 'sni',
                'name' => 'Sertifikasi SNI (Standar Nasional Indonesia)',
                'slug' => 'sertifikasi-sni',
                'timeline_estimation' => '6-12 Bulan',
                'price' => 25000000,
                'requirements_info' => "• Merek terdaftar di DJKI\n• Spesifikasi & foto produk teknis\n• Akta Perusahaan, NIB & NPWP\n• Sertifikat ISO 9001 (bila ada)\n• Daftar fasilitas pabrik & peralatan uji\n• Hasil uji lab terakreditasi KAN",
                'description' => 'Pendampingan sertifikasi Standar Nasional Indonesia dari persiapan dokumen teknis hingga audit kesesuaian mutu pabrik.',
            ],
            // Kategori: BPOM
            [
                'category_slug' => 'bpom',
                'name' => 'SMKPO (Keamanan Pangan Olahan Sarana Peredaran)',
                'slug' => 'smkpo-bpom',
                'timeline_estimation' => '20 Hari Kerja',
                'price' => 13000000,
                'requirements_info' => "• NIB & NPWP Perusahaan\n• Akta Pendirian & KTP Direktur\n• Denah Bangunan Sarana Distribusi\n• SOP Perusahaan & Pengendalian Hama\n• Dokumen Legalitas Sarana Penyimpanan",
                'description' => 'Penerapan Sistem Manajemen Keamanan Pangan Olahan di Sarana Peredaran untuk kepatuhan distribusi produk pangan BPOM.',
            ],
            [
                'category_slug' => 'halal-bpjph',
                'name' => 'Sertifikasi Halal BPJPH & MUI',
                'slug' => 'sertifikasi-halal-bpjph',
                'timeline_estimation' => '2-4 Bulan',
                'price' => 8500000,
                'requirements_info' => "• NIB & Data Usaha\n• Informasi Tipe Produk & Formula\n• Sertifikat Penyelia Halal\n• Manual Sistem Jaminan Produk Halal (SJPH)\n• Daftar Bahan & Matriks Produk",
                'description' => 'Fasilitasi sertifikasi halal resmi terintegrasi BPJPH Kementerian Agama untuk menjamin kehalalan produk edar.',
            ],
            // Kategori: Kemenkes
            [
                'category_slug' => 'kemenkes',
                'name' => 'CPPKRTB (Cara Pembuatan yang Baik PKRT)',
                'slug' => 'cppkrtb-kemenkes',
                'timeline_estimation' => '6-12 Bulan',
                'price' => 35000000,
                'requirements_info' => "• NIB & NPWP\n• Akta Pendirian & Struktur Organisasi\n• Denah Fasilitas Produksi PKRT\n• SOP Produksi & Quality Control\n• Data Personel Penanggung Jawab Teknis",
                'description' => 'Sertifikasi standar cara pembuatan perbekalan kesehatan rumah tangga yang baik untuk pabrik dan produsen lokal.',
            ],
            [
                'category_slug' => 'kemenkes',
                'name' => 'CPB ALKES (Cara Pembuatan Alat Kesehatan Baik)',
                'slug' => 'cpb-alkes-kemenkes',
                'timeline_estimation' => '6-12 Bulan',
                'price' => 35000000,
                'requirements_info' => "• NIB & NPWP\n• SOP Pengendalian Dokumen & Mutu\n• Data Penanggung Jawab Teknis (PJT)\n• Denah Pabrik & Kalibrasi Alat\n• Prosedur Uji Mutu & Sterilisasi",
                'description' => 'Sertifikasi standar sarana produksi alat kesehatan nasional sesuai standar Permenkes RI.',
            ],
            [
                'category_slug' => 'kemenkes',
                'name' => 'Izin Edar Alat Kesehatan (Alkes) Kemenkes',
                'slug' => 'izin-edar-alkes-kemenkes',
                'timeline_estimation' => '1-3 Bulan',
                'price' => 10000000,
                'requirements_info' => "• Sertifikat IDAK / Izin Produksi\n• Letter of Authorization (LoA) untuk impor\n• Certificate of Free Sale (CFS)\n• Hasil Uji Lab & Uji Klinis\n• Brosur, Manual Book & Label Kemasan",
                'description' => 'Penerbitan Nomor Izin Edar (NIE) Alkes Kemenkes RI untuk alkes elektromedis, non-elektromedis, dan diagnostik in-vitro.',
            ],
            [
                'category_slug' => 'kemenkes',
                'name' => 'Izin Edar Produk PKRT Kemenkes',
                'slug' => 'izin-edar-pkrt-kemenkes',
                'timeline_estimation' => '1-3 Bulan',
                'price' => 7000000,
                'requirements_info' => "• Sertifikat Izin Produksi PKRT\n• Hasil Uji Laboratorium Terakreditasi\n• Formula & Komposisi Kimia\n• Desain Label & Kemasan Asli\n• Surat Perjanjian Maklon (bila ada)",
                'description' => 'Pengurusan izin edar perbekalan kesehatan rumah tangga seperti disinfektan, antiseptik, deterjen, dan popok dewasa.',
            ],
            // Kategori: IDAK & Distribusi
            [
                'category_slug' => 'idak',
                'name' => 'IDAK (Izin Distributor Alat Kesehatan)',
                'slug' => 'idak-distributor-alkes',
                'timeline_estimation' => '1-2 Bulan',
                'price' => 15000000,
                'requirements_info' => "• NIB & NPWP Perusahaan\n• Akta Perusahaan & SK Kemenkumham\n• KTP & NPWP Direktur\n• Ijazah Penanggung Jawab Teknis (Elektromedik/Farmasi)\n• Denah Gudang & Sarana Penyimpanan\n• SOP Distribusi Alkes",
                'description' => 'Pengurusan sertifikat izin distributor alat kesehatan untuk mendistribusikan alkes ke rumah sakit dan fasilitas kesehatan.',
            ],
            [
                'category_slug' => 'idak',
                'name' => 'CDAKB (Cara Distribusi Alat Kesehatan yang Baik)',
                'slug' => 'cdakb-sertifikasi',
                'timeline_estimation' => '6-12 Bulan',
                'price' => 35000000,
                'requirements_info' => "• Sertifikat IDAK Aktif\n• SOP Pengelolaan Produk & Recall\n• Catatan Distribusi (Batch Record)\n• Bukti Validasi & Kalibrasi Suhu\n• Data Personel PJT Terlatih",
                'description' => 'Sertifikasi sistem mutu distribusi alat kesehatan yang baik untuk memastikan keamanan produk selama penyimpanan dan pengiriman.',
            ],
            // Kategori: CDOB & Farmasi
            [
                'category_slug' => 'cdob',
                'name' => 'Izin Pedagang Besar Farmasi (PBF)',
                'slug' => 'izin-pedagang-besar-farmasi',
                'timeline_estimation' => '1-2 Bulan',
                'price' => 15000000,
                'requirements_info' => "• NIB & NPWP Badan Usaha\n• Akta Notaris & Pengesahan\n• STRA / SIPA Apoteker Penanggung Jawab\n• KTP & Ijazah Apoteker\n• Denah Gudang Standar PBF\n• Daftar Kelengkapan Fasilitas Gudang",
                'description' => 'Pendampingan izin operasional pedagang besar farmasi untuk pengadaan dan penyaluran sediaan farmasi.',
            ],
            [
                'category_slug' => 'cdob',
                'name' => 'CDOB (Cara Distribusi Obat yang Baik)',
                'slug' => 'cdob-sertifikasi-farmasi',
                'timeline_estimation' => '3-6 Bulan',
                'price' => 35000000,
                'requirements_info' => "• Izin PBF Terbit\n• Manual Mutu CDOB Lengkap\n• Laporan Pemetaan Suhu Gudang (Mapping)\n• Struktur Organisasi & Uraian Tugas\n• Program Pelatihan & Audit Internal",
                'description' => 'Sertifikasi pemenuhan standar mutu distribusi obat kimia dan bahan obat sesuai pedoman Badan POM RI.',
            ],
            // Kategori: BPOM & Kosmetik
            [
                'category_slug' => 'bpom',
                'name' => 'Notifikasi Izin Edar Kosmetik BPOM Impor & Lokal',
                'slug' => 'notifikasi-kosmetik-bpom',
                'timeline_estimation' => '1 Bulan',
                'price' => 10000000,
                'requirements_info' => "• Letter of Authorization (LoA) Resmi\n• Certificate of Free Sale (CFS) Negara Asal\n• Sertifikat GMP / CPKB Pabrik Pembuat\n• Formula Komposisi Kuantitatif\n• Data Uji Mutu & Keamanan Kosmetik",
                'description' => 'Pendaftaran nomor notifikasi kosmetik BPOM untuk produk skincare, makeup, dan perawatan tubuh impor maupun lokal.',
            ],
            [
                'category_slug' => 'bpom',
                'name' => 'Izin Edar Obat Bahan Alam & Suplemen Kesehatan',
                'slug' => 'izin-edar-obat-bahan-alam',
                'timeline_estimation' => '3-6 Bulan',
                'price' => 50000000,
                'requirements_info' => "• Sertifikat CPOTB / CPB Produsen\n• Hasil Uji Preklinis & Keamanan\n• Laporan Uji Stabilitas Produk\n• Formula Bahan Baku Terstandar\n• Desain Label, Kemasan & Penandaan",
                'description' => 'Pendaftaran Nomor Izin Edar (NIE) jamu, obat herbal terstandar (OHT), fitofarmaka, dan suplemen kesehatan di Badan POM.',
            ],
            [
                'category_slug' => 'bpom',
                'name' => 'Surat Keterangan Impor (SKI) Produk BPOM',
                'slug' => 'ski-produk-bpom',
                'timeline_estimation' => '10 Hari Kerja',
                'price' => 1000000,
                'requirements_info' => "• Nomor Izin Edar (NIE) Aktif\n• Invoice & Packing List Impor\n• Bill of Lading (B/L) / Airway Bill\n• Certificate of Analysis (CoA) per Batch\n• Surat Pernyataan Keaslian Dokumen",
                'description' => 'Pengurusan Surat Keterangan Impor border & post-border untuk pengeluaran barang komoditas BPOM di pelabuhan bea cukai.',
            ],
            // Kategori: DJKI & Merek
            [
                'category_slug' => 'djki',
                'name' => 'Pendaftaran Perlindungan Merek Dagang & Jasa',
                'slug' => 'pendaftaran-merek-djki',
                'timeline_estimation' => '1 Hari Kerja Formulir / 1-2 Tahun Sertifikat',
                'price' => 5000000,
                'requirements_info' => "• Label / Logo Merek Resolusi Tinggi\n• KTP & NPWP Pemohon / Direktur\n• Akta Perusahaan (bila atas nama PT/CV)\n• Tanda Tangan Digital Pemohon\n• Penentuan Kelas Barang / Jasa (Nice Classification)",
                'description' => 'Pendaftaran hak eksklusif merek dagang ke Direktorat Jenderal Kekayaan Intelektual (DJKI) Kemenkumham RI.',
            ],
        ];

        foreach ($servicesData as $srvData) {
            $catSlug = $srvData['category_slug'];
            $cat = $categories[$catSlug] ?? null;

            if ($cat) {
                Service::updateOrCreate(
                    ['slug' => $srvData['slug']],
                    [
                        'category_id' => $cat->id,
                        'name' => $srvData['name'],
                        'slug' => $srvData['slug'],
                        'timeline_estimation' => $srvData['timeline_estimation'],
                        'price' => $srvData['price'],
                        'cost' => $srvData['price'],
                        'requirements_info' => $srvData['requirements_info'],
                        'requirements_summary' => $srvData['requirements_info'],
                        'estimation_time' => $srvData['timeline_estimation'],
                        'description' => $srvData['description'],
                    ]
                );
            }
        }
    }
}
