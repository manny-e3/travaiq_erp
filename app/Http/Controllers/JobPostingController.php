<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Models\Department;
use App\Services\JobPostingService;
use Illuminate\Http\Request;

class JobPostingController extends Controller
{
    protected $jobPostingService;

    public function __construct(JobPostingService $jobPostingService)
    {
        $this->jobPostingService = $jobPostingService;
    }

    public function index()
    {
        $postings = $this->jobPostingService->getAll();
        return view('admin.hr.recruitment.jobs.index', compact('postings'));
    }

    // For external API listing
    public function apiIndex()
    {
        $postings = $this->jobPostingService->getActive();
        return response()->json([
            'success' => true,
            'data' => $postings
        ]);
    }

    public function create()
    {
        $departments = Department::all();
        $designations = \App\Models\Designation::all();
        return view('admin.hr.recruitment.jobs.create', compact('departments', 'designations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'designation_id' => 'required|exists:designations,id',
            'title' => 'nullable|string|max:255',
            'description' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'location' => 'required|string|max:255',
            'employment_type' => 'required|string|max:255',
            'status' => 'required|in:Draft,Published,Closed',
            'closing_date' => 'nullable|date',
            'ai_pass_score' => 'nullable|integer|min:0|max:100',
        ]);

        $validated['title'] = \App\Models\Designation::find($validated['designation_id'])?->name;

        if (!isset($validated['ai_pass_score'])) {
            $validated['ai_pass_score'] = 40;
        }

        $this->jobPostingService->createPosting($validated, auth()->id());

        return redirect()->route('admin.hr.recruitment.jobs.index')->with('success', 'Job posting created successfully.');
    }

    public function show(JobPosting $job)
    {
        $job->load('department', 'applications');
        $jobPosting = $job; // alias for the view
        return view('admin.hr.recruitment.jobs.show', compact('jobPosting'));
    }

    public function edit(JobPosting $job)
    {
        $departments = Department::all();
        $designations = \App\Models\Designation::all();
        $jobPosting = $job; // alias for the view
        return view('admin.hr.recruitment.jobs.edit', compact('jobPosting', 'departments', 'designations'));
    }

    public function update(Request $request, JobPosting $job)
    {
        $validated = $request->validate([
            'designation_id' => 'required|exists:designations,id',
            'title' => 'nullable|string|max:255',
            'description' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'location' => 'required|string|max:255',
            'employment_type' => 'required|string|max:255',
            'status' => 'required|in:Draft,Published,Closed',
            'closing_date' => 'nullable|date',
            'ai_pass_score' => 'nullable|integer|min:0|max:100',
        ]);

        $validated['title'] = \App\Models\Designation::find($validated['designation_id'])?->name;

        if (!isset($validated['ai_pass_score'])) {
            $validated['ai_pass_score'] = 40;
        }

        $this->jobPostingService->updatePosting($job, $validated);

        return redirect()->route('admin.hr.recruitment.jobs.index')->with('success', 'Job posting updated successfully.');
    }

    public function destroy(JobPosting $job)
    {
        $this->jobPostingService->deletePosting($job);
        return redirect()->route('admin.hr.recruitment.jobs.index')->with('success', 'Job posting deleted successfully.');
    }
}
