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
        Schema::table('interviews', function (Blueprint $table) {
            $table->string('location_or_link')->nullable()->change();
            $table->text('assessment_details')->nullable()->after('location_or_link');
            $table->string('assessment_file')->nullable()->after('assessment_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->dropColumn(['assessment_details', 'assessment_file']);
            $table->string('location_or_link')->nullable(false)->change();
        });
    }
};
