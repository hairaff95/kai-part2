<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordResetRequest extends Model
{
    // ── Konstanta siklus ───────────────────────────────────────────────────────
    public const TEMP_PASSWORD_DELAY_SECONDS = 120; // Kirim temp password setelah 2 menit pending jika belum di-approve/reject

    public const TEMP_PASSWORD_LIFETIME_MINS = 2;   // Temp password berlaku 2 menit

    public const OTP_SESSION_LIFETIME_MINS = 2;   // Session OTP setelah approve berlaku 2 menit

    public const MAX_REQUESTS_PER_CYCLE = 3;   // Maks 3 request sebelum di-block

    protected $fillable = [
        'user_id',
        'otp_code',
        'status',
        'request_count',
        'blocked_until',
        'otp_expires_at',
        'request_expires_at',
        'approved_at',
        'completed_at',
        'temp_password',
        'temp_password_sent_at',
        'temp_password_expires_at',
    ];

    protected $casts = [
        'otp_expires_at' => 'datetime',
        'request_expires_at' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'blocked_until' => 'datetime',
        'temp_password_sent_at' => 'datetime',
        'temp_password_expires_at' => 'datetime',
    ];

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Status helpers ────────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isOtpValid(): bool
    {
        return $this->status === 'approved'
            && $this->otp_expires_at !== null
            && $this->otp_expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->request_expires_at !== null
            && $this->request_expires_at->isPast()
            && $this->status === 'pending';
    }

    // ── Temp password helpers ─────────────────────────────────────────────────

    /**
     * Apakah temp password masih valid (belum expired, berlaku 2 menit).
     */
    public function isTempPasswordValid(): bool
    {
        return $this->temp_password !== null
            && $this->temp_password_expires_at !== null
            && $this->temp_password_expires_at->isFuture();
    }

    /**
     * Apakah sudah waktunya kirim temp password:
     * - Status pending
     * - Belum pernah kirim temp password pada request ini
     * - Request sudah pending >= TEMP_PASSWORD_DELAY_SECONDS (2 menit)
     */
    public function shouldSendTempPassword(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        if ($this->temp_password_sent_at !== null) {
            return false;
        }

        return $this->created_at->diffInSeconds(now()) >= self::TEMP_PASSWORD_DELAY_SECONDS;
    }

    // ── Block / cycle helpers ─────────────────────────────────────────────────

    /**
     * Apakah user sedang di-block (request_count >= MAX di request ini)?
     */
    public function isBlocked(): bool
    {
        return $this->request_count >= self::MAX_REQUESTS_PER_CYCLE
            && $this->status === 'pending';
    }

    /**
     * Hitung sisa request yang diperbolehkan sebelum block.
     */
    public function remainingRequests(): int
    {
        return max(0, self::MAX_REQUESTS_PER_CYCLE - $this->request_count);
    }

    /**
     * Ambil request aktif milik user (pending atau approved).
     */
    public static function getActiveRequest(int $userId): ?self
    {
        return self::where('user_id', $userId)
            ->whereIn('status', ['pending', 'approved'])
            ->latest('id')
            ->first();
    }
}
