<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetLifecycleEvent;

class AssetLifecycleService
{
    public function recordEvent(Asset $asset, array $data, int $userId): AssetLifecycleEvent
    {
        $data['recorded_by'] = $userId;
        $data['value_change'] = $data['value_change'] ?? 0.00;
        
        // If it's a disposal or write-off, the value change is effectively setting it to 0 or salvage
        if ($data['event_type'] === 'Disposal') {
            $asset->update(['status' => 'Disposed']);
        } elseif ($data['event_type'] === 'Write-off') {
            $asset->update(['status' => 'Written-Off']);
        } elseif ($data['event_type'] === 'Maintenance') {
            $asset->update(['status' => 'Maintenance']);
        }

        return $asset->lifecycleEvents()->create($data);
    }
}
