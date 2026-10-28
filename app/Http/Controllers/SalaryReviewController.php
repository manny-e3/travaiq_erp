<?php

namespace App\Http\Controllers;

use App\Models\SalaryReview;
use App\Models\Appraisal;
use App\Models\Designation;
use App\Services\PerformanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalaryReviewController extends Controller
{
    protected $performanceService;

    public function __construct(PerformanceService $performanceService)
    {
        $this->performanceService = $performanceService;
    }

    public function index()
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $pendingReviews = SalaryReview::with('user', 'proposedDesignation', 'currentDesignation')
            ->where('status', 'Pending')
            ->get();

        $processedReviews = SalaryReview::with('user', 'proposedDesignation', 'currentDesignation', 'approvedBy')
            ->whereIn('status', ['Approved', 'Rejected'])
            ->get();

        return view('admin.hr.performance.salary_reviews', compact('pendingReviews', 'processedReviews'));
    }

    public function store(Request $request, Appraisal $appraisal)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'proposed_salary' => 'required|numeric|min:1',
            'proposed_designation_id' => 'nullable|exists:designations,id',
            'proposed_job_title' => 'nullable|string|max:255',
            'effective_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        try {
            $this->performanceService->proposeSalaryReview($appraisal, $request->all());
            return redirect()->route('admin.hr.salary-reviews.index')->with('success', 'Salary & Promotion Review proposed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function approve(SalaryReview $review)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        try {
            $this->performanceService->approveSalaryReview($review, Auth::user());
            return redirect()->route('admin.hr.salary-reviews.index')->with('success', 'Salary and Promotion Review approved. Employee files updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject(SalaryReview $review)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        try {
            $this->performanceService->rejectSalaryReview($review, Auth::user());
            return redirect()->route('admin.hr.salary-reviews.index')->with('success', 'Salary and Promotion Review rejected.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
