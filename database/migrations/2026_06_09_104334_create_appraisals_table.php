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
        Schema::create('appraisals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appraisal_cycle_id')->constrained('appraisal_cycles')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('manager_id')->constrained('users')->onDelete('cascade');
            $table->decimal('self_rating', 3, 2)->nullable();
            $table->text('self_comment')->nullable();
            $table->decimal('manager_rating', 3, 2)->nullable();
            $table->text('manager_comment')->nullable();
            $table->decimal('calibrated_rating', 3, 2)->nullable();
            $table->text('calibrated_comment')->nullable();
            $table->string('status')->default('Pending Self');
            $table->timestamps();

            $table->unique(['appraisal_cycle_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appraisals');
    }
};
