<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Cek apakah admin sudah ada, jika belum buat baru
        $exists = DB::table('users')->where('email', 'admin@prastasolusi.com')->first();

        if (!$exists) {
            DB::table('users')->insert([
                'name'       => 'Admin Prasta',
                'email'      => 'admin@prastasolusi.com',
                'password'   => Hash::make('prasta@admin2026'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('✅ Admin user berhasil dibuat: admin@prastasolusi.com');
        } else {
            $this->command->info('ℹ️  Admin sudah ada, seeder dilewati.');
        }
    }
}
