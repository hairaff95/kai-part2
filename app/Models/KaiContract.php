<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KaiContract extends Model
{
    protected $table = 'contracts';

    protected $primaryKey = 'nomor_kontrak';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'nomor_kontrak',
        'tenant_id',
        'nomor_aset',
        'tanggal_kontrak',
        'kelompok',
        'masa_awal',
        'masa_akhir',
        'nilai_kontrak',
        'created_at',
    ];

    protected $casts = [
        'masa_awal' => 'date',
        'masa_akhir' => 'date',
        'nilai_kontrak' => 'float',
        'created_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(KaiAsset::class, 'nomor_aset', 'nomor_aset');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PaymentRecord::class, 'nomor_kontrak', 'nomor_kontrak');
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    /**
     * Nilai kontrak format Rupiah.
     */
    public function getNilaiFormattedAttribute(): string
    {
        $val = (float) $this->nilai_kontrak;
        if ($val >= 1_000_000_000) {
            return 'Rp '.number_format($val / 1_000_000_000, 2, ',', '.').' M';
        }

        return 'Rp '.number_format($val, 0, ',', '.');
    }

    /**
     * Status kontrak otomatis berdasarkan masa_akhir vs hari ini.
     *
     * Logika:
     * - masa_akhir null / tidak valid → 'Belum Ada Tanggal Akhir'
     * - masa_akhir >= hari ini (startOfDay) → 'Kontrak Aktif'
     * - masa_akhir < hari ini → 'Backlog'
     *
     * Kontrak dianggap aktif sepanjang hari terakhirnya (batas akhir hari = end of day),
     * artinya kontrak berakhir 31 Des 2026 masih aktif hingga tengah malam 31 Des 2026.
     *
     * @return string 'Kontrak Aktif' | 'Backlog' | 'Belum Ada Tanggal Akhir'
     */
    public function getStatusKontrakAttribute(): string
    {
        if (! $this->masa_akhir) {
            return 'Belum Ada Tanggal Akhir';
        }

        try {
            $akhir = $this->masa_akhir instanceof CarbonInterface
                ? $this->masa_akhir->copy()->startOfDay()
                : Carbon::parse((string) $this->masa_akhir)->startOfDay();

            $today = now()->startOfDay();

            return $akhir->gte($today) ? 'Kontrak Aktif' : 'Backlog';
        } catch (\Throwable $e) {
            return 'Belum Ada Tanggal Akhir';
        }
    }

    /**
     * Kelas Tailwind CSS badge untuk status kontrak.
     */
    public function getStatusKontrakBadgeAttribute(): string
    {
        return match ($this->status_kontrak) {
            'Kontrak Aktif' => 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800',
            'Backlog' => 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-200/60 dark:border-red-800',
            default => 'bg-gray-100 dark:bg-white/10 text-gray-500 dark:text-gray-400 border border-gray-200/60 dark:border-white/10',
        };
    }

    /**
     * Sisa hari kontrak dalam format human-readable.
     */
    public function getSisaHariAttribute(): string
    {
        if (! $this->masa_akhir) {
            return '-';
        }

        try {
            $akhir = $this->masa_akhir instanceof CarbonInterface
                ? $this->masa_akhir->copy()->startOfDay()
                : Carbon::parse((string) $this->masa_akhir)->startOfDay();

            $today = now()->startOfDay();

            if ($akhir->lt($today)) {
                return 'Sudah berakhir';
            }
            if ($akhir->eq($today)) {
                return 'Hari ini';
            }

            $months = (int) $today->diffInMonths($akhir);
            $days = (int) $today->copy()->addMonths($months)->diffInDays($akhir);

            if ($months > 0) {
                return $months.' bulan'.($days > 0 ? ' '.$days.' hari' : '');
            }

            return $days.' hari';
        } catch (\Throwable $e) {
            return '-';
        }
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Scope filter status kontrak di level SQL (konsisten dengan accessor).
     */
    public function scopeStatusKontrak($query, string $status)
    {
        $today = now()->toDateString();

        return match ($status) {
            'aktif' => $query->whereNotNull('masa_akhir')->whereDate('masa_akhir', '>=', $today),
            'backlog' => $query->whereNotNull('masa_akhir')->whereDate('masa_akhir', '<', $today),
            'tanpa_tanggal' => $query->whereNull('masa_akhir'),
            default => $query,
        };
    }
}
