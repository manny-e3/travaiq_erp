<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Services\AssetService;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    protected $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    public function index(Request $request)
    {
        $assets = $this->assetService->getFilteredAssets($request->only(['status', 'category_id']));
        $categories = AssetCategory::all();

        return view('admin.assets.index', compact('assets', 'categories'));
    }

    public function create()
    {
        $dependencies = $this->assetService->getDependenciesForCreate();
        $dependencies['suggestedTag'] = $this->assetService->generateAssetTag();

        return view('admin.assets.create', $dependencies);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_tag' => 'required|string|unique:assets,asset_tag',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:asset_categories,id',
            'location_id' => 'required|exists:asset_locations,id',
            'custodian_id' => 'nullable|exists:users,id',
            'specifications' => 'nullable|string',
            'purchase_date' => 'required|date',
            'purchase_cost' => 'required|numeric|min:0',
            'salvage_value' => 'required|numeric|min:0|lt:purchase_cost',
            'useful_life_years' => 'required|integer|min:1',
            'depreciation_method' => 'required|in:straight_line,reducing_balance',
            'status' => 'required|in:Active,Maintenance,Disposed,Written-Off',
        ]);

        $asset = $this->assetService->createAsset($validated, auth()->id());

        return redirect()->route('admin.assets.show', $asset)->with('success', 'Asset registered successfully.');
    }

    public function show(Asset $asset)
    {
        $this->assetService->getAssetWithDetails($asset);
        
        // Generate a simple QR Code URL (using a free API for demonstration)
        // In a real production app, use a package like simplesoftwareio/simple-qrcode
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode(route('admin.assets.show', $asset));

        return view('admin.assets.show', compact('asset', 'qrCodeUrl'));
    }

    public function edit(Asset $asset)
    {
        $dependencies = $this->assetService->getDependenciesForCreate();
        $dependencies['asset'] = $asset;

        return view('admin.assets.edit', $dependencies);
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:asset_categories,id',
            'location_id' => 'required|exists:asset_locations,id',
            'custodian_id' => 'nullable|exists:users,id',
            'specifications' => 'nullable|string',
            'status' => 'required|in:Active,Maintenance,Disposed,Written-Off',
        ]);

        $this->assetService->updateAsset($asset, $validated);

        return redirect()->route('admin.assets.show', $asset)->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        $this->assetService->deleteAsset($asset);
        return redirect()->route('admin.assets.index')->with('success', 'Asset deleted successfully.');
    }
}
