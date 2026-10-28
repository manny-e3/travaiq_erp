<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'appraisal_id',
        'user_id',
        'current_salary',
        'proposed_salary',
        'current_designation_id',
        'proposed_designation_id',
        'current_job_title',
        'proposed_job_title',
        'effective_date',
        'status',
        'approved_by_id',
        'notes',
    ];

    protected $casts = [
        'current_salary' => 'float',
        'proposed_salary' => 'float',
        'effective_date' => 'date',
    ];

    public function appraisal()
    {
        return $this->belongsTo(Appraisal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currentDesignation()
    {
        return $this->belongsTo(Designation::class, 'current_designation_id');
    }

    public function proposedDesignation()
    {
        return $this->belongsTo(Designation::class, 'proposed_designation_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }
}
