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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('user_email');
            $table->string('plan_name');
            $table->string('plan_value');
            $table->tinyInteger('subscription_type')->comment('0: Quartarly, 1: Monthly, 2: Yearly');
            $table->date('start_date');
            $table->date('end_date');
            $table->tinyInteger('subscription_status')->comment('0: Payment Failed, 1: Active, 2: Expired');
            $table->string('type');
            $table->string('stripe_id')->unique();
            $table->string('stripe_status');
            $table->string('stripe_price')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0: Inactive, 1: Active')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'stripe_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
