<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketLog;
use App\Models\TicketReply;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HelpdeskManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $techAgent;
    protected $financeAgent;
    protected $engineeringDept;
    protected $financeDept;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // 2. Setup Departments
        $this->engineeringDept = Department::create(['name' => 'Engineering']);
        $this->financeDept = Department::create(['name' => 'Finance']);

        // 3. Setup Users/Agents
        $this->adminUser = User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@admin.com',
            'is_active' => true,
        ]);
        $this->adminUser->assignRole($adminRole);

        $this->techAgent = User::factory()->create([
            'name' => 'Tech Engineer',
            'email' => 'tech@agent.com',
            'is_active' => true,
            'department_id' => $this->engineeringDept->id,
        ]);
        $this->techAgent->assignRole($userRole);

        $this->financeAgent = User::factory()->create([
            'name' => 'Finance Analyst',
            'email' => 'finance@agent.com',
            'is_active' => true,
            'department_id' => $this->financeDept->id,
        ]);
        $this->financeAgent->assignRole($userRole);
    }

    /**
     * Test creating a ticket with automatic customer intake, SLA deadline calculation, and intelligent routing.
     */
    public function test_ticket_creation_with_sla_and_intelligent_routing()
    {
        $payload = [
            'customer_name' => 'Alice Smith',
            'customer_email' => 'alice@gmail.com',
            'customer_phone' => '+2348033333333',
            'customer_company' => 'Smith & Co',
            'subject' => 'Server downtime after upgrade',
            'description' => 'Our production server is unreachable with error 502.',
            'priority' => 'Urgent',
            'category' => 'Technical',
            'channel' => 'Email',
        ];

        // 1. Submit ticket creation form as admin
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.helpdesk.store'), $payload);

        // 2. Verify redirect to show view
        $ticket = Ticket::first();
        $this->assertNotNull($ticket);
        $response->assertRedirect(route('admin.helpdesk.show', $ticket));

        // 3. Verify Customer record was automatically created/resolved
        $this->assertDatabaseHas('customers', [
            'email' => 'alice@gmail.com',
            'name' => 'Alice Smith',
            'phone' => '+2348033333333',
            'company' => 'Alice Smith' ? 'Smith & Co' : null,
        ]);

        // 4. Verify Ticket key format and values
        $this->assertMatchesRegularExpression('/^TKT-\d{8}-\d{4}$/', $ticket->ticket_key);
        $this->assertEquals('Alice Smith', $ticket->customer->name);
        $this->assertEquals('Technical', $ticket->category);
        $this->assertEquals('Urgent', $ticket->priority);

        // 5. Verify SLA duration for 'Urgent' is 2 hours
        $expectedSlaDeadline = Carbon::now()->addHours(2);
        $this->assertTrue($ticket->sla_deadline->between($expectedSlaDeadline->copy()->subMinute(), $expectedSlaDeadline->copy()->addMinute()));

        // 6. Verify intelligent routing assigned the ticket to Tech Agent (Engineering department)
        $this->assertEquals($this->techAgent->id, $ticket->assigned_to_id);

        // 7. Verify audit logs
        $this->assertDatabaseHas('ticket_logs', [
            'ticket_id' => $ticket->id,
            'action' => 'Ticket Created',
        ]);
        $this->assertDatabaseHas('ticket_logs', [
            'ticket_id' => $ticket->id,
            'action' => 'Auto Routed Agent',
        ]);
    }

    /**
     * Test communication thread and automatic status transitions.
     */
    public function test_ticket_replies_and_automatic_status_transition()
    {
        // 1. Create a ticket in Open state
        $customer = Customer::create([
            'name' => 'Bob Johnson',
            'email' => 'bob@gmail.com',
        ]);

        $ticket = Ticket::create([
            'ticket_key' => 'TKT-20260609-0001',
            'customer_id' => $customer->id,
            'subject' => 'Billing dispute',
            'description' => 'I was charged twice this month.',
            'status' => 'Open',
            'priority' => 'Medium',
            'category' => 'Billing',
            'assigned_to_id' => $this->financeAgent->id,
            'sla_deadline' => Carbon::now()->addHours(24),
        ]);

        // 2. Submit an agent reply
        $response = $this->actingAs($this->financeAgent)
            ->post(route('admin.helpdesk.reply', $ticket), [
                'message' => 'Hello Bob, we have reversed the duplicate charge. Please check your card statement.',
            ]);

        $response->assertRedirect();
        
        // 3. Verify reply was logged
        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id' => $ticket->id,
            'user_id' => $this->financeAgent->id,
            'message' => 'Hello Bob, we have reversed the duplicate charge. Please check your card statement.',
        ]);

        // 4. Verify ticket auto transitioned to 'In Progress' because an agent replied
        $ticket->refresh();
        $this->assertEquals('In Progress', $ticket->status);

        // 5. Verify audit trail log
        $this->assertDatabaseHas('ticket_logs', [
            'ticket_id' => $ticket->id,
            'action' => 'Reply Posted',
        ]);
        $this->assertDatabaseHas('ticket_logs', [
            'ticket_id' => $ticket->id,
            'action' => 'Status Changed',
            'old_value' => 'Open',
            'new_value' => 'In Progress',
        ]);
    }

    /**
     * Test manually changing status, assignee, and verifying SLA breach tracking/analytics.
     */
    public function test_ticket_metadata_updates_and_analytics()
    {
        $customer = Customer::create([
            'name' => 'Charlie Rose',
            'email' => 'charlie@gmail.com',
        ]);

        $ticket = Ticket::create([
            'ticket_key' => 'TKT-20260609-0002',
            'customer_id' => $customer->id,
            'subject' => 'Password reset issue',
            'description' => 'Cannot reset password via recovery page.',
            'status' => 'In Progress',
            'priority' => 'Medium',
            'category' => 'Account',
            'assigned_to_id' => $this->techAgent->id,
            'sla_deadline' => Carbon::now()->subHour(), // Set SLA in the past to simulate breach
        ]);

        // 1. Verify SLA status is breached
        $this->assertTrue(Carbon::now()->gt($ticket->sla_deadline));

        // 2. Update status to Resolved
        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.helpdesk.updateTicket', $ticket), [
                'status' => 'Resolved',
                'assigned_to_id' => $this->financeAgent->id, // Reassign to finance agent
                'priority' => 'High',
            ]);

        $response->assertRedirect();
        $ticket->refresh();

        // 3. Verify status, resolved timestamp, and assignment updates
        $this->assertEquals('Resolved', $ticket->status);
        $this->assertNotNull($ticket->resolved_at);
        $this->assertEquals($this->financeAgent->id, $ticket->assigned_to_id);
        $this->assertEquals('High', $ticket->priority);

        // 4. Verify audit logs contain all update events
        $this->assertDatabaseHas('ticket_logs', [
            'ticket_id' => $ticket->id,
            'action' => 'Status Changed',
            'old_value' => 'In Progress',
            'new_value' => 'Resolved',
        ]);
        $this->assertDatabaseHas('ticket_logs', [
            'ticket_id' => $ticket->id,
            'action' => 'Agent Assigned',
            'new_value' => 'Finance Analyst',
        ]);
        $this->assertDatabaseHas('ticket_logs', [
            'ticket_id' => $ticket->id,
            'action' => 'Priority Updated',
            'new_value' => 'High',
        ]);
    }
}
