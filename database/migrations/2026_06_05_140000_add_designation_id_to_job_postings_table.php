<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->foreignId('designation_id')->nullable()->constrained('designations')->nullOnDelete();
        });

        // Migrate existing job posting titles to designations
        $postings = DB::table('job_postings')->get();
        foreach ($postings as $posting) {
            if (!empty($posting->title)) {
                $title = trim($posting->title);
                $designation = DB::table('designations')->where('name', $title)->first();
                if (!$designation) {
                    $designationId = DB::table('designations')->insertGetId([
                        'name' => $title,
                        'description' => 'Migrated from job posting: ' . $title,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $designationId = $designation->id;
                }

                DB::table('job_postings')
                    ->where('id', $posting->id)
                    ->update(['designation_id' => $designationId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropForeign(['designation_id']);
            $table->dropColumn('designation_id');
        });
    }
};
