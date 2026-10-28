<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeerFeedback extends Model
{
    use HasFactory;

    protected $table = 'peer_feedbacks';

    protected $fillable = [
        'appraisal_id',
        'reviewer_id',
        'relationship',
        'rating',
        'feedback',
        'status',
    ];

    protected $casts = [
        'rating' => 'float',
    ];

    public function appraisal()
    {
        return $this->belongsTo(Appraisal::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
