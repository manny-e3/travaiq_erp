<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name','email','password','department_id','employee_id',
        'phone','avatar','job_title','designation_id','is_active',
    ];

    protected $hidden = ['password','remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=6576ff&color=fff';
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function profile()
    {
        return $this->hasOne(EmployeeProfile::class);
    }

    public function contracts()
    {
        return $this->hasMany(EmployeeContract::class);
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function employmentHistories()
    {
        return $this->hasMany(EmploymentHistory::class)->orderBy('start_date', 'desc');
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function overtimeRecords()
    {
        return $this->hasMany(OvertimeRecord::class);
    }

    public function performanceGoals()
    {
        return $this->hasMany(PerformanceGoal::class);
    }

    public function appraisals()
    {
        return $this->hasMany(Appraisal::class);
    }

    public function managedAppraisals()
    {
        return $this->hasMany(Appraisal::class, 'manager_id');
    }

    public function peerFeedbackRequests()
    {
        return $this->hasMany(PeerFeedback::class, 'reviewer_id');
    }

    public function salaryReviews()
    {
        return $this->hasMany(SalaryReview::class);
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to_id');
    }
}
