<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use App\Models\EmployeeContract;
use App\Models\PerformanceGoal;
use App\Models\AppraisalCycle;
use App\Models\Appraisal;
use App\Models\PeerFeedback;
use App\Models\SalaryReview;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class PerformanceManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $managerUser;
    protected $employeeUser;
    protected $peerUser;
    protected $designationSenior;
    protected $designationJunior;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup roles
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Create Department & Designations
        $dept = Department::create(['name' => 'Engineering']);
        $this->designationJunior = Designation::create(['name' => 'Junior Developer', 'department_id' => $dept->id]);
        $this->designationSenior = Designation::create(['name' => 'Senior Developer', 'department_id' => $dept->id]);

        // Create Admin
        $this->adminUser = User::create([
            'name' => 'HR Director Admin',
            'email' => 'admin_perf@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->adminUser->assignRole('admin');

        // Create Manager
        $this->managerUser = User::create([
            'name' => 'Engineering Manager',
            'email' => 'mgr_perf@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->managerUser->assignRole('user');

        // Create Employee
        $this->employeeUser = User::create([
            'name' => 'Koko Emmanuel',
            'email' => 'koko@example.com',
            'password' => bcrypt('password'),
            'department_id' => $dept->id,
            'designation_id' => $this->designationJunior->id,
            'job_title' => 'Junior Backend Dev',
            'is_active' => true,
        ]);
        $this->employeeUser->assignRole('user');

        // Create Peer
        $this->peerUser = User::create([
            'name' => 'Chinedu Okafor',
            'email' => 'chinedu@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->peerUser->assignRole('user');

        // Set up active employee contract
        EmployeeContract::create([
            'user_id' => $this->employeeUser->id,
            'contract_type' => 'Full-time',
            'start_date' => now()->subYear(),
            'basic_salary' => 150000.00,
            'status' => 'active',
        ]);
    }

    public function test_employee_can_set_goal_and_update_progress(): void
    {
        // Set goal
        $response = $this->actingAs($this->employeeUser)
            ->post(route('admin.hr.goals.store'), [
                'title' => 'Optimize Database Queries',
                'description' => 'Reduce page load time by 30%',
                'target_value' => 30,
                'unit' => '%',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(3)->format('Y-m-d'),
                'weight' => 2,
            ]);

        $response->assertRedirect(route('admin.hr.goals.index'));
        $this->assertDatabaseHas('performance_goals', [
            'user_id' => $this->employeeUser->id,
            'title' => 'Optimize Database Queries',
            'current_value' => 0,
            'status' => 'In Progress',
        ]);

        $goal = PerformanceGoal::first();

        // Update progress to 15 (In Progress)
        $responseProgress1 = $this->actingAs($this->employeeUser)
            ->put(route('admin.hr.goals.updateProgress', $goal), [
                'current_value' => 15,
            ]);

        $responseProgress1->assertRedirect(route('admin.hr.goals.index'));
        $this->assertEquals(15, $goal->fresh()->current_value);
        $this->assertEquals('In Progress', $goal->fresh()->status);

        // Update progress to 35 (Achieved)
        $responseProgress2 = $this->actingAs($this->employeeUser)
            ->put(route('admin.hr.goals.updateProgress', $goal), [
                'current_value' => 35,
            ]);

        $responseProgress2->assertRedirect(route('admin.hr.goals.index'));
        $this->assertEquals(35, $goal->fresh()->current_value);
        $this->assertEquals('Achieved', $goal->fresh()->status);
    }

    public function test_complete_appraisal_and_peer_feedback_workflow(): void
    {
        // 1. Admin creates Appraisal Cycle
        $cycle = AppraisalCycle::create([
            'title' => 'Q4 Performance Review',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
            'status' => 'Active',
        ]);

        // 2. Admin invites employee to appraisal
        $responseInvite = $this->actingAs($this->adminUser)
            ->post(route('admin.hr.appraisals.invite'), [
                'appraisal_cycle_id' => $cycle->id,
                'user_id' => $this->employeeUser->id,
                'manager_id' => $this->managerUser->id,
            ]);

        $responseInvite->assertRedirect(route('admin.hr.appraisals.index'));
        $this->assertDatabaseHas('appraisals', [
            'appraisal_cycle_id' => $cycle->id,
            'user_id' => $this->employeeUser->id,
            'manager_id' => $this->managerUser->id,
            'status' => 'Pending Self',
        ]);

        $appraisal = Appraisal::first();

        // 3. Employee submits self appraisal
        $responseSelf = $this->actingAs($this->employeeUser)
            ->post(route('admin.hr.appraisals.submitSelf', $appraisal), [
                'self_rating' => 4.0,
                'self_comment' => 'Completed all my database tuning tasks on time.',
            ]);

        $responseSelf->assertRedirect(route('admin.hr.appraisals.index'));
        $this->assertEquals('Pending Manager', $appraisal->fresh()->status);
        $this->assertEquals(4.0, $appraisal->fresh()->self_rating);

        // 4. Manager requests Peer 360 Feedback
        $responsePeerReq = $this->actingAs($this->managerUser)
            ->post(route('admin.hr.appraisals.requestPeer', $appraisal), [
                'reviewer_id' => $this->peerUser->id,
                'relationship' => 'Peer',
            ]);

        $responsePeerReq->assertRedirect(route('admin.hr.appraisals.index'));
        $this->assertDatabaseHas('peer_feedbacks', [
            'appraisal_id' => $appraisal->id,
            'reviewer_id' => $this->peerUser->id,
            'status' => 'Pending',
        ]);

        $feedback = PeerFeedback::first();

        // 5. Peer submits feedback
        $responsePeerSubmit = $this->actingAs($this->peerUser)
            ->post(route('admin.hr.peer-feedback.submit', $feedback), [
                'rating' => 5.0,
                'feedback' => 'Koko has excellent team collaboration and delivery skills.',
            ]);

        $responsePeerSubmit->assertRedirect(route('admin.hr.appraisals.index'));
        $this->assertEquals('Submitted', $feedback->fresh()->status);
        $this->assertEquals(5.0, $feedback->fresh()->rating);

        // 6. Manager submits appraisal
        $responseManager = $this->actingAs($this->managerUser)
            ->post(route('admin.hr.appraisals.submitManager', $appraisal), [
                'manager_rating' => 4.5,
                'manager_comment' => 'Koko demonstrated great skill enhancements.',
            ]);

        $responseManager->assertRedirect(route('admin.hr.appraisals.index'));
        $this->assertEquals('Pending Calibration', $appraisal->fresh()->status);
        $this->assertEquals(4.5, $appraisal->fresh()->manager_rating);

        // 7. HR Admin calibrates rating and completes appraisal
        $responseCalibration = $this->actingAs($this->adminUser)
            ->post(route('admin.hr.appraisals.submitCalibration', $appraisal), [
                'calibrated_rating' => 4.8,
                'calibrated_comment' => 'Exceptional delivery aligned with high peer feedback score.',
            ]);

        $responseCalibration->assertRedirect(route('admin.hr.appraisals.index'));
        $this->assertEquals('Completed', $appraisal->fresh()->status);
        $this->assertEquals(4.8, $appraisal->fresh()->calibrated_rating);
    }

    public function test_salary_and_promotion_link_on_calibration(): void
    {
        // Setup complete appraisal
        $cycle = AppraisalCycle::create([
            'title' => 'Q4 Review',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
            'status' => 'Active',
        ]);
        $appraisal = Appraisal::create([
            'appraisal_cycle_id' => $cycle->id,
            'user_id' => $this->employeeUser->id,
            'manager_id' => $this->managerUser->id,
            'status' => 'Completed',
            'self_rating' => 4.0,
            'manager_rating' => 4.0,
            'calibrated_rating' => 4.5,
        ]);

        // Propose Salary Review & Promotion
        $responsePropose = $this->actingAs($this->adminUser)
            ->post(route('admin.hr.salary-reviews.store', $appraisal), [
                'proposed_salary' => 200000.00,
                'proposed_designation_id' => $this->designationSenior->id,
                'proposed_job_title' => 'Senior Backend Developer',
                'effective_date' => now()->format('Y-m-d'),
                'notes' => 'Promoted due to top-tier calibrated score',
            ]);

        $responsePropose->assertRedirect(route('admin.hr.salary-reviews.index'));
        $this->assertDatabaseHas('salary_reviews', [
            'appraisal_id' => $appraisal->id,
            'user_id' => $this->employeeUser->id,
            'proposed_salary' => 200000.00,
            'proposed_designation_id' => $this->designationSenior->id,
            'proposed_job_title' => 'Senior Backend Developer',
            'status' => 'Pending',
        ]);

        $review = SalaryReview::first();

        // Approve Salary Review & Promotion
        $responseApprove = $this->actingAs($this->adminUser)
            ->post(route('admin.hr.salary-reviews.approve', $review));

        $responseApprove->assertRedirect(route('admin.hr.salary-reviews.index'));
        
        // Assertions:
        // 1. Contract basic salary updated
        $contract = EmployeeContract::where('user_id', $this->employeeUser->id)->first();
        $this->assertEquals(200000.00, $contract->basic_salary);

        // 2. User details updated
        $user = $this->employeeUser->fresh();
        $this->assertEquals($this->designationSenior->id, $user->designation_id);
        $this->assertEquals('Senior Backend Developer', $user->job_title);

        // 3. Employment history logged
        $this->assertDatabaseHas('employment_histories', [
            'user_id' => $this->employeeUser->id,
            'job_title' => 'Senior Backend Developer',
            'designation_id' => $this->designationSenior->id,
            'basic_salary' => 200000.00,
            'change_reason' => 'Salary Increase & Promotion based on Performance Appraisal Cycle',
        ]);

        // 4. Review marked as Approved
        $this->assertEquals('Approved', $review->fresh()->status);
        $this->assertEquals($this->adminUser->id, $review->fresh()->approved_by_id);
    }
}
