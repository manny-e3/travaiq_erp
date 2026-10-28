<?php

namespace App\Http\Controllers;

use App\Models\PayrollRun;
use App\Models\PayrollEntry;
use App\Models\GeneralLedgerEntry;
use App\Services\PayrollService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function index()
    {
        $payrollRuns = PayrollRun::with('processedBy')->orderBy('period', 'desc')->get();
        
        $stats = [
            'total_runs' => $payrollRuns->count(),
            'total_approved' => $payrollRuns->where('status', 'Approved')->count(),
            'total_gross_processed' => $payrollRuns->where('status', 'Approved')->sum('total_gross'),
            'total_net_paid' => $payrollRuns->where('status', 'Approved')->sum('total_net'),
        ];

        return view('admin.hr.payroll.index', compact('payrollRuns', 'stats'));
    }

    public function create()
    {
        return view('admin.hr.payroll.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030',
        ]);

        $period = $request->year . '-' . str_pad($request->month, 2, '0', STR_PAD_LEFT);

        // Check if an Approved payroll run already exists for this period
        $existsApproved = PayrollRun::where('period', $period)->where('status', 'Approved')->exists();
        if ($existsApproved) {
            return redirect()->back()->withErrors(['period' => 'An approved payroll run already exists for this period.']);
        }

        $run = $this->payrollService->initializePayrollRun($period, Auth::id());

        return redirect()->route('admin.hr.payroll.show', $run)
            ->with('success', 'Payroll run initialized successfully as Draft.');
    }

    public function show(PayrollRun $payroll)
    {
        $payroll->load('entries.user.designation', 'entries.user.department');
        return view('admin.hr.payroll.show', compact('payroll'));
    }

    public function updateAllowance(Request $request, PayrollEntry $payrollEntry)
    {
        $request->validate([
            'allowances' => 'required|numeric|min:0',
        ]);

        if ($payrollEntry->payrollRun->status !== 'Draft') {
            return redirect()->back()->with('error', 'Cannot edit allowances on an approved payroll run.');
        }

        $this->payrollService->updateEntryAllowance($payrollEntry, $request->allowances);

        return redirect()->back()->with('success', 'Allowances updated and Net salary re-calculated.');
    }

    public function approve(PayrollRun $payrollRun)
    {
        try {
            $this->payrollService->approvePayrollRun($payrollRun);
            return redirect()->route('admin.hr.payroll.show', $payrollRun)
                ->with('success', 'Payroll run approved successfully. Transactions posted to General Ledger.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error processing payroll: ' . $e->getMessage());
        }
    }

    public function downloadPayslip(PayrollEntry $payrollEntry)
    {
        $payrollEntry->load('user.designation', 'user.department', 'payrollRun');
        
        $pdf = Pdf::loadView('admin.hr.payroll.payslip_pdf', [
            'entry' => $payrollEntry,
        ]);

        $filename = 'payslip_' . str_replace(' ', '_', $payrollEntry->user->name) . '_' . $payrollEntry->payrollRun->period . '.pdf';
        
        return $pdf->download($filename);
    }

    public function glPostings()
    {
        $entries = GeneralLedgerEntry::orderBy('posting_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $stats = [
            'total_debits' => $entries->sum('debit'),
            'total_credits' => $entries->sum('credit'),
        ];

        return view('admin.hr.payroll.gl_entries', compact('entries', 'stats'));
    }
}
