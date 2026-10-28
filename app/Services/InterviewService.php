<?php

namespace App\Services;

use App\Models\Interview;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Mail;
use App\Mail\InterviewScheduledMail;
use App\Mail\InterviewerAssignedMail;

class InterviewService
{
    public function scheduleInterview(JobApplication $application, array $data): Interview
    {
        // Update application status to Interviewing
        if ($application->status !== 'Interviewing') {
            $application->update(['status' => 'Interviewing']);
        }

        $data['job_application_id'] = $application->id;
        $interviewerIds = $data['interviewer_ids'];
        unset($data['interviewer_ids']);
        
        // Handle assessment file upload
        if (isset($data['assessment_file']) && $data['assessment_file'] instanceof \Illuminate\Http\UploadedFile) {
            $file = $data['assessment_file'];
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assessments'), $filename);
            $data['assessment_file'] = $filename;
        }
        
        $interview = Interview::create($data);
        $interview->interviewers()->sync($interviewerIds);

        // Send Interview Scheduled Email to Applicant
        Mail::to($application->email)->send(new InterviewScheduledMail($interview));

        // Send Assignment Emails to Interviewers
        foreach ($interview->interviewers as $interviewer) {
            Mail::to($interviewer->email)->send(new InterviewerAssignedMail($interview, $interviewer));
        }

        return $interview;
    }

    public function updateInterview(Interview $interview, array $data): bool
    {
        return $interview->update($data);
    }

    public function cancelInterview(Interview $interview): bool
    {
        return $interview->update(['status' => 'Cancelled']);
    }
}
