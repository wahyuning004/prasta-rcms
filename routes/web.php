<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Client\DocumentController as ClientDocumentController;
use App\Http\Controllers\Client\OrderController as ClientOrderController;
use App\Http\Controllers\Client\ProfileController as ClientProfileController;
use App\Http\Controllers\Client\ReviewController as ClientReviewController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Finance\FinanceController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\Legal\LegalController;
use App\Http\Controllers\Marketing\MarketingController;
use App\Http\Controllers\Officer\OfficerController;
use Illuminate\Support\Facades\Route;

// ================================================================
// ROUTE PUBLIK: Landing Page, Katalog Layanan & Detail Layanan
// Dapat diakses tanpa login oleh siapapun
// ================================================================
Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::get('/katalog', [LandingPageController::class, 'catalog'])->name('catalog.index');
Route::get('/catalog', [LandingPageController::class, 'catalog'])->name('catalog.legacy');
Route::get('/layanan/{slug}', [LandingPageController::class, 'showService'])->name('services.show');
Route::post('/layanan/konsultasi-dokumen', [LandingPageController::class, 'submitDocConsultation'])->name('services.consultation');

// ================================================================
// ROUTE AUTH: Hanya bisa diakses tamu (belum login)
// ================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('otp.verify');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('otp.verify.post');
    Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('otp.resend');
    Route::get('/verify-email', function () {
        return redirect()->route('otp.verify');
    })->name('verify.email');
});

// Logout: hanya bisa diakses saat sudah login
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ================================================================
// ROUTE TERPROTEKSI: Wajib login, diarahkan sesuai role
// ================================================================
Route::middleware('auth')->group(function () {
    // Redirect dashboard utama (akan diarahkan sesuai role user)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Akses file privat aman (diverifikasi pemilik pesanan atau petugas berwenang di controller)
    Route::get('/pembayaran/{payment}/bukti', [ClientOrderController::class, 'viewPaymentProof'])->name('payments.proof');
    Route::get('/dokumen/versi/{version}/unduh', [ClientDocumentController::class, 'downloadVersion'])->name('documents.version.download');
    Route::get('/dokumen/{document}/unduh', [ClientDocumentController::class, 'downloadCurrent'])->name('documents.current.download');

    // ============================================================
    // 1. ADMIN & DIREKTUR: Akses penuh ke seluruh manajemen sistem
    // ============================================================
    Route::middleware('role:admin,direktur')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('services', ServiceController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['show', 'create', 'edit']);

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
        Route::get('/dashboard', [MarketingController::class, 'dashboard'])->name('dashboard');
        Route::get('/tickets', [MarketingController::class, 'tickets'])->name('tickets');
        Route::post('/consultation/{id}/reply', [MarketingController::class, 'replyConsultation'])->name('consultation.reply');
        Route::get('/promos', [MarketingController::class, 'promos'])->name('promos.index');
        Route::post('/promos', [MarketingController::class, 'storePromo'])->name('promos.store');
        Route::put('/promos/{id}', [MarketingController::class, 'updatePromo'])->name('promos.update');
        Route::delete('/promos/{id}', [MarketingController::class, 'destroyPromo'])->name('promos.destroy');
        Route::get('/reviews', [MarketingController::class, 'reviews'])->name('reviews.index');
        Route::post('/reviews/{id}/moderate', [MarketingController::class, 'moderateReview'])->name('reviews.moderate');
        Route::get('/profil', [MarketingController::class, 'profile'])->name('profile.edit');
        Route::put('/profil', [MarketingController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profil/password', [MarketingController::class, 'updateProfilePassword'])->name('profile.password');
    });

    // B. LEGAL: Verifikasi dokumen, generate BAK, simpan checksheet regulasi, update tracking
    Route::middleware('role:legal,konsultan,admin')->prefix('legal')->name('legal.')->group(function () {
        Route::get('/dashboard', [LegalController::class, 'dashboard'])->name('dashboard');
        Route::post('/document/{id}/verify', [LegalController::class, 'verifyDocument'])->name('document.verify');
        Route::get('/document/{id}/bak', [LegalController::class, 'generateBak'])->name('document.bak');
        Route::post('/checksheet/save', [LegalController::class, 'saveChecksheet'])->name('checksheet.save');
        Route::post('/tracking/update', [LegalController::class, 'updateTracking'])->name('tracking.update');
    });

    // ============================================================
    // C. FINANCE & ADMINISTRATION: Dasbor mandiri keuangan,
    // antrean verifikasi pembayaran, monitoring transaksi & status klien
    // ============================================================
    Route::middleware('role:finance,admin')->prefix('finance')->name('finance.')->group(function () {
        Route::get('/', function () {
            return redirect()->route('finance.dashboard');
        });
        Route::get('/dashboard', [FinanceController::class, 'dashboard'])->name('dashboard');

        // Antrean Verifikasi Pembayaran Digital / Transfer Klien
        Route::get('/pembayaran', [FinanceController::class, 'payments'])->name('payments');
        Route::get('/payments', [FinanceController::class, 'payments'])->name('payments.alias');
        Route::get('/payment/{id}/audit', [FinanceController::class, 'paymentAudit'])->name('payment.audit');
        Route::post('/payment/{id}/verify', [FinanceController::class, 'verifyPayment'])->name('payment.verify');

        // Monitoring Transaksi & Status Pembayaran Pelanggan
        Route::get('/transaksi', [FinanceController::class, 'transactions'])->name('transactions');
        Route::get('/transactions', [FinanceController::class, 'transactions'])->name('transactions.alias');

        // Penerbitan & Pengelolaan Invoice Tagihan Transaksi Layanan
        Route::get('/invoices', [FinanceController::class, 'invoices'])->name('invoices');
        Route::post('/invoices/issue', [FinanceController::class, 'issueInvoice'])->name('invoices.issue');
        Route::post('/invoices/{id}/status', [FinanceController::class, 'updateInvoiceStatus'])->name('invoices.status');
        Route::post('/invoices/{id}/due-date', [FinanceController::class, 'updateInvoiceDueDate'])->name('invoices.due-date');
        Route::get('/invoice/{orderId}', [FinanceController::class, 'generateInvoice'])->name('invoice.generate');
        Route::get('/invoice/{orderId}/pdf', [FinanceController::class, 'downloadInvoicePdf'])->name('invoice.pdf');

        // Laporan Rekapitulasi Pendapatan & Arus Kas Perusahaan (Cash Flow)
        Route::get('/laporan', [FinanceController::class, 'reports'])->name('reports');
        Route::get('/reports', [FinanceController::class, 'reports'])->name('reports.alias');
        Route::get('/laporan/export/xlsx', [FinanceController::class, 'exportReportXlsx'])->name('reports.export.xlsx');
        Route::get('/laporan/export/pdf', [FinanceController::class, 'exportReportPdf'])->name('reports.export.pdf');
        Route::get('/laporan/export', [FinanceController::class, 'exportReport'])->name('reports.export');

        // Ekspor Mutasi Transaksi Keuangan (.xlsx, .pdf, .csv)
        Route::get('/export/xlsx', [FinanceController::class, 'exportTransactionsXlsx'])->name('export.xlsx');
        Route::get('/export/pdf', [FinanceController::class, 'exportTransactionsPdf'])->name('export.pdf');
        Route::get('/export/excel', [FinanceController::class, 'exportExcel'])->name('export.excel');

        // Linimasa Stepper & Arsip Server Internal (Modul Operasional Terpisah)
        Route::get('/linimasa', [FinanceController::class, 'tracking'])->name('tracking');
        Route::post('/tracking/add', [FinanceController::class, 'addTracking'])->name('tracking.add');
        Route::get('/arsip', [FinanceController::class, 'archives'])->name('archives');
        Route::post('/archives/upload', [FinanceController::class, 'uploadArchive'])->name('archives.upload');
        Route::delete('/archives/{id}', [FinanceController::class, 'destroyArchive'])->name('archives.destroy');

        // Manajemen Profil & Keamanan Akun Staf Finance
        Route::get('/profil', [FinanceController::class, 'profile'])->name('profile.edit');
        Route::put('/profil', [FinanceController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profil/password', [FinanceController::class, 'updateProfilePassword'])->name('profile.password');
    });

    // ============================================================
    // D. DOCUMENT & LICENSING OFFICER: Dasbor operasional mandiri,
    // antrean pemeriksaan kelengkapan dokumen & tenggat waktu pengerjaan proyek
    // ============================================================
    Route::middleware('role:officer,petugas,admin')->prefix('officer')->name('officer.')->group(function () {
        Route::get('/', function () {
            return redirect()->route('officer.dashboard');
        });
        Route::get('/dashboard', [OfficerController::class, 'dashboard'])->name('dashboard');

        // Antrean Pemeriksaan Kelengkapan Dokumen Klien & Audit Keaslian
        Route::get('/dokumen', [OfficerController::class, 'documents'])->name('documents');
        Route::get('/dokumen/{id}', [OfficerController::class, 'showDocument'])->name('documents.show');
        Route::get('/dokumen/{id}/audit', [OfficerController::class, 'auditDocumentJson'])->name('documents.audit');
        Route::get('/dokumen/{id}/unduh', [OfficerController::class, 'downloadDocument'])->name('documents.download');
        Route::get('/dokumen/{id}/versi/{versionId}/unduh', [OfficerController::class, 'downloadVersion'])->name('documents.download-version');
        Route::post('/dokumen/{id}/verifikasi', [OfficerController::class, 'verifyDocument'])->name('documents.verify');

        // Pemantauan Tenggat Waktu & Linimasa Pengerjaan Proyek (SLA)
        Route::get('/projek', [OfficerController::class, 'projects'])->name('projects');
        Route::get('/projek/ekspor/excel', [OfficerController::class, 'exportProjectsExcel'])->name('projects.export-excel');
        Route::get('/projek/ekspor/pdf', [OfficerController::class, 'exportProjectsPdf'])->name('projects.export-pdf');
        Route::get('/projek/{id}/linimasa', [OfficerController::class, 'projectTimelineJson'])->name('projects.timeline');
        Route::post('/projek/{id}/tahap', [OfficerController::class, 'updateProjectStage'])->name('projects.update-stage');

        // Manajemen Profil & Keamanan Akun Staf Officer
        Route::get('/profil', [OfficerController::class, 'profile'])->name('profile.edit');
        Route::put('/profil', [OfficerController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profil/password', [OfficerController::class, 'updateProfilePassword'])->name('profile.password');
    });

    // Kompatibilitas mundur: redirect role lama ke dashboard sesuai role baru
    Route::get('/konsultan/dashboard', function () {
        return redirect()->route(auth()->user()->getDashboardRoute());
    })->name('konsultan.dashboard');

    Route::get('/petugas/dashboard', function () {
        return redirect()->route(auth()->user()->getDashboardRoute());
    })->name('petugas.dashboard');

    // ============================================================
    // 3. KLIEN / PELANGGAN: Portal mandiri fitur F - L
    // ============================================================
    Route::middleware('role:klien')->prefix('klien')->name('klien.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'klienDashboard'])->name('dashboard');
        Route::get('/konsultasi', [DashboardController::class, 'klienConsultations'])->name('consultations.index');
        Route::post('/consultation/submit', [DashboardController::class, 'submitConsultation'])->name('consultation.submit');

        // Fitur F: Manajemen Profil Pelanggan
        Route::get('/profil', [ClientProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profil', [ClientProfileController::class, 'update'])->name('profile.update');
        Route::put('/profil/password', [ClientProfileController::class, 'updatePassword'])->name('profile.password');

        // Katalog & Detail Layanan Internal Portal Klien
        Route::get('/layanan', [ClientOrderController::class, 'services'])->name('services.index');
        Route::get('/layanan/{service}', [ClientOrderController::class, 'showService'])->whereNumber('service')->name('services.show');

        // Fitur H: Voucher Promosi & Checkout
        Route::get('/checkout/{service:slug}', [ClientOrderController::class, 'showCheckout'])->name('checkout.show');
        Route::post('/checkout/validate-voucher', [ClientOrderController::class, 'validateVoucher'])->name('checkout.validate-voucher');
        Route::post('/order/checkout', [ClientOrderController::class, 'storeCheckout'])->name('order.checkout');
        Route::get('/pesanan/{orderNumber}/konfirmasi', [ClientOrderController::class, 'confirmation'])->name('orders.confirmation');

        // Fitur I, J, K, L: Pesanan, Invoice, Pembayaran, Dokumen & Versioning, Tracking, Rating & Ulasan
        Route::get('/pesanan', [ClientOrderController::class, 'index'])->name('orders.index');
        Route::get('/pesanan/{order}', [ClientOrderController::class, 'show'])->name('orders.show');
        Route::post('/pesanan/{order}/batal', [ClientOrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/pesanan/{order}/invoice', [ClientOrderController::class, 'showInvoice'])->name('orders.invoice');
        Route::get('/pesanan/{order}/invoice/pdf', [ClientOrderController::class, 'downloadInvoicePdf'])->name('orders.invoice.pdf');
        Route::post('/pesanan/{order}/pembayaran', [ClientOrderController::class, 'uploadPayment'])->name('orders.payment.store');
        Route::post('/pesanan/{order}/dokumen', [ClientDocumentController::class, 'upload'])->name('orders.documents.upload');
        Route::post('/pesanan/{order}/ulasan', [ClientReviewController::class, 'store'])->name('orders.review.store');
    });
});
