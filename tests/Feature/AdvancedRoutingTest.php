<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Services\HelpdeskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvancedRoutingTest extends TestCase
{
    use RefreshDatabase;

    protected $helpdeskService;
    protected $engDept;
    protected $agentA;
    protected $agentB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helpdeskService = new HelpdeskService();

        // Setup Engineering Department
        $this->engDept = Department::create(['name' => 'Engineering', 'description' => 'Tech Team']);

        // Agent A: Skill [Network], Location [Europe/London]
        $this->agentA = User::factory()->create([
            'name' => 'Agent Network Europe',
            'email' => 'agent.a@company.com',
            'department_id' => $this->engDept->id,
            'is_active' => true,
        ]);
        EmployeeProfile::create([
            'user_id' => $this->agentA->id,
            'skills' => ['Network', 'Linux'],
            'address' => '123 London Road, Europe',
        ]);

        // Agent B: Skill [Database], Location [North America/NY]
        $this->agentB = User::factory()->create([
            'name' => 'Agent Database US',
            'email' => 'agent.b@company.com',
            'department_id' => $this->engDept->id,
            'is_active' => true,
        ]);
        EmployeeProfile::create([
            'user_id' => $this->agentB->id,
            'skills' => ['Database', 'SQL'],
            'address' => '456 New York Ave, North America',
        ]);
    }

    /** @test */
    public function it_routes_based_on_skills_and_geography_rules()
    {
        // Case A: Technical Category + Network Content + Europe Customer (.de domain)
        // Should match Engineering Department, select Agent A (Network Europe)
        $ticketA = $this->helpdeskService->createTicket([
            'customer_name' => 'Hans Schmidt',
            'customer_email' => 'hans@schmidt.de',
            'subject' => 'Cannot connect to VPN network gateway',
            'description' => 'Getting timeout errors when establishing connection.',
            'category' => 'Technical',
            'priority' => 'Medium',
        ]);

        $this->assertEquals($this->agentA->id, $ticketA->assigned_to_id);

        // Case B: Technical Category + Database Content + US Customer (.us domain)
        // Should match Engineering Department, select Agent B (Database US)
        $ticketB = $this->helpdeskService->createTicket([
            'customer_name' => 'John Wayne',
            'customer_email' => 'john@wayne.us',
            'subject' => 'Database sql query is extremely slow',
            'description' => 'Postgres database connection pool is full.',
            'category' => 'Technical',
            'priority' => 'High',
        ]);

        $this->assertEquals($this->agentB->id, $ticketB->assigned_to_id);
    }

    /** @test */
    public function it_routes_based_on_workload_load_balancing()
    {
        // Assign 1 ticket to Agent A (Network Europe)
        $ticket1 = $this->helpdeskService->createTicket([
            'customer_name' => 'Customer 1',
            'customer_email' => 'cust1@germany.eu',
            'subject' => 'Network VPN error',
            'description' => 'Fix connection issue.',
            'category' => 'Technical',
        ]);

        $this->assertEquals($this->agentA->id, $ticket1->assigned_to_id);
        $this->assertEquals(1, $this->agentA->assignedTickets()->whereIn('status', ['Open', 'In Progress'])->count());
        $this->assertEquals(0, $this->agentB->assignedTickets()->whereIn('status', ['Open', 'In Progress'])->count());

        // Create a new general/fallback engineering ticket (which matches both Agent A and Agent B equally)
        // Should load balance and assign to Agent B because Agent B has 0 tickets compared to Agent A's 1 ticket.
        $ticket2 = $this->helpdeskService->createTicket([
            'customer_name' => 'Customer 2',
            'customer_email' => 'cust2@company.com',
            'subject' => 'General engineering bug',
            'description' => 'A random web issue.',
            'category' => 'Technical',
        ]);

        $this->assertEquals($this->agentB->id, $ticket2->assigned_to_id);
    }
}
