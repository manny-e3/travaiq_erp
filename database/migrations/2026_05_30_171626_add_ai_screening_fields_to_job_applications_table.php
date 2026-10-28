<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->unsignedTinyInteger('ai_score')->nullable()->after('status');
            $table->string('ai_recommendation')->nullable()->after('ai_score'); // Shortlist, Review, Reject
            $table->text('ai_summary')->nullable()->after('ai_recommendation');
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn(['ai_score', 'ai_recommendation', 'ai_summary']);
        });
    }
};
