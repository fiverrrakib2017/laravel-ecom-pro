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
        Schema::create('customer_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('pop_id');
            $table->unsignedBigInteger('area_id');
            $table->enum('device_type', ['onu', 'router', 'fiber', 'other']);
            $table->string('device_name')->nullable(); // Optional, for naming or model
            $table->string('serial_number')->nullable(); // ONU বা router-এর জন্য useful
            $table->date('assigned_date')->nullable();
            $table->date('returned_date')->nullable();
            $table->enum('status', ['assigned', 'returned', 'damaged'])->default('assigned');
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
        Schema::dropIfExists('customer_devices');
    }
};
