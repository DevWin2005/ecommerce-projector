<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = trim((string) config('seeding.admin.email'));
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = 'admin@example.com';
        }

        $password = (string) config('seeding.admin.password');
        if (strlen($password) < 6) {
            $password = 'AdminPassword123!';
        }

        $existing = User::where('email', $email)->first();
        if ($existing) {
            if ($existing->role !== 'admin') {
                $existing->role = 'admin';
                $existing->verify = $existing->verify ?? now();
                $existing->email_verified_at = $existing->email_verified_at ?? now();
                $existing->save();
                $this->command?->info("Updated existing account {$email} to admin role.");
            } else {
                $this->command?->info('Admin already exists; existing account kept.');
            }
            return;
        }

        $admin = new User();
        $admin->forceFill([
            'name' => config('seeding.admin.name') ?: 'Shop Admin',
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'verify' => now(),
            'email_verified_at' => now(),
        ])->save();

        $this->command?->info('Admin created and verified successfully.');
    }
}