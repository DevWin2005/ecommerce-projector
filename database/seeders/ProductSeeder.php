<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Máy Chiếu Mini Di Động
            ['Beecube star', 'Máy Chiếu Mini Di Động', 5000000, 20],
            ['Máy Chiếu Mini Wanbo T6 Max 4K Auto Focus', 'Máy Chiếu Mini Di Động', 4290000, 25],
            ['Máy chiếu Mini Wanbo T2 Max New', 'Máy Chiếu Mini Di Động', 3850000, 30],

            // Máy Chiếu Gia Đình 4K
            ['Máy Chiếu Gia Đình Beecube Xtreme II Rạp Phim Tại Gia', 'Máy Chiếu Gia Đình 4K', 5990000, 15],
            ['Máy chiếu 4K XGIMI Horizon Pro', 'Máy Chiếu Gia Đình 4K', 28900000, 10],

            // Máy Chiếu Văn Phòng & Trường Học
            ['Máy chiếu văn phòng Epson EB-E01 XGA', 'Máy Chiếu Văn Phòng & Trường Học', 9900000, 12],
            ['Máy chiếu hội thảo ViewSonic PA503W', 'Máy Chiếu Văn Phòng & Trường Học', 11500000, 8],

            // Máy Chiếu Siêu Gần (UST Laser)
            ['Máy chiếu siêu gần Dangbei Mars Pro 4K Laser', 'Máy Chiếu Siêu Gần (UST Laser)', 35000000, 5],

            // Màn Chiếu & Phụ Kiện
            ['Màn chiếu 3 chân Dalite 100 inch (1m78 x 1m78)', 'Màn Chiếu & Phụ Kiện', 850000, 50],
            ['Giá treo máy chiếu đa năng xoay 360 độ', 'Màn Chiếu & Phụ Kiện', 250000, 100],
        ];

        foreach ($products as [$name, $categoryName, $price, $quantity]) {
            $category = Category::firstOrCreate(['name' => $categoryName]);
            Product::firstOrCreate(
                ['name' => $name, 'category_id' => $category->id],
                ['price' => $price, 'quantity' => $quantity]
            );
        }
    }
}