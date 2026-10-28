<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_run_id',
        'user_id',
        'basic_salary',
        'allowances',
        'gross_salary',
        'pension_deduction',
        'nhf_deduction',
        'paye_tax',
        'net_salary',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'pension_deduction' => 'decimal:2',
        'nhf_deduction' => 'decimal:2',
        'paye_tax' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    public function payrollRun()
    {
        return $this->belongsTo(PayrollRun::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
