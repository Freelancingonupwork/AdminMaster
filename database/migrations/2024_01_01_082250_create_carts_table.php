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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('product_id');
            $table->string('product_name');
            $table->string('product_price');
            $table->integer('category_id');
            $table->integer('coupon_id');
            $table->string('product_image');
            $table->string('product_discount')->nullable();
            $table->integer('shipping_charge')->nullable();
            $table->integer('quantity')->default(1)->nullable();
            $table->string('product_total')->nullable();
            $table->tinyInteger('payment_status')->default(0)->nullable();
            $table->string('session_id')->nullable();
            $table->tinyInteger('status')->default(1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
