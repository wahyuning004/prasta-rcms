<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Aktor: Admin & Direktur (Akses: /admin/dashboard)
        User::updateOrCreate(
            ['email' => 'admin@izinyou.com'],
            [
                'name' => 'Administrator IzinYou',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '081234567890',
                'company_name' => 'CV Prasta Solusi Indonesia',
                'address' => 'Babelan, Bekasi, Jawa Barat',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'direktur@izinyou.com'],
            [
                'name' => 'Prayoga (Direktur)',
                'password' => Hash::make('password'),
                'role' => 'direktur',
                'phone' => '081234567891',
                'company_name' => 'CV Prasta Solusi Indonesia',
                'address' => 'Babelan, Bekasi, Jawa Barat',
                'email_verified_at' => now(),
            ]
        );

        // Akun Admin alternatif untuk kompatibilitas
        User::updateOrCreate(
            ['email' => 'admin@prastasolusi.com'],
            [
                'name' => 'Admin Utama Prasta',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '081234567892',
                'company_name' => 'CV Prasta Solusi Indonesia',
                'address' => 'Babelan, Bekasi, Jawa Barat',
                'email_verified_at' => now(),
            ]
        );

        // 2. Aktor: Staf Internal / Petugas Layanan (Akses: /konsultan/dashboard)
        // a. Marketing & Client Relations
        User::updateOrCreate(
            ['email' => 'marketing@izinyou.com'],
            [
                'name' => 'Ari Agusta (Marketing & Client Relations)',
                'password' => Hash::make('password'),
                'role' => 'marketing',
                'phone' => '082111223344',
                'company_name' => 'CV Prasta Solusi Indonesia',
                'address' => 'Bekasi, Jawa Barat',
                'email_verified_at' => now(),
            ]
        );

        // b. Legal & Certification Specialist
        User::updateOrCreate(
            ['email' => 'legal@izinyou.com'],
            [
                'name' => 'Ahmad (Legal & Certification Specialist)',
                'password' => Hash::make('password'),
                'role' => 'legal',
                'phone' => '082155667788',
                'company_name' => 'CV Prasta Solusi Indonesia',
                'address' => 'Bekasi, Jawa Barat',
                'email_verified_at' => now(),
            ]
        );

        // c. Finance & Administration
        User::updateOrCreate(
            ['email' => 'finance@izinyou.com'],
            [
                'name' => 'Dania (Finance & Administration)',
                'password' => Hash::make('password'),
                'role' => 'finance',
                'phone' => '082199887766',
                'company_name' => 'CV Prasta Solusi Indonesia',
                'address' => 'Bekasi, Jawa Barat',
                'email_verified_at' => now(),
            ]
        );

        // d. Document & Licensing Officer
        User::updateOrCreate(
            ['email' => 'officer@izinyou.com'],
            [
                'name' => 'Iqbal (Document & Licensing Officer)',
                'password' => Hash::make('password'),
                'role' => 'officer',
                'phone' => '082144332211',
                'company_name' => 'CV Prasta Solusi Indonesia',
                'address' => 'Bekasi, Jawa Barat',
                'email_verified_at' => now(),
            ]
        );

        // e. Konsultan Teknis Umum
        User::updateOrCreate(
            ['email' => 'konsultan@izinyou.com'],
            [
                'name' => 'Dr. Hendra Gunawan, S.Si (Konsultan Teknis)',
                'password' => Hash::make('password'),
                'role' => 'konsultan',
                'phone' => '081298765432',
                'company_name' => 'CV Prasta Solusi Indonesia',
                'address' => 'Bandung, Jawa Barat',
                'email_verified_at' => now(),
            ]
        );

        // 3. Aktor: Pelanggan / Klien (Akses: /klien/dashboard)
        $klien = User::updateOrCreate(
            ['email' => 'klien@izinyou.com'],
            [
                'name' => 'Budi Santoso (PT Sejahtera Medika)',
                'password' => Hash::make('password'),
                'role' => 'klien',
                'phone' => '085612345678',
                'company_name' => 'PT Sejahtera Medika Nusantara',
                'address' => 'Kawasan Industri Cikarang, Jawa Barat',
                'email_verified_at' => now(),
            ]
        );

        // Pastikan ada sampel order untuk klien
        $order = DB::table('orders')->where('id', 1)->first();
        if (!$order) {
            $orderId = DB::table('orders')->insertGetId([
                'order_number' => 'ORD-20261008-001',
                'user_id' => $klien->id,
                'promo_id' => null,
                'total_cost' => 3500000.00,
                'status' => 'processing',
                'created_at' => now()->subDays(5),
                'updated_at' => now(),
            ]);
        } else {
            $orderId = $order->id;
        }

        // Pastikan dokumen sampel ada
        $docExists = DB::table('client_documents')->where('id', 3)->first();
        if (!$docExists) {
            DB::table('client_documents')->insert([
                [
                    'id' => 3,
                    'order_id' => $orderId,
                    'service_requirement_id' => 1,
                    'file_path' => 'uploads/documents/dokumen_teknis_mutu_alkes.pdf',
                    'status' => 'uploaded',
                    'notes' => 'Menunggu verifikasi teknis dari staf legal/konsultan',
                    'created_at' => now()->subHours(6),
                    'updated_at' => now()->subHours(6),
                ]
            ]);
        }

        // Pastikan konsultasi sampel ada
        $consExists = DB::table('free_consultations')->where('id', 2)->first();
        if (!$consExists) {
            DB::table('free_consultations')->insert([
                'id' => 2,
                'user_id' => $klien->id,
                'subject' => 'Konsultasi Perpanjangan Izin Edar Alkes Elektromedis',
                'message' => 'Selamat siang tim IzinYou, kami berencana memperpanjang izin edar alat elektromedis yang akan kedaluwarsa bulan depan. Apakah format uji berkala mengikuti ketentuan Kemenkes terbaru?',
                'attachment' => null,
                'response' => null,
                'status' => 'pending',
                'created_at' => now()->subHours(3),
                'updated_at' => now()->subHours(3),
            ]);
        }
    }
}
