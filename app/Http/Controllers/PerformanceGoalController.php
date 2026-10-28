<?php

namespace App\Http\Controllers;

use App\Models\PerformanceGoal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerformanceGoalController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $goals = PerformanceGoal::with('user.designation', 'user.department', 'creator')
                ->orderBy('created_at', 'desc')
                ->get();
            $staff = User::where('is_active', true)->get();
            return view('admin.hr.performance.goals', compact('goals', 'staff'));
        }

        $goals = PerformanceGoal::with('creator')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.hr.performance.goals', compact('goals'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('admin');

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_value' => 'required|integer|min:1',
            'unit' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'weight' => 'required|integer|min:1|max:10',
            'user_id' => $isAdmin ? 'required|exists:users,id' : 'nullable',
        ]);

        $targetUserId = $isAdmin ? $request->user_id : $user->id;

        PerformanceGoal::create([
            'user_id' => $targetUserId,
            'title' => $request->title,
            'description' => $request->description,
            'target_value' => $request->target_value,
            'current_value' => 0,
            'unit' => $request->unit,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'In Progress',
            'weight' => $request->weight,
            'created_by_id' => $user->id,
        ]);

        return redirect()->route('admin.hr.goals.index')->with('success', 'Performance goal (OKR) set successfully.');
    }

    public function updateProgress(Request $request, PerformanceGoal $goal)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin') && $goal->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'current_value' => 'required|integer|min:0',
        ]);

        $status = 'In Progress';
        if ($request->current_value >= $goal->target_value) {
            $status = 'Achieved';
        } elseif (now()->gt($goal->end_date) && $request->current_value < $goal->target_value) {
            $status = 'Missed';
        }

        $goal->update([
            'current_value' => $request->current_value,
            'status' => $status,
        ]);

        return redirect()->route('admin.hr.goals.index')->with('success', 'Goal progress updated successfully.');
    }
}
