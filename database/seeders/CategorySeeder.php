<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Máy Chiếu Mini Di Động',
            'Máy Chiếu Gia Đình 4K',
            'Máy Chiếu Văn Phòng & Trường Học',
            'Máy Chiếu Siêu Gần (UST Laser)',
            'Màn Chiếu & Phụ Kiện',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}