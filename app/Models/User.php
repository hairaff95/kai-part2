<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['username', 'name', 'email', 'email_verified_at', 'password', 'role', 'is_active', 'remember_token'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'superadmin']);
    }

    public function isAkuntansi(): bool
    {
        return in_array($this->role, ['akuntansi', 'superadmin', 'admin']);
    }

    public function isKna(): bool
    {
        return in_array($this->role, ['kna', 'superadmin', 'admin']);
    }

    public function isAsset(): bool
    {
        return in_array($this->role, ['asset', 'superadmin', 'admin']);
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'superadmin' => 'Super Admin',
            'admin' => 'Admin Daop 4',
            'akuntansi' => 'Tim Akuntansi',
            'kna' => 'Tim KNA',
            'asset' => 'Divisi Aset',
            default => ucfirst($this->role ?? 'User'),
        };
    }
}
