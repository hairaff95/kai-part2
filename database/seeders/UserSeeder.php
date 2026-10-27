<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'username' => 'superadmin',
                'name' => 'Super Admin KAI Daop 4',
                'email' => 'superadmin123@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('superadmin123'),
                'role' => 'superadmin',
                'is_active' => true,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'akuntansi',
                'name' => 'Tim Akuntansi Daop 4',
                'email' => 'akuntansi@kai-daop4.id',
                'email_verified_at' => now(),
                'password' => Hash::make('akuntansi123'),
                'role' => 'akuntansi',
                'is_active' => true,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'kna',
                'name' => 'Tim KNA Daop 4',
                'email' => 'kna@kai-daop4.id',
                'email_verified_at' => now(),
                'password' => Hash::make('kna123'),
                'role' => 'kna',
                'is_active' => true,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'asset',
                'name' => 'Tim Divisi Aset Daop 4',
                'email' => 'asset@kai-daop4.id',
                'email_verified_at' => now(),
                'password' => Hash::make('asset123'),
                'role' => 'asset',
                'is_active' => true,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['username' => $userData['username']],
                $userData
            );
        }
    }
}
