<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ExcelImportController;
use Illuminate\Support\Facades\Route;

// ── Dashboard ────────────────────────────────────────────────────────────────
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// ── Daftar Kontrak ───────────────────────────────────────────────────────────
Route::get('/kontrak', [ContractController::class, 'index'])->name('contracts.index');

// ── Data Tagihan ─────────────────────────────────────────────────────────────
Route::get('/tagihan', [PaymentController::class, 'index'])->name('payments.index');

// ── Import CSV / Excel ───────────────────────────────────────────────────────
Route::get('/import', fn () => view('import.index'))->name('import.index');
Route::post('/import', [ExcelImportController::class, 'import'])->name('import.store');
