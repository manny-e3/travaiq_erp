<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Models\JobApplication;
use App\Models\User;
use App\Services\JobApplicationService;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    protected $applicationService;

    public function __construct(JobApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    // For external API submissions
    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'job_posting_id' => 'required|exists:job_postings,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'resume_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'cover_letter' => 'nullable|string',
        ]);

        $this->applicationService->submitApplication($validated, $request->file('resume_file'));

        return response()->json(['message' => 'Application submitted successfully.'], 201);
    }

    // For internal HR manual submissions
    public function store(Request $request, JobPosting $jobPosting)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'resume_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'cover_letter' => 'nullable|string',
        ]);

        $validated['job_posting_id'] = $jobPosting->id;

        $this->applicationService->submitApplication($validated, $request->file('resume_file'));

        return back()->with('success', 'Applicant added successfully.');
    }

    public function show(JobApplication $jobApplication)
    {
        $jobApplication->load(['jobPosting', 'interviews.interviewers', 'offer']);
        $users = User::where('is_active', true)->get(); // For interviewers
        return view('admin.hr.recruitment.applications.show', compact('jobApplication', 'users'));
    }

    public function updateStatus(Request $request, JobApplication $jobApplication)
    {
        $validated = $request->validate([
            'status' => 'required|in:New,In Review,Interviewing,Offered,Hired,Rejected'
        ]);

        $this->applicationService->updateStatus($jobApplication, $validated['status']);

        return back()->with('success', 'Application status updated.');
    }

    public function downloadResume(JobApplication $jobApplication)
    {
        if (!$jobApplication->resume_path || !\Illuminate\Support\Facades\Storage::disk('public')->exists($jobApplication->resume_path)) {
            abort(404, 'Resume not found');
        }
        return \Illuminate\Support\Facades\Storage::disk('public')->download($jobApplication->resume_path);
    }

    public function downloadOffer(JobApplication $jobApplication)
    {
        if (!$jobApplication->offer || !\Illuminate\Support\Facades\Storage::disk('public')->exists($jobApplication->offer->document_path)) {
            abort(404, 'Offer not found');
        }
        return \Illuminate\Support\Facades\Storage::disk('public')->download($jobApplication->offer->document_path);
    }

    public function screen(JobApplication $jobApplication)
    {
        \App\Jobs\ScreenApplicationWithAI::dispatch($jobApplication);
        return back()->with('success', 'AI Screening has been queued. Results will appear shortly.');
    }

    public function bulkReject(Request $request)
    {
        $validated = $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:job_applications,id'
        ]);

        $applications = JobApplication::whereIn('id', $validated['application_ids'])->get();
        $count = 0;
        foreach ($applications as $app) {
            // Only reject if not already rejected
            if ($app->status !== 'Rejected') {
                $this->applicationService->updateStatus($app, 'Rejected');
                $count++;
            }
        }

        return back()->with('success', "$count applications manually rejected and emails sent.");
    }

    public function onboard(JobApplication $jobApplication)
    {
        $user = $this->applicationService->onboardAsEmployee($jobApplication);

        return redirect()->route('admin.hr.employees.show', ['user' => $user->id])
            ->with('success', 'Candidate successfully onboarded as employee!');
    }

    public function destroy(JobApplication $jobApplication)
    {
        $this->applicationService->deleteApplication($jobApplication);
        return redirect()->route('admin.hr.recruitment.jobs.show', $jobApplication->job_posting_id)
            ->with('success', 'Application deleted.');
    }
}
