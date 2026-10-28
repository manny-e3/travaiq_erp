<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_application_id',
        'scheduled_at',
        'location_or_link',
        'assessment_details',
        'assessment_file',
        'notes',
        'status',
        'interview_stage',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function interviewers()
    {
        return $this->belongsToMany(User::class, 'interview_user');
    }
}
