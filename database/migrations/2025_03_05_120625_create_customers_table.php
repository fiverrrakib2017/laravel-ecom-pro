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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('fullname', 100);
            $table->string('phone', 15)->unique();
            $table->string('nid', 20)->unique()->nullable();
            $table->text('address')->nullable();
            $table->integer('con_charge')->default(0);
            $table->integer('amount')->default(0);
            $table->string('username', 100)->unique();
            $table->string('password', 255);
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('pop_id');
            $table->unsignedBigInteger('area_id');
            $table->unsignedBigInteger('router_id');
            $table->enum('status', ['active','online', 'offline', 'blocked', 'expired','disabled'])->default('active');
            $table->date('expire_date')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('liabilities', ['YES', 'NO'])->default('NO');
            $table->enum('onu_type', ['customer', 'company'])->default('customer');
            $table->integer('is_delete')->default('0');
            $table->timestamps();

            $table->foreign('package_id')->references('id')->on('branch_packages')->onDelete('cascade');
            $table->foreign('pop_id')->references('id')->on('pop_branches')->onDelete('cascade');
            $table->foreign('area_id')->references('id')->on('pop_areas')->onDelete('cascade');
            $table->foreign('router_id')->references('id')->on('routers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
