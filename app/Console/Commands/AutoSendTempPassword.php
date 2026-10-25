<?php

namespace App\Console\Commands;

use App\Mail\TempPasswordMail;
use App\Models\PasswordResetRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AutoSendTempPassword extends Command
{
    protected $signature = 'password:auto-send-temp';

    protected $description = 'Kirim password sementara ke user yang requestnya belum disetujui superadmin setelah 2 menit.';

    public function handle(): void
    {
        // Ambil semua request dengan status pending yang belum expired
        $pendingRequests = PasswordResetRequest::with('user')
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('request_expires_at')
                    ->orWhere('request_expires_at', '>', now());
            })
            ->get();

        $sent = 0;

        foreach ($pendingRequests as $resetRequest) {
            // Lewati jika user tidak aktif atau tidak ada
            if (! $resetRequest->user || ! $resetRequest->user->is_active) {
                continue;
            }

            // Cek apakah sudah waktunya kirim (>= 2 menit sejak request)
            if (! $resetRequest->shouldSendTempPassword()) {
                continue;
            }

            // Generate password sementara baru: 12 karakter alfanumerik
            $plainPassword = Str::random(12);
            $expiresAt = now()->addMinutes(PasswordResetRequest::TEMP_PASSWORD_LIFETIME_MINS); // berlaku 2 menit

            // Update record dengan password baru
            $resetRequest->update([
                'temp_password' => $plainPassword,
                'temp_password_sent_at' => now(),
                'temp_password_expires_at' => $expiresAt,
            ]);

            // Force logout semua sesi yang sedang login via temp password
            $this->invalidateUserTempPasswordSessions($resetRequest->user_id);

            // Kirim email password baru
            try {
                Mail::to($resetRequest->user->email)
                    ->send(new TempPasswordMail($resetRequest->user, $plainPassword));

                $sent++;
                Log::info("AutoSendTempPassword: Sent new temp password to {$resetRequest->user->email}, expires at {$expiresAt}");
            } catch (\Exception $e) {
                Log::error("AutoSendTempPassword: Failed to send to {$resetRequest->user->email} — {$e->getMessage()}");
            }
        }

        $this->info("AutoSendTempPassword: {$sent} email terkirim.");
    }

    /**
     * Hapus/invalidate session database untuk user tertentu yang login via temp password.
     */
    private function invalidateUserTempPasswordSessions(int $userId): void
    {
        try {
            if (config('session.driver') !== 'database') {
                return;
            }

            $sessions = DB::table(config('session.table', 'sessions'))
                ->where('user_id', $userId)
                ->get();

            foreach ($sessions as $session) {
                $payload = @unserialize(base64_decode($session->payload));

                if (! is_array($payload)) {
                    continue;
                }

                $isUsingTemp = false;
                foreach ($payload as $key => $value) {
                    if (str_contains((string) $key, 'is_using_temp_password') && $value === true) {
                        $isUsingTemp = true;
                        break;
                    }
                }

                if ($isUsingTemp) {
                    DB::table(config('session.table', 'sessions'))
                        ->where('id', $session->id)
                        ->delete();

                    Log::info("AutoSendTempPassword: Invalidated temp password session for user_id={$userId}");
                }
            }
        } catch (\Exception $e) {
            Log::warning("AutoSendTempPassword: Could not invalidate sessions — {$e->getMessage()}");
        }
    }
}
