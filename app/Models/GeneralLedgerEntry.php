<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralLedgerEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'posting_date',
        'reference',
        'account_name',
        'account_type',
        'debit',
        'credit',
        'description',
    ];

    protected $casts = [
        'posting_date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];
}
