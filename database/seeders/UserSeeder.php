<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::updateOrCreate(
            ['email' => 'lekleksyahrul@gmail.com'],
            [
                'username' => 'superadmin',
                'name' => 'Super Admin KAI Daop 4',
                'password' => Hash::make('superadmin123'),
                'role' => 'superadmin',
                'is_active' => true,
            ]
        );

        // Admin
        User::updateOrCreate(
            ['email' => 'admin@kai-daop4.id'],
            [
                'username' => 'admin.daop4',
                'name' => 'Admin KAI Daop 4',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }
}
