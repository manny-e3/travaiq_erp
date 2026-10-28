<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\User;
use App\Services\LeaveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    protected $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $year = now()->year;

        // Auto-initialize current user's balance
        $this->leaveService->initializeBalances($user, $year);

        if ($user->hasRole('admin')) {
            // Admin View
            $pendingRequests = LeaveRequest::with('user.designation', 'user.department')
                ->where('status', 'Pending')
                ->orderBy('created_at', 'desc')
                ->get();

            $processedRequests = LeaveRequest::with('user.designation', 'user.department', 'approvedBy')
                ->whereIn('status', ['Approved', 'Rejected'])
                ->orderBy('updated_at', 'desc')
                ->get();

            // Auto-initialize balances for all active users so admin table is fully populated
            $activeUsers = User::where('is_active', true)->with('profile', 'department', 'designation')->get();
            foreach ($activeUsers as $activeUser) {
                $this->leaveService->initializeBalances($activeUser, $year);
            }

            $allBalances = LeaveBalance::with('user.designation', 'user.department')
                ->where('year', $year)
                ->orderBy('user_id')
                ->get()
                ->groupBy('user_id');

            return view('admin.hr.leaves.index', compact('pendingRequests', 'processedRequests', 'allBalances', 'activeUsers'));
        }

        // Regular Employee View
        $myBalances = LeaveBalance::where('user_id', $user->id)
            ->where('year', $year)
            ->get();

        $myRequests = LeaveRequest::with('approvedBy')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.hr.leaves.index', compact('myBalances', 'myRequests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|string|in:Annual,Sick,Maternity,Paternity,Unpaid',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $this->leaveService->submitLeaveRequest(Auth::user(), $request->all());
            return redirect()->route('admin.hr.leaves.index')->with('success', 'Leave request submitted successfully and is pending approval.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['leave_error' => $e->getMessage()])->withInput();
        }
    }

    public function approve(LeaveRequest $leave)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        try {
            $this->leaveService->approveLeaveRequest($leave, Auth::user());
            return redirect()->route('admin.hr.leaves.index')->with('success', 'Leave request approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, LeaveRequest $leave)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        try {
            $this->leaveService->rejectLeaveRequest($leave, Auth::user(), $request->rejection_reason);
            return redirect()->route('admin.hr.leaves.index')->with('success', 'Leave request rejected successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function updateBalance(Request $request, LeaveBalance $balance)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'allocated_days' => 'required|integer|min:0',
        ]);

        $balance->allocated_days = $request->allocated_days;
        $balance->remaining_days = $request->allocated_days - $balance->used_days;
        $balance->save();

        return redirect()->route('admin.hr.leaves.index')->with('success', 'Leave balance updated successfully.');
    }
}
