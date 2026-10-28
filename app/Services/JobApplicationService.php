<?php

namespace App\Services;

use App\Models\JobApplication;
use App\Jobs\ScreenApplicationWithAI;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationReceivedMail;
use App\Mail\ApplicationRejectedMail;
use App\Models\User;
use App\Models\EmployeeProfile;
use App\Models\EmployeeContract;
use App\Models\EmployeeDocument;
use App\Models\EmploymentHistory;
use Illuminate\Support\Facades\Hash;
use App\Mail\UserWelcomeMail;
use Barryvdh\DomPDF\Facade\Pdf;

class JobApplicationService
{
    public function submitApplication(array $data, $resumeFile): JobApplication
    {
        if ($resumeFile) {
            $filename = time() . '_' . Str::slug($resumeFile->getClientOriginalName()) . '.' . $resumeFile->getClientOriginalExtension();
            $directory = app()->environment('testing') ? public_path('testing_resumes') : public_path('resumes');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            $resumeFile->move($directory, $filename);
            $data['resume_path'] = $filename;
        }

        $data['status'] = 'In Review';
        $application = JobApplication::create($data);

        // Send Application Received Email
        Mail::to($application->email)->send(new ApplicationReceivedMail($application));

        // Dispatch AI Screening in the background queue
        ScreenApplicationWithAI::dispatch($application);

        return $application;
    }

    public function updateStatus(JobApplication $application, string $status): bool
    {
        $updated = $application->update(['status' => $status]);

        if ($updated && $status === 'Rejected') {
            Mail::to($application->email)->send(new ApplicationRejectedMail($application));
        }

        if ($updated && $status === 'Hired') {
            $this->onboardAsEmployee($application);
        }

        return $updated;
    }

    public function onboardAsEmployee(JobApplication $application): User
    {
        // 1. Check if user already exists
        $user = User::where('email', $application->email)->first();
        $isNewUser = !$user;
        $password = null;

        if ($isNewUser) {
            $password = Str::random(12);
            $employeeId = 'EMP-' . str_pad((User::max('id') ?? 0) + 1, 5, '0', STR_PAD_LEFT);

            $user = User::create([
                'name' => $application->full_name,
                'email' => $application->email,
                'phone' => $application->phone,
                'department_id' => $application->jobPosting->department_id,
                'designation_id' => $application->jobPosting->designation_id,
                'job_title' => $application->jobPosting->title,
                'employee_id' => $employeeId,
                'is_active' => true,
                'password' => Hash::make($password),
            ]);

            $user->assignRole('user');
        }

        // Update application status to "Hired"
        $application->update(['status' => 'Hired']);

        // 2. Ensure EmployeeProfile exists
        $user->profile()->firstOrCreate([], [
            'education' => [],
            'certifications' => [],
            'skills' => [],
            'languages' => [],
        ]);

        // 3. Ensure EmployeeContract is created from the JobOffer details if available
        $contractDocumentPath = null;
        if ($application->offer) {
            $newContractFileName = 'contract_' . $user->id . '_' . time() . '.pdf';
            $contractDirectory = app()->environment('testing')
                ? storage_path('app/public/testing_employee_contracts')
                : storage_path('app/public/employee_contracts');
            if (!file_exists($contractDirectory)) {
                mkdir($contractDirectory, 0755, true);
            }

            $contractType = $application->jobPosting->employment_type ?? 'Full-time';
            $contractDuration = $application->offer->contract_duration_months ?? 12;
            $endDate = \Carbon\Carbon::parse($application->offer->expected_start_date)->addMonths($contractDuration);

            // Generate the contract PDF dynamically using the new template
            $pdf = Pdf::loadView('admin.hr.recruitment.contract_pdf', [
                'application' => $application,
                'salary' => $application->offer->salary_offer,
                'startDate' => $application->offer->expected_start_date,
                'contractType' => $contractType,
                'contractDuration' => $contractDuration,
            ]);

            file_put_contents($contractDirectory . '/' . $newContractFileName, $pdf->output());
            $contractDocumentPath = (app()->environment('testing') ? 'testing_employee_contracts/' : 'employee_contracts/') . $newContractFileName;

            // Deactivate other active contracts
            $user->contracts()->where('status', 'Active')->update(['status' => 'Expired']);

            $user->contracts()->firstOrCreate([
                'contract_type' => $contractType,
                'start_date' => $application->offer->expected_start_date,
            ], [
                'end_date' => $endDate,
                'basic_salary' => $application->offer->salary_offer,
                'status' => 'Active',
                'document_path' => $contractDocumentPath,
                'notes' => 'Contract generated automatically from accepted job offer.',
            ]);
        }

        // 4. Copy resume to EmployeeDocument vault if available
        if ($application->resume_path) {
            $resumeSource = app()->environment('testing')
                ? public_path('testing_resumes/' . $application->resume_path)
                : public_path('resumes/' . $application->resume_path);
            if (file_exists($resumeSource)) {
                $newResumeFileName = 'resume_' . time() . '_' . $application->resume_path;
                $vaultDirectory = app()->environment('testing')
                    ? storage_path('app/public/testing_employee_vault/' . $user->id)
                    : storage_path('app/public/employee_vault/' . $user->id);
                if (!file_exists($vaultDirectory)) {
                    mkdir($vaultDirectory, 0755, true);
                }
                copy($resumeSource, $vaultDirectory . '/' . $newResumeFileName);

                $user->documents()->firstOrCreate([
                    'document_name' => 'Resume / CV',
                    'document_type' => 'CV',
                ], [
                    'file_path' => (app()->environment('testing') ? 'testing_employee_vault/' : 'employee_vault/') . $user->id . '/' . $newResumeFileName,
                    'notes' => 'Resume uploaded during recruitment process.',
                ]);
            }
        }

        // 5. Create Employment History entry
        $user->employmentHistories()->firstOrCreate([
            'designation_id' => $application->jobPosting->designation_id,
            'job_title' => $application->jobPosting->title,
            'start_date' => $application->offer ? $application->offer->expected_start_date : now(),
        ], [
            'department_id' => $application->jobPosting->department_id,
            'end_date' => null,
            'basic_salary' => $application->offer ? $application->offer->salary_offer : 0,
            'change_reason' => 'Initial Hiring (Onboarded from recruitment)',
            'notes' => 'Onboarded automatically from job application.',
        ]);

        // 6. Send the welcome email with temporary credentials and attached contract if new user
        if ($isNewUser) {
            try {
                Mail::to($user->email)->send(new UserWelcomeMail($user, $password, $contractDocumentPath));
            } catch (\Exception $e) {
                logger()->error('Failed to send welcome email on onboarding: ' . $e->getMessage());
            }
        }

        return $user;
    }

    public function deleteApplication(JobApplication $application): bool
    {
        if ($application->resume_path && file_exists(public_path('resumes/' . $application->resume_path))) {
            unlink(public_path('resumes/' . $application->resume_path));
        }
        return $application->delete();
    }
}
