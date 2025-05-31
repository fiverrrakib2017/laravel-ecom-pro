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
        Schema::create('tasks', function (Blueprint $table) {
             $table->id();
             $table->string('title');
            // $table->text('description')->nullable();
            // $table->unsignedBigInteger('pop_id')->nullable();
            //  $table->unsignedBigInteger('area_id')->nullable();

            // $table->enum('task_type', ['installation', 'complain', 'collection', 'disconnection', 'followup', 'other'])->default('other');

            // $table->unsignedBigInteger('employee_id')->nullable();
            // $table->unsignedBigInteger('created_by');
            // $table->unsignedBigInteger('completed_by')->nullable();

            // $table->dateTime('start_time')->nullable();
            // $table->dateTime('end_time')->nullable();

            // $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            // $table->text('note')->nullable();
            // $table->text('feedback')->nullable();

            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
