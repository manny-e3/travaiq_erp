<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sla_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('customer_tier')->default('Standard'); // Standard, Gold, Platinum
            $table->string('category')->nullable(); // Technical, Billing, Account, Feedback, General, null for any
            $table->string('priority')->default('Medium'); // Low, Medium, High, Urgent
            $table->decimal('response_time_hours', 8, 2); // e.g. 0.5 (30 mins), 4.0
            $table->decimal('resolution_time_hours', 8, 2); // e.g. 2.0, 24.0
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sla_policies');
    }
};
