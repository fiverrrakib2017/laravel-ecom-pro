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
        Schema::create('hotspot_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotspot_profile_id')->constrained()->cascadeOnDelete();
            $table->string('username');
            $table->text('password_encrypted'); // store encrypted for print/recovery
            $table->string('mac_lock')->nullable();
            $table->enum('status',['active','disabled','expired','blocked'])->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->bigInteger('upload_bytes')->default(0);
            $table->bigInteger('download_bytes')->default(0);
            $table->bigInteger('uptime_seconds')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('comment')->nullable();
            $table->timestamps();
            $table->unique(['router_id','username']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotspot_users');
    }
};
