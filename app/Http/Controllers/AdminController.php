<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\JobPosting;
use App\Models\JobApplication;
use App\Models\Ticket;
use App\Models\Customer;
use App\Models\LeaveRequest;
use App\Models\PayrollRun;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        // 1. Workforce & Organization
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = $totalUsers - $activeUsers;
        $totalDepartments = Department::count();
        $departments = Department::withCount('users')->orderByDesc('users_count')->take(5)->get();

        // 2. Asset Infrastructure
        $totalAssets = Asset::count();
        $activeAssets = Asset::where('status', 'Active')->count();
        $maintenanceAssets = Asset::where('status', 'Maintenance')->count();
        $totalAssetValue = Asset::sum('purchase_cost') ?: 0;
        $recentAssets = Asset::with(['category', 'location', 'custodian'])->latest()->take(5)->get();
        $assetCategories = AssetCategory::withCount('assets')->get();

        // 3. Recruitment Pipeline
        $openJobsCount = JobPosting::whereIn('status', ['Published', 'Open', 'active'])->count();
        if ($openJobsCount === 0) {
            $openJobsCount = JobPosting::count();
        }
        $totalApplications = JobApplication::count();
        $pendingApplications = JobApplication::whereIn('status', ['New', 'In Review'])->count();
        $interviewingApplications = JobApplication::where('status', 'Interviewing')->count();
        $offeredApplications = JobApplication::where('status', 'Offered')->count();
        $recentApplications = JobApplication::with('jobPosting')->latest()->take(5)->get();

        // 4. Helpdesk & Operations
        $totalTickets = Ticket::count();
        $openTickets = Ticket::whereIn('status', ['Open', 'In Progress'])->count();
        $resolvedTickets = Ticket::where('status', 'Resolved')->count();
        $urgentTickets = Ticket::where('priority', 'Urgent')->count();
        $recentTickets = Ticket::with(['customer', 'assignedTo'])->latest()->take(5)->get();

        // 5. Leave & Attendance
        $pendingLeaves = LeaveRequest::where('status', 'Pending')->count();
        $recentLeaves = LeaveRequest::with('user')->latest()->take(5)->get();

        // 6. Payroll Summary
        $latestPayroll = PayrollRun::latest()->first();

        // 7. System Currency
        $currencySymbol = Setting::get('currency_symbol', '₦');

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'totalDepartments',
            'departments',
            'totalAssets',
            'activeAssets',
            'maintenanceAssets',
            'totalAssetValue',
            'recentAssets',
            'assetCategories',
            'openJobsCount',
            'totalApplications',
            'pendingApplications',
            'interviewingApplications',
            'offeredApplications',
            'recentApplications',
            'totalTickets',
            'openTickets',
            'resolvedTickets',
            'urgentTickets',
            'recentTickets',
            'pendingLeaves',
            'recentLeaves',
            'latestPayroll',
            'currencySymbol'
        ));
    }
}
