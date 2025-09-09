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
        Schema::create('hotspot_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('mikrotik_profile'); 
            $table->string('rate_limit')->nullable(); // e.g. 5M/5M (fallback)
            $table->unsignedInteger('shared_users')->default(1);
            $table->string('idle_timeout')->nullable(); // e.g. 5m
            $table->string('keepalive_timeout')->nullable();
            $table->string('session_timeout')->nullable(); // e.g. 1d
            $table->unsignedInteger('validity_days')->default(1); // voucher validity
            $table->unsignedInteger('price_minor')->default(0); // store in minor unit (paisa)
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['router_id','mikrotik_profile']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotspot_profiles');
    }
};
