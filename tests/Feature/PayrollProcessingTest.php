<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use App\Models\EmployeeContract;
use App\Models\PayrollRun;
use App\Models\PayrollEntry;
use App\Models\GeneralLedgerEntry;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class PayrollProcessingTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $employee1;
    protected $employee2;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup admin role and admin user
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        $this->adminUser = User::create([
            'name' => 'HR Admin Manager',
            'email' => 'hr_admin@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->adminUser->assignRole('admin');

        // Setup department and designation
        $dept = Department::create(['name' => 'Finance', 'description' => 'Finance Dept']);
        $des = Designation::create(['name' => 'Accountant', 'description' => 'Accountant role']);

        // Create Employee 1: Basic Salary = 100,000 NGN
        $this->employee1 = User::create([
            'name' => 'Taiwo Balogun',
            'email' => 'taiwo.balogun@example.com',
            'password' => bcrypt('password'),
            'employee_id' => 'EMP-001',
            'department_id' => $dept->id,
            'designation_id' => $des->id,
            'is_active' => true,
        ]);
        $this->employee1->assignRole('user');
        
        EmployeeContract::create([
            'user_id' => $this->employee1->id,
            'contract_type' => 'Full-time',
            'start_date' => now()->subMonth()->format('Y-m-d'),
            'basic_salary' => 100000.00,
            'status' => 'Active',
        ]);

        // Create Employee 2: Basic Salary = 350,000 NGN (higher tax slab)
        $this->employee2 = User::create([
            'name' => 'Chinedu Okafor',
            'email' => 'chinedu.okafor@example.com',
            'password' => bcrypt('password'),
            'employee_id' => 'EMP-002',
            'department_id' => $dept->id,
            'designation_id' => $des->id,
            'is_active' => true,
        ]);
        $this->employee2->assignRole('user');

        EmployeeContract::create([
            'user_id' => $this->employee2->id,
            'contract_type' => 'Full-time',
            'start_date' => now()->subMonth()->format('Y-m-d'),
            'basic_salary' => 350000.00,
            'status' => 'Active',
        ]);
    }

    public function test_admin_can_initialize_draft_payroll_run(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.hr.payroll.store'), [
                'month' => 6,
                'year' => 2026,
            ]);

        $response->assertSessionHas('success');
        $run = PayrollRun::first();
        $this->assertNotNull($run);
        $this->assertEquals('2026-06', $run->period);
        $this->assertEquals('Draft', $run->status);

        // Verify entries
        $entries = PayrollEntry::where('payroll_run_id', $run->id)->get();
        $this->assertCount(2, $entries);

        // Verify calculations for Employee 1 (Basic = 100,000)
        $entry1 = $entries->where('user_id', $this->employee1->id)->first();
        $this->assertNotNull($entry1);
        $this->assertEquals(100000.00, $entry1->basic_salary);
        $this->assertEquals(0.00, $entry1->allowances);
        $this->assertEquals(100000.00, $entry1->gross_salary);
        
        // Pension: 8% of Basic = 8,000
        $this->assertEquals(8000.00, $entry1->pension_deduction);
        
        // NHF: 2.5% of Basic = 2,500
        $this->assertEquals(2500.00, $entry1->nhf_deduction);

        // Verify calculations for Employee 2 (Basic = 350,000)
        $entry2 = $entries->where('user_id', $this->employee2->id)->first();
        $this->assertNotNull($entry2);
        $this->assertEquals(350000.00, $entry2->basic_salary);
        $this->assertEquals(28000.00, $entry2->pension_deduction); // 8% of 350,000
        $this->assertEquals(8750.00, $entry2->nhf_deduction); // 2.5% of 350,000
    }

    public function test_admin_can_update_allowance_in_draft_payroll(): void
    {
        // Initialize payroll first
        $run = PayrollRun::create([
            'period' => '2026-06',
            'status' => 'Draft',
            'processed_by_id' => $this->adminUser->id,
        ]);

        $entry = PayrollEntry::create([
            'payroll_run_id' => $run->id,
            'user_id' => $this->employee1->id,
            'basic_salary' => 100000.00,
            'allowances' => 0.00,
            'gross_salary' => 100000.00,
            'pension_deduction' => 8000.00,
            'nhf_deduction' => 2500.00,
            'paye_tax' => 1000.00,
            'net_salary' => 88500.00,
        ]);

        // Put request to update allowance
        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.hr.payroll-entry.allowance', $entry), [
                'allowances' => 20000.00,
            ]);

        $response->assertSessionHas('success');
        $entry->refresh();

        // Verify recalculated gross-to-net
        $this->assertEquals(20000.00, $entry->allowances);
        $this->assertEquals(120000.00, $entry->gross_salary);
        
        // Pension and NHF are base-only or recalculated if base is updated. Basic remains 100,000, so:
        $this->assertEquals(8000.00, $entry->pension_deduction); // 8% of 100,000
        $this->assertEquals(2500.00, $entry->nhf_deduction); // 2.5% of 100,000
        
        // Gross is now 120,000. Let's make sure Net is calculated correctly
        $this->assertTrue($entry->net_salary > 88500.00);
    }

    public function test_admin_can_approve_payroll_run_and_post_to_gl(): void
    {
        // 1. Setup Draft Payroll Run
        $run = PayrollRun::create([
            'period' => '2026-06',
            'status' => 'Draft',
            'total_gross' => 450000.00,
            'total_deductions' => 50000.00,
            'total_net' => 400000.00,
            'processed_by_id' => $this->adminUser->id,
        ]);

        $entry1 = PayrollEntry::create([
            'payroll_run_id' => $run->id,
            'user_id' => $this->employee1->id,
            'basic_salary' => 100000.00,
            'allowances' => 0.00,
            'gross_salary' => 100000.00,
            'pension_deduction' => 8000.00,
            'nhf_deduction' => 2500.00,
            'paye_tax' => 1000.00,
            'net_salary' => 88500.00,
        ]);

        $entry2 = PayrollEntry::create([
            'payroll_run_id' => $run->id,
            'user_id' => $this->employee2->id,
            'basic_salary' => 350000.00,
            'allowances' => 0.00,
            'gross_salary' => 350000.00,
            'pension_deduction' => 28000.00,
            'nhf_deduction' => 8750.00,
            'paye_tax' => 20000.00,
            'net_salary' => 293250.00,
        ]);

        // 2. Approve Run
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.hr.payroll.approve', $run));

        $response->assertSessionHas('success');
        $run->refresh();

        $this->assertEquals('Approved', $run->status);
        $this->assertNotNull($run->processed_at);

        // 3. Verify General Ledger Entries
        $glEntries = GeneralLedgerEntry::where('reference', 'PAYROLL-RUN-' . $run->id)->get();
        
        // Assert Double-Entry balancing: Total Debits == Total Credits
        $totalDebit = $glEntries->sum('debit');
        $totalCredit = $glEntries->sum('credit');

        $this->assertTrue($totalDebit > 0);
        $this->assertEquals($totalDebit, $totalCredit);

        // Check specific accounts exist
        $this->assertNotNull($glEntries->where('account_name', 'Salary Expense')->where('debit', 450000.00)->first());
        $this->assertNotNull($glEntries->where('account_name', 'PAYE Tax Payable')->where('credit', 21000.00)->first());
        $this->assertNotNull($glEntries->where('account_name', 'Pension Payable')->where('credit', 36000.00)->first());
        $this->assertNotNull($glEntries->where('account_name', 'NHF Payable')->where('credit', 11250.00)->first());
        $this->assertNotNull($glEntries->where('account_name', 'Salaries Payable')->where('credit', 381750.00)->first());
    }

    public function test_admin_can_download_payslip(): void
    {
        $run = PayrollRun::create([
            'period' => '2026-06',
            'status' => 'Approved',
            'processed_by_id' => $this->adminUser->id,
        ]);

        $entry = PayrollEntry::create([
            'payroll_run_id' => $run->id,
            'user_id' => $this->employee1->id,
            'basic_salary' => 100000.00,
            'allowances' => 0.00,
            'gross_salary' => 100000.00,
            'pension_deduction' => 8000.00,
            'nhf_deduction' => 2500.00,
            'paye_tax' => 1000.00,
            'net_salary' => 88500.00,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.hr.payroll-entry.payslip', $entry));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }
}
