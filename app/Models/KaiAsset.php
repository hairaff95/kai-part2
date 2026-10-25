<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KaiAsset extends Model
{
    protected $table = 'assets';

    protected $primaryKey = 'nomor_aset';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'nomor_aset',
        'blok_aset',
        'jenis_aset',
        'peruntukan',
        'luas_tanah',
        'luas_bangunan',
        'kmhm',
        'latitude',
        'longitude',
        'created_at',
    ];

    protected $casts = [
        'luas_tanah' => 'float',
        'luas_bangunan' => 'float',
        'latitude' => 'float',
        'longitude' => 'float',
        'created_at' => 'datetime',
    ];

    public function contract(): HasOne
    {
        return $this->hasOne(KaiContract::class, 'nomor_aset', 'nomor_aset');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(KaiContract::class, 'nomor_aset', 'nomor_aset');
    }

    // Accessor luas tanah terformat
    public function getLuasTanahFormattedAttribute(): string
    {
        if (! $this->luas_tanah) {
            return '-';
        }
        $val = number_format((float) $this->luas_tanah, 2, ',', '.');

        return rtrim(rtrim($val, '0'), ',').' m²';
    }
}
