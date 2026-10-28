<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Services\AssetLifecycleService;
use Illuminate\Http\Request;

class AssetLifecycleController extends Controller
{
    protected $lifecycleService;

    public function __construct(AssetLifecycleService $lifecycleService)
    {
        $this->lifecycleService = $lifecycleService;
    }

    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'event_type' => 'required|in:Revaluation,Maintenance,Disposal,Write-off',
            'event_date' => 'required|date',
            'value_change' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $this->lifecycleService->recordEvent($asset, $validated, auth()->id());

        return redirect()->route('admin.assets.show', $asset)->with('success', 'Lifecycle event recorded successfully.');
    }
}
