<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test nomor HP yang mengandung huruf (seperti '0899965254a') ditolak mutlak.
     */
    public function test_phone_number_with_letters_is_strictly_rejected(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Budi Santoso',
            'company'               => 'PT Sukses Makmur',
            'email'                 => 'valid.pic@perusahaan.com',
            'phone'                 => '0899965254a', // Ada huruf 'a'
            'password'              => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
            'terms'                 => 'on',
            'g-recaptcha-response'  => 'mock-token',
        ]);

        $response->assertSessionHasErrors(['phone']);
        $this->assertDatabaseMissing('users', [
            'email' => 'valid.pic@perusahaan.com',
        ]);
    }

    /**
     * Test nama PIC yang mengandung angka (seperti 'wah123') ditolak.
     */
    public function test_pic_name_with_numbers_is_rejected(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'wah123', // Ada angka 123
            'company'               => 'PT Sukses Makmur',
            'email'                 => 'wah.test@perusahaan.com',
            'phone'                 => '081234567890',
            'password'              => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
            'terms'                 => 'on',
            'g-recaptcha-response'  => 'mock-token',
        ]);

        $response->assertSessionHasErrors(['name']);
        $this->assertDatabaseMissing('users', [
            'email' => 'wah.test@perusahaan.com',
        ]);
    }

    /**
     * Test nomor HP yang bukan format seluler Indonesia ditolak.
     */
    public function test_phone_number_with_invalid_indonesian_format_is_rejected(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Budi Santoso',
            'company'               => 'PT Sukses Makmur',
            'email'                 => 'format.test@perusahaan.com',
            'phone'                 => '12345', // Terlalu pendek & bukan 08/628
            'password'              => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
            'terms'                 => 'on',
            'g-recaptcha-response'  => 'mock-token',
        ]);

        $response->assertSessionHasErrors(['phone']);
    }

    /**
     * Test password yang tidak mengandung kombinasi angka ditolak.
     */
    public function test_weak_password_without_numbers_is_rejected(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Budi Santoso',
            'company'               => 'PT Sukses Makmur',
            'email'                 => 'weak.pwd@perusahaan.com',
            'phone'                 => '081234567890',
            'password'              => 'hanyahurufsaja',
            'password_confirmation' => 'hanyahurufsaja',
            'terms'                 => 'on',
            'g-recaptcha-response'  => 'mock-token',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /**
     * Test registrasi dengan seluruh format valid berhasil dan mengarahkan ke OTP.
     */
    public function test_valid_registration_passes_and_redirects_to_otp(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Budi Santoso, S.Kom',
            'company'               => 'PT Sukses Berkah Abadi',
            'email'                 => 'testing.budi@suksesabadi.co.id',
            'phone'                 => '089876543210',
            'password'              => 'BudiSandi123',
            'password_confirmation' => 'BudiSandi123',
            'terms'                 => '1',
            'g-recaptcha-response'  => 'mock-token',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('otp.verify'));

        $this->assertDatabaseHas('users', [
            'email' => 'testing.budi@suksesabadi.co.id',
            'phone' => '089876543210',
            'role'  => 'klien',
        ]);
    }

    /**
     * Test login menggunakan nomor WhatsApp berhasil.
     */
    public function test_user_can_login_using_phone_number(): void
    {
        $user = User::factory()->create([
            'email'             => 'klien.login@perusahaan.com',
            'phone'             => '081299887766',
            'password'          => bcrypt('Password123'),
            'role'              => 'klien',
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email'                => '081299887766', // Input nomor WhatsApp
            'password'             => 'Password123',
            'g-recaptcha-response' => 'mock-token',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test login menggunakan email berhasil.
     */
    public function test_user_can_login_using_email(): void
    {
        $user = User::factory()->create([
            'email'             => 'klien.email@perusahaan.com',
            'phone'             => '081233445566',
            'password'          => bcrypt('Password123'),
            'role'              => 'klien',
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email'                => 'klien.email@perusahaan.com',
            'password'             => 'Password123',
            'g-recaptcha-response' => 'mock-token',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test format login yang tidak valid (bukan email dan bukan HP) ditolak.
     */
    public function test_invalid_login_format_is_rejected(): void
    {
        $response = $this->post('/login', [
            'email'                => 'karakter_rusak_123$$$',
            'password'             => 'Password123',
            'g-recaptcha-response' => 'mock-token',
        ]);

        $response->assertSessionHasErrors(['email']);
    }
}

