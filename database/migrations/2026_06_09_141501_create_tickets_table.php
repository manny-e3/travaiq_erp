<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_key')->unique();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('subject');
            $table->text('description');
            $table->string('status')->default('Open'); // Open, In Progress, Resolved, Closed
            $table->string('priority')->default('Medium'); // Low, Medium, High, Urgent
            $table->string('category')->default('General'); // Technical, Billing, Account, Feedback, General
            $table->string('channel')->default('Portal'); // Email, Phone, Portal, API
            $table->foreignId('assigned_to_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('sla_deadline')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
