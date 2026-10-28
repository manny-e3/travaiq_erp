<?php

namespace App\Services;

use App\Models\AssetCategory;

class AssetCategoryService
{
    public function getAllWithAssetCount()
    {
        return AssetCategory::withCount('assets')->get();
    }

    public function getAll()
    {
        return AssetCategory::all();
    }

    public function createCategory(array $data): AssetCategory
    {
        return AssetCategory::create($data);
    }

    public function updateCategory(AssetCategory $category, array $data): bool
    {
        return $category->update($data);
    }

    public function deleteCategory(AssetCategory $category): bool
    {
        if ($category->assets()->count() > 0) {
            return false;
        }

        return $category->delete();
    }
}
