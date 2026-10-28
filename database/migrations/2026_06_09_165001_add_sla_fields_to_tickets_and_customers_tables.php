<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('tier')->default('Standard')->after('company'); // Standard, Gold, Platinum
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->timestamp('response_deadline')->nullable()->after('assigned_to_id');
            $table->timestamp('first_responded_at')->nullable()->after('response_deadline');
            $table->boolean('is_sla_breached')->default(false)->after('resolved_at');
            $table->foreignId('sla_policy_id')->nullable()->after('is_sla_breached')->constrained('sla_policies')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sla_policy_id');
            $table->dropColumn(['response_deadline', 'first_responded_at', 'is_sla_breached']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('tier');
        });
    }
};
