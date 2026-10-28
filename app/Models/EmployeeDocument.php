<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_name',
        'document_type',
        'file_path',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
