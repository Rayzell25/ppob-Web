<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // updateOrCreate memastikan akun tidak akan ganda meski dijalankan berkali-kali
        User::updateOrCreate(
            ['email' => 'anamrusak@gmail.com'],
            [
                'name' => 'Admin Rayzell',
                'password' => Hash::make('Cakep1Team'),
                'email_verified_at' => now(),
                'role' => 'admin',
            ]
        );
    }
}
