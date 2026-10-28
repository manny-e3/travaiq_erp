<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Department;
use App\Models\Designation;
use App\Models\JobPosting;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\UploadedFile;
use App\Mail\ApplicationReceivedMail;

class CareersPortalTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'is_active' => true
        ]);
    }

    protected function tearDown(): void
    {
        $dir = public_path('testing_resumes');
        if (is_dir($dir)) {
            $this->deleteDirectoryRecursive($dir);
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
                @unlink($path);
            }
        }
        @rmdir($dir);
    }

    /** @test */
    public function public_can_view_careers_page_with_published_jobs()
    {
        $department = Department::create(['name' => 'Engineering', 'description' => 'Tech Team']);
        $designation = Designation::create(['name' => 'PHP Developer', 'description' => 'PHP Developer role']);

        // Create job postings with different status
        $publishedJob = JobPosting::create([
            'title' => 'PHP Developer',
            'designation_id' => $designation->id,
            'description' => '<p>Join our software department.</p>',
            'department_id' => $department->id,
            'location' => 'Remote',
            'employment_type' => 'Full-time',
            'status' => 'Published',
            'closing_date' => now()->addDays(10),
            'ai_pass_score' => 50,
            'created_by' => $this->user->id,
        ]);

        $draftJob = JobPosting::create([
            'title' => 'UI Designer (Draft)',
            'designation_id' => $designation->id,
            'description' => '<p>Draft role.</p>',
            'department_id' => $department->id,
            'location' => 'Onsite',
            'employment_type' => 'Part-time',
            'status' => 'Draft',
            'closing_date' => now()->addDays(5),
            'ai_pass_score' => 40,
            'created_by' => $this->user->id,
        ]);

        // Visit careers page
        $response = $this->get(route('careers.index'));
        $response->assertStatus(200);
        $response->assertSee('PHP Developer');
        $response->assertSee('Remote');
        $response->assertSee('Full-time');
        
        // Assert draft job is hidden
        $response->assertDontSee('UI Designer (Draft)');
    }

    /** @test */
    public function public_can_filter_jobs_by_parameters()
    {
        $deptEng = Department::create(['name' => 'Engineering']);
        $deptMkt = Department::create(['name' => 'Marketing']);
        $designation = Designation::create(['name' => 'General Specialist']);

        $job1 = JobPosting::create([
            'title' => 'Developer',
            'designation_id' => $designation->id,
            'description' => 'Software writing',
            'department_id' => $deptEng->id,
            'location' => 'Berlin',
            'employment_type' => 'Full-time',
            'status' => 'Published',
            'created_by' => $this->user->id,
        ]);

        $job2 = JobPosting::create([
            'title' => 'Marketer',
            'designation_id' => $designation->id,
            'description' => 'Campaign management',
            'department_id' => $deptMkt->id,
            'location' => 'London',
            'employment_type' => 'Part-time',
            'status' => 'Published',
            'created_by' => $this->user->id,
        ]);

        // Filter by location
        $responseLoc = $this->get(route('careers.index', ['location' => 'Berlin']));
        $responseLoc->assertStatus(200);
        $responseLoc->assertSee('Developer');
        $responseLoc->assertDontSee('Marketer');

        // Filter by type
        $responseType = $this->get(route('careers.index', ['employment_type' => 'Part-time']));
        $responseType->assertStatus(200);
        $responseType->assertDontSee('Developer');
        $responseType->assertSee('Marketer');

        // Filter by department
        $responseDept = $this->get(route('careers.index', ['department_id' => $deptEng->id]));
        $responseDept->assertStatus(200);
        $responseDept->assertSee('Developer');
        $responseDept->assertDontSee('Marketer');
    }

    /** @test */
    public function public_can_view_individual_job_and_requires_published_status()
    {
        $department = Department::create(['name' => 'Engineering']);
        $designation = Designation::create(['name' => 'Engineer']);

        $publishedJob = JobPosting::create([
            'title' => 'DevOps Engineer',
            'designation_id' => $designation->id,
            'description' => '<h2>DevOps Description</h2><p>Scale servers.</p>',
            'department_id' => $department->id,
            'location' => 'Remote',
            'employment_type' => 'Full-time',
            'status' => 'Published',
            'created_by' => $this->user->id,
        ]);

        $draftJob = JobPosting::create([
            'title' => 'Product Manager PM',
            'designation_id' => $designation->id,
            'description' => 'Plan roadmap.',
            'department_id' => $department->id,
            'location' => 'Remote',
            'employment_type' => 'Full-time',
            'status' => 'Draft',
            'created_by' => $this->user->id,
        ]);

        // 1. Get published job page
        $responsePub = $this->get(route('careers.show', $publishedJob->id));
        $responsePub->assertStatus(200);
        $responsePub->assertSee('DevOps Engineer');
        // Check HTML renders correctly unescaped
        $responsePub->assertSee('<h2>DevOps Description</h2>', false);

        // 2. Get draft job page (should return 404)
        $responseDraft = $this->get(route('careers.show', $draftJob->id));
        $responseDraft->assertStatus(404);
    }

    /** @test */
    public function public_can_submit_job_application_with_validation()
    {
        Mail::fake();

        $department = Department::create(['name' => 'Engineering']);
        $designation = Designation::create(['name' => 'Engineer']);

        $job = JobPosting::create([
            'title' => 'Support Engineer',
            'designation_id' => $designation->id,
            'description' => 'Help customers.',
            'department_id' => $department->id,
            'location' => 'Remote',
            'employment_type' => 'Full-time',
            'status' => 'Published',
            'created_by' => $this->user->id,
        ]);

        $this->assertEquals(0, JobApplication::count());

        // Prepare mock resume file
        $mockResume = UploadedFile::fake()->create('resume.pdf', 150, 'application/pdf');

        $payload = [
            'first_name' => 'Candidate',
            'last_name' => 'User',
            'email' => 'candidate@jobs.com',
            'phone' => '123-456-7890',
            'cover_letter' => 'Here is my cover letter detailing why I am the best fit.',
            'resume_file' => $mockResume,
        ];

        // Act: post application form submit
        $response = $this->post(route('careers.apply', $job->id), $payload);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert database entry exists
        $this->assertEquals(1, JobApplication::count());
        $application = JobApplication::first();
        $this->assertEquals('Candidate', $application->first_name);
        $this->assertEquals('User', $application->last_name);
        $this->assertEquals('candidate@jobs.com', $application->email);
        $this->assertEquals('In Review', $application->status);
        $this->assertNotNull($application->resume_path);
        
        // Assert file exists in testing resumes directory
        $this->assertFileExists(public_path('testing_resumes/' . $application->resume_path));

        // Assert Application Received Mail was sent
        Mail::assertQueued(ApplicationReceivedMail::class, function($mail) use ($application) {
            return $mail->hasTo($application->email) && $mail->application->id === $application->id;
        });
    }
}
