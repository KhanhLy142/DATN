<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Khóa chính
            $table->string('name'); // Tên sản phẩm
            $table->string('sku')->unique(); // Mã sản phẩm (bắt buộc cho mỹ phẩm)
            $table->unsignedBigInteger('brand_id'); // FK -> brands
            $table->unsignedBigInteger('category_id'); // FK -> categories
            $table->text('description')->nullable(); // Mô tả sản phẩm
            $table->decimal('base_price', 10, 2); // Giá cơ bản (bắt buộc)
            $table->string('image')->nullable(); // Hình ảnh chính
            $table->boolean('status')->default(1); // Trạng thái hiển thị
            $table->timestamps(); // Tạo & cập nhật thời gian

            // Ràng buộc khóa ngoại
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
