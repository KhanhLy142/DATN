<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên danh mục
            $table->text('description')->nullable(); // Mô tả
            $table->unsignedBigInteger('parent_id')->nullable(); // Danh mục cha (có thể null)
            $table->boolean('status')->default(1); // 1 = hiển thị, 0 = ẩn
            $table->timestamps();

            // Khóa ngoại tự tham chiếu
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
