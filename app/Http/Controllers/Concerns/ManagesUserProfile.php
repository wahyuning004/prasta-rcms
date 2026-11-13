<?php

namespace App\Http\Controllers\Concerns;

use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

trait ManagesUserProfile
{
    /**
     * Normalisasi input dasar profil (nama, email, HP, dan perusahaan).
     */
    protected function normalizeBaseProfileInputs(Request $request): void
    {
        $request->merge([
            'name' => trim((string) preg_replace('/\s+/', ' ', (string) $request->input('name', ''))),
            'email' => strtolower(trim((string) $request->input('email', ''))),
            'phone' => trim((string) $request->input('phone', '')),
            'company_name' => trim((string) preg_replace('/\s+/', ' ', (string) $request->input('company_name', ''))),
        ]);
    }

    /**
     * Aturan validasi nama lengkap tanpa angka.
     *
     * @return array<int, mixed>
     */
    protected function profileNameRules(bool $allowParenthesesAndAmpersand = false): array
    {
        $regex = $allowParenthesesAndAmpersand
            ? '/^[\pL\s\.\'\,\-\(\)\&]+$/u'
            : '/^[\pL\s\.\'\,\-]+$/u';

        return [
            'required',
            'string',
            'min:3',
            'max:100',
            'regex:'.$regex,
            function (string $attribute, mixed $value, \Closure $fail): void {
                if (preg_match('/[0-9]/', (string) $value)) {
                    $fail('Nama lengkap tidak boleh mengandung angka.');
                }
            },
        ];
    }

    /**
     * Aturan validasi email akun unik.
     *
     * @return array<int, mixed>
     */
    protected function profileEmailRules(int $ignoreUserId): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            Rule::unique('users', 'email')->ignore($ignoreUserId),
        ];
    }

    /**
     * Aturan validasi nomor HP / WhatsApp Indonesia beserta cek duplikasi terstandardisasi.
     *
     * @return array<int, mixed>
     */
    protected function profilePhoneRules(int $ignoreUserId): array
    {
        return [
            'required',
            'string',
            function (string $attribute, mixed $value, \Closure $fail) use ($ignoreUserId): void {
                $val = (string) $value;
                if (preg_match('/[a-zA-Z]/', $val)) {
                    $fail('Nomor HP / WhatsApp hanya boleh berisi angka.');

                    return;
                }
                if (! preg_match('/^\+?[0-9]+$/', $val)) {
                    $fail('Nomor HP / WhatsApp tidak boleh mengandung spasi atau simbol selain "+" di awal.');

                    return;
                }
                if (! preg_match('/^(?:\+62|62|0)8[1-9][0-9]{7,11}$/', $val)) {
                    $fail('Format nomor HP tidak valid. Gunakan nomor seluler Indonesia (08xx / 628xx / +628xx, 10–14 digit).');

                    return;
                }

                $formattedPhone = WhatsAppService::formatPhoneNumber($val);
                $localPhone = '0'.substr($formattedPhone, 2);
                $plusPhone = '+'.$formattedPhone;

                $exists = User::where('id', '!=', $ignoreUserId)
                    ->whereIn('phone', [$localPhone, $formattedPhone, $plusPhone, $val])
                    ->exists();

                if ($exists) {
                    $fail('Nomor HP / WhatsApp ini sudah digunakan oleh akun lain.');
                }
            },
        ];
    }

    /**
     * Pesan error default untuk validasi profil dasar.
     *
     * @return array<string, string>
     */
    protected function baseProfileMessages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama lengkap minimal 3 karakter.',
            'name.regex' => 'Nama lengkap hanya boleh berisi huruf dan tanda baca standar.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah digunakan oleh akun lain.',
            'phone.required' => 'Nomor HP / WhatsApp wajib diisi.',
        ];
    }

    /**
     * Konversi nomor telepon valid ke format lokal 08xxx.
     */
    protected function normalizePhoneToLocal(string $phone): string
    {
        $formattedPhone = WhatsAppService::formatPhoneNumber($phone);

        return '0'.substr($formattedPhone, 2);
    }

    /**
     * Validasi dan perbarui kata sandi milik akun yang sedang login.
     */
    protected function performPasswordUpdate(Request $request, string $redirectRoute): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:100',
                'regex:/^(?=.*[a-zA-Z])(?=.*\d).{8,}$/',
                'confirmed',
            ],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.regex' => 'Kata sandi baru harus mengandung kombinasi huruf dan angka.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $request->user()->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        return redirect()->route($redirectRoute)
            ->with('success', 'Kata sandi akun berhasil diperbarui.');
    }
}
