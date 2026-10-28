<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetLocation;
use App\Models\User;

class AssetService
{
    public function getFilteredAssets(array $filters)
    {
        $query = Asset::with(['category', 'location', 'custodian']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query->latest()->paginate(15);
    }

    public function getDependenciesForCreate()
    {
        return [
            'categories' => AssetCategory::all(),
            'locations' => AssetLocation::all(),
            'users' => User::where('is_active', true)->get(),
        ];
    }

    public function generateAssetTag(): string
    {
        return 'AST-' . strtoupper(substr(uniqid(), -6));
    }

    public function createAsset(array $data, int $userId): Asset
    {
        $asset = Asset::create($data);

        // Auto-record the Acquisition event
        $asset->lifecycleEvents()->create([
            'event_type' => 'Acquisition',
            'event_date' => $asset->purchase_date,
            'value_change' => $asset->purchase_cost,
            'notes' => 'Initial asset registration',
            'recorded_by' => $userId,
        ]);

        return $asset;
    }

    public function getAssetWithDetails(Asset $asset)
    {
        return $asset->load(['category', 'location', 'custodian', 'lifecycleEvents.recordedBy', 'documents.uploader']);
    }

    public function updateAsset(Asset $asset, array $data): bool
    {
        return $asset->update($data);
    }

    public function deleteAsset(Asset $asset): bool
    {
        return $asset->delete();
    }
}
