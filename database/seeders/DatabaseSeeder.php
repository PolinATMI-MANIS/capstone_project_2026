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
        User::updateOrCreate(
            ['email' => 'superadmin@capstone.com'],
            [
                'name'     => 'Super Admin Capstone', 
                'password' => Hash::make('password'), 
                'role'     => 'super_admin'
            ]
        );

        // 2. Akun Admin
        User::updateOrCreate(
            ['email' => 'admin@capstone.com'],
            [
                'name'     => 'Admin Production', 
                'password' => Hash::make('password'), 
                'role'     => 'admin'
            ]
        );

        // 3. Akun User Biasa
        User::updateOrCreate(
            ['email' => 'user@capstone.com'],
            [
                'name'     => 'Operator User', 
                'password' => Hash::make('password'), 
                'role'     => 'user'
            ]
        );
    }
}