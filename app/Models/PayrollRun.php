<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'period',
        'status',
        'total_gross',
        'total_deductions',
        'total_net',
        'processed_by_id',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'total_gross' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_net' => 'decimal:2',
    ];

    public function entries()
    {
        return $this->hasMany(PayrollEntry::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by_id');
    }
}
