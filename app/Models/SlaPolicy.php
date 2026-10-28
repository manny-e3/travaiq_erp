<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlaPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'customer_tier',
        'category',
        'priority',
        'response_time_hours',
        'resolution_time_hours',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'response_time_hours' => 'decimal:2',
        'resolution_time_hours' => 'decimal:2',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
