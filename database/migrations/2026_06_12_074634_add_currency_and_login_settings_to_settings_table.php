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
        \DB::table('settings')->insertOrIgnore([
            [
                'key' => 'currency_symbol',
                'value' => '$',
                'group' => 'Localization',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency_code',
                'value' => 'USD',
                'group' => 'Localization',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'login_enabled',
                'value' => '1',
                'group' => 'Security',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::table('settings')
            ->whereIn('key', ['currency_symbol', 'currency_code', 'login_enabled'])
            ->delete();
    }
};
