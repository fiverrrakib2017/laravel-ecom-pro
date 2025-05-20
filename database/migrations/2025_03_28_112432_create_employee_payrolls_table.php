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
        Schema::create('employee_payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('salary_id');
            $table->date('month_year');
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('advance_salary', 10, 2);
            $table->decimal('loan_deduction', 10, 2);
            $table->decimal('tax', 10, 2);
            $table->decimal('net_salary', 10, 2);
            $table->date('payment_date')->nullable();
            $table->enum('payment_method', ['Cash', 'Bank'])->default('Cash');
            $table->enum('status', ['Paid', 'Unpaid'])->default('Unpaid');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('salary_id')->references('id')->on('employee_salaries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_payrolls');
    }
};
