<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id'); // FK đến bảng products

            $table->string('variant_name')->nullable(); // Tên hiển thị biến thể: "Son đỏ 3g", "Nước hoa hồng 200ml"
            $table->string('color')->nullable();        // Màu sắc
            $table->string('volume')->nullable();       // Dung tích
            $table->string('scent')->nullable();        // Mùi hương

            $table->decimal('price', 10, 2)->default(0); // Giá riêng
            $table->integer('stock_quantity')->default(0); // Tồn kho riêng
            $table->boolean('status')->default(1); // 1 = có sẵn, 0 = hết hàng/ẩn

            $table->timestamps();

            // Ràng buộc khóa ngoại
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
