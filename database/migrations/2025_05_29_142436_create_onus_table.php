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
        Schema::create('onus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_id')->constrained('olt_devices')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');

            $table->string('name', 100);
            $table->string('serial_number', 100);
            $table->string('mac_address', 50);
            $table->string('pon_port', 50);
            $table->string('vlan_id', 50)->nullable();

            $table->enum('status', ['online', 'offline'])->default('offline');
            $table->decimal('rx_power', 5, 2)->nullable(); // Optional
            $table->integer('distance')->nullable();       // in meters

            $table->timestamp('last_online')->nullable();
            $table->timestamp('offline_time')->nullable();
            $table->text('offline_reason')->nullable();
            $table->timestamp('last_updated_at')->nullable();

            $table->string('location', 100)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onus');
    }
};
