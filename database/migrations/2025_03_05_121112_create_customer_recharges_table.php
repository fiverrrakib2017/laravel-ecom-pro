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
        Schema::create('customer_recharges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('pop_id');
            $table->unsignedBigInteger('area_id');
            $table->integer('amount');
            $table->text('recharge_month')->nullable();
            $table->date('paid_until')->nullable();
            $table->text('note')->nullable();
            $table->text('voucher_no')->nullable();
            $table->enum('transaction_type', ['cash', 'credit', 'bkash', 'nagad', 'due_paid', 'other']);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('pop_id')->references('id')->on('pop_branches')->onDelete('cascade');
            $table->foreign('area_id')->references('id')->on('pop_areas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_recharges');
    }
};
