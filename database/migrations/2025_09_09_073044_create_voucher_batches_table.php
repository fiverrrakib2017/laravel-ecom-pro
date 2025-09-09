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
        Schema::create('voucher_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotspot_profile_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. Eid Offer 1GB 2H
            $table->unsignedInteger('qty');
            $table->string('code_prefix')->nullable();
            $table->unsignedInteger('username_length')->default(8);
            $table->unsignedInteger('password_length')->default(6);
            $table->unsignedInteger('validity_days_override')->nullable();
            $table->timestamp('expires_at')->nullable(); // batch-level hard expiry
            $table->unsignedInteger('price_minor')->default(0);
            $table->enum('status',['draft','generated','pushed','archived'])->default('draft');
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_batches');
    }
};
