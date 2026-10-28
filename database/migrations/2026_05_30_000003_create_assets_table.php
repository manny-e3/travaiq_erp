<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_tag')->unique();
            $table->string('name');
            $table->foreignId('category_id')->constrained('asset_categories')->onDelete('restrict');
            $table->foreignId('location_id')->constrained('asset_locations')->onDelete('restrict');
            $table->foreignId('custodian_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('specifications')->nullable();
            
            // Financials
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 15, 2)->default(0);
            $table->decimal('salvage_value', 15, 2)->default(0);
            $table->integer('useful_life_years')->default(1);
            $table->enum('depreciation_method', ['straight_line', 'reducing_balance'])->default('straight_line');
            
            // Status
            $table->enum('status', ['Active', 'Maintenance', 'Disposed', 'Written-Off'])->default('Active');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
