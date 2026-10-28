<?php

namespace App\Services;

use App\Models\JobPosting;
use Carbon\Carbon;

class JobPostingService
{
    public function getAll()
    {
        return JobPosting::with('department')->latest()->get();
    }

    public function getActive()
    {
        return JobPosting::with('department')->where('status', 'Published')->latest()->get();
    }

    public function createPosting(array $data, int $userId): JobPosting
    {
        $data['created_by'] = $userId;
        
        if (isset($data['closing_date']) && Carbon::parse($data['closing_date'])->isPast()) {
            $data['status'] = 'Closed';
        }

        $posting = JobPosting::create($data);

        if ($posting->status === 'Closed') {
            $this->autoRejectCandidates($posting);
        }

        return $posting;
    }

    public function updatePosting(JobPosting $jobPosting, array $data): bool
    {
        if (isset($data['closing_date']) && Carbon::parse($data['closing_date'])->isPast()) {
            $data['status'] = 'Closed';
        }

        $wasClosed = $jobPosting->status === 'Closed';
        $updated = $jobPosting->update($data);

        if (!$wasClosed && $jobPosting->status === 'Closed') {
            $this->autoRejectCandidates($jobPosting);
        }

        return $updated;
    }

    public function deletePosting(JobPosting $jobPosting): bool
    {
        return $jobPosting->delete();
    }

    public function autoRejectCandidates(JobPosting $jobPosting): void
    {
        $applications = $jobPosting->applications()
            ->whereIn('status', ['New', 'In Review'])
            ->where('ai_score', '<', $jobPosting->ai_pass_score ?? 40)
            ->get();

        $applicationService = app(\App\Services\JobApplicationService::class);
        foreach ($applications as $application) {
            $applicationService->updateStatus($application, 'Rejected');
        }
    }
}
