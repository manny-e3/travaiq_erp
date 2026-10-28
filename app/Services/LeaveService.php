<?php

namespace App\Services;

use App\Models\User;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveService
{
    /**
     * Initialize default leave balances for a user and year, considering gender.
     */
    public function initializeBalances(User $user, int $year): void
    {
        $user->load('profile');

        // Check if balances are already initialized for this year
        $exists = LeaveBalance::where('user_id', $user->id)->where('year', $year)->exists();
        if ($exists) {
            return;
        }

        // Base leave allocations
        $allocations = [
            'Annual' => 20,
            'Sick' => 10,
        ];

        // Gender-specific leave allocations
        $gender = $user->profile ? strtolower($user->profile->gender) : null;
        if ($gender === 'female') {
            $allocations['Maternity'] = 90;
        } elseif ($gender === 'male') {
            $allocations['Paternity'] = 5;
        }

        foreach ($allocations as $type => $days) {
            LeaveBalance::create([
                'user_id' => $user->id,
                'leave_type' => $type,
                'allocated_days' => $days,
                'used_days' => 0,
                'remaining_days' => $days,
                'year' => $year,
            ]);
        }
    }

    /**
     * Calculate working days (weekdays only) between two dates.
     */
    public function calculateWorkingDays(Carbon $start, Carbon $end): int
    {
        $days = 0;
        $temp = $start->copy();

        while ($temp->lte($end)) {
            if ($temp->isWeekday()) {
                $days++;
            }
            $temp->addDay();
        }

        return $days;
    }

    /**
     * Submit a leave request after validating remaining balances.
     */
    public function submitLeaveRequest(User $user, array $data): LeaveRequest
    {
        $start = Carbon::parse($data['start_date']);
        $end = Carbon::parse($data['end_date']);
        $year = $start->year;

        // Initialize balances if they aren't already
        $this->initializeBalances($user, $year);

        $totalDays = $this->calculateWorkingDays($start, $end);
        if ($totalDays <= 0) {
            throw new \InvalidArgumentException('The leave request must contain at least one working day.');
        }

        $leaveType = $data['leave_type'];

        if ($leaveType !== 'Unpaid') {
            $balance = LeaveBalance::where('user_id', $user->id)
                ->where('leave_type', $leaveType)
                ->where('year', $year)
                ->first();

            if (!$balance) {
                throw new \InvalidArgumentException("You do not have a balance record for {$leaveType} leave (possibly restricted by gender configuration).");
            }

            if ($totalDays > $balance->remaining_days) {
                throw new \InvalidArgumentException("Insufficient leave balance. You requested {$totalDays} days, but only have {$balance->remaining_days} days remaining.");
            }
        }

        return LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type' => $leaveType,
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'total_days' => $totalDays,
            'reason' => $data['reason'] ?? null,
            'status' => 'Pending',
        ]);
    }

    /**
     * Approve leave request and deduct balance.
     */
    public function approveLeaveRequest(LeaveRequest $request, User $approver): void
    {
        DB::transaction(function () use ($request, $approver) {
            $request->refresh();
            if ($request->status !== 'Pending') {
                throw new \InvalidArgumentException('This leave request has already been processed.');
            }

            if ($request->leave_type !== 'Unpaid') {
                $year = $request->start_date->year;
                $balance = LeaveBalance::where('user_id', $request->user_id)
                    ->where('leave_type', $request->leave_type)
                    ->where('year', $year)
                    ->first();

                if (!$balance) {
                    throw new \InvalidArgumentException("Leave balance record not found for {$request->leave_type} in year {$year}.");
                }

                if ($balance->remaining_days < $request->total_days) {
                    throw new \InvalidArgumentException("Cannot approve request. Employee only has {$balance->remaining_days} days remaining, but request requires {$request->total_days} days.");
                }

                $balance->used_days += $request->total_days;
                $balance->remaining_days = $balance->allocated_days - $balance->used_days;
                $balance->save();
            }

            $request->update([
                'status' => 'Approved',
                'approved_by_id' => $approver->id,
                'approved_at' => now(),
            ]);
        });
    }

    /**
     * Reject leave request.
     */
    public function rejectLeaveRequest(LeaveRequest $request, User $approver, string $reason): void
    {
        if ($request->status !== 'Pending') {
            throw new \InvalidArgumentException('This leave request has already been processed.');
        }

        $request->update([
            'status' => 'Rejected',
            'approved_by_id' => $approver->id,
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }
}
