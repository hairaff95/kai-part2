<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentRecord extends Model
{
    protected $table = 'payment_records';

    public $timestamps = false;

    protected $fillable = [
        'nomor_kontrak',
        'termin_ke',
        'virtual_account',
        'tanggal_termin',
        'total_bayar',
        'invoice_id',
        'transaction_id',
        'created_at',
    ];

    protected $casts = [
        'tanggal_termin' => 'date',
        'total_bayar' => 'float',
        'termin_ke' => 'integer',
        'created_at' => 'datetime',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(KaiContract::class, 'nomor_kontrak', 'nomor_kontrak');
    }

    // Accessor total bayar format Rupiah
    public function getTotalBayarFormattedAttribute(): string
    {
        return 'Rp '.number_format((float) $this->total_bayar, 0, ',', '.');
    }
}
