<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->enum('user_type', ['customer', 'staff'])->default('customer');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->index('user_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
