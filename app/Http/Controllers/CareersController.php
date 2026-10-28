<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Services\JobPostingService;
use App\Services\JobApplicationService;
use Illuminate\Http\Request;

class CareersController extends Controller
{
    protected $jobPostingService;
    protected $jobApplicationService;

    public function __construct(JobPostingService $jobPostingService, JobApplicationService $jobApplicationService)
    {
        $this->jobPostingService = $jobPostingService;
        $this->jobApplicationService = $jobApplicationService;
    }

    /**
     * Display a listing of open job positions.
     */
    public function index(Request $request)
    {
        $query = JobPosting::with(['department', 'designation'])->where('status', 'Published');

        // Search by title or description
        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(function($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            });
        }

        // Filter by location
        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        // Filter by employment type
        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $postings = $query->latest()->get();

        // Get unique locations and departments for filtering
        $locations = JobPosting::where('status', 'Published')->distinct()->pluck('location')->filter()->toArray();
        $departments = \App\Models\Department::all();

        return view('portal.careers.index', compact('postings', 'locations', 'departments'));
    }

    /**
     * Show the detailed description and application form for a job posting.
     */
    public function show($id)
    {
        $job = JobPosting::with(['department', 'designation'])->findOrFail($id);

        if ($job->status !== 'Published') {
            abort(404, 'This job posting is not currently accepting applications.');
        }

        $jobPosting = $job; // Alias for consistency
        return view('portal.careers.show', compact('jobPosting'));
    }

    /**
     * Process an application submission for a specific job.
     */
    public function apply(Request $request, $id)
    {
        $job = JobPosting::findOrFail($id);

        if ($job->status !== 'Published') {
            abort(404, 'This job posting is not currently accepting applications.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'cover_letter' => 'nullable|string',
            'resume_file' => 'required|file|mimes:pdf,doc,docx|max:10240', // Max 10MB
        ]);

        $applicationData = [
            'job_posting_id' => $job->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'cover_letter' => $validated['cover_letter'] ?? null,
        ];

        $this->jobApplicationService->submitApplication($applicationData, $request->file('resume_file'));

        return redirect()->back()->with('success', 'Your application for ' . $job->title . ' has been submitted successfully! We have sent a confirmation email to ' . $validated['email'] . '.');
    }
}
