<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChangeRequestController;
use App\Http\Controllers\MasterClientController;
use App\Http\Controllers\MasterPegawaiController;
use App\Http\Controllers\MasterStatusController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Always accessible login view
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Real OTP Email Verification Code Login
    Route::get('/auth/otp', [AuthController::class, 'showOtpForm'])->name('auth.otp');
    Route::post('/auth/otp/send', [AuthController::class, 'sendOtp'])->name('auth.otp.send');
    Route::post('/auth/otp/verify', [AuthController::class, 'verifyOtp'])->name('auth.otp.verify');
    Route::post('/auth/otp/resend', [AuthController::class, 'resendOtp'])->name('auth.otp.resend');

    // Google Socialite OAuth
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

    // Password Reset Flow (Figma Alur)
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/check-email', [AuthController::class, 'showCheckEmail'])->name('password.check-email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', fn () => redirect()->route('change-requests.index'));

    // Master Data for Admin (Client, Pegawai, Status)
    Route::middleware('role:admin')->prefix('master')->name('master.')->group(function () {
        // Master Client
        Route::resource('clients', MasterClientController::class);

        // Master Pegawai
        Route::resource('pegawai', MasterPegawaiController::class);

        // Master Status
        Route::get('status', [MasterStatusController::class, 'index'])->name('status.index');
        Route::post('status', [MasterStatusController::class, 'store'])->name('status.store');
        Route::put('status/{status}', [MasterStatusController::class, 'update'])->name('status.update');
    });

    Route::controller(ChangeRequestController::class)->group(function () {
        // 1. Index & Global Exports
        Route::get('/change-requests', 'index')->name('change-requests.index');
        Route::get('/change-requests/export/excel', 'exportExcel')->name('change-requests.export.excel');
        Route::get('/change-requests/export/excel-admin', 'exportExcel')->name('change-requests.export.excel.admin');
        Route::get('/change-requests/export/pdf', 'exportPdf')->name('change-requests.export.pdf');

        // PM Dev Board
        Route::middleware('role:pm,admin')->get('/dev-board', 'devboard')->name('change-requests.devboard');

        // 2. Dual-path Submission (Client & PM can create)
        Route::middleware('role:client,pm,admin')->group(function () {
            Route::get('/change-requests/create', 'create')->name('change-requests.create');
            Route::post('/change-requests', 'store')->name('change-requests.store');
        });

        // 3. Client Messaging & Quotation & Profile & Minor Edit
        Route::middleware('role:client')->group(function () {
            Route::get('/client/profile', 'profile')->name('client.profile');
            Route::put('/change-requests/{changeRequest}/client-edit', 'clientMinorEdit')->name('change-requests.client.edit');
            Route::post('/change-requests/{changeRequest}/message', 'sendClientMessage')->name('change-requests.message');
            Route::post('/change-requests/{changeRequest}/quotation/approve', 'approveQuotationClient')->name('change-requests.quotation.client');
        });

        Route::middleware('role:pm,pmh')->group(function () {
            Route::post('/change-requests/{changeRequest}/quotation/approve-tp', 'approveQuotationTp')->name('change-requests.quotation.tp');
        });

        // 4. Change Request Details & Attachments
        Route::get('/change-requests/{changeRequest}', 'show')->name('change-requests.show');
        Route::get('/change-requests/{changeRequest}/initial-file', 'downloadInitialAttachment')->name('change-requests.initial-file');

        // 5. Internal Editing (PM, PMH, Finance, Admin)
        Route::middleware('role:pm,pmh,presales,finance,admin')->group(function () {
            Route::get('/change-requests/{changeRequest}/edit', 'edit')->name('change-requests.edit');
            Route::put('/change-requests/{changeRequest}', 'update')->name('change-requests.update');
            Route::delete('/change-requests/{changeRequest}', 'destroy')->name('change-requests.destroy');
            Route::post('/change-requests/{changeRequest}/sequential-status', 'updateSequentialStatus')->name('change-requests.sequential-status');
        });

        // 6. Marketing / Finance Internal Record Keeping Only (Quotation, Invoices, GR, Jurnal)
        Route::middleware('role:presales,finance,admin')->group(function () {
            Route::post('/change-requests/{changeRequest}/quotation', 'saveQuotation')->name('change-requests.quotation.save');
            Route::post('/change-requests/{changeRequest}/invoices', 'storeInvoice')->name('change-requests.invoices.store');
            Route::put('/change-requests/invoices/{invoice}/status', 'updateInvoiceStatus')->name('change-requests.invoices.status');
            Route::get('/change-requests/{changeRequest}/invoice', 'downloadInvoice')->name('change-requests.invoice');
        });
    });

    // Profile Route (Pengaturan)
    Route::get('/profile', [\App\Http\Controllers\PMHead\PMHeadController::class, 'profile'])->name('profile');
    Route::post('/profile/password', [\App\Http\Controllers\PMHead\PMHeadController::class, 'updatePassword'])->name('profile.password');

    // Modul PM Head (Sesuai Desain Figma)
    Route::middleware('role:pmh,admin')->prefix('pm-head')->name('pmh.')->group(function () {
        Route::get('/change-requests', [\App\Http\Controllers\PMHead\PMHeadController::class, 'semuaCr'])->name('change-requests');
        Route::get('/persetujuan', [\App\Http\Controllers\PMHead\PMHeadController::class, 'persetujuan'])->name('persetujuan');
        Route::get('/development', [\App\Http\Controllers\PMHead\PMHeadController::class, 'development'])->name('development');
        Route::get('/golive', [\App\Http\Controllers\PMHead\PMHeadController::class, 'golive'])->name('golive');
        Route::get('/outstanding-payment', [\App\Http\Controllers\PMHead\PMHeadController::class, 'outstandingPayment'])->name('outstanding-payment');
        Route::get('/outstanding-payment/export', [\App\Http\Controllers\PMHead\PMHeadController::class, 'exportPayment'])->name('outstanding-payment.export');
        Route::get('/review/{changeRequest}', [\App\Http\Controllers\PMHead\PMHeadController::class, 'review'])->name('review');
        Route::post('/review/{changeRequest}/decision', [\App\Http\Controllers\PMHead\PMHeadController::class, 'submitDecision'])->name('decision');
    });
});
