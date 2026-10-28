<?php

namespace App\Services;

use App\Models\User;
use App\Models\PayrollRun;
use App\Models\PayrollEntry;
use App\Models\GeneralLedgerEntry;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollService
{
    /**
     * Calculate Gross-to-Net breakdown based on Nigerian statutory rules.
     */
    public function calculateGrossToNet(float $basicSalary, float $allowances): array
    {
        $grossSalary = $basicSalary + $allowances;

        // 1. Pension: 8% of Basic Salary
        $pension = round($basicSalary * 0.08, 2);

        // 2. NHF: 2.5% of Basic Salary
        $nhf = round($basicSalary * 0.025, 2);

        // 3. PAYE Tax Calculation (Progressive Brackets)
        $annualGross = $grossSalary * 12;
        
        // Consolidated Relief Allowance (CRA): Max(200,000, 1% of gross) + 20% of gross
        $annualCRA = max(200000, $annualGross * 0.01) + ($annualGross * 0.20);
        $monthlyCRA = round($annualCRA / 12, 2);

        // Taxable income
        $monthlyTaxable = $grossSalary - $pension - $nhf - $monthlyCRA;
        $annualTaxable = max(0.00, $monthlyTaxable * 12);

        $annualPaye = 0.00;
        $remaining = $annualTaxable;

        // progressive PIT slabs:
        // - First 300,000 @ 7%
        if ($remaining > 300000) {
            $annualPaye += 300000 * 0.07;
            $remaining -= 300000;
        } else {
            $annualPaye += $remaining * 0.07;
            $remaining = 0;
        }

        // - Next 300,000 @ 11%
        if ($remaining > 300000) {
            $annualPaye += 300000 * 0.11;
            $remaining -= 300000;
        } else {
            $annualPaye += $remaining * 0.11;
            $remaining = 0;
        }

        // - Next 500,000 @ 15%
        if ($remaining > 500000) {
            $annualPaye += 500000 * 0.15;
            $remaining -= 500000;
        } else {
            $annualPaye += $remaining * 0.15;
            $remaining = 0;
        }

        // - Next 500,000 @ 19%
        if ($remaining > 500000) {
            $annualPaye += 500000 * 0.19;
            $remaining -= 500000;
        } else {
            $annualPaye += $remaining * 0.19;
            $remaining = 0;
        }

        // - Next 1,600,000 @ 21%
        if ($remaining > 1600000) {
            $annualPaye += 1600000 * 0.21;
            $remaining -= 1600000;
        } else {
            $annualPaye += $remaining * 0.21;
            $remaining = 0;
        }

        // - Above 3,200,000 @ 24%
        if ($remaining > 0) {
            $annualPaye += $remaining * 0.24;
        }

        $monthlyPaye = round($annualPaye / 12, 2);

        // Minimum Tax: 1% of Monthly Gross
        $minimumTax = round($grossSalary * 0.01, 2);
        if ($monthlyPaye < $minimumTax) {
            $monthlyPaye = $minimumTax;
        }

        // Deductions
        $totalDeductions = round($pension + $nhf + $monthlyPaye, 2);

        // Net Salary
        $netSalary = round($grossSalary - $totalDeductions, 2);

        return [
            'basic_salary' => round($basicSalary, 2),
            'allowances' => round($allowances, 2),
            'gross_salary' => round($grossSalary, 2),
            'pension_deduction' => $pension,
            'nhf_deduction' => $nhf,
            'paye_tax' => $monthlyPaye,
            'net_salary' => $netSalary,
        ];
    }

    /**
     * Initialize a draft payroll run for a target period.
     */
    public function initializePayrollRun(string $period, int $processedById): PayrollRun
    {
        return DB::transaction(function () use ($period, $processedById) {
            // Remove existing draft run for the same period if any to avoid duplication
            PayrollRun::where('period', $period)->where('status', 'Draft')->delete();

            $run = PayrollRun::create([
                'period' => $period,
                'status' => 'Draft',
                'processed_by_id' => $processedById,
            ]);

            // Fetch active employees who have active contracts
            $employees = User::where('is_active', true)
                ->whereHas('contracts', function ($q) {
                    $q->where('status', 'Active');
                })->get();

            $totalGross = 0.00;
            $totalDeductions = 0.00;
            $totalNet = 0.00;

            foreach ($employees as $employee) {
                $contract = $employee->contracts()->where('status', 'Active')->first();
                $basicSalary = $contract ? $contract->basic_salary : 0.00;

                $calculations = $this->calculateGrossToNet($basicSalary, 0.00);

                PayrollEntry::create(array_merge([
                    'payroll_run_id' => $run->id,
                    'user_id' => $employee->id,
                ], $calculations));

                $totalGross += $calculations['gross_salary'];
                $totalDeductions += ($calculations['pension_deduction'] + $calculations['nhf_deduction'] + $calculations['paye_tax']);
                $totalNet += $calculations['net_salary'];
            }

            $run->update([
                'total_gross' => $totalGross,
                'total_deductions' => $totalDeductions,
                'total_net' => $totalNet,
            ]);

            return $run;
        });
    }

    /**
     * Update allowance for a specific payroll entry.
     */
    public function updateEntryAllowance(PayrollEntry $entry, float $allowances): PayrollEntry
    {
        return DB::transaction(function () use ($entry, $allowances) {
            $calculations = $this->calculateGrossToNet($entry->basic_salary, $allowances);
            $entry->update($calculations);

            $run = $entry->payrollRun;
            $this->recalculateRunTotals($run);

            return $entry;
        });
    }

    /**
     * Recalculate run totals based on entry lines.
     */
    public function recalculateRunTotals(PayrollRun $run): void
    {
        $totals = $run->entries()->selectRaw('
            SUM(gross_salary) as total_gross,
            SUM(pension_deduction + nhf_deduction + paye_tax) as total_deductions,
            SUM(net_salary) as total_net
        ')->first();

        $run->update([
            'total_gross' => $totals->total_gross ?? 0.00,
            'total_deductions' => $totals->total_deductions ?? 0.00,
            'total_net' => $totals->total_net ?? 0.00,
        ]);
    }

    /**
     * Approve the payroll run, seal it, and automatically post to General Ledger.
     */
    public function approvePayrollRun(PayrollRun $run): PayrollRun
    {
        return DB::transaction(function () use ($run) {
            if ($run->status !== 'Draft') {
                throw new \Exception("Only Draft payroll runs can be processed.");
            }

            // Recalculate to be sure
            $this->recalculateRunTotals($run);

            // Fetch aggregate totals of deductions and earnings
            $summary = $run->entries()->selectRaw('
                SUM(gross_salary) as gross,
                SUM(pension_deduction) as pension,
                SUM(nhf_deduction) as nhf,
                SUM(paye_tax) as paye,
                SUM(net_salary) as net
            ')->first();

            $run->update([
                'status' => 'Approved',
                'processed_at' => now(),
            ]);

            // GL Double-Entry Posting:
            // 1. Debit Salary Expense: Total Gross
            GeneralLedgerEntry::create([
                'posting_date' => Carbon::now()->toDateString(),
                'reference' => 'PAYROLL-RUN-' . $run->id,
                'account_name' => 'Salary Expense',
                'account_type' => 'Expense',
                'debit' => $summary->gross ?? 0.00,
                'credit' => 0.00,
                'description' => 'Gross payroll salary expense for period ' . $run->period,
            ]);

            // 2. Credit PAYE Tax Payable
            if (($summary->paye ?? 0) > 0) {
                GeneralLedgerEntry::create([
                    'posting_date' => Carbon::now()->toDateString(),
                    'reference' => 'PAYROLL-RUN-' . $run->id,
                    'account_name' => 'PAYE Tax Payable',
                    'account_type' => 'Liability',
                    'debit' => 0.00,
                    'credit' => $summary->paye,
                    'description' => 'PAYE statutory tax deductions for period ' . $run->period,
                ]);
            }

            // 3. Credit Pension Payable
            if (($summary->pension ?? 0) > 0) {
                GeneralLedgerEntry::create([
                    'posting_date' => Carbon::now()->toDateString(),
                    'reference' => 'PAYROLL-RUN-' . $run->id,
                    'account_name' => 'Pension Payable',
                    'account_type' => 'Liability',
                    'debit' => 0.00,
                    'credit' => $summary->pension,
                    'description' => 'Statutory employee pension contributions for period ' . $run->period,
                ]);
            }

            // 4. Credit NHF Payable
            if (($summary->nhf ?? 0) > 0) {
                GeneralLedgerEntry::create([
                    'posting_date' => Carbon::now()->toDateString(),
                    'reference' => 'PAYROLL-RUN-' . $run->id,
                    'account_name' => 'NHF Payable',
                    'account_type' => 'Liability',
                    'debit' => 0.00,
                    'credit' => $summary->nhf,
                    'description' => 'Statutory National Housing Fund deductions for period ' . $run->period,
                ]);
            }

            // 5. Credit Salaries Payable (Net Salary)
            GeneralLedgerEntry::create([
                'posting_date' => Carbon::now()->toDateString(),
                'reference' => 'PAYROLL-RUN-' . $run->id,
                'account_name' => 'Salaries Payable',
                'account_type' => 'Liability',
                'debit' => 0.00,
                'credit' => $summary->net ?? 0.00,
                'description' => 'Net salaries payable to employees for period ' . $run->period,
            ]);

            return $run;
        });
    }
}
