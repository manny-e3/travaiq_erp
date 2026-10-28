<?php

namespace App\Http\Controllers;

use App\Models\AppraisalCycle;
use App\Models\Appraisal;
use App\Models\PeerFeedback;
use App\Models\User;
use App\Models\Designation;
use App\Services\PerformanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppraisalController extends Controller
{
    protected $performanceService;

    public function __construct(PerformanceService $performanceService)
    {
        $this->performanceService = $performanceService;
    }

    public function index()
    {
        $user = Auth::user();
        $cycles = AppraisalCycle::orderBy('created_at', 'desc')->get();
        $designations = Designation::all();

        if ($user->hasRole('admin')) {
            $appraisals = Appraisal::with('cycle', 'user.designation', 'user.department', 'manager', 'peerFeedbacks.reviewer')
                ->orderBy('created_at', 'desc')
                ->get();
            $staff = User::where('is_active', true)->get();
            return view('admin.hr.performance.appraisals', compact('cycles', 'appraisals', 'staff', 'designations'));
        }

        // Employee view
        $mySelfAppraisals = Appraisal::with('cycle', 'manager')
            ->where('user_id', $user->id)
            ->get();

        $myManagerReviews = Appraisal::with('cycle', 'user')
            ->where('manager_id', $user->id)
            ->get();

        $myPeerFeedbackRequests = PeerFeedback::with('appraisal.user', 'appraisal.cycle')
            ->where('reviewer_id', $user->id)
            ->get();

        return view('admin.hr.performance.appraisals', compact('cycles', 'mySelfAppraisals', 'myManagerReviews', 'myPeerFeedbackRequests'));
    }

    public function storeCycle(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string|in:Draft,Active,Completed',
        ]);

        AppraisalCycle::create($request->all());

        return redirect()->route('admin.hr.appraisals.index')->with('success', 'Appraisal cycle created successfully.');
    }

    public function invite(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'appraisal_cycle_id' => 'required|exists:appraisal_cycles,id',
            'user_id' => 'required|exists:users,id',
            'manager_id' => 'required|exists:users,id|different:user_id',
        ]);

        try {
            Appraisal::create([
                'appraisal_cycle_id' => $request->appraisal_cycle_id,
                'user_id' => $request->user_id,
                'manager_id' => $request->manager_id,
                'status' => 'Pending Self',
            ]);
            return redirect()->route('admin.hr.appraisals.index')->with('success', 'Employee successfully invited to appraisal cycle.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Employee has already been invited to this appraisal cycle.');
        }
    }

    public function submitSelf(Request $request, Appraisal $appraisal)
    {
        if (Auth::id() !== $appraisal->user_id) {
            abort(403);
        }

        $request->validate([
            'self_rating' => 'required|numeric|min:1|max:5',
            'self_comment' => 'required|string|max:1000',
        ]);

        try {
            $this->performanceService->submitSelfAppraisal($appraisal, $request->self_rating, $request->self_comment);
            return redirect()->route('admin.hr.appraisals.index')->with('success', 'Self appraisal submitted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function submitManager(Request $request, Appraisal $appraisal)
    {
        if (Auth::id() !== $appraisal->manager_id) {
            abort(403);
        }

        $request->validate([
            'manager_rating' => 'required|numeric|min:1|max:5',
            'manager_comment' => 'required|string|max:1000',
        ]);

        try {
            $this->performanceService->submitManagerAppraisal($appraisal, $request->manager_rating, $request->manager_comment);
            return redirect()->route('admin.hr.appraisals.index')->with('success', 'Manager review submitted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function submitCalibration(Request $request, Appraisal $appraisal)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'calibrated_rating' => 'required|numeric|min:1|max:5',
            'calibrated_comment' => 'required|string|max:1000',
        ]);

        try {
            $this->performanceService->calibrateAppraisal($appraisal, $request->calibrated_rating, $request->calibrated_comment);
            return redirect()->route('admin.hr.appraisals.index')->with('success', 'Appraisal calibrated and finalized successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function requestPeer(Request $request, Appraisal $appraisal)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin') && $appraisal->manager_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'reviewer_id' => 'required|exists:users,id|different:user_id',
            'relationship' => 'required|string|in:Peer,Subordinate,Stakeholder',
        ]);

        PeerFeedback::create([
            'appraisal_id' => $appraisal->id,
            'reviewer_id' => $request->reviewer_id,
            'relationship' => $request->relationship,
            'status' => 'Pending',
        ]);

        return redirect()->route('admin.hr.appraisals.index')->with('success', 'Peer 360° feedback request sent.');
    }

    public function submitPeerFeedback(Request $request, PeerFeedback $feedback)
    {
        if (Auth::id() !== $feedback->reviewer_id) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'feedback' => 'required|string|max:1000',
        ]);

        $feedback->update([
            'rating' => $request->rating,
            'feedback' => $request->feedback,
            'status' => 'Submitted',
        ]);

        return redirect()->route('admin.hr.appraisals.index')->with('success', 'Peer feedback submitted successfully.');
    }
}
