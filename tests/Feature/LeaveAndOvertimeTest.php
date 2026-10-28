<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\EmployeeProfile;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\OvertimeRecord;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class LeaveAndOvertimeTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $femaleEmployee;
    protected $maleEmployee;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup roles
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Create Admin
        $this->adminUser = User::create([
            'name' => 'HR Manager Admin',
            'email' => 'admin_hr@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->adminUser->assignRole('admin');

        // Create Female Employee
        $this->femaleEmployee = User::create([
            'name' => 'Fatima Bello',
            'email' => 'fatima@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->femaleEmployee->assignRole('user');
        EmployeeProfile::create([
            'user_id' => $this->femaleEmployee->id,
            'gender' => 'Female',
        ]);

        // Create Male Employee
        $this->maleEmployee = User::create([
            'name' => 'Oluwaseun Adebayo',
            'email' => 'seun@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->maleEmployee->assignRole('user');
        EmployeeProfile::create([
            'user_id' => $this->maleEmployee->id,
            'gender' => 'Male',
        ]);
    }

    public function test_gender_specific_leave_balance_initialization(): void
    {
        // Act as admin visiting leaves page which auto-initializes all active staff balances
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.hr.leaves.index'));

        $response->assertStatus(200);

        // Verify female employee balances: Annual, Sick, Maternity. NO Paternity.
        $this->assertDatabaseHas('leave_balances', [
            'user_id' => $this->femaleEmployee->id,
            'leave_type' => 'Annual',
            'allocated_days' => 20,
        ]);
        $this->assertDatabaseHas('leave_balances', [
            'user_id' => $this->femaleEmployee->id,
            'leave_type' => 'Maternity',
            'allocated_days' => 90,
        ]);
        $this->assertDatabaseMissing('leave_balances', [
            'user_id' => $this->femaleEmployee->id,
            'leave_type' => 'Paternity',
        ]);

        // Verify male employee balances: Annual, Sick, Paternity. NO Maternity.
        $this->assertDatabaseHas('leave_balances', [
            'user_id' => $this->maleEmployee->id,
            'leave_type' => 'Paternity',
            'allocated_days' => 5,
        ]);
        $this->assertDatabaseMissing('leave_balances', [
            'user_id' => $this->maleEmployee->id,
            'leave_type' => 'Maternity',
        ]);
    }

    public function test_employee_cannot_request_leave_exceeding_remaining_balance(): void
    {
        // Initialize balances
        $this->actingAs($this->adminUser)->get(route('admin.hr.leaves.index'));

        // Request 25 days of Annual leave (Limit is 20 days)
        $response = $this->actingAs($this->femaleEmployee)
            ->post(route('admin.hr.leaves.store'), [
                'leave_type' => 'Annual',
                'start_date' => Carbon::parse('next monday')->format('Y-m-d'),
                // 5 weeks later = 25 working days
                'end_date' => Carbon::parse('next monday')->addWeeks(5)->subDays(3)->format('Y-m-d'),
                'reason' => 'Long holiday request',
            ]);

        $response->assertSessionHasErrors(['leave_error']);
        $this->assertDatabaseCount('leave_requests', 0);
    }

    public function test_leave_approval_workflow_and_realtime_balance_deductions(): void
    {
        // Initialize balances
        $this->actingAs($this->adminUser)->get(route('admin.hr.leaves.index'));

        // Submit request for 5 working days (Monday to Friday)
        $start = Carbon::parse('next monday');
        $end = Carbon::parse('next monday')->addDays(4); // Friday

        $response = $this->actingAs($this->femaleEmployee)
            ->post(route('admin.hr.leaves.store'), [
                'leave_type' => 'Annual',
                'start_date' => $start->format('Y-m-d'),
                'end_date' => $end->format('Y-m-d'),
                'reason' => 'One week resting leave',
            ]);

        $response->assertRedirect(route('admin.hr.leaves.index'));
        $this->assertDatabaseHas('leave_requests', [
            'user_id' => $this->femaleEmployee->id,
            'leave_type' => 'Annual',
            'total_days' => 5,
            'status' => 'Pending',
        ]);

        $request = LeaveRequest::first();

        // Approve leave request as Admin
        $responseApprove = $this->actingAs($this->adminUser)
            ->post(route('admin.hr.leaves.approve', $request));

        $responseApprove->assertRedirect(route('admin.hr.leaves.index'));

        // Assert request status and balance changes
        $this->assertEquals('Approved', $request->fresh()->status);
        $this->assertDatabaseHas('leave_balances', [
            'user_id' => $this->femaleEmployee->id,
            'leave_type' => 'Annual',
            'used_days' => 5,
            'remaining_days' => 15,
        ]);
    }

    public function test_admin_can_edit_staff_leave_allocations(): void
    {
        // Initialize balances
        $this->actingAs($this->adminUser)->get(route('admin.hr.leaves.index'));

        $balance = LeaveBalance::where('user_id', $this->femaleEmployee->id)
            ->where('leave_type', 'Sick')
            ->first();

        // Update allocated days to 15
        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.hr.leaves.updateBalance', $balance), [
                'allocated_days' => 15,
            ]);

        $response->assertRedirect(route('admin.hr.leaves.index'));
        $this->assertEquals(15, $balance->fresh()->allocated_days);
        $this->assertEquals(15, $balance->fresh()->remaining_days);
    }

    public function test_employee_can_log_overtime_and_admin_approves(): void
    {
        // Log Overtime
        $response = $this->actingAs($this->maleEmployee)
            ->post(route('admin.hr.overtime.store'), [
                'date' => now()->subDay()->format('Y-m-d'),
                'hours' => 3.5,
                'rate_multiplier' => 1.5,
                'description' => 'System integration debug session',
            ]);

        $response->assertRedirect(route('admin.hr.overtime.index'));
        $this->assertDatabaseHas('overtime_records', [
            'user_id' => $this->maleEmployee->id,
            'hours' => 3.5,
            'rate_multiplier' => 1.50,
            'status' => 'Pending',
        ]);

        $overtime = OvertimeRecord::first();

        // Approve Overtime as Admin
        $responseApprove = $this->actingAs($this->adminUser)
            ->post(route('admin.hr.overtime.approve', $overtime));

        $responseApprove->assertRedirect(route('admin.hr.overtime.index'));
        $this->assertEquals('Approved', $overtime->fresh()->status);
        $this->assertEquals($this->adminUser->id, $overtime->fresh()->approved_by_id);
    }
}
