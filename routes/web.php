<?php

// Import facades dan seluruh controller yang dibutuhkan
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Staff\MarketingController;
use App\Http\Controllers\Staff\LegalController;
use App\Http\Controllers\Staff\FinanceController;

// ================================================================
// ROUTE PUBLIK: Landing Page, Katalog Layanan & Detail Layanan
// Dapat diakses tanpa login oleh siapapun
// ================================================================
Route::get('/', [LandingPageController::class, 'index'])->name('home');                                              // Halaman utama landing page
Route::get('/katalog', [LandingPageController::class, 'catalog'])->name('catalog.index');                            // Halaman tersendiri Katalog Semua Layanan & Voucher Promo
Route::get('/layanan/{slug}', [LandingPageController::class, 'showService'])->name('services.show');                 // Detail layanan berdasarkan slug
Route::post('/layanan/konsultasi-dokumen', [LandingPageController::class, 'submitDocConsultation'])->name('services.consultation'); // Kirim form konsultasi dokumen

// ================================================================
// ROUTE AUTH: Hanya bisa diakses tamu (belum login)
// ================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');           // Tampilkan form login
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');         // Proses login
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');  // Tampilkan form daftar akun
    Route::post('/register', [AuthController::class, 'register'])->name('register.post'); // Proses registrasi akun baru
    Route::get('/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('otp.verify');        // Form verifikasi OTP
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('otp.verify.post');      // Proses verifikasi OTP
    Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('otp.resend');           // Kirim ulang OTP
    Route::get('/verify-email', function () { return redirect()->route('otp.verify'); })->name('verify.email'); // Redirect ke halaman OTP
});

// Logout: hanya bisa diakses saat sudah login
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ================================================================
// ROUTE TERPROTEKSI: Wajib login, diarahkan sesuai role
// ================================================================
Route::middleware('auth')->group(function () {
    // Redirect dashboard utama (akan diarahkan sesuai role user)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ============================================================
    // 1. ADMIN & DIREKTUR: Akses penuh ke seluruh manajemen sistem
    // ============================================================
    Route::middleware('role:admin,direktur')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard'); // Dashboard admin
        
        // CRUD master kategori layanan perizinan
        Route::resource('categories', CategoryController::class);

        // CRUD master layanan perizinan & sertifikasi
        Route::resource('services', ServiceController::class);

        // CRUD user staf internal (tanpa show, create, edit — dikelola via modal/AJAX)
        Route::resource('users', UserController::class)->except(['show', 'create', 'edit']);

        // Ekspor laporan keuangan & mutu ke PDF / Excel
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/finance/pdf', [ReportController::class, 'exportFinancePdf'])->name('reports.finance.pdf');
        Route::get('/reports/finance/excel', [ReportController::class, 'exportFinanceExcel'])->name('reports.finance.excel');
        Route::get('/reports/quality/pdf', [ReportController::class, 'exportQualityPdf'])->name('reports.quality.pdf');
        Route::get('/reports/quality/excel', [ReportController::class, 'exportQualityExcel'])->name('reports.quality.excel');
    });

    // ============================================================
    // 2. STAF MULTI-ROLE: Dashboard terpisah sesuai fungsi kerja
    // ============================================================
    
    // A. MARKETING: Kelola konsultasi klien, promo, dan moderasi ulasan
    Route::middleware('role:marketing,admin')->prefix('marketing')->name('marketing.')->group(function () {
        Route::get('/dashboard', [MarketingController::class, 'dashboard'])->name('dashboard');                          // Dashboard marketing
        Route::post('/consultation/{id}/reply', [MarketingController::class, 'replyConsultation'])->name('consultation.reply'); // Balas konsultasi klien
        Route::post('/promos', [MarketingController::class, 'storePromo'])->name('promos.store');                        // Tambah promo baru
        Route::put('/promos/{id}', [MarketingController::class, 'updatePromo'])->name('promos.update');                  // Update promo
        Route::delete('/promos/{id}', [MarketingController::class, 'destroyPromo'])->name('promos.destroy');             // Hapus promo
        Route::post('/reviews/{id}/moderate', [MarketingController::class, 'moderateReview'])->name('reviews.moderate'); // Moderasi ulasan klien
    });

    // B. LEGAL: Verifikasi dokumen, generate BAK, simpan checksheet regulasi
    Route::middleware('role:legal,konsultan,admin')->prefix('legal')->name('legal.')->group(function () {
        Route::get('/dashboard', [LegalController::class, 'dashboard'])->name('dashboard');                   // Dashboard legal
        Route::post('/document/{id}/verify', [LegalController::class, 'verifyDocument'])->name('document.verify'); // Verifikasi dokumen klien
        Route::get('/document/{id}/bak', [LegalController::class, 'generateBak'])->name('document.bak');     // Generate Berita Acara Kelengkapan (BAK)
        Route::post('/checksheet/save', [LegalController::class, 'saveChecksheet'])->name('checksheet.save'); // Simpan checksheet audit regulasi
    });

    // C. FINANCE: Verifikasi pembayaran, invoice, tracking, arsip keuangan
    Route::middleware('role:finance,officer,petugas,admin')->prefix('finance')->name('finance.')->group(function () {
        Route::get('/dashboard', [FinanceController::class, 'dashboard'])->name('dashboard');                          // Dashboard keuangan
        Route::post('/payment/{id}/verify', [FinanceController::class, 'verifyPayment'])->name('payment.verify');     // Verifikasi bukti pembayaran
        Route::get('/invoice/{orderId}', [FinanceController::class, 'generateInvoice'])->name('invoice.generate');    // Generate invoice PDF
        Route::post('/tracking/add', [FinanceController::class, 'addTracking'])->name('tracking.add');                // Tambah data tracking progres
        Route::post('/archives/upload', [FinanceController::class, 'uploadArchive'])->name('archives.upload');        // Upload arsip dokumen
        Route::delete('/archives/{id}', [FinanceController::class, 'destroyArchive'])->name('archives.destroy');      // Hapus arsip
        Route::get('/export/excel', [FinanceController::class, 'exportExcel'])->name('export.excel');                 // Ekspor data keuangan ke Excel
    });

    // Kompatibilitas mundur: redirect role lama ke dashboard sesuai role baru
    Route::get('/konsultan/dashboard', function () {
        return redirect()->route(auth()->user()->getDashboardRoute()); // Arahkan konsultan ke dashboard yang sesuai
    })->name('konsultan.dashboard');

    Route::get('/petugas/dashboard', function () {
        return redirect()->route(auth()->user()->getDashboardRoute()); // Arahkan petugas ke dashboard yang sesuai
    })->name('petugas.dashboard');

    // ============================================================
    // 3. KLIEN / PELANGGAN: Portal mandiri untuk order & konsultasi
    // ============================================================
    Route::middleware('role:klien')->prefix('klien')->name('klien.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'klienDashboard'])->name('dashboard');                       // Dashboard klien
        Route::post('/consultation/submit', [DashboardController::class, 'submitConsultation'])->name('consultation.submit'); // Kirim pertanyaan konsultasi
        Route::post('/order/checkout', [DashboardController::class, 'checkoutOrder'])->name('order.checkout');              // Proses checkout / pemesanan layanan
    });
});
