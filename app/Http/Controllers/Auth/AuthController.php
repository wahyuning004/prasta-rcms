<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->getDashboardRoute());
        }

        return view('auth.login');
    }

    /**
     * Proses login menggunakan database dengan validasi email/nomor WhatsApp,
     * reCAPTCHA & status verifikasi klien.
     */
    public function login(Request $request)
    {
        // 1. Ambil dan bersihkan input login (bisa email atau nomor WhatsApp)
        $rawInput = trim((string) $request->input('email', $request->input('login', '')));
        $password = (string) $request->input('password', '');

        // 2. Deteksi apakah input merupakan format email atau nomor WhatsApp
        $isEmail = filter_var($rawInput, FILTER_VALIDATE_EMAIL) !== false;
        // Format HP Indonesia: 08xx, 628xx, atau +628xx (10–14 digit angka murni)
        $isPhone = (bool) preg_match('/^(?:\+62|62|0)8[1-9][0-9]{7,11}$/', $rawInput);

        // Validasi format kredensial login
        if (empty($rawInput)) {
            return back()
                ->withInput($request->only('email', 'login'))
                ->withErrors([
                    'email' => 'Alamat email atau nomor WhatsApp wajib diisi.',
                ]);
        }

        if (! $isEmail && ! $isPhone) {
            return back()
                ->withInput($request->only('email', 'login'))
                ->withErrors([
                    'email' => 'Format email atau nomor WhatsApp tidak valid. Gunakan email yang benar atau nomor HP Indonesia (contoh: 081234567890).',
                ]);
        }

        // Validasi Password
        $request->validate([
            'password' => ['required'],
        ], [
            'password.required' => 'Password wajib diisi.',
        ]);

        // 3. Validasi Google reCAPTCHA v2
        if (! $this->verifyRecaptcha($request->input('g-recaptcha-response'), $request->ip())) {
            return back()
                ->withInput($request->only('email', 'login'))
                ->withErrors([
                    'g-recaptcha-response' => 'Silakan centang verifikasi "Saya bukan robot" (reCAPTCHA).',
                ]);
        }

        $remember = $request->boolean('remember-me');

        // 4. Cari pengguna berdasarkan Email atau Nomor WhatsApp
        if ($isEmail) {
            $user = User::where('email', strtolower($rawInput))->first();
        } else {
            // Normalisasi nomor telepon untuk dicocokkan ke database
            $formattedPhone = WhatsAppService::formatPhoneNumber($rawInput); // e.g. 6281234567890
            $localPhone = '0'.substr($formattedPhone, 2);              // e.g. 081234567890
            $plusPhone = '+'.$formattedPhone;                         // e.g. +6281234567890

            $user = User::whereIn('phone', [$localPhone, $formattedPhone, $plusPhone, $rawInput])->first();
        }

        // 5. Periksa kecocokan kredensial
        if ($user && Hash::check($password, $user->password)) {
            // Cek apakah akun klien masih unverified (belum verifikasi OTP)
            if ($user->isKlien() && is_null($user->email_verified_at)) {
                // Generate OTP baru jika belum ada atau sudah expired
                $otp = sprintf('%06d', random_int(100000, 999999));
                $user->otp_code = $otp;
                $user->otp_expires_at = now()->addMinutes(10);
                $user->save();

                // Kirim OTP via WhatsApp
                WhatsAppService::sendOtp($user->phone ?? '', $otp, $user->name);

                session([
                    'pending_user_id' => $user->id,
                    'pending_phone' => $user->phone,
                    'pending_email' => $user->email,
                    'pending_name' => $user->name,
                    'otp_attempts' => 0,
                ]);

                if (config('app.debug')) {
                    session()->flash('debug_otp', $otp);
                }

                return redirect()->route('otp.verify')
                    ->with('warning', 'Akun Anda belum aktif. Kami telah mengirimkan 6 digit kode OTP ke nomor WhatsApp Anda ('.($user->phone ?? 'terdaftar').').');
            }

            // Login dan redirect ke dashboard
            Auth::login($user, $remember);
            $request->session()->regenerate();
            $targetRoute = $user->getDashboardRoute();

            return redirect()->intended(route($targetRoute))
                ->with('success', 'Selamat datang kembali, '.$user->name.'! Anda masuk sebagai '.$user->getRoleLabel().'.');
        }

        return back()
            ->withInput($request->only('email', 'login'))
            ->withErrors([
                'email' => 'Email/nomor WhatsApp atau password yang Anda masukkan salah. Silakan periksa kembali.',
            ]);
    }

    /**
     * Tampilkan formulir registrasi klien baru.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->getDashboardRoute());
        }

        return view('auth.register');
    }

    /**
     * Proses registrasi klien baru dengan validasi karakter ketat (airtight).
     * Mencegah karakter campur pada nomor HP, karakter angka pada nama PIC, dll.
     */
    public function register(Request $request)
    {
        // 1. Bersihkan dan rapikan input awal
        $rawEmail = strtolower(trim((string) $request->input('email', '')));
        $rawPhone = trim((string) $request->input('phone', ''));
        $rawName = trim(preg_replace('/\s+/', ' ', (string) $request->input('name', '')));
        $rawCompany = trim(preg_replace('/\s+/', ' ', (string) $request->input('company', '')));

        // Merge nilai yang sudah dirapikan ke request sebelum divalidasi
        $request->merge([
            'email' => $rawEmail,
            'phone' => $rawPhone,
            'name' => $rawName,
            'company' => $rawCompany,
        ]);

        // 2. Validasi Input Register Ketat (Anti-Terkecoh)
        $validated = $request->validate([
            // Nama Lengkap PIC: Hanya boleh huruf dan tanda baca nama standar, tidak boleh ada angka atau simbol aneh
            'name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[\pL\s\.\'\,\-]+$/u',
                function ($attribute, $value, $fail) {
                    if (preg_match('/[0-9]/', $value)) {
                        $fail('Nama lengkap PIC tidak boleh mengandung angka. Harap masukkan nama asli.');
                    }
                },
            ],

            // Nama Perusahaan: Minimal 3 karakter, harus mengandung huruf, tidak boleh simbol berbahaya
            'company' => [
                'required',
                'string',
                'min:3',
                'max:150',
                'regex:/^(?=.*[\pL])[a-zA-Z0-9\s\.\,\-\/\&\'\(\)]+$/u',
                function ($attribute, $value, $fail) {
                    if (preg_match('/[<>{}";$]/', $value)) {
                        $fail('Nama perusahaan tidak boleh mengandung karakter khusus berbahaya.');
                    }
                },
            ],

            // Email: Format RFC valid, lowercase, tidak ada spasi atau karakter aneh
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            ],

            // Nomor HP / WhatsApp: Mutlak hanya angka murni! Jika ada 1 huruf pun, tolak langsung!
            'phone' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    // Pengecekan 1: Tolak jika ada huruf alfabet (a-z, A-Z)
                    if (preg_match('/[a-zA-Z]/', $value)) {
                        $fail('Nomor WhatsApp hanya boleh berupa angka! Tidak boleh ada huruf sama sekali.');

                        return;
                    }

                    // Pengecekan 2: Tolak jika ada spasi, strip, atau karakter non-angka (selain '+' di depan)
                    if (! preg_match('/^\+?[0-9]+$/', $value)) {
                        $fail('Nomor WhatsApp hanya boleh berisi angka tanpa spasi atau simbol.');

                        return;
                    }

                    // Pengecekan 3: Format seluler Indonesia (08, 628, atau +628) dengan panjang 10-14 digit
                    if (! preg_match('/^(?:\+62|62|0)8[1-9][0-9]{7,11}$/', $value)) {
                        $fail('Format nomor WhatsApp tidak valid. Harus nomor seluler Indonesia yang diawali 08, 628, atau +628 dengan 10–14 digit angka.');

                        return;
                    }
                },
            ],

            // Password: Minimal 8 karakter, wajib kombinasi huruf dan angka
            'password' => [
                'required',
                'string',
                'min:8',
                'max:100',
                'regex:/^(?=.*[a-zA-Z])(?=.*\d).{8,}$/',
                'confirmed',
            ],

            // Persetujuan Terms & Conditions
            'terms' => ['accepted'],
        ], [
            'name.required' => 'Nama lengkap PIC wajib diisi.',
            'name.min' => 'Nama lengkap PIC minimal 3 karakter.',
            'name.max' => 'Nama lengkap PIC maksimal 100 karakter.',
            'name.regex' => 'Nama lengkap PIC hanya boleh berisi huruf dan tanda baca nama (titik, koma, petik tunggal, strip).',
            'company.required' => 'Nama perusahaan wajib diisi.',
            'company.min' => 'Nama perusahaan minimal 3 karakter.',
            'company.max' => 'Nama perusahaan maksimal 150 karakter.',
            'company.regex' => 'Nama perusahaan harus valid dan mengandung huruf.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.regex' => 'Format alamat email tidak valid atau domain tidak lengkap.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'password.regex' => 'Password harus mengandung kombinasi huruf dan angka.',
            'password.confirmed' => 'Konfirmasi password tidak cocok dengan password.',
            'terms.accepted' => 'Anda harus menyetujui Syarat & Ketentuan untuk melanjutkan pendaftaran.',
        ]);

        // 3. Validasi Google reCAPTCHA v2
        if (! $this->verifyRecaptcha($request->input('g-recaptcha-response'), $request->ip())) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors([
                    'g-recaptcha-response' => 'Silakan centang verifikasi "Saya bukan robot" (reCAPTCHA).',
                ]);
        }

        // 4. Normalisasi format nomor telepon ke format konsisten Indonesia (08xxxxxxxxx)
        $formattedPhone = WhatsAppService::formatPhoneNumber($rawPhone); // 628xxxxxxxx
        $localPhone = '0'.substr($formattedPhone, 2);             // 08xxxxxxxx
        $plusPhone = '+'.$formattedPhone;                        // +628xxxxxxxx

        // 5. Validasi Keunikan Email & Nomor WhatsApp terhadap akun yang sudah aktif (Verified)
        $existingEmailUser = User::where('email', $rawEmail)->first();
        if ($existingEmailUser && ! is_null($existingEmailUser->email_verified_at)) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors([
                    'email' => 'Alamat email ini sudah terdaftar dan aktif. Silakan masuk ke akun Anda.',
                ]);
        }

        $existingPhoneUser = User::whereIn('phone', [$localPhone, $formattedPhone, $plusPhone])->first();
        if ($existingPhoneUser && ! is_null($existingPhoneUser->email_verified_at)) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors([
                    'phone' => 'Nomor WhatsApp ini sudah terdaftar dan aktif pada akun lain. Silakan gunakan nomor lain atau masuk ke akun Anda.',
                ]);
        }

        // Jika nomor HP sedang dipakai oleh akun unverified dengan email berbeda, tolak untuk mencegah tabrakan OTP
        if ($existingPhoneUser && is_null($existingPhoneUser->email_verified_at) && $existingPhoneUser->email !== $rawEmail) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors([
                    'phone' => 'Nomor WhatsApp ini sedang dalam proses verifikasi pada pendaftaran lain. Silakan gunakan nomor lain atau selesaikan verifikasi.',
                ]);
        }

        // 6. Generate Token OTP 6-Digit Angka
        $otp = sprintf('%06d', random_int(100000, 999999));

        // 7. Simpan atau perbarui data pendaftaran unverified
        if ($existingEmailUser && is_null($existingEmailUser->email_verified_at)) {
            // Pengguna mencoba daftar ulang karena OTP sebelumnya expired / salah input
            $user = $existingEmailUser;
            $user->name = $validated['name'];
            $user->company_name = $validated['company'];
            $user->phone = $localPhone;
            $user->password = Hash::make($validated['password']);
            $user->otp_code = $otp;
            $user->otp_expires_at = now()->addMinutes(10);
            $user->save();
        } else {
            // Pendaftaran baru
            $user = User::create([
                'name' => $validated['name'],
                'company_name' => $validated['company'],
                'email' => $rawEmail,
                'phone' => $localPhone,
                'password' => Hash::make($validated['password']),
                'role' => 'klien',
                'email_verified_at' => null, // Status Unverified
                'otp_code' => $otp,
                'otp_expires_at' => now()->addMinutes(10),
            ]);
        }

        // 8. Kirim Token OTP via WhatsApp Gateway
        WhatsAppService::sendOtp($user->phone, $otp, $user->name);

        // 9. Simpan state sesi pendaftaran untuk halaman verifikasi OTP
        session([
            'pending_user_id' => $user->id,
            'pending_phone' => $user->phone,
            'pending_email' => $user->email,
            'pending_name' => $user->name,
            'otp_attempts' => 0,
            'last_otp_resend' => now()->timestamp,
        ]);

        if (config('app.debug')) {
            session()->flash('debug_otp', $otp);
        }

        return redirect()->route('otp.verify')
            ->with('success', 'Pendaftaran berhasil! Kode OTP verifikasi 6-digit telah dikirimkan ke nomor WhatsApp Anda ('.$user->phone.').');
    }

    /**
     * Tampilkan halaman verifikasi OTP.
     */
    public function showVerifyOtp(Request $request)
    {
        $userId = session('pending_user_id');

        if (! $userId) {
            return redirect()->route('register')
                ->with('error', 'Sesi pendaftaran tidak ditemukan. Silakan isi form registrasi terlebih dahulu.');
        }

        $user = User::find($userId);

        if (! $user) {
            session()->forget(['pending_user_id', 'pending_phone', 'pending_email', 'pending_name', 'otp_attempts']);

            return redirect()->route('register')
                ->with('error', 'Data pengguna tidak ditemukan. Silakan daftar kembali.');
        }

        // Jika sudah terverifikasi, arahkan ke login
        if (! is_null($user->email_verified_at)) {
            return redirect()->route('login')
                ->with('info', 'Akun Anda sudah aktif. Silakan masuk dengan email/nomor WhatsApp dan password.');
        }

        return view('auth.verify-otp', [
            'user' => $user,
            'phone' => session('pending_phone', $user->phone),
            'email' => session('pending_email', $user->email),
        ]);
    }

    /**
     * Proses verifikasi 6-digit OTP token dengan proteksi brute-force.
     */
    public function verifyOtp(Request $request)
    {
        $userId = session('pending_user_id', $request->input('user_id'));

        if (! $userId) {
            return redirect()->route('register')
                ->with('error', 'Sesi verifikasi berakhir. Silakan daftar kembali.');
        }

        $user = User::find($userId);

        if (! $user) {
            return redirect()->route('register')
                ->with('error', 'Pengguna tidak ditemukan.');
        }

        // Proteksi Brute-Force: Maksimal 5 kali percobaan gagal
        $attempts = (int) session('otp_attempts', 0);
        if ($attempts >= 5) {
            $user->otp_code = null;
            $user->otp_expires_at = null;
            $user->save();

            return back()->withErrors([
                'otp' => 'Anda telah salah memasukkan kode OTP sebanyak 5 kali. Demi keamanan akun, kode OTP telah hangus. Silakan klik "Kirim Ulang Kode OTP" untuk meminta kode baru.',
            ]);
        }

        // Gabungkan input jika dikirim per-kotak array atau satu field
        $otpInput = $request->input('otp');
        if (is_array($otpInput)) {
            $otpCode = implode('', $otpInput);
        } else {
            $otpCode = trim((string) $otpInput);
        }

        // Validasi format OTP: Mutlak 6 digit angka
        if (empty($otpCode) || strlen($otpCode) !== 6 || ! ctype_digit($otpCode)) {
            return back()->withErrors([
                'otp' => 'Masukkan 6 digit kode OTP angka secara lengkap.',
            ]);
        }

        // Validasi masa berlaku OTP
        if ($user->otp_expires_at && now()->isAfter($user->otp_expires_at)) {
            return back()->withErrors([
                'otp' => 'Kode OTP telah kadaluarsa (melewati batas 10 menit). Silakan klik "Kirim Ulang Kode OTP".',
            ]);
        }

        // Validasi kecocokan kode OTP
        if ($user->otp_code !== $otpCode) {
            $attempts++;
            session(['otp_attempts' => $attempts]);
            $remaining = 5 - $attempts;

            $errorMsg = $remaining > 0
                ? "Kode OTP tidak cocok. Periksa kembali pesan di WhatsApp Anda (Sisa kesempatan: {$remaining}x)."
                : 'Kode OTP tidak cocok dan batas percobaan telah habis. Silakan klik "Kirim Ulang Kode OTP".';

            return back()->withErrors([
                'otp' => $errorMsg,
            ]);
        }

        // Aktifkan akun (Verified)
        $user->email_verified_at = now();
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        // Bersihkan sesi pending
        session()->forget(['pending_user_id', 'pending_phone', 'pending_email', 'pending_name', 'otp_attempts', 'last_otp_resend']);

        // Login otomatis pengguna
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('klien.dashboard')
            ->with('success', 'Selamat, nomor WhatsApp dan akun Anda berhasil diverifikasi! Selamat datang di IzinYou.');
    }

    /**
     * Kirim ulang kode OTP via WhatsApp dengan batas waktu cooldown 60 detik.
     */
    public function resendOtp(Request $request)
    {
        $userId = session('pending_user_id', $request->input('user_id'));

        if (! $userId) {
            return redirect()->route('register')
                ->with('error', 'Sesi tidak valid. Silakan lakukan pendaftaran ulang.');
        }

        $user = User::find($userId);

        if (! $user) {
            return redirect()->route('register')
                ->with('error', 'Data pengguna tidak ditemukan.');
        }

        // Cooldown cek (60 detik)
        $lastResend = (int) session('last_otp_resend', 0);
        $secondsSinceLast = now()->timestamp - $lastResend;
        if ($secondsSinceLast < 60 && $lastResend > 0) {
            $waitSeconds = 60 - $secondsSinceLast;

            return back()->withErrors([
                'otp' => "Harap tunggu {$waitSeconds} detik lagi sebelum meminta kode OTP baru.",
            ]);
        }

        // Generate kode OTP baru
        $otp = sprintf('%06d', random_int(100000, 999999));
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        // Reset attempt counter & catat waktu resend
        session([
            'otp_attempts' => 0,
            'last_otp_resend' => now()->timestamp,
        ]);

        // Kirim via WhatsApp Gateway
        WhatsAppService::sendOtp($user->phone, $otp, $user->name);

        if (config('app.debug')) {
            session()->flash('debug_otp', $otp);
        }

        return back()->with('success', 'Kode OTP baru telah berhasil dikirimkan ke WhatsApp Anda ('.$user->phone.').');
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda berhasil keluar dari sistem.');
    }

    /**
     * Helper verifikasi respon Google reCAPTCHA v2.
     */
    protected function verifyRecaptcha(?string $token, ?string $ip = null): bool
    {
        if (empty($token)) {
            return false;
        }

        $secret = config('services.recaptcha.secret_key', env('RECAPTCHA_SECRET_KEY'));

        if (empty($secret)) {
            return true;
        }

        try {
            $response = Http::asForm()->timeout(5)->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $ip,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return (bool) ($data['success'] ?? false);
            }
        } catch (\Exception $e) {
            Log::warning('reCAPTCHA siteverify error: '.$e->getMessage());
            // Dalam mode testing dengan test key Google, izinkan bypass jika gagal koneksi
            if ($secret === '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe') {
                return true;
            }
        }

        return false;
    }
}
