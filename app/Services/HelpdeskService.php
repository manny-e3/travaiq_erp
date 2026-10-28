<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\TicketLog;
use App\Models\User;
use App\Models\Department;
use App\Models\SlaPolicy;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HelpdeskService
{
    /**
     * Create a new ticket with automatic customer creation, SLA calculation, and intelligent routing.
     */
    public function createTicket(array $data): Ticket
    {
        $ticket = DB::transaction(function () use ($data) {
            // 1. Identify or Create Customer
            $customer = Customer::firstOrCreate(
                ['email' => $data['customer_email']],
                [
                    'name' => $data['customer_name'],
                    'phone' => $data['customer_phone'] ?? null,
                    'company' => $data['customer_company'] ?? null,
                    'tier' => $data['customer_tier'] ?? 'Standard',
                ]
            );

            // If tier is specified and different, update customer
            if (isset($data['customer_tier']) && $data['customer_tier'] !== $customer->tier) {
                $customer->update(['tier' => $data['customer_tier']]);
            }

            // 2. Generate Unique Ticket Key: TKT-YYYYMMDD-XXXX
            $keyPrefix = 'TKT-' . date('Ymd') . '-';
            $uniqueKey = $keyPrefix . str_pad(mt_rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            while (Ticket::where('ticket_key', $uniqueKey)->exists()) {
                $uniqueKey = $keyPrefix . str_pad(mt_rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            }

            // 3. SLA Deadlines Calculation
            $priority = $data['priority'] ?? 'Medium';
            $category = $data['category'] ?? 'General';
            
            $policy = $this->findMatchingPolicy($customer->tier, $priority, $category);
            
            if ($policy) {
                $responseHours = (float) $policy->response_time_hours;
                $resolutionHours = (float) $policy->resolution_time_hours;
                $slaPolicyId = $policy->id;
            } else {
                // Default fallback targets
                $responseHours = match ($priority) {
                    'Urgent' => 2.0,
                    'High' => 4.0,
                    'Medium' => 8.0,
                    'Low' => 24.0,
                    default => 8.0,
                };
                $resolutionHours = match ($priority) {
                    'Urgent' => 2.0,
                    'High' => 12.0,
                    'Medium' => 24.0,
                    'Low' => 48.0,
                    default => 24.0,
                };
                $slaPolicyId = null;
            }

            $responseDeadline = Carbon::now()->addMinutes((int)($responseHours * 60));
            $slaDeadline = Carbon::now()->addMinutes((int)($resolutionHours * 60));

            // 4. Intelligent Routing
            $assignedAgent = $this->determineIntelligentAgent($category, $data['description'] ?? '', $customer);

            // 5. Create Ticket
            $ticket = Ticket::create([
                'ticket_key' => $uniqueKey,
                'customer_id' => $customer->id,
                'subject' => $data['subject'],
                'description' => $data['description'],
                'status' => 'Open',
                'priority' => $priority,
                'category' => $category,
                'channel' => $data['channel'] ?? 'Portal',
                'assigned_to_id' => $assignedAgent?->id,
                'response_deadline' => $responseDeadline,
                'sla_deadline' => $slaDeadline,
                'sla_policy_id' => $slaPolicyId,
            ]);

            // 6. Audit Logs
            TicketLog::create([
                'ticket_id' => $ticket->id,
                'user_id' => $data['creator_id'] ?? null,
                'action' => 'Ticket Created',
                'new_value' => $uniqueKey,
            ]);

            if ($assignedAgent) {
                TicketLog::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => null,
                    'action' => 'Auto Routed Agent',
                    'new_value' => $assignedAgent->name . ' (Intelligent Routing)',
                ]);
            }

            return $ticket;
        });

        // Dispatch email notifications outside transaction
        try {
            \Illuminate\Support\Facades\Mail::to($ticket->customer->email)->queue(new \App\Mail\TicketAcknowledgedMail($ticket));
            
            if ($ticket->assignedTo) {
                \Illuminate\Support\Facades\Mail::to($ticket->assignedTo->email)->queue(new \App\Mail\TicketAssignedMail($ticket));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("SLA Ticket notifications failed: " . $e->getMessage());
        }

        return $ticket;
    }

    /**
     * Find the best matching active SLA policy based on tier, category, and priority.
     */
    public function findMatchingPolicy(string $tier, string $priority, ?string $category): ?SlaPolicy
    {
        // 1. Match Tier + Category + Priority
        if ($category) {
            $policy = SlaPolicy::where('is_active', true)
                ->where('customer_tier', $tier)
                ->where('category', $category)
                ->where('priority', $priority)
                ->first();
            if ($policy) {
                return $policy;
            }
        }

        // 2. Match Tier + Priority (any category)
        $policy = SlaPolicy::where('is_active', true)
            ->where('customer_tier', $tier)
            ->whereNull('category')
            ->where('priority', $priority)
            ->first();
        if ($policy) {
            return $policy;
        }

        // 3. Match Priority only (any tier, any category)
        $policy = SlaPolicy::where('is_active', true)
            ->whereNull('customer_tier')
            ->whereNull('category')
            ->where('priority', $priority)
            ->first();
        if ($policy) {
            return $policy;
        }

        return null;
    }

    /**
     * Submit a reply to a ticket. Updates status to In Progress if agent replies.
     */
    public function addReply(Ticket $ticket, string $message, ?User $actor): TicketReply
    {
        return DB::transaction(function () use ($ticket, $message, $actor) {
            $reply = TicketReply::create([
                'ticket_id' => $ticket->id,
                'user_id' => $actor?->id,
                'message' => $message,
            ]);

            TicketLog::create([
                'ticket_id' => $ticket->id,
                'user_id' => $actor?->id,
                'action' => 'Reply Posted',
                'new_value' => $actor ? 'Agent reply' : 'Customer reply',
            ]);

            // If an agent replies and first_responded_at is null, calculate Response SLA
            if ($actor && is_null($ticket->first_responded_at)) {
                $now = Carbon::now();
                $ticket->first_responded_at = $now;

                $isBreached = $ticket->response_deadline ? $now->gt($ticket->response_deadline) : false;
                if ($isBreached) {
                    $ticket->is_sla_breached = true;
                    TicketLog::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $actor->id,
                        'action' => 'Response SLA Breached',
                        'old_value' => $ticket->response_deadline->format('Y-m-d H:i:s'),
                        'new_value' => $now->format('Y-m-d H:i:s'),
                    ]);
                } else {
                    TicketLog::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $actor->id,
                        'action' => 'Response SLA Achieved',
                        'new_value' => $now->format('Y-m-d H:i:s'),
                    ]);
                }
                $ticket->save();
            }

            // If an agent (actor is present) replies to an Open ticket, auto transition to In Progress
            if ($actor && $ticket->status === 'Open') {
                $this->updateStatus($ticket, 'In Progress', $actor);
            }

            return $reply;
        });
    }

    /**
     * Update ticket status and set resolution time if resolved/closed.
     */
    public function updateStatus(Ticket $ticket, string $status, ?User $actor): void
    {
        DB::transaction(function () use ($ticket, $status, $actor) {
            $oldStatus = $ticket->status;
            if ($oldStatus === $status) {
                return;
            }

            $resolvedAt = null;
            if (in_array($status, ['Resolved', 'Closed'])) {
                $now = Carbon::now();
                $resolvedAt = $now;

                if ($ticket->status !== 'Resolved' && $ticket->status !== 'Closed') {
                    $isBreached = $ticket->sla_deadline ? $now->gt($ticket->sla_deadline) : false;
                    if ($isBreached) {
                        $ticket->is_sla_breached = true;
                        TicketLog::create([
                            'ticket_id' => $ticket->id,
                            'user_id' => $actor?->id,
                            'action' => 'Resolution SLA Breached',
                            'old_value' => $ticket->sla_deadline->format('Y-m-d H:i:s'),
                            'new_value' => $now->format('Y-m-d H:i:s'),
                        ]);
                    } else {
                        TicketLog::create([
                            'ticket_id' => $ticket->id,
                            'user_id' => $actor?->id,
                            'action' => 'Resolution SLA Achieved',
                            'new_value' => $now->format('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }

            $ticket->update([
                'status' => $status,
                'resolved_at' => $resolvedAt ?: $ticket->resolved_at,
            ]);

            TicketLog::create([
                'ticket_id' => $ticket->id,
                'user_id' => $actor?->id,
                'action' => 'Status Changed',
                'old_value' => $oldStatus,
                'new_value' => $status,
            ]);
        });
    }

    /**
     * Manually assign agent to a ticket.
     */
    public function assignAgent(Ticket $ticket, User $agent, ?User $actor): void
    {
        DB::transaction(function () use ($ticket, $agent, $actor) {
            $oldAgentName = $ticket->assignedTo?->name ?? 'Unassigned';

            $ticket->update([
                'assigned_to_id' => $agent->id,
            ]);

            TicketLog::create([
                'ticket_id' => $ticket->id,
                'user_id' => $actor?->id,
                'action' => 'Agent Assigned',
                'old_value' => $oldAgentName,
                'new_value' => $agent->name,
            ]);
        });

        // Notify assigned agent
        try {
            $ticket->refresh();
            if ($ticket->assignedTo) {
                \Illuminate\Support\Facades\Mail::to($ticket->assignedTo->email)->queue(new \App\Mail\TicketAssignedMail($ticket));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("SLA Agent manual assignment notification failed: " . $e->getMessage());
        }
    }

    /**
     * Determine support agent based on workload, category routing rules, skill requirements, and customer geography.
     */
    protected function determineIntelligentAgent(string $category, string $description = '', ?Customer $customer = null): ?User
    {
        // 1. Rule-Based Routing: Map category to target Department
        $targetDeptName = match ($category) {
            'Technical' => 'Engineering',
            'Billing', 'Account' => 'Finance',
            'Feedback', 'General' => 'Customer Support',
            default => null,
        };

        $query = User::where('is_active', true)->with(['department', 'profile']);

        if ($targetDeptName) {
            $query->whereHas('department', function ($q) use ($targetDeptName) {
                $q->where('name', $targetDeptName);
            });
        }

        $candidates = $query->get();

        // Fallback if no matching agents exist in target department
        if ($candidates->isEmpty()) {
            $candidates = User::where('is_active', true)->with(['department', 'profile'])->get();
        }

        if ($candidates->isEmpty()) {
            return null;
        }

        // 2. Geography-Based Routing: Detect customer region and match against agent address region
        $customerRegion = null;
        if ($customer) {
            $email = strtolower($customer->email);
            if (str_ends_with($email, '.uk') || str_ends_with($email, '.de') || str_ends_with($email, '.fr') || str_ends_with($email, '.eu')) {
                $customerRegion = 'Europe';
            } elseif (str_ends_with($email, '.jp') || str_ends_with($email, '.cn') || str_ends_with($email, '.au') || str_ends_with($email, '.ap')) {
                $customerRegion = 'APAC';
            } elseif (str_ends_with($email, '.us') || str_ends_with($email, '.ca')) {
                $customerRegion = 'North America';
            }
        }

        if ($customerRegion) {
            $geoMatches = $candidates->filter(function ($user) use ($customerRegion) {
                return $user->profile && $user->profile->address && str_contains(strtolower($user->profile->address), strtolower($customerRegion));
            });
            if ($geoMatches->isNotEmpty()) {
                $candidates = $geoMatches;
            }
        }

        // 3. Skills-Based Routing: Match ticket keyword requirements against agent profile skills
        $requiredSkill = null;
        $text = strtolower($category . ' ' . $description);
        
        if (str_contains($text, 'network') || str_contains($text, 'connection') || str_contains($text, 'vpn') || str_contains($text, 'dns')) {
            $requiredSkill = 'Network';
        } elseif (str_contains($text, 'database') || str_contains($text, 'sql') || str_contains($text, 'query') || str_contains($text, 'postgres')) {
            $requiredSkill = 'Database';
        } elseif (str_contains($text, 'billing') || str_contains($text, 'invoice') || str_contains($text, 'refund') || str_contains($text, 'checkout')) {
            $requiredSkill = 'Billing';
        } elseif (str_contains($text, 'api') || str_contains($text, 'webhook') || str_contains($text, 'integration')) {
            $requiredSkill = 'API';
        }

        if ($requiredSkill) {
            $skillsMatches = $candidates->filter(function ($user) use ($requiredSkill) {
                return $user->profile && is_array($user->profile->skills) && in_array($requiredSkill, $user->profile->skills);
            });
            if ($skillsMatches->isNotEmpty()) {
                $candidates = $skillsMatches;
            }
        }

        // 4. Load Balancing: Route to candidate with the lowest open/in-progress ticket count
        return $candidates->sortBy(function ($user) {
            return $user->assignedTickets()
                ->whereIn('status', ['Open', 'In Progress'])
                ->count();
        })->first();
    }

    /**
     * Fetch helpdesk resolution and SLA compliance analytics.
     */
    public function getAnalytics(): array
    {
        $totalTickets = Ticket::count();
        $openTickets = Ticket::whereIn('status', ['Open', 'In Progress'])->count();
        $resolvedClosed = Ticket::whereIn('status', ['Resolved', 'Closed'])->get();

        $slaCompliant = 0;
        $totalResolved = $resolvedClosed->count();
        $resolutionTimeSum = 0;

        foreach ($resolvedClosed as $t) {
            if ($t->resolved_at && $t->sla_deadline && $t->resolved_at->lte($t->sla_deadline)) {
                $slaCompliant++;
            }
            if ($t->resolved_at) {
                $resolutionTimeSum += $t->created_at->diffInHours($t->resolved_at);
            }
        }

        $slaRate = $totalResolved > 0 ? round(($slaCompliant / $totalResolved) * 100, 1) : 100.0;
        $avgResolutionTime = $totalResolved > 0 ? round($resolutionTimeSum / $totalResolved, 1) : 0.0;

        $categoriesBreakdown = Ticket::selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        $prioritiesBreakdown = Ticket::selectRaw('priority, count(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        $channelsBreakdown = Ticket::selectRaw('channel, count(*) as count')
            ->groupBy('channel')
            ->pluck('count', 'channel')
            ->toArray();

        $agents = User::where('is_active', true)->withCount(['assignedTickets' => function ($q) {
            $q->whereIn('status', ['Open', 'In Progress']);
        }])->get();

        $agentWorkloads = [];
        foreach ($agents as $agent) {
            if ($agent->assigned_tickets_count > 0) {
                $agentWorkloads[$agent->name] = $agent->assigned_tickets_count;
            }
        }

        return [
            'total_tickets' => $totalTickets,
            'open_tickets' => $openTickets,
            'closed_tickets' => $totalResolved,
            'sla_compliance_rate' => $slaRate,
            'avg_resolution_hours' => $avgResolutionTime,
            'categories' => $categoriesBreakdown,
            'priorities' => $prioritiesBreakdown,
            'channels' => $channelsBreakdown,
            'agent_workloads' => $agentWorkloads,
        ];
    }

    /**
     * Classify email subject and body into a valid ticket category.
     */
    public function classifyEmail(string $subject, string $body): string
    {
        $text = strtolower($subject . ' ' . $body);

        if (str_contains($text, 'invoice') || str_contains($text, 'billing') || str_contains($text, 'charge') || str_contains($text, 'payment') || str_contains($text, 'receipt') || str_contains($text, 'refund') || str_contains($text, 'pricing') || str_contains($text, 'subscription') || str_contains($text, 'pay')) {
            return 'Billing';
        }

        if (str_contains($text, 'bug') || str_contains($text, 'crash') || str_contains($text, 'error') || str_contains($text, 'broken') || str_contains($text, 'failure') || str_contains($text, 'technical') || str_contains($text, 'install') || str_contains($text, 'setup') || str_contains($text, 'download') || str_contains($text, 'issue') || str_contains($text, 'not working') || str_contains($text, 'slow') || str_contains($text, 'crashed')) {
            return 'Technical';
        }

        if (str_contains($text, 'password') || str_contains($text, 'profile') || str_contains($text, 'username') || str_contains($text, 'login') || str_contains($text, 'reset') || str_contains($text, 'register') || str_contains($text, 'signup') || str_contains($text, 'delete account') || str_contains($text, 'deactivate') || str_contains($text, 'credentials') || str_contains($text, 'auth')) {
            return 'Account';
        }

        if (str_contains($text, 'feedback') || str_contains($text, 'suggestion') || str_contains($text, 'idea') || str_contains($text, 'review') || str_contains($text, 'improvement') || str_contains($text, 'recommendation')) {
            return 'Feedback';
        }

        return 'General';
    }
}
