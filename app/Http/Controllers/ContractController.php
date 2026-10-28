<?php

namespace App\Http\Controllers;

use App\Models\EmployeeContract;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeContract::with(['user.department']);

        // Search by user fields
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        // Filter by contract type
        if ($request->filled('contract_type')) {
            $query->where('contract_type', $request->contract_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Check if filtering for expiring contracts (< 30 days)
        if ($request->filled('expiring')) {
            $query->where('status', 'Active')
                  ->whereNotNull('end_date')
                  ->where('end_date', '<=', Carbon::now()->addDays(30))
                  ->where('end_date', '>=', Carbon::now());
        }

        $contracts = $query->latest('start_date')->paginate(15)->withQueryString();

        // Calculate statistics for contract overview
        $totalContracts = EmployeeContract::count();
        $activeContracts = EmployeeContract::where('status', 'Active')->count();
        $expiringSoon = EmployeeContract::where('status', 'Active')
            ->whereNotNull('end_date')
            ->where('end_date', '<=', Carbon::now()->addDays(30))
            ->where('end_date', '>=', Carbon::now())
            ->count();
        
        $terminatedContracts = EmployeeContract::where('status', 'Terminated')->count();

        return view('admin.hr.contracts.index', compact(
            'contracts', 
            'totalContracts', 
            'activeContracts', 
            'expiringSoon', 
            'terminatedContracts'
        ));
    }
}
