<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Department;
use App\Services\HelpdeskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class InboundEmailTest extends TestCase
{
    use RefreshDatabase;

    protected $helpdeskService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helpdeskService = new HelpdeskService();

        // Create routing target departments & active agents
        $engDept = Department::create(['name' => 'Engineering', 'description' => 'Tech Team']);
        $finDept = Department::create(['name' => 'Finance', 'description' => 'Billing Team']);

        User::factory()->create([
            'name' => 'Tech Agent',
            'department_id' => $engDept->id,
            'is_active' => true,
        ]);

        User::factory()->create([
            'name' => 'Billing Agent',
            'department_id' => $finDept->id,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_classifies_email_content_correctly()
    {
        // Billing keywords
        $categoryA = $this->helpdeskService->classifyEmail('Invoice payment query', 'Please send me the pricing for subscription');
        $this->assertEquals('Billing', $categoryA);

        // Technical keywords
        $categoryB = $this->helpdeskService->classifyEmail('Server crashed', 'I am seeing database connection error on login');
        $this->assertEquals('Technical', $categoryB);

        // Account keywords
        $categoryC = $this->helpdeskService->classifyEmail('Reset password', 'How do I change my profile login info');
        $this->assertEquals('Account', $categoryC);

        // Feedback keywords
        $categoryD = $this->helpdeskService->classifyEmail('Product suggestion', 'Here is my idea for dashboard improvements');
        $this->assertEquals('Feedback', $categoryD);

        // General
        $categoryE = $this->helpdeskService->classifyEmail('Hello', 'Just saying hi.');
        $this->assertEquals('General', $categoryE);
    }

    /** @test */
    public function it_runs_inbound_email_artisan_command_in_mock_mode()
    {
        $this->assertEquals(0, Ticket::count());

        // Run the fetch command in mock mode
        Artisan::call('helpdesk:fetch-emails', ['--mock' => true]);

        // Mock mode processes exactly 3 mock emails
        $this->assertEquals(3, Ticket::count());

        // Assert Billing ticket was created and routed to Finance Agent
        $billingTicket = Ticket::where('category', 'Billing')->first();
        $this->assertNotNull($billingTicket);
        $this->assertEquals('Email', $billingTicket->channel);
        $this->assertEquals('Billing Agent', $billingTicket->assignedTo->name);

        // Assert Technical ticket was created and routed to Tech Agent
        $techTicket = Ticket::where('category', 'Technical')->first();
        $this->assertNotNull($techTicket);
        $this->assertEquals('Email', $techTicket->channel);
        $this->assertEquals('Tech Agent', $techTicket->assignedTo->name);
    }

    /** @test */
    public function it_submits_ticket_through_public_customer_portal()
    {
        $response = $this->get(route('tickets.create'));
        $response->assertStatus(200);
        $response->assertSee('Customer Support Portal');

        $this->assertEquals(0, Ticket::count());

        $postData = [
            'customer_name' => 'Alice Cooper',
            'customer_email' => 'alice@cooper.com',
            'customer_phone' => '555-901-2834',
            'customer_company' => 'Cooper Agency',
            'subject' => 'Cannot view my dashboard reports',
            'description' => 'The screen is blank when I navigate to goals section.',
            'category' => 'Technical',
            'priority' => 'High',
        ];

        $submitResponse = $this->post(route('tickets.store'), $postData);

        // Assert redirect back
        $submitResponse->assertRedirect(route('tickets.create'));

        $this->assertEquals(1, Ticket::count());

        $ticket = Ticket::first();
        $this->assertEquals('Cannot view my dashboard reports', $ticket->subject);
        $this->assertEquals('Portal', $ticket->channel);
        $this->assertEquals('Technical', $ticket->category);

        // Verify routed correctly to Tech Agent
        $this->assertEquals('Tech Agent', $ticket->assignedTo->name);

        // Verify customer was created
        $customer = Customer::where('email', 'alice@cooper.com')->first();
        $this->assertNotNull($customer);
        $this->assertEquals('Alice Cooper', $customer->name);
        $this->assertEquals('Standard', $customer->tier); // Default tier
    }
}
