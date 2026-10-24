<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $table = 'tenants';

    public $timestamps = false;

    protected $fillable = [
        'nama_mitra',
        'alamat_mitra',
        'status_mitra',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function contracts(): HasMany
    {
        return $this->hasMany(KaiContract::class, 'tenant_id');
    }
}
