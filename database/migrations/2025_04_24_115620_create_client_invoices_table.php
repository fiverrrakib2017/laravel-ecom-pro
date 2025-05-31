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
        Schema::create('client_invoices', function (Blueprint $table) {
            $table->id();
            $table->text('transaction_number')->nullable();
            $table->integer('usr_id')->nullable();
            $table->unsignedBigInteger('client_id');
            $table->date('invoice_date');
            $table->decimal('sub_total', 15, 2);
            $table->decimal('discount', 15, 2);
            $table->decimal('grand_total', 15, 2);
            $table->decimal('due_amount', 15, 2);
            $table->decimal('paid_amount', 15, 2);
            $table->text('note')->nullable();
            $table->integer('status')->comment('0=Draf,1=Completed');
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_invoices');
    }
};
