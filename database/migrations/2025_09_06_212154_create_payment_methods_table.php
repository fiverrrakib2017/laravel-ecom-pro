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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Bkash, Nagad, Rocket, SSLCommerz, Stripe
            $table->string('account_number')->nullable(); // merchant number / account number
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('callback_url')->nullable();
            $table->boolean('status')->default(1); // 1 = Active, 0 = Inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
