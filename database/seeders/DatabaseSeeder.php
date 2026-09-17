<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@capstone.com'],
            ['name' => 'Super Admin Capstone', 'password' => Hash::make('password'), 'role' => 'super_admin']
        );

        User::updateOrCreate(
            ['email' => 'admin@capstone.com'],
            ['name' => 'Admin Production', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'user@capstone.com'],
            ['name' => 'Operator User', 'password' => Hash::make('password'), 'role' => 'user']
        );
    }
}