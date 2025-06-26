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
        Schema::create('routers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pop_id');
            $table->string('name');
            $table->string('ip_address');
            $table->string('username');
            $table->string('password');
            $table->string('port')->default('8728');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('is_radius', ['yes', 'no']);
            $table->string('api_version')->nullable();
            $table->string('location')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('pop_id')->references('id')->on('pop_branches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routers');
    }
};
