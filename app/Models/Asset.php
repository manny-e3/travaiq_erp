<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Asset extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::saved(function ($asset) {
            cache()->forget("asset.{$asset->id}.current_value");
        });

        static::deleted(function ($asset) {
            cache()->forget("asset.{$asset->id}.current_value");
        });
    }

    protected $fillable = [
        'asset_tag',
        'name',
        'category_id',
        'location_id',
        'custodian_id',
        'specifications',
        'purchase_date',
        'purchase_cost',
        'salvage_value',
        'useful_life_years',
        'depreciation_method',
        'status',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_cost' => 'decimal:2',
        'salvage_value' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(AssetLocation::class, 'location_id');
    }

    public function custodian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'custodian_id');
    }

    public function lifecycleEvents(): HasMany
    {
        return $this->hasMany(AssetLifecycleEvent::class, 'asset_id')->orderBy('event_date', 'desc')->orderBy('created_at', 'desc');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AssetDocument::class, 'asset_id')->orderBy('created_at', 'desc');
    }

    /**
     * Calculate current value dynamically based on depreciation method and revaluations.
     */
    public function getCurrentValueAttribute(): float
    {
        return cache()->remember("asset.{$this->id}.current_value", 3600, function () {
            if ($this->status === 'Written-Off') return 0.00;
            if ($this->status === 'Disposed') return 0.00; // Technically it's gone, or maybe realized salvage value

            // 1. Get baseline: start from purchase or the most recent Revaluation
            $latestRevaluation = $this->lifecycleEvents()->where('event_type', 'Revaluation')->first();
            
            $baseValue = $latestRevaluation ? $latestRevaluation->value_change : $this->purchase_cost;
            $baseDate = $latestRevaluation ? $latestRevaluation->event_date : $this->purchase_date;

            if (!$baseDate || !$baseValue || $this->useful_life_years <= 0) {
                return (float) $baseValue;
            }

            $yearsElapsed = $baseDate->diffInDays(Carbon::now()) / 365.25;

            // Ensure we don't depreciate past useful life
            if ($yearsElapsed >= $this->useful_life_years) {
                return (float) $this->salvage_value;
            }

            $depreciableAmount = $baseValue - $this->salvage_value;

            if ($this->depreciation_method === 'straight_line') {
                $annualDepreciation = $depreciableAmount / $this->useful_life_years;
                $totalDepreciation = $annualDepreciation * $yearsElapsed;
                $currentValue = $baseValue - $totalDepreciation;
            } else {
                // Reducing balance (Double Declining Balance method roughly)
                // Rate = (100% / Useful Life) * Factor (usually 2, sometimes 1.5)
                // Let's use a standard standard reducing rate where Salvage Value determines rate
                // formula: Rate = 1 - (Salvage / PurchaseCost) ^ (1/Years)
                
                // Safety check for math domain errors
                if ($baseValue <= 0 || $this->salvage_value <= 0) {
                    // Fallback to straight line if salvage is 0 to avoid ln(0) errors
                    $annualDepreciation = $baseValue / $this->useful_life_years;
                    $totalDepreciation = $annualDepreciation * $yearsElapsed;
                    $currentValue = $baseValue - $totalDepreciation;
                } else {
                    $rate = 1 - pow((float) ($this->salvage_value / $baseValue), (1 / $this->useful_life_years));
                    $currentValue = $baseValue * pow(1 - $rate, $yearsElapsed);
                }
            }

            return max((float) $this->salvage_value, round($currentValue, 2));
        });
    }
}
