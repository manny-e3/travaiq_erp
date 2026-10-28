<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employment_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('job_title');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('basic_salary', 12, 2)->nullable();
            $table->string('change_reason'); // Promotion, Transfer, Initial Hiring, Salary Adjustment, etc.
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employment_histories');
    }
};
