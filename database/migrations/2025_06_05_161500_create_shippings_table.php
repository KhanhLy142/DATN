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
        Schema::create('shippings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->text('shipping_address');
            $table->enum('shipping_method', ['standard', 'express']);
            $table->enum('shipping_status', ['pending', 'shipped', 'delivered']);
            $table->string('province', 255)->nullable();
            $table->decimal('shipping_fee', 10, 2)->default(0.00);
            $table->text('shipping_note')->nullable();
            $table->string('tracking_code', 100)->nullable();
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shippings');
    }
};
