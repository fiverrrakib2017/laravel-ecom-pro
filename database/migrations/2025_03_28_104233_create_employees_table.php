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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('phone_2')->nullable();
            $table->date('hire_date');
            $table->string('address');
            $table->string('photo')->nullable();
            $table->string('father_name');
            $table->string('mother_name');
            $table->string('gender');
            $table->date('birth_date');
            $table->string('national_id')->unique();
            $table->string('religion');
            $table->string('blood_group')->nullable();
            $table->string('highest_education');
            $table->string('previous_school')->nullable();
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('designation_id');
            $table->decimal('salary', 10, 2);
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone');
            $table->text('remarks')->nullable();
            $table->enum('status', ['active', 'inactive','resigned'])->default('active');
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('designation_id')->references('id')->on('designations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
