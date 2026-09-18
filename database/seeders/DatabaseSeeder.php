<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Super Admin
        User::create([
            'name'     => 'Super Admin',
            'email'    => 'superadmin@capstone.com',
            'password' => Hash::make('password'),
            'role'     => 'superadmin',
        ]);

        // 2. Akun Admin
        User::create([
            'name'     => 'Admin R&D',
            'email'    => 'admin@capstone.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // 3. Akun User Biasa
        User::create([
            'name'     => 'User Pengaju',
            'email'    => 'user@capstone.com',
            'password' => Hash::make('password'),
            'role'     => 'user',
        ]);
    }
}