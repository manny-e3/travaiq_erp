<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_record_write_off_event_and_updates_status_correctly(): void
    {
        $user = User::factory()->create();
        $category = AssetCategory::create(['name' => 'IT Equipment']);
        $location = AssetLocation::create(['name' => 'HQ Office']);

        $asset = Asset::create([
            'asset_tag' => 'AST-123456',
            'name' => 'Test Laptop',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'purchase_date' => '2026-06-11',
            'purchase_cost' => 1000.00,
            'salvage_value' => 200.00,
            'useful_life_years' => 5,
            'depreciation_method' => 'straight_line',
            'status' => 'Active',
        ]);

        $response = $this->actingAs($user)->post(route('admin.assets.lifecycle.store', $asset), [
            'event_type' => 'Write-off',
            'event_date' => '2026-07-21',
            'value_change' => 0.00,
            'notes' => 'Damaged beyond repair',
        ]);

        $response->assertRedirect(route('admin.assets.show', $asset));
        $asset->refresh();
        $this->assertEquals('Written-Off', $asset->status);
        $this->assertDatabaseHas('asset_lifecycle_events', [
            'asset_id' => $asset->id,
            'event_type' => 'Write-off',
            'notes' => 'Damaged beyond repair',
        ]);
    }

    public function test_can_record_disposal_event_and_updates_status_correctly(): void
    {
        $user = User::factory()->create();
        $category = AssetCategory::create(['name' => 'IT Equipment']);
        $location = AssetLocation::create(['name' => 'HQ Office']);

        $asset = Asset::create([
            'asset_tag' => 'AST-123456',
            'name' => 'Test Laptop',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'purchase_date' => '2026-06-11',
            'purchase_cost' => 1000.00,
            'salvage_value' => 200.00,
            'useful_life_years' => 5,
            'depreciation_method' => 'straight_line',
            'status' => 'Active',
        ]);

        $response = $this->actingAs($user)->post(route('admin.assets.lifecycle.store', $asset), [
            'event_type' => 'Disposal',
            'event_date' => '2026-07-21',
            'value_change' => 200.00,
            'notes' => 'Sold to employee',
        ]);

        $response->assertRedirect(route('admin.assets.show', $asset));
        $asset->refresh();
        $this->assertEquals('Disposed', $asset->status);
        $this->assertDatabaseHas('asset_lifecycle_events', [
            'asset_id' => $asset->id,
            'event_type' => 'Disposal',
            'notes' => 'Sold to employee',
        ]);
    }

    public function test_can_record_lifecycle_event_with_null_value_change(): void
    {
        $user = User::factory()->create();
        $category = AssetCategory::create(['name' => 'IT Equipment']);
        $location = AssetLocation::create(['name' => 'HQ Office']);

        $asset = Asset::create([
            'asset_tag' => 'AST-123456',
            'name' => 'Test Laptop',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'purchase_date' => '2026-06-11',
            'purchase_cost' => 1000.00,
            'salvage_value' => 200.00,
            'useful_life_years' => 5,
            'depreciation_method' => 'straight_line',
            'status' => 'Active',
        ]);

        $response = $this->actingAs($user)->post(route('admin.assets.lifecycle.store', $asset), [
            'event_type' => 'Write-off',
            'event_date' => '2026-07-21',
            'value_change' => null,
            'notes' => 'Damaged beyond repair',
        ]);

        $response->assertRedirect(route('admin.assets.show', $asset));
        $asset->refresh();
        $this->assertEquals('Written-Off', $asset->status);
        $this->assertDatabaseHas('asset_lifecycle_events', [
            'asset_id' => $asset->id,
            'event_type' => 'Write-off',
            'value_change' => 0.00,
            'notes' => 'Damaged beyond repair',
        ]);
    }

    public function test_asset_current_value_is_cached_and_invalidated_correctly(): void
    {
        $user = User::factory()->create();
        $category = AssetCategory::create(['name' => 'IT Equipment']);
        $location = AssetLocation::create(['name' => 'HQ Office']);

        $asset = Asset::create([
            'asset_tag' => 'AST-999999',
            'name' => 'Depreciating Laptop',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'purchase_date' => '2026-06-11',
            'purchase_cost' => 1000.00,
            'salvage_value' => 200.00,
            'useful_life_years' => 5,
            'depreciation_method' => 'straight_line',
            'status' => 'Active',
        ]);

        $cacheKey = "asset.{$asset->id}.current_value";

        // Verify not in cache initially
        $this->assertFalse(cache()->has($cacheKey));

        // Retrieve current value (calculates and stores in cache)
        $value = $asset->current_value;
        $this->assertTrue(cache()->has($cacheKey));
        $this->assertEquals($value, cache()->get($cacheKey));

        // Create a new lifecycle event (e.g. Write-off or Revaluation) which should clear the cache
        $this->actingAs($user)->post(route('admin.assets.lifecycle.store', $asset), [
            'event_type' => 'Write-off',
            'event_date' => '2026-07-21',
            'value_change' => 0.00,
            'notes' => 'Write off',
        ]);

        // Cache must be cleared on event storage
        $this->assertFalse(cache()->has($cacheKey));
    }
}
