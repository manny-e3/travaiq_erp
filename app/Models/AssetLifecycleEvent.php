<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetLifecycleEvent extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::saved(function ($event) {
            cache()->forget("asset.{$event->asset_id}.current_value");
        });

        static::deleted(function ($event) {
            cache()->forget("asset.{$event->asset_id}.current_value");
        });
    }

    protected $fillable = [
        'asset_id',
        'event_type',
        'event_date',
        'value_change',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'event_date' => 'date',
        'value_change' => 'decimal:2',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
