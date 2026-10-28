<?php

namespace App\Services;

use App\Models\AssetLocation;

class AssetLocationService
{
    public function getAllWithAssetCount()
    {
        return AssetLocation::withCount('assets')->get();
    }

    public function getAll()
    {
        return AssetLocation::all();
    }

    public function createLocation(array $data): AssetLocation
    {
        return AssetLocation::create($data);
    }

    public function updateLocation(AssetLocation $location, array $data): bool
    {
        return $location->update($data);
    }

    public function deleteLocation(AssetLocation $location): bool
    {
        if ($location->assets()->count() > 0) {
            return false;
        }

        return $location->delete();
    }
}
