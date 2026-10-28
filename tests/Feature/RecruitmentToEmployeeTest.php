<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Department;
use App\Models\JobPosting;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Mail\UserWelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RecruitmentToEmployeeTest extends TestCase
{
    use RefreshDatabase;

    protected $createdFiles = [];

    protected function setUp(): void
    {
        parent::setUp();
        Role::findOrCreate('user');
        Role::findOrCreate('admin');
    }

    protected function tearDown(): void
    {
        // Clean up created files
        foreach ($this->createdFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        
        // Clean up created directories
        $dirs = [
            public_path('testing_resumes'),
            public_path('testing_offer_letter'),
            storage_path('app/public/testing_employee_contracts'),
        ];
        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                // Remove files in directory
                $files = glob($dir . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                rmdir($dir);
            }
        }

        // Clean up employee vault directory dynamically
        $vaultBase = storage_path('app/public/testing_employee_vault');
        if (is_dir($vaultBase)) {
            $this->deleteDirectoryRecursive($vaultBase);
        }

        parent::tearDown();
    }

    private function deleteDirectoryRecursive($dir)
    {
        if (!file_exists($dir)) return;
        $items = array_diff(scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->deleteDirectoryRecursive($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    public function test_candidate_onboarding_creates_employee_records_and_sends_email(): void
    {
        $this->withoutExceptionHandling();
        Mail::fake();

        // 1. Create a Department, User (Admin), and JobPosting
        $department = Department::create([
            'name' => 'Engineering',
            'description' => 'Software Dev'
        ]);

        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $adminUser->assignRole('admin');

        $designation = \App\Models\Designation::create([
            'name' => 'Laravel Developer',
            'description' => 'Laravel Developer Designation'
        ]);

        $jobPosting = JobPosting::create([
            'title' => 'Laravel Developer',
            'designation_id' => $designation->id,
            'description' => 'Scale our products',
            'department_id' => $department->id,
            'location' => 'Remote',
            'employment_type' => 'Full-time',
            'status' => 'Open',
            'closing_date' => now()->addDays(30),
            'created_by' => $adminUser->id,
        ]);

        // 2. Prepare mock files
        $resumesDir = public_path('testing_resumes');
        if (!file_exists($resumesDir)) {
            mkdir($resumesDir, 0755, true);
        }
        $resumeName = 'test_resume_' . time() . '.pdf';
        $resumeFullPath = $resumesDir . '/' . $resumeName;
        file_put_contents($resumeFullPath, 'Fake PDF Resume Content');
        $this->createdFiles[] = $resumeFullPath;

        $offersDir = public_path('testing_offer_letter');
        if (!file_exists($offersDir)) {
            mkdir($offersDir, 0755, true);
        }
        $offerLetterName = 'test_offer_' . time() . '.pdf';
        $offerLetterFullPath = $offersDir . '/' . $offerLetterName;
        file_put_contents($offerLetterFullPath, 'Fake PDF Offer Content');
        $this->createdFiles[] = $offerLetterFullPath;

        // 3. Create JobApplication with a resume
        $jobApplication = JobApplication::create([
            'job_posting_id' => $jobPosting->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
            'resume_path' => $resumeName,
            'cover_letter' => 'I would love this job.',
            'status' => 'Offered',
        ]);

        // 4. Create JobOffer
        $jobOffer = JobOffer::create([
            'job_application_id' => $jobApplication->id,
            'salary_offer' => 85000.00,
            'expected_start_date' => now()->addDays(14)->format('Y-m-d'),
            'document_path' => $offerLetterName,
            'status' => 'Accepted',
        ]);

        // Refresh application model to load the newly created offer relationship
        $jobApplication->refresh();

        // 5. Act: Trigger Onboarding via Post Route as Admin
        $response = $this->actingAs($adminUser)
            ->post(route('admin.hr.recruitment.applications.onboard', $jobApplication));

        // 6. Assert redirect and success
        $user = User::where('email', 'john.doe@example.com')->first();
        $this->assertNotNull($user);

        $response->assertRedirect(route('admin.hr.employees.show', ['user' => $user->id]));
        $response->assertSessionHas('success');

        // 7. Verify Job Application status updated
        $jobApplication->refresh();
        $this->assertEquals('Hired', $jobApplication->status);

        // 8. Verify User record creation details
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('1234567890', $user->phone);
        $this->assertEquals($department->id, $user->department_id);
        $this->assertEquals($designation->id, $user->designation_id);
        $this->assertEquals('Laravel Developer', $user->job_title);
        $this->assertTrue($user->is_active);
        $this->assertStringStartsWith('EMP-', $user->employee_id);
        $this->assertTrue($user->hasRole('user'));

        // 11. Verify EmployeeContract exists and offer PDF is copied
        $contract = $user->contracts()->first();
        $this->assertNotNull($contract);
        $this->assertEquals('Full-time', $contract->contract_type);
        $this->assertEquals(85000.00, $contract->basic_salary);
        $this->assertEquals('Active', $contract->status);
        $this->assertNotNull($contract->document_path);
        $expectedEndDate = \Carbon\Carbon::parse($jobOffer->expected_start_date)->addMonths(12);
        $this->assertEquals($expectedEndDate->format('Y-m-d'), $contract->end_date->format('Y-m-d'));

        // 9. Verify Welcome Mail sent
        Mail::assertQueued(UserWelcomeMail::class, function ($mail) use ($user, $contract) {
            return $mail->hasTo($user->email) && 
                   $mail->user->id === $user->id && 
                   $mail->contractPath === $contract->document_path;
        });
        $this->assertFileExists(storage_path('app/public/' . $contract->document_path));

        // 10. Verify EmployeeProfile exists
        $this->assertNotNull($user->profile);

        // 12. Verify EmployeeDocument exists and resume PDF is copied
        $document = $user->documents()->first();
        $this->assertNotNull($document);
        $this->assertEquals('Resume / CV', $document->document_name);
        $this->assertEquals('CV', $document->document_type);
        $this->assertNotNull($document->file_path);
        $this->assertFileExists(storage_path('app/public/' . $document->file_path));

        // 13. Verify EmploymentHistory exists
        $history = $user->employmentHistories()->first();
        $this->assertNotNull($history);
        $this->assertEquals($designation->id, $history->designation_id);
        $this->assertEquals('Laravel Developer', $history->job_title);
        $this->assertEquals($department->id, $history->department_id);
        $this->assertEquals(85000.00, $history->basic_salary);
        $this->assertEquals('Initial Hiring (Onboarded from recruitment)', $history->change_reason);
    }

    public function test_automatic_onboarding_triggered_on_status_hired(): void
    {
        $this->withoutExceptionHandling();
        Mail::fake();

        $department = Department::create([
            'name' => 'Marketing',
            'description' => 'Branding'
        ]);

        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $adminUser->assignRole('admin');

        $designation = \App\Models\Designation::create([
            'name' => 'Copywriter',
            'description' => 'Copywriter Designation'
        ]);

        $jobPosting = JobPosting::create([
            'title' => 'Copywriter',
            'designation_id' => $designation->id,
            'description' => 'Write copy',
            'department_id' => $department->id,
            'location' => 'Remote',
            'employment_type' => 'Full-time',
            'status' => 'Open',
            'closing_date' => now()->addDays(30),
            'created_by' => $adminUser->id,
        ]);

        // Prepare mock resume
        $resumesDir = public_path('testing_resumes');
        if (!file_exists($resumesDir)) {
            mkdir($resumesDir, 0755, true);
        }
        $resumeName = 'test_resume_jane_' . time() . '.pdf';
        $resumeFullPath = $resumesDir . '/' . $resumeName;
        file_put_contents($resumeFullPath, 'Fake PDF Resume Content');
        $this->createdFiles[] = $resumeFullPath;

        $jobApplication = JobApplication::create([
            'job_posting_id' => $jobPosting->id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'phone' => '0987654321',
            'resume_path' => $resumeName,
            'status' => 'Offered',
        ]);

        // Put request to update status to Hired
        $response = $this->actingAs($adminUser)
            ->put(route('admin.hr.recruitment.applications.updateStatus', $jobApplication), [
                'status' => 'Hired'
            ]);

        $response->assertSessionHas('success');

        // Verify user created
        $user = User::where('email', 'jane.smith@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Jane Smith', $user->name);
        $this->assertEquals($department->id, $user->department_id);
        $this->assertEquals($designation->id, $user->designation_id);
        $this->assertEquals('Copywriter', $user->job_title);
        $this->assertStringStartsWith('EMP-', $user->employee_id);

        Mail::assertQueued(UserWelcomeMail::class);
    }

    public function test_contractor_onboarding_sets_contract_type_duration_and_computed_end_date(): void
    {
        Mail::fake();

        // 1. Prepare environment: Setup Roles/Permissions & Designation
        $roleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $roleUser = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        
        $department = Department::create([
            'name' => 'Consulting',
            'description' => 'External Consultants'
        ]);

        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin_contract@test.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $adminUser->assignRole('admin');

        $designation = \App\Models\Designation::create([
            'name' => 'Senior Consultant',
            'description' => 'Senior Consultant Designation'
        ]);

        $jobPosting = JobPosting::create([
            'title' => 'Senior Consultant',
            'designation_id' => $designation->id,
            'description' => 'Provide specialist advice',
            'department_id' => $department->id,
            'location' => 'Remote',
            'employment_type' => 'Contract',
            'status' => 'Open',
            'closing_date' => now()->addDays(30),
            'created_by' => $adminUser->id,
        ]);

        // Create application
        $jobApplication = JobApplication::create([
            'job_posting_id' => $jobPosting->id,
            'first_name' => 'Jane',
            'last_name' => 'Consultant',
            'email' => 'jane.consultant@example.com',
            'phone' => '1234567890',
            'resume_path' => 'fake_resume.pdf',
            'status' => 'Offered',
        ]);

        // Create JobOffer with duration of 6 months
        $jobOffer = JobOffer::create([
            'job_application_id' => $jobApplication->id,
            'salary_offer' => 120000.00,
            'expected_start_date' => now()->addDays(10)->format('Y-m-d'),
            'contract_duration_months' => 6,
            'document_path' => 'fake_offer.pdf',
            'status' => 'Accepted',
        ]);

        $jobApplication->refresh();

        // Act: Onboard candidate
        $response = $this->actingAs($adminUser)
            ->post(route('admin.hr.recruitment.applications.onboard', $jobApplication));

        // Assert redirect
        $user = User::where('email', 'jane.consultant@example.com')->first();
        $this->assertNotNull($user);

        $response->assertRedirect(route('admin.hr.employees.show', ['user' => $user->id]));
        $jobApplication->refresh();
        $this->assertEquals('Hired', $jobApplication->status);

        // Verify EmployeeContract details
        $contract = $user->contracts()->first();
        $this->assertNotNull($contract);
        $this->assertEquals('Contract', $contract->contract_type);
        $this->assertEquals(120000.00, $contract->basic_salary);
        $this->assertEquals('Active', $contract->status);
        
        // Assert the computed end date is correct (expected_start_date + 6 months)
        $expectedEndDate = \Carbon\Carbon::parse($jobOffer->expected_start_date)->addMonths(6);
        $this->assertEquals($expectedEndDate->format('Y-m-d'), $contract->end_date->format('Y-m-d'));
    }

    public function test_full_time_offer_letter_does_not_require_or_render_contract_duration(): void
    {
        Mail::fake();

        $department = Department::create([
            'name' => 'Engineering',
            'description' => 'Software Dev'
        ]);

        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin_ft@test.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $adminUser->assignRole('admin');

        $designation = \App\Models\Designation::create([
            'name' => 'Senior Developer',
            'description' => 'Senior Developer Designation'
        ]);

        $jobPosting = JobPosting::create([
            'title' => 'Senior Developer',
            'designation_id' => $designation->id,
            'description' => 'Develop clean code',
            'department_id' => $department->id,
            'location' => 'Remote',
            'employment_type' => 'Full-time',
            'status' => 'Open',
            'closing_date' => now()->addDays(30),
            'created_by' => $adminUser->id,
        ]);

        // Create application
        $jobApplication = JobApplication::create([
            'job_posting_id' => $jobPosting->id,
            'first_name' => 'Jack',
            'last_name' => 'Fulltime',
            'email' => 'jack.fulltime@example.com',
            'phone' => '1234567890',
            'resume_path' => 'fake_resume.pdf',
            'status' => 'Interviewing',
        ]);

        // Create a final round interview so that hasCompletedFinalRound returns true
        \App\Models\Interview::create([
            'job_application_id' => $jobApplication->id,
            'interview_stage' => 'Final Round',
            'status' => 'Completed',
            'scheduled_at' => now()->subDays(1),
            'location_or_link' => 'Office',
        ]);

        // Post request to store offer without contract_duration_months
        $response = $this->actingAs($adminUser)
            ->post(route('admin.hr.recruitment.offers.store', $jobApplication), [
                'salary_offer' => 150000.00,
                'expected_start_date' => now()->addDays(15)->format('Y-m-d'),
                // 'contract_duration_months' is omitted
            ]);

        $response->assertRedirect();
        
        // Assert JobOffer is created in DB with contract_duration_months as null
        $offer = JobOffer::where('job_application_id', $jobApplication->id)->first();
        $this->assertNotNull($offer);
        $this->assertNull($offer->contract_duration_months);

        // Keep track of generated PDF for cleanup
        $directory = public_path('testing_offer_letter');
        $this->createdFiles[] = $directory . '/' . $offer->document_path;

        // Verify PDF contents (render view to HTML and assert)
        $pdfHtml = view('admin.hr.recruitment.offer_pdf', [
            'application' => $jobApplication,
            'salary' => 150000.00,
            'startDate' => now()->addDays(15)->format('Y-m-d'),
            'contractDuration' => null,
        ])->render();

        $this->assertStringContainsString('OFFER OF EMPLOYMENT', $pdfHtml);
        $this->assertStringContainsString('on a full-time basis', $pdfHtml);
        $this->assertStringNotContainsString('initial duration of', $pdfHtml);
        $this->assertStringNotContainsString('contract base', $pdfHtml);
    }
}
