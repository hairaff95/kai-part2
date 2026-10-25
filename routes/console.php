<?php

use App\Console\Commands\AutoResetExpiredPasswordRequests;
use App\Console\Commands\AutoSendTempPassword;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Fallback: jalankan auto-send setiap menit jika daemon tidak berjalan
Schedule::command(AutoSendTempPassword::class)->everyMinute();

// Auto-reset request yang sudah expired (24 jam)
Schedule::command(AutoResetExpiredPasswordRequests::class)->hourly();
