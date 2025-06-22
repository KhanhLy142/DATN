<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên nhà cung cấp
            $table->string('email')->nullable(); // Email liên hệ
            $table->string('phone')->nullable(); // Số điện thoại
            $table->text('address')->nullable(); // Địa chỉ
            $table->boolean('status')->default(1); // 1 = hoạt động, 0 = ngưng
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
