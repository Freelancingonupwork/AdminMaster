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
            $table->id();
            $table->integer('category_id');
            $table->string('product_name');
            $table->string('product_code');
            $table->string('slug');
            $table->string('description', 10000)->nullable();
            $table->string('product_price')->nullable();
            $table->string('product_image')->nullable();
            $table->string('promo_code')->nullable();
            $table->tinyInteger('subscription_type')->default(1)->comment('0: Life time with subscription', '1: One time with subscription')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
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
