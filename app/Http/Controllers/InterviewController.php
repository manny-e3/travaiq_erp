<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\Interview;
use App\Services\InterviewService;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    protected $interviewService;

    public function __construct(InterviewService $interviewService)
    {
        $this->interviewService = $interviewService;
    }

    public function store(Request $request, JobApplication $jobApplication)
    {
        $validated = $request->validate([
            'interviewer_ids' => 'required|array|min:1',
            'interviewer_ids.*' => 'exists:users,id',
            'interview_stage' => 'required|string',
            'scheduled_at' => 'required|date',
            'location_or_link' => 'required_unless:interview_stage,Assessment,Phone Screen',
            'assessment_details' => 'nullable|string',
            'assessment_file' => 'nullable|file|mimes:pdf,doc,docx,zip|max:5120', // 5MB max
            'notes' => 'nullable|string',
        ]);

        $this->interviewService->scheduleInterview($jobApplication, $validated);

        return back()->with('success', 'Interview scheduled successfully.');
    }

    public function update(Request $request, Interview $interview)
    {
        $validated = $request->validate([
            'status' => 'required|in:Scheduled,Completed,Cancelled'
        ]);

        $this->interviewService->updateInterview($interview, $validated);

        return back()->with('success', 'Interview status updated.');
    }
}
