<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Primary Key
            
            // Khóa phụ tới bảng categories
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            
            $table->string('sku')->unique()->nullable(); // Mã sản phẩm
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('brand')->nullable(); // Epson, Sony, Wanbo...
            
            // Giá & Kho
            $table->decimal('price', 15, 2);
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->string('warranty')->default('12 tháng');
            
            // Cột cố định làm Bộ lọc (Filter)
            $table->integer('brightness')->nullable();   // ANSI Lumens
            $table->string('resolution')->nullable();    // HD, Full HD 1080p, 4K
            $table->string('display_tech')->nullable();  // DLP, 3LCD, Laser
            $table->string('os')->nullable();            // Android TV, None...
            
            // Hình ảnh & Mô tả
            $table->string('image')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            
            // Cột JSON chứa thông số chi tiết
            $table->json('specifications')->nullable();
            
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};