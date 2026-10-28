<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Department;
use App\Services\HelpdeskService;
use App\Mail\TicketAcknowledgedMail;
use App\Mail\TicketAssignedMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TicketNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected $helpdeskService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helpdeskService = new HelpdeskService();
    }

    /** @test */
    public function it_dispatches_notifications_on_ticket_creation()
    {
        Mail::fake();

        // Create routing target department & active agent
        $engDept = Department::create(['name' => 'Engineering', 'description' => 'Tech Team']);
        $agent = User::factory()->create([
            'name' => 'Tech Expert',
            'email' => 'expert@tech.com',
            'department_id' => $engDept->id,
            'is_active' => true,
        ]);

        $ticket = $this->helpdeskService->createTicket([
            'customer_name' => 'Sarah Connor',
            'customer_email' => 'sarah@connor.com',
            'subject' => 'Connection refused on login page',
            'description' => 'The screen is throwing 500 error.',
            'category' => 'Technical',
            'priority' => 'High',
            'channel' => 'Portal',
        ]);

        // Assert customer email notification sent
        Mail::assertQueued(TicketAcknowledgedMail::class, function ($mail) use ($ticket) {
            return $mail->hasTo('sarah@connor.com') && $mail->ticket->id === $ticket->id;
        });

        // Assert agent assignment notification sent
        Mail::assertQueued(TicketAssignedMail::class, function ($mail) use ($ticket, $agent) {
            return $mail->hasTo('expert@tech.com') && $mail->ticket->id === $ticket->id;
        });
    }

    /** @test */
    public function it_dispatches_notification_on_manual_agent_assignment()
    {
        Mail::fake();

        $engDept = Department::create(['name' => 'Engineering', 'description' => 'Tech Team']);
        $agent1 = User::factory()->create([
            'name' => 'Agent One',
            'email' => 'one@tech.com',
            'department_id' => $engDept->id,
            'is_active' => true,
        ]);

        $agent2 = User::factory()->create([
            'name' => 'Agent Two',
            'email' => 'two@tech.com',
            'department_id' => $engDept->id,
            'is_active' => true,
        ]);

        // Create ticket (will auto-route to agent1 due to workload)
        $ticket = $this->helpdeskService->createTicket([
            'customer_name' => 'Sarah Connor',
            'customer_email' => 'sarah@connor.com',
            'subject' => 'Connection refused',
            'description' => 'Error 500.',
            'category' => 'Technical',
            'priority' => 'High',
        ]);

        Mail::assertQueued(TicketAssignedMail::class, function ($mail) use ($agent1) {
            return $mail->hasTo('one@tech.com');
        });

        // Manually assign agent2
        $this->helpdeskService->assignAgent($ticket, $agent2, null);

        // Assert assignment notification was dispatched to agent2
        Mail::assertQueued(TicketAssignedMail::class, function ($mail) use ($agent2) {
            return $mail->hasTo('two@tech.com');
        });
    }
}
