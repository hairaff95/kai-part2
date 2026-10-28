<?php

namespace App\Console\Commands;

use App\Mail\TempPasswordMail;
use App\Models\PasswordResetRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TempPasswordDaemon extends Command
{
    protected $signature = 'password:temp-daemon {--stop : Stop the daemon}';

    protected $description = 'Daemon: cek setiap 10 detik, kirim password sementara jika superadmin belum approve (berlaku 2 menit).';

    private bool $shouldRun = true;

    public function handle(): void
    {
        $this->info('TempPasswordDaemon started. Checking every 10 seconds...');
        $this->info('Press Ctrl+C to stop.');

        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGTERM, function () {
                $this->shouldRun = false;
            });
            pcntl_signal(SIGINT, function () {
                $this->shouldRun = false;
            });
        }

        while ($this->shouldRun) {
            $this->processPendingRequests();

            if (function_exists('pcntl_signal_dispatch')) {
                pcntl_signal_dispatch();
            }

            sleep(10);
        }

        $this->info('TempPasswordDaemon stopped.');
    }

    private function processPendingRequests(): void
    {
        $pendingRequests = PasswordResetRequest::with('user')
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('request_expires_at')
                    ->orWhere('request_expires_at', '>', now());
            })
            ->get();

        $sent = 0;

        foreach ($pendingRequests as $resetRequest) {
            if (! $resetRequest->user || ! $resetRequest->user->is_active) {
                continue;
            }

            if (! $resetRequest->shouldSendTempPassword()) {
                continue;
            }

            // Generate password sementara baru
            $plainPassword = Str::random(12);
            $expiresAt = now()->addMinutes(PasswordResetRequest::TEMP_PASSWORD_LIFETIME_MINS); // berlaku 2 menit

            $resetRequest->update([
                'temp_password' => $plainPassword,
                'temp_password_sent_at' => now(),
                'temp_password_expires_at' => $expiresAt,
            ]);

            $this->invalidateUserTempPasswordSessions($resetRequest->user_id);

            try {
                Mail::to($resetRequest->user->email)
                    ->send(new TempPasswordMail($resetRequest->user, $plainPassword));

                $sent++;
                $this->line('['.now()->format('H:i:s').'] Sent to '.$resetRequest->user->email.' (expires: '.$expiresAt->format('H:i:s').')');
                Log::info("TempPasswordDaemon: Sent to {$resetRequest->user->email}, expires {$expiresAt}");
            } catch (\Exception $e) {
                $this->error('['.now()->format('H:i:s').'] Failed: '.$e->getMessage());
                Log::error("TempPasswordDaemon: Failed to send to {$resetRequest->user->email} — {$e->getMessage()}");
            }
        }

        if ($sent > 0) {
            $this->info('['.now()->format('H:i:s')."] {$sent} password sementara terkirim.");
        }
    }

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

                foreach ($payload as $key => $value) {
                    if (str_contains((string) $key, 'is_using_temp_password') && $value === true) {
                        DB::table(config('session.table', 'sessions'))
                            ->where('id', $session->id)
                            ->delete();
                        Log::info("TempPasswordDaemon: Invalidated session for user_id={$userId}");
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning("TempPasswordDaemon: Could not invalidate sessions — {$e->getMessage()}");
        }
    }
}
