<?php

namespace App\Http\Controllers;

use App\Models\AssetLocation;
use App\Services\AssetLocationService;
use Illuminate\Http\Request;

class AssetLocationController extends Controller
{
    protected $locationService;

    public function __construct(AssetLocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function index()
    {
        $locations = $this->locationService->getAllWithAssetCount();
        return view('admin.assets.locations.index', compact('locations'));
    }

    public function create()
    {
        return view('admin.assets.locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $this->locationService->createLocation($validated);

        return redirect()->route('admin.asset-locations.index')->with('success', 'Location created successfully.');
    }

    public function edit(AssetLocation $assetLocation)
    {
        return view('admin.assets.locations.edit', compact('assetLocation'));
    }

    public function update(Request $request, AssetLocation $assetLocation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $this->locationService->updateLocation($assetLocation, $validated);

        return redirect()->route('admin.asset-locations.index')->with('success', 'Location updated successfully.');
    }

    public function destroy(AssetLocation $assetLocation)
    {
        if (!$this->locationService->deleteLocation($assetLocation)) {
            return back()->with('error', 'Cannot delete location because it has assets associated with it.');
        }

        return redirect()->route('admin.asset-locations.index')->with('success', 'Location deleted successfully.');
    }
}
