<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use App\Services\AssetCategoryService;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
{
    protected $categoryService;

    public function __construct(AssetCategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->getAllWithAssetCount();
        return view('admin.assets.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.assets.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $this->categoryService->createCategory($validated);

        return redirect()->route('admin.asset-categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(AssetCategory $assetCategory)
    {
        return view('admin.assets.categories.edit', compact('assetCategory'));
    }

    public function update(Request $request, AssetCategory $assetCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $this->categoryService->updateCategory($assetCategory, $validated);

        return redirect()->route('admin.asset-categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(AssetCategory $assetCategory)
    {
        if (!$this->categoryService->deleteCategory($assetCategory)) {
            return back()->with('error', 'Cannot delete category because it has assets associated with it.');
        }

        return redirect()->route('admin.asset-categories.index')->with('success', 'Category deleted successfully.');
    }
}
