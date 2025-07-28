<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Enums\Role; // Pastikan namespace dan enum ini sudah benar

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // 🧑‍ Regular User
        // Menggunakan nilai default jika ENV tidak diatur (untuk dev/testing)
        $userEmail = env('USER_EMAIL', 'user@example.com');
        $userPassword = env('USER_PASSWORD', 'password');

        if (!User::where('email', $userEmail)->exists()) {
            User::create([
                'name' => 'Test User',
                'email' => $userEmail,
                'password' => Hash::make($userPassword),
                'phone' => '081234567890',
                'address' => 'User Address',
                'role' => Role::USER,
            ]);
        }

        // 👨‍💼 Admin User
        // Menggunakan nilai default jika ENV tidak diatur (untuk dev/testing)
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $adminPassword = env('ADMIN_PASSWORD', 'password');

        if (!User::where('email', $adminEmail)->exists()) {
            User::create([
                'name' => 'Admin User',
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'phone' => '089876543210',
                'address' => 'Admin Address',
                'role' => Role::ADMIN,
            ]);
        }
    }
}
