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
        Schema::create('olt_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();

            $table->enum('brand', ['Huawei', 'ZTE', 'Fiberhome', 'Alcatel', 'Cisco', 'Visiontek','VSOL', 'CDATA', 'BDCOM', 'ECOM', 'TBS', 'Corelink', 'Other'])->default('Other');

            $table->enum('mode', ['GPON', 'XG-PON', 'EPON', 'XGS-PON', 'NG-PON2'])->default('GPON');

            $table->string('ip_address')->unique();
            $table->unsignedSmallInteger('port')->default('22');
            $table->enum('protocol', ['SSH', 'Telnet'])->default('SSH');
            $table->string('snmp_community')->nullable();
            $table->enum('snmp_version', ['v1', 'v2c', 'v3'])->default('v2c');
            $table->string('username');
            $table->string('password');
            $table->string('vendor')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('firmware_version')->nullable();
            $table->string('location')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('olt_devices');
    }
};
