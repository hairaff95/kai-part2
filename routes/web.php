<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExcelImportController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\PasswordResetRequestController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;

// ── Authentication ───────────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// ── Alur Reset Password ──────────────────────────────────────────────────────
Route::get('/ubah-kata-sandi/request', [PasswordResetRequestController::class, 'showRequestForm'])->name('password.request');
Route::post('/ubah-kata-sandi/request', [PasswordResetRequestController::class, 'submitRequest'])->name('password.submit-request');
Route::get('/ubah-kata-sandi/status', [PasswordResetRequestController::class, 'requestStatus'])->name('password.request.status');
Route::get('/ubah-kata-sandi/status/poll', [PasswordResetRequestController::class, 'pollStatus'])->name('password.request.poll');
Route::get('/ubah-kata-sandi/akses/{resetRequest}', [PasswordResetRequestController::class, 'accessViaToken'])->name('password.access-token');
Route::get('/verifikasi-kode', [PasswordResetRequestController::class, 'showVerifyOtp'])->name('password.verify');
Route::post('/verifikasi-kode', [PasswordResetRequestController::class, 'verifyOtp'])->name('password.verify.post');
Route::post('/verifikasi-kode/kirim-ulang', [PasswordResetRequestController::class, 'resendOtp'])->name('password.resend-otp');
Route::get('/ubah-kata-sandi', [PasswordResetRequestController::class, 'showResetPassword'])->name('password.reset');
Route::post('/ubah-kata-sandi', [PasswordResetRequestController::class, 'resetPassword'])->name('password.reset.post');

// ── Public / Read-Only Routes ─────────────────────────────────────────────────
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/dashboard/update-kinerja', [DashboardController::class, 'updateKinerja'])->name('dashboard.update_kinerja');
Route::get('/dashboard/live-chart', [DashboardController::class, 'liveChartData'])->name('dashboard.live_chart');

Route::get('/peta', [MapController::class, 'index'])->name('map.index');
Route::get('/map', fn () => redirect()->route('map.index'));
Route::post('/peta/aset/{nomor_aset}/status', [MapController::class, 'updateStatus'])->name('map.asset.update');

Route::get('/kontrak', [ContractController::class, 'index'])->name('contracts.index');
Route::get('/tagihan', [PaymentController::class, 'index'])->name('payments.index');

// ── Super Admin: Pengaturan & Persetujuan Sandi ──────────────────────────────
Route::middleware(['auth', 'temp_pwd_expiry'])->group(function () {
    Route::get('/pengaturan', [SuperAdminController::class, 'settingsIndex'])->name('settings.index');
    Route::post('/pengaturan/profile', [SuperAdminController::class, 'profileUpdate'])->name('settings.profile.update');
    Route::post('/pengaturan/admins', [SuperAdminController::class, 'adminStore'])->name('admins.store');
    Route::post('/pengaturan/admins/{admin}/toggle', [SuperAdminController::class, 'adminToggleActive'])->name('admins.toggle');
    Route::delete('/pengaturan/admins/{admin}', [SuperAdminController::class, 'adminDestroy'])->name('admins.destroy');

    Route::post('/pengaturan/reset-requests/{resetRequest}/approve', [SuperAdminController::class, 'approveRequest'])->name('reset-requests.approve');
    Route::post('/pengaturan/reset-requests/{resetRequest}/reject', [SuperAdminController::class, 'rejectRequest'])->name('reset-requests.reject');
    Route::get('/superadmin/reset-requests', [SuperAdminController::class, 'settingsIndex'])->name('superadmin.reset-requests');

    // ── Super Admin / Admin CRUD Routes (Wajib Login & Super Admin) ──────────────
    Route::middleware('superadmin')->group(function () {
        // CRUD Kontrak
        Route::get('/kontrak/create', [ContractController::class, 'create'])->name('contracts.create');
        Route::post('/kontrak', [ContractController::class, 'store'])->name('contracts.store');
        Route::get('/kontrak/{contract}/edit', [ContractController::class, 'edit'])
            ->name('contracts.edit')
            ->where('contract', '.*');
        Route::put('/kontrak/{contract}', [ContractController::class, 'update'])
            ->name('contracts.update')
            ->where('contract', '.*');
        Route::delete('/kontrak/{contract}', [ContractController::class, 'destroy'])
            ->name('contracts.destroy')
            ->where('contract', '.*');

        // CRUD Tagihan
        Route::get('/tagihan/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/tagihan', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/tagihan/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
        Route::put('/tagihan/{payment}', [PaymentController::class, 'update'])->name('payments.update');
        Route::delete('/tagihan/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    });

    // Import CSV / Excel
    Route::get('/import', fn () => view('import.index'))->name('import.index');
    Route::post('/import', [ExcelImportController::class, 'import'])->name('import.store');
    Route::post('/pengaturan/import', [ExcelImportController::class, 'import'])->name('settings.import-excel');
});
