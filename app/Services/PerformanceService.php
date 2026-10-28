<?php

namespace App\Services;

use App\Models\Appraisal;
use App\Models\SalaryReview;
use App\Models\EmployeeContract;
use App\Models\EmploymentHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PerformanceService
{
    /**
     * Submit Self-Appraisal.
     */
    public function submitSelfAppraisal(Appraisal $appraisal, float $rating, string $comment): void
    {
        if ($appraisal->status !== 'Pending Self') {
            throw new \InvalidArgumentException('This appraisal is not in the self-appraisal stage.');
        }

        if ($rating < 1.0 || $rating > 5.0) {
            throw new \InvalidArgumentException('Appraisal rating must be between 1.0 and 5.0.');
        }

        $appraisal->update([
            'self_rating' => $rating,
            'self_comment' => $comment,
            'status' => 'Pending Manager',
        ]);
    }

    /**
     * Submit Manager Appraisal.
     */
    public function submitManagerAppraisal(Appraisal $appraisal, float $rating, string $comment): void
    {
        if ($appraisal->status !== 'Pending Manager') {
            throw new \InvalidArgumentException('This appraisal is not in the manager-appraisal stage.');
        }

        if ($rating < 1.0 || $rating > 5.0) {
            throw new \InvalidArgumentException('Appraisal rating must be between 1.0 and 5.0.');
        }

        $appraisal->update([
            'manager_rating' => $rating,
            'manager_comment' => $comment,
            'status' => 'Pending Calibration',
        ]);
    }

    /**
     * Calibrate rating by HR Admin.
     */
    public function calibrateAppraisal(Appraisal $appraisal, float $rating, string $comment): void
    {
        if ($appraisal->status !== 'Pending Calibration') {
            throw new \InvalidArgumentException('This appraisal is not ready for calibration.');
        }

        if ($rating < 1.0 || $rating > 5.0) {
            throw new \InvalidArgumentException('Appraisal rating must be between 1.0 and 5.0.');
        }

        $appraisal->update([
            'calibrated_rating' => $rating,
            'calibrated_comment' => $comment,
            'status' => 'Completed',
        ]);
    }

    /**
     * Propose Salary & Promotion Review.
     */
    public function proposeSalaryReview(Appraisal $appraisal, array $data): SalaryReview
    {
        if ($appraisal->status !== 'Completed') {
            throw new \InvalidArgumentException('Appraisal must be completed/calibrated before proposing salary review.');
        }

        $activeContract = EmployeeContract::where('user_id', $appraisal->user_id)
            ->where('status', 'active')
            ->first();

        if (!$activeContract) {
            throw new \InvalidArgumentException('The employee does not have an active employment contract.');
        }

        $user = User::findOrFail($appraisal->user_id);

        $exists = SalaryReview::where('appraisal_id', $appraisal->id)->where('status', 'Pending')->exists();
        if ($exists) {
            throw new \InvalidArgumentException('A pending salary review has already been proposed for this appraisal.');
        }

        return SalaryReview::create([
            'appraisal_id' => $appraisal->id,
            'user_id' => $appraisal->user_id,
            'current_salary' => $activeContract->basic_salary,
            'proposed_salary' => $data['proposed_salary'],
            'current_designation_id' => $user->designation_id,
            'proposed_designation_id' => $data['proposed_designation_id'] ?? $user->designation_id,
            'current_job_title' => $user->job_title,
            'proposed_job_title' => $data['proposed_job_title'] ?? $user->job_title,
            'effective_date' => $data['effective_date'] ?? now()->format('Y-m-d'),
            'status' => 'Pending',
            'notes' => $data['notes'] ?? null,
        ]);
    }

    /**
     * Approve Salary & Promotion Review.
     */
    public function approveSalaryReview(SalaryReview $review, User $approver): void
    {
        DB::transaction(function () use ($review, $approver) {
            $review->refresh();
            if ($review->status !== 'Pending') {
                throw new \InvalidArgumentException('This salary review has already been processed.');
            }

            // Update active contract salary
            $activeContract = EmployeeContract::where('user_id', $review->user_id)
                ->where('status', 'active')
                ->first();

            if ($activeContract) {
                $activeContract->update([
                    'basic_salary' => $review->proposed_salary
                ]);
            }

            // Update User details (Promotion)
            $user = User::findOrFail($review->user_id);
            if ($review->proposed_designation_id) {
                $user->designation_id = $review->proposed_designation_id;
            }
            if ($review->proposed_job_title) {
                $user->job_title = $review->proposed_job_title;
            }
            $user->save();

            // Insert into Employment History
            EmploymentHistory::create([
                'user_id' => $review->user_id,
                'job_title' => $review->proposed_job_title ?? $user->job_title,
                'designation_id' => $review->proposed_designation_id ?? $user->designation_id,
                'department_id' => $user->department_id,
                'start_date' => $review->effective_date ?? now(),
                'basic_salary' => $review->proposed_salary,
                'change_reason' => 'Salary Increase & Promotion based on Performance Appraisal Cycle',
                'notes' => $review->notes,
            ]);

            // Mark review as approved
            $review->update([
                'status' => 'Approved',
                'approved_by_id' => $approver->id,
            ]);
        });
    }

    /**
     * Reject Salary & Promotion Review.
     */
    public function rejectSalaryReview(SalaryReview $review, User $approver): void
    {
        $review->refresh();
        if ($review->status !== 'Pending') {
            throw new \InvalidArgumentException('This salary review has already been processed.');
        }

        $review->update([
            'status' => 'Rejected',
            'approved_by_id' => $approver->id,
        ]);
    }
}
