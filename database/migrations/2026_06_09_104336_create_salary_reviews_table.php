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
        Schema::create('salary_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appraisal_id')->nullable()->constrained('appraisals')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('current_salary', 12, 2);
            $table->decimal('proposed_salary', 12, 2);
            $table->foreignId('current_designation_id')->nullable()->constrained('designations')->onDelete('set null');
            $table->foreignId('proposed_designation_id')->nullable()->constrained('designations')->onDelete('set null');
            $table->string('current_job_title')->nullable();
            $table->string('proposed_job_title')->nullable();
            $table->date('effective_date');
            $table->string('status')->default('Pending');
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_reviews');
    }
};
