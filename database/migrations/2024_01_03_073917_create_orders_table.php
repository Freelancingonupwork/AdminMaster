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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('user_email');
            $table->integer('product_id');
            $table->integer('category_id');
            $table->integer('coupon_id');
            $table->integer('cart_id');
            $table->string('order_type')->nullable();
            $table->date('order_date');
            $table->time('order_time');
            $table->tinyInteger('is_paid')->comment('0: No, 1: Yes')->nullable();
            $table->tinyInteger('status')->default(1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
