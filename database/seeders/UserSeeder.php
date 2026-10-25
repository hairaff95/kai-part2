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
                'id' => 1,
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
                'id' => 2,
                'username' => 'admin.daop4',
                'name' => 'Admin KAI Daop 4',
                'email' => 'admin@kai-daop4.id',
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'username' => 'user.daop4',
                'name' => 'Staff KAI Daop 4',
                'email' => 'user@kai-daop4.id',
                'email_verified_at' => now(),
                'password' => Hash::make('user123'),
                'role' => 'user',
                'is_active' => true,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['id' => $userData['id']],
                $userData
            );
        }
    }
}
