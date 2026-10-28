<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gender',
        'date_of_birth',
        'marital_status',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'education',
        'certifications',
        'skills',
        'languages',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'education' => 'array',
        'certifications' => 'array',
        'skills' => 'array',
        'languages' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
