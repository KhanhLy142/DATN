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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên khách hàng
            $table->string('email')->unique(); // Email khách hàng, unique
            $table->string('password'); // Mật khẩu
            $table->string('phone')->nullable(); // Số điện thoại, có thể null
            $table->text('address')->nullable(); // Địa chỉ, có thể null
            $table->timestamps(); // Thời gian tạo và cập nhật
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
