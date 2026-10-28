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
        Schema::create('interview_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::table('interviews', function (Blueprint $table) {
            $table->dropForeign(['interviewer_id']);
            $table->dropColumn('interviewer_id');
        });
    }

    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->foreignId('interviewer_id')->nullable()->constrained('users')->onDelete('cascade');
        });
        
        Schema::dropIfExists('interview_user');
    }
};
