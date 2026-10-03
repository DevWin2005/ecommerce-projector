<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo hoặc cập nhật tài khoản quản trị viên tối cao
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Quản Trị Viên',
                'password' => Hash::make('123456'), // Mật khẩu đăng nhập: 123456
                'role' => 'admin',
            ]
        );
    }
}