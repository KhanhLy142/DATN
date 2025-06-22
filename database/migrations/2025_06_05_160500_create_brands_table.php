<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên thương hiệu
            $table->text('description')->nullable(); // Mô tả
            $table->string('logo')->nullable(); // Logo thương hiệu
            $table->string('country')->nullable(); // Quốc gia xuất xứ
            $table->unsignedBigInteger('supplier_id');
            $table->boolean('status')->default(1); // 1 = hoạt động, 0 = ngưng
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
