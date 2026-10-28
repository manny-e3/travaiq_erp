<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'designation_id',
        'description',
        'department_id',
        'location',
        'employment_type',
        'status',
        'closing_date',
        'created_by',
        'ai_pass_score',
    ];

    protected $casts = [
        'closing_date' => 'date',
    ];

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }
}
