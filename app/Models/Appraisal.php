<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appraisal extends Model
{
    use HasFactory;

    protected $fillable = [
        'appraisal_cycle_id',
        'user_id',
        'manager_id',
        'self_rating',
        'self_comment',
        'manager_rating',
        'manager_comment',
        'calibrated_rating',
        'calibrated_comment',
        'status',
    ];

    protected $casts = [
        'self_rating' => 'float',
        'manager_rating' => 'float',
        'calibrated_rating' => 'float',
    ];

    public function cycle()
    {
        return $this->belongsTo(AppraisalCycle::class, 'appraisal_cycle_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function peerFeedbacks()
    {
        return $this->hasMany(PeerFeedback::class);
    }

    public function salaryReview()
    {
        return $this->hasOne(SalaryReview::class);
    }
}
