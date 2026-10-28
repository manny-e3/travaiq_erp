<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\SlaPolicy;
use App\Models\Ticket;
use App\Models\TicketLog;
use App\Models\User;
use App\Services\HelpdeskService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlaManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $helpdeskService;
    protected $agent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helpdeskService = new HelpdeskService();
        $this->agent = User::factory()->create(['is_active' => true]);
        Carbon::setTestNow(Carbon::create(2026, 6, 9, 10, 0, 0));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /** @test */
    public function it_matches_the_most_specific_sla_policy()
    {
        // 1. Create specific Gold Tier Urgent Technical policy
        $goldUrgentTechPolicy = SlaPolicy::create([
            'name' => 'Gold Urgent Tech Policy',
            'customer_tier' => 'Gold',
            'category' => 'Technical',
            'priority' => 'Urgent',
            'response_time_hours' => 0.5, // 30 minutes
            'resolution_time_hours' => 2.0, // 2 hours
            'is_active' => true,
        ]);

        // 2. Create generic Gold Tier Urgent policy (no category)
        $goldUrgentPolicy = SlaPolicy::create([
            'name' => 'Gold Urgent Generic Policy',
            'customer_tier' => 'Gold',
            'category' => null,
            'priority' => 'Urgent',
            'response_time_hours' => 1.0,
            'resolution_time_hours' => 4.0,
            'is_active' => true,
        ]);

        // 3. Create fall-back Urgent policy (any tier, any category)
        $globalUrgentPolicy = SlaPolicy::create([
            'name' => 'Global Urgent Policy',
            'customer_tier' => 'Standard',
            'category' => null,
            'priority' => 'Urgent',
            'response_time_hours' => 2.0,
            'resolution_time_hours' => 6.0,
            'is_active' => true,
        ]);

        // Test Case A: Specific Gold Technical match
        $ticketA = $this->helpdeskService->createTicket([
            'customer_name' => 'John Gold',
            'customer_email' => 'john@gold.com',
            'customer_tier' => 'Gold',
            'subject' => 'System Down',
            'description' => 'Server is crashed.',
            'category' => 'Technical',
            'priority' => 'Urgent',
        ]);

        $this->assertEquals($goldUrgentTechPolicy->id, $ticketA->sla_policy_id);
        $this->assertEquals(30, Carbon::now()->diffInMinutes($ticketA->response_deadline));
        $this->assertEquals(120, Carbon::now()->diffInMinutes($ticketA->sla_deadline));

        // Test Case B: Gold Billing match (should fall back to Gold Urgent Generic Policy)
        $ticketB = $this->helpdeskService->createTicket([
            'customer_name' => 'Jane Gold',
            'customer_email' => 'jane@gold.com',
            'customer_tier' => 'Gold',
            'subject' => 'Overcharged',
            'description' => 'I was double charged.',
            'category' => 'Billing',
            'priority' => 'Urgent',
        ]);

        $this->assertEquals($goldUrgentPolicy->id, $ticketB->sla_policy_id);
        $this->assertEquals(60, Carbon::now()->diffInMinutes($ticketB->response_deadline));
        $this->assertEquals(240, Carbon::now()->diffInMinutes($ticketB->sla_deadline));
    }

    /** @test */
    public function it_falls_back_to_defaults_if_no_policies_match()
    {
        $ticket = $this->helpdeskService->createTicket([
            'customer_name' => 'Standard Cust',
            'customer_email' => 'standard@cust.com',
            'customer_tier' => 'Standard',
            'subject' => 'General Inquiry',
            'description' => 'Just asking some questions.',
            'category' => 'General',
            'priority' => 'Medium',
        ]);

        $this->assertNull($ticket->sla_policy_id);
        // Fallback for Medium: 8h response, 24h resolution
        $this->assertEquals(480, Carbon::now()->diffInMinutes($ticket->response_deadline));
        $this->assertEquals(1440, Carbon::now()->diffInMinutes($ticket->sla_deadline));
    }

    /** @test */
    public function it_tracks_first_response_sla_and_flags_breaches()
    {
        $ticket = $this->helpdeskService->createTicket([
            'customer_name' => 'SLA Cust',
            'customer_email' => 'sla@cust.com',
            'subject' => 'Test Response',
            'description' => 'Need response.',
            'priority' => 'Urgent',
        ]);

        // Urgent fallback response deadline is 2 hours (12:00:00)
        $this->assertEquals(Carbon::create(2026, 6, 9, 12, 0, 0), $ticket->response_deadline);

        // Case A: Reply before deadline (11:00:00)
        Carbon::setTestNow(Carbon::create(2026, 6, 9, 11, 0, 0));
        $this->helpdeskService->addReply($ticket, 'Happy to help!', $this->agent);

        $ticket->refresh();
        $this->assertEquals(Carbon::create(2026, 6, 9, 11, 0, 0), $ticket->first_responded_at);
        $this->assertFalse($ticket->is_sla_breached);
        $this->assertTrue(TicketLog::where('ticket_id', $ticket->id)->where('action', 'Response SLA Achieved')->exists());

        // Case B: Second reply after deadline does not update first_responded_at or trigger breach
        Carbon::setTestNow(Carbon::create(2026, 6, 9, 13, 0, 0));
        $this->helpdeskService->addReply($ticket, 'Follow up message', $this->agent);
        $ticket->refresh();
        $this->assertEquals(Carbon::create(2026, 6, 9, 11, 0, 0), $ticket->first_responded_at);
        $this->assertFalse($ticket->is_sla_breached);

        // Case C: New ticket, reply after deadline (13:00:00)
        Carbon::setTestNow(Carbon::create(2026, 6, 9, 10, 0, 0));
        $ticket2 = $this->helpdeskService->createTicket([
            'customer_name' => 'SLA Cust 2',
            'customer_email' => 'sla2@cust.com',
            'subject' => 'Test Response 2',
            'description' => 'Need response fast.',
            'priority' => 'Urgent',
        ]);

        Carbon::setTestNow(Carbon::create(2026, 6, 9, 13, 0, 0));
        $this->helpdeskService->addReply($ticket2, 'Sorry for delay.', $this->agent);

        $ticket2->refresh();
        $this->assertEquals(Carbon::create(2026, 6, 9, 13, 0, 0), $ticket2->first_responded_at);
        $this->assertTrue($ticket2->is_sla_breached);
        $this->assertTrue(TicketLog::where('ticket_id', $ticket2->id)->where('action', 'Response SLA Breached')->exists());
    }

    /** @test */
    public function it_tracks_resolution_sla_and_flags_breaches()
    {
        $ticket = $this->helpdeskService->createTicket([
            'customer_name' => 'Resolve Cust',
            'customer_email' => 'resolve@cust.com',
            'subject' => 'Resolution Target',
            'description' => 'Test resolution target.',
            'priority' => 'Urgent',
        ]);

        // Urgent fallback resolution deadline is 2 hours (12:00:00)
        $this->assertEquals(Carbon::create(2026, 6, 9, 12, 0, 0), $ticket->sla_deadline);

        // Case A: Resolve after deadline (15:00:00)
        Carbon::setTestNow(Carbon::create(2026, 6, 9, 15, 0, 0));
        $this->helpdeskService->updateStatus($ticket, 'Resolved', $this->agent);

        $ticket->refresh();
        $this->assertEquals(Carbon::create(2026, 6, 9, 15, 0, 0), $ticket->resolved_at);
        $this->assertTrue($ticket->is_sla_breached);
        $this->assertTrue(TicketLog::where('ticket_id', $ticket->id)->where('action', 'Resolution SLA Breached')->exists());
    }
}
