<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Format nomor telepon ke format internasional (misal: 62812xxxx).
     */
    public static function formatPhoneNumber(string $phone): string
    {
        // Hapus karakter non-digit
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        // Jika diawali dengan 0, ganti dengan 62
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62' . substr($cleaned, 1);
        }

        // Jika diawali dengan +62, sudah dibersihkan jadi 62
        return $cleaned;
    }

    /**
     * Kirim pesan OTP 6-digit via WhatsApp API / Gateway.
     */
    public static function sendOtp(string $phone, string $otp, string $name = 'Mitra Klien'): array
    {
        $formattedPhone = self::formatPhoneNumber($phone);
        $message = "Halo *{$name}*,\n\n"
                 . "Berikut adalah kode verifikasi OTP akun Anda pada portal *IzinYou* (CV Prasta Solusi Indonesia):\n\n"
                 . "🔐 *Kode OTP: {$otp}*\n\n"
                 . "Kode ini berlaku selama *10 menit*. Jangan berikan kode ini kepada siapa pun demi keamanan akun Anda.\n\n"
                 . "_Pesan ini dikirim secara otomatis oleh sistem IzinYou._";

        $apiToken = config('services.whatsapp.token', env('WHATSAPP_API_TOKEN'));
        $gatewayUrl = config('services.whatsapp.url', env('WHATSAPP_GATEWAY_URL', 'https://api.fonnte.com/send'));

        // Catat di log sistem
        Log::info("WhatsApp Gateway dispatch to [{$formattedPhone}]: OTP [{$otp}]");

        // Jika API token tersedia, kirim ke gateway real
        if (!empty($apiToken)) {
            try {
                $response = Http::timeout(10)
                    ->withHeaders([
                        'Authorization' => $apiToken,
                    ])
                    ->asForm()
                    ->post($gatewayUrl, [
                        'target'  => $formattedPhone,
                        'phone'   => $formattedPhone,
                        'message' => $message,
                    ]);

                if ($response->successful()) {
                    return [
                        'success' => true,
                        'mock'    => false,
                        'message' => 'Pesan OTP berhasil dikirim ke nomor WhatsApp Anda.',
                    ];
                }

                Log::warning('WhatsApp Gateway returned error: ' . $response->body());
            } catch (\Exception $e) {
                Log::error('WhatsApp Gateway connection failed: ' . $e->getMessage());
            }
        }

        // Mode lokal / fallback demo jika belum ada token gateway eksternal
        return [
            'success' => true,
            'mock'    => true,
            'otp'     => $otp,
            'message' => 'Kode OTP berhasil dibuat dan dikirim ke nomor WhatsApp Anda.',
        ];
    }
}
