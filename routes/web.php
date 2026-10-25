<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExcelImportController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// ── Authentication ───────────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// ── Public / Read-Only Routes ─────────────────────────────────────────────────
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/kontrak', [ContractController::class, 'index'])->name('contracts.index');
Route::get('/tagihan', [PaymentController::class, 'index'])->name('payments.index');

// ── Super Admin / Admin CRUD Routes (Wajib Login & Super Admin) ──────────────
Route::middleware(['auth', 'superadmin'])->group(function () {
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

    // Import CSV / Excel
    Route::get('/import', fn () => view('import.index'))->name('import.index');
    Route::post('/import', [ExcelImportController::class, 'import'])->name('import.store');
});
