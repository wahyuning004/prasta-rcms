<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class MultiStaffSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Promos Seeder
        $promos = [
            [
                'code' => 'PRASMAS25',
                'discount_amount' => 500000.00,
                'discount_percentage' => null,
                'quota' => 30,
                'used_count' => 8,
                'is_active' => true,
                'expired_date' => now()->addMonths(3)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'IZINHEMAT',
                'discount_amount' => 0.00,
                'discount_percentage' => 10.00,
                'quota' => 50,
                'used_count' => 14,
                'is_active' => true,
                'expired_date' => now()->addMonths(2)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'LEGALCEPAT',
                'discount_amount' => 250000.00,
                'discount_percentage' => null,
                'quota' => 20,
                'used_count' => 4,
                'is_active' => true,
                'expired_date' => now()->addMonths(1)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($promos as $promo) {
            DB::table('promos')->updateOrInsert(
                ['code' => $promo['code']],
                $promo
            );
        }

        // 2. Reviews Seeder
        $klien = User::where('role', 'klien')->first();
        $order = DB::table('orders')->first();

        if ($klien && $order) {
            $sampleReviews = [
                [
                    'order_id' => $order->id,
                    'user_id' => $klien->id,
                    'rating' => 5,
                    'comment' => 'Proses pengurusan Izin Edar Alkes dari Kemenkes RI dibantu secara sangat profesional dan cepat. Dashboard IzinYou sangat memudahkan tracking real-time berkas kami.',
                    'status' => 'published',
                    'is_featured' => true,
                    'created_at' => now()->subDays(3),
                    'updated_at' => now()->subDays(3),
                ],
                [
                    'order_id' => $order->id,
                    'user_id' => $klien->id,
                    'rating' => 5,
                    'comment' => 'Pendampingan sertifikasi halal BPJPH dan verifikasi berkas teknis dari tim Legal sangat responsif. Sangat terbantu!',
                    'status' => 'published',
                    'is_featured' => true,
                    'created_at' => now()->subDays(5),
                    'updated_at' => now()->subDays(5),
                ],
                [
                    'order_id' => $order->id,
                    'user_id' => $klien->id,
                    'rating' => 4,
                    'comment' => 'Pelayanan sertifikat CDOB farmasi berjalan dengan lancar dan transparan. Konsultan paham betul aturan BPOM.',
                    'status' => 'published',
                    'is_featured' => false,
                    'created_at' => now()->subDays(7),
                    'updated_at' => now()->subDays(7),
                ],
                [
                    'order_id' => $order->id,
                    'user_id' => $klien->id,
                    'rating' => 5,
                    'comment' => 'Pendaftaran akun klien baru sangat aman karena ada verifikasi 2 langkah OTP WhatsApp. Sukses terus CV Prasta!',
                    'status' => 'pending',
                    'is_featured' => false,
                    'created_at' => now()->subHours(8),
                    'updated_at' => now()->subHours(8),
                ],
            ];

            foreach ($sampleReviews as $rev) {
                $exists = DB::table('reviews')
                    ->where('user_id', $rev['user_id'])
                    ->where('comment', $rev['comment'])
                    ->first();

                if (!$exists) {
                    DB::table('reviews')->insert($rev);
                }
            }
        }

        // 3. Private Archives Seeder
        $archives = [
            [
                'title' => 'SK Penunjukan Resmi Konsultan CV Prasta Solusi Indonesia 2026',
                'category' => 'Legalitas Perusahaan',
                'file_path' => 'private_archives/legal/SK_Prasta_Solusi_2026.pdf',
                'file_size' => '2.4 MB',
                'uploaded_by' => 'Iqbal (Licensing Officer)',
                'notes' => 'Arsip induk legalitas penunjukan konsultan teknis resmi Kementerian.',
                'created_at' => now()->subDays(12),
                'updated_at' => now()->subDays(12),
            ],
            [
                'title' => 'Sertifikat Akreditasi Sistem Manajemen Mutu ISO 9001:2015',
                'category' => 'Sertifikat Resmi',
                'file_path' => 'private_archives/certificates/ISO_9001_Prasta_Certified.pdf',
                'file_size' => '1.8 MB',
                'uploaded_by' => 'Dania (Finance & Administration)',
                'notes' => 'Sertifikat pemenuhan standar mutu pelayanan perizinan.',
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20),
            ],
            [
                'title' => 'SOP Kajian Kepatuhan Regulasi Kemenkes, BPOM & BPJPH (Rev. 4)',
                'category' => 'Kajian Regulasi',
                'file_path' => 'private_archives/sop/SOP_Kepatuhan_Regulasi_KAK_2026.pdf',
                'file_size' => '3.5 MB',
                'uploaded_by' => 'Ahmad (Legal Specialist)',
                'notes' => 'Standar operasional kajian teknis berkas izin edar dan audit.',
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ],
            [
                'title' => 'Template Berita Acara Kajian Teknis (BAK) Resmi Prasta',
                'category' => 'Berita Acara',
                'file_path' => 'private_archives/templates/Format_BAK_Resmi_Prasta.docx',
                'file_size' => '620 KB',
                'uploaded_by' => 'Ahmad (Legal Specialist)',
                'notes' => 'Formulir standar penerbitan berita acara verifikasi dokumen klien.',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
        ];

        foreach ($archives as $arch) {
            $exists = DB::table('private_archives')->where('title', $arch['title'])->first();
            if (!$exists) {
                DB::table('private_archives')->insert($arch);
            }
        }

        // 4. Pastikan ada data Pembayaran Sampel untuk Verifikasi Finance
        if ($order) {
            $paymentWaiting = DB::table('payments')->where('order_id', $order->id)->first();
            if (!$paymentWaiting) {
                DB::table('payments')->insert([
                    'order_id' => $order->id,
                    'payment_method' => 'Transfer Bank Mandiri',
                    'amount' => 3500000.00,
                    'payment_proof' => 'uploads/payments/bukti_transfer_mandiri_pt_sejahtera.jpg',
                    'status' => 'waiting_confirmation',
                    'created_at' => now()->subHours(4),
                    'updated_at' => now()->subHours(4),
                ]);
            }
        }
    }
}
