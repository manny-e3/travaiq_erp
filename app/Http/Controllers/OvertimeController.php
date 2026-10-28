<?php

namespace App\Http\Controllers;

use App\Models\OvertimeRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OvertimeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        if ($user->hasRole('admin')) {
            // Admin View
            $pendingOvertime = OvertimeRecord::with('user.designation', 'user.department')
                ->where('status', 'Pending')
                ->orderBy('date', 'desc')
                ->get();

            $processedOvertime = OvertimeRecord::with('user.designation', 'user.department', 'approvedBy')
                ->whereIn('status', ['Approved', 'Rejected'])
                ->orderBy('updated_at', 'desc')
                ->get();

            $totalHoursThisMonth = OvertimeRecord::where('status', 'Approved')
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum('hours');

            return view('admin.hr.leaves.overtime', compact('pendingOvertime', 'processedOvertime', 'totalHoursThisMonth'));
        }

        // Regular Employee View
        $myOvertime = OvertimeRecord::with('approvedBy')
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        $myHoursThisMonth = OvertimeRecord::where('user_id', $user->id)
            ->where('status', 'Approved')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('hours');

        return view('admin.hr.leaves.overtime', compact('myOvertime', 'myHoursThisMonth'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'hours' => 'required|numeric|min:0.5|max:16',
            'rate_multiplier' => 'required|numeric|in:1.5,2.0',
            'description' => 'required|string|max:500',
        ]);

        OvertimeRecord::create([
            'user_id' => Auth::id(),
            'date' => $request->date,
            'hours' => $request->hours,
            'rate_multiplier' => $request->rate_multiplier,
            'description' => $request->description,
            'status' => 'Pending',
        ]);

        return redirect()->route('admin.hr.overtime.index')->with('success', 'Overtime hours logged successfully and pending approval.');
    }

    public function approve(OvertimeRecord $overtime)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        if ($overtime->status !== 'Pending') {
            return redirect()->back()->with('error', 'This overtime record has already been processed.');
        }

        $overtime->update([
            'status' => 'Approved',
            'approved_by_id' => Auth::id(),
        ]);

        return redirect()->route('admin.hr.overtime.index')->with('success', 'Overtime record approved successfully.');
    }

    public function reject(OvertimeRecord $overtime)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        if ($overtime->status !== 'Pending') {
            return redirect()->back()->with('error', 'This overtime record has already been processed.');
        }

        $overtime->update([
            'status' => 'Rejected',
            'approved_by_id' => Auth::id(),
        ]);

        return redirect()->route('admin.hr.overtime.index')->with('success', 'Overtime record rejected successfully.');
    }
}
