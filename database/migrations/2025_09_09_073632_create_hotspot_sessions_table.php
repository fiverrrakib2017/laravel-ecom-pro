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
        Schema::create('hotspot_sessions', function (Blueprint $table) {
            $table->id();
           $table->foreignId('router_id')->constrained('routers')->cascadeOnDelete();
            $table->string('username');
            $table->string('mac')->nullable();
            $table->string('ip')->nullable();
            $table->timestamp('login_time')->nullable();
            $table->timestamp('logout_time')->nullable();
            $table->bigInteger('uptime_seconds')->default(0);
            $table->bigInteger('download_bytes')->default(0);
            $table->bigInteger('upload_bytes')->default(0);
            $table->string('terminate_cause')->nullable();
            $table->boolean('was_kicked')->default(false);
            $table->foreignId('voucher_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->index(['router_id','username','login_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotspot_sessions');
    }
};
