<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create designations table
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Add designation_id to users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('designation_id')->nullable()->constrained('designations')->nullOnDelete();
        });

        // 3. Add designation_id to employment_histories
        Schema::table('employment_histories', function (Blueprint $table) {
            $table->foreignId('designation_id')->nullable()->constrained('designations')->nullOnDelete();
        });

        // 4. Migrate existing data
        $this->migrateExistingJobTitlesToDesignations();
    }

    public function down(): void
    {
        Schema::table('employment_histories', function (Blueprint $table) {
            $table->dropForeign(['designation_id']);
            $table->dropColumn('designation_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['designation_id']);
            $table->dropColumn('designation_id');
        });

        Schema::dropIfExists('designations');
    }

    private function migrateExistingJobTitlesToDesignations(): void
    {
        // Get all unique job titles from users
        $userTitles = DB::table('users')
            ->whereNotNull('job_title')
            ->where('job_title', '<>', '')
            ->pluck('job_title')
            ->toArray();

        // Get all unique job titles from employment_histories
        $historyTitles = DB::table('employment_histories')
            ->whereNotNull('job_title')
            ->where('job_title', '<>', '')
            ->pluck('job_title')
            ->toArray();

        $uniqueTitles = array_unique(array_merge($userTitles, $historyTitles));

        foreach ($uniqueTitles as $title) {
            $title = trim($title);
            if (empty($title)) {
                continue;
            }

            // Create designation if it doesn't exist
            $designationId = DB::table('designations')->insertGetId([
                'name' => $title,
                'description' => 'Migrated from existing job title: ' . $title,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update users
            DB::table('users')
                ->where('job_title', $title)
                ->update(['designation_id' => $designationId]);

            // Update employment histories
            DB::table('employment_histories')
                ->where('job_title', $title)
                ->update(['designation_id' => $designationId]);
        }
    }
};
