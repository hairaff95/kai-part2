<?php

namespace App\Console\Commands;

use App\Mail\TempPasswordMail;
use App\Models\PasswordResetRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AutoResetExpiredPasswordRequests extends Command
{
    protected $signature = 'password-requests:auto-reset';

    protected $description = 'Auto-reset password user yang requestnya sudah 24 jam belum diapprove oleh super admin.';

    public function handle(): int
    {
        // Cari semua request pending yang sudah lewat 24 jam
        $expiredRequests = PasswordResetRequest::with('user')
            ->where('status', 'pending')
            ->where('request_expires_at', '<=', now())
            ->get();

        if ($expiredRequests->isEmpty()) {
            $this->info('Tidak ada request yang kedaluwarsa.');

            return self::SUCCESS;
        }

        $processed = 0;

        foreach ($expiredRequests as $resetRequest) {
            $user = $resetRequest->user;

            if (! $user) {
                continue;
            }

            // Generate password sementara yang aman
            $tempPassword = Str::random(8).random_int(10, 99).'!';

            // Update password user
            $user->update([
                'password' => Hash::make($tempPassword),
            ]);

            // Update status request
            $resetRequest->update([
                'status' => 'auto_reset',
                'completed_at' => now(),
            ]);

            // Kirim email dengan password sementara
            try {
                Mail::to($user->email)->send(new TempPasswordMail($user, $tempPassword));
                $this->info("Auto-reset berhasil untuk: {$user->email}");
            } catch (\Exception $e) {
                $this->error("Gagal kirim email ke {$user->email}: ".$e->getMessage());
                \Log::error('Auto-reset email gagal: '.$e->getMessage());
            }

            $processed++;
        }

        $this->info("Total request yang diproses: {$processed}");

        return self::SUCCESS;
    }
}
