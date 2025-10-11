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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 100);
            $table->string('phone', 15)->index();
            $table->string('email', 120)->nullable();
            $table->text('address')->nullable();

            $table->enum('source', ['facebook', 'referral', 'walk_in', 'website', 'phone_call', 'other'])->default('other')->index();
            $table->enum('status', ['new', 'contacted', 'qualified', 'unqualified', 'converted', 'lost'])        ->default('new')->index();

            $table->enum('priority', ['high', 'medium', 'low'])->default('medium')->index();
            $table->enum('interest_level', ['high', 'medium', 'low'])->default('medium');
            $table->string('service_interest', 150)->nullable();
            $table->text('feedback')->nullable();
            $table->integer('lead_score')->default(0);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('estimated_close_date')->nullable();
            $table->boolean('follow_up_required')->default(false);
            $table->timestamp('first_contacted_at')->nullable();

            // Date when the lead was last contacted
            $table->timestamp('last_contacted_at')->nullable();

            // Source campaign for the lead (e.g., marketing campaign ID)
            $table->string('campaign_source', 100)->nullable();

            // Whether this lead has been engaged with multiple times (follow-up count)
            $table->integer('follow_up_count')->default(0);

            // Comments/notes on the lead for internal purposes
            $table->text('internal_notes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
