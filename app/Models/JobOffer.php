<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_application_id',
        'salary_offer',
        'expected_start_date',
        'contract_duration_months',
        'document_path',
        'status',
    ];

    protected $casts = [
        'expected_start_date' => 'date',
    ];

    public function application()
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }
}
