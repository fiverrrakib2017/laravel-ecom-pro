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
        Schema::create('olt_laser_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_device_id')->constrained('olt_devices')->onDelete('cascade');
            $table->string('pon_port');
            $table->enum('laser_status', ['normal', 'warning', 'critical'])->default('normal');
            $table->string('signal_strength')->nullable();
            $table->string('temperature')->nullable();
            $table->string('voltage')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('olt_laser_logs');
    }
};
