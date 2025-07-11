<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cod','bank_transfer','vnpay']);
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded']);
            $table->string('vnpay_transaction_id', 255)->nullable();
            $table->text('payment_note')->nullable();
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
