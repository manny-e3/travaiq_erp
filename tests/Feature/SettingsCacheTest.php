<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SettingsCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_are_cached_on_retrieval_and_cleared_on_update(): void
    {
        // 1. Create a setting
        $setting = Setting::create([
            'key' => 'test_setting_key',
            'value' => 'original_value',
            'group' => 'Test',
        ]);

        // Verify it is not initially in the cache
        $this->assertFalse(Cache::has('setting.test_setting_key'));

        // 2. Retrieve the setting (this should trigger caching)
        $value = Setting::get('test_setting_key');
        $this->assertEquals('original_value', $value);
        $this->assertTrue(Cache::has('setting.test_setting_key'));
        $this->assertEquals('original_value', Cache::get('setting.test_setting_key'));

        // 3. Update the setting
        $setting->update(['value' => 'updated_value']);

        // Verify the cache was cleared
        $this->assertFalse(Cache::has('setting.test_setting_key'));

        // 4. Retrieve again (should re-cache with updated value)
        $newValue = Setting::get('test_setting_key');
        $this->assertEquals('updated_value', $newValue);
        $this->assertTrue(Cache::has('setting.test_setting_key'));
        $this->assertEquals('updated_value', Cache::get('setting.test_setting_key'));
    }
}
