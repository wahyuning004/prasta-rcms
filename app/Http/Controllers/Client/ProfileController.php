<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Concerns\ManagesUserProfile;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    use ManagesUserProfile;

    /**
     * Tampilkan halaman profil pelanggan.
     */
    public function edit(Request $request): View
    {
        return view('klien.profile', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Perbarui data profil pelanggan yang sedang login.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $this->normalizeBaseProfileInputs($request);
        $rawNib = preg_replace('/\s+/', '', (string) $request->input('nib', ''));
        $rawNpwp = trim((string) $request->input('npwp', ''));

        $request->merge([
            'nib' => $rawNib !== '' ? $rawNib : null,
            'npwp' => $rawNpwp !== '' ? $rawNpwp : null,
        ]);

        $validated = $request->validate([
            'name' => $this->profileNameRules(),
            'email' => $this->profileEmailRules($user->id),
            'phone' => $this->profilePhoneRules($user->id),
            'company_name' => [
                'required',
                'string',
                'min:3',
                'max:150',
                'regex:/^(?=.*[\pL])[a-zA-Z0-9\s\.\,\-\/\&\'\(\)]+$/u',
            ],
            'address' => ['required', 'string', 'min:5', 'max:500'],
            'pic_position' => ['nullable', 'string', 'max:100'],
            'nib' => ['nullable', 'regex:/^[0-9]{13}$/'],
            'npwp' => ['nullable', 'string', 'max:30', 'regex:/^[0-9\.\-]{15,20}$/'],
            'business_field' => ['nullable', 'string', 'max:150'],
        ], array_merge($this->baseProfileMessages(), [
            'company_name.required' => 'Nama perusahaan wajib diisi.',
            'company_name.min' => 'Nama perusahaan minimal 3 karakter.',
            'company_name.regex' => 'Format nama perusahaan tidak valid.',
            'address.required' => 'Alamat perusahaan / domisili wajib diisi.',
            'address.min' => 'Alamat minimal 5 karakter.',
            'nib.regex' => 'Nomor Induk Berusaha (NIB) harus terdiri dari tepat 13 digit angka.',
            'npwp.regex' => 'Format NPWP tidak valid (contoh: 94.812.334.2-407.000 atau 15–16 digit angka).',
        ]));

        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $this->normalizePhoneToLocal($validated['phone']),
            'company_name' => $validated['company_name'],
            'address' => trim($validated['address']),
            'pic_position' => $validated['pic_position'] ? trim($validated['pic_position']) : null,
            'nib' => $validated['nib'] ?? null,
            'npwp' => $validated['npwp'] ?? null,
            'business_field' => $validated['business_field'] ? trim($validated['business_field']) : null,
        ])->save();

        return redirect()->route('klien.profile.edit')
            ->with('success', 'Data profil pelanggan dan perusahaan berhasil diperbarui.');
    }

    /**
     * Perbarui kata sandi akun pelanggan.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        return $this->performPasswordUpdate($request, 'klien.profile.edit');
    }
}
