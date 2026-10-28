<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Services\HelpdeskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HelpdeskController extends Controller
{
    protected $helpdeskService;

    public function __construct(HelpdeskService $helpdeskService)
    {
        $this->helpdeskService = $helpdeskService;
    }

    /**
     * Display a listing of tickets and support analytics.
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['customer', 'assignedTo'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $tickets = $query->paginate(15);
        $analytics = $this->helpdeskService->getAnalytics();
        $agents = User::where('is_active', true)->get();

        return view('admin.helpdesk.index', compact('tickets', 'analytics', 'agents'));
    }

    /**
     * Store a newly created ticket.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_company' => 'nullable|string|max:255',
            'customer_tier' => 'sometimes|required|in:Standard,Gold,Platinum',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'category' => 'required|in:Technical,Billing,Account,Feedback,General',
            'channel' => 'required|in:Email,Phone,Portal,API',
        ]);

        $ticket = $this->helpdeskService->createTicket(
            array_merge($validated, ['creator_id' => Auth::id()])
        );

        return redirect()->route('admin.helpdesk.show', $ticket)
            ->with('success', "Ticket {$ticket->ticket_key} successfully logged and intelligently routed.");
    }

    /**
     * Display the specified ticket with conversation thread and audit logs.
     */
    public function show(Ticket $ticket)
    {
        $ticket->load(['customer', 'assignedTo', 'replies.user', 'logs.user']);
        $agents = User::where('is_active', true)->get();
        $articles = \App\Models\KbArticle::published()->orderBy('title')->get();

        return view('admin.helpdesk.show', compact('ticket', 'agents', 'articles'));
    }

    /**
     * Submit a reply message on the ticket.
     */
    public function reply(Request $request, Ticket $ticket)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $this->helpdeskService->addReply($ticket, $request->message, Auth::user());

        return redirect()->back()->with('success', 'Reply successfully sent.');
    }

    /**
     * Update ticket attributes (priority, category, assignee, status).
     */
    public function updateTicket(Request $request, Ticket $ticket)
    {
        $request->validate([
            'priority' => 'nullable|in:Low,Medium,High,Urgent',
            'category' => 'nullable|in:Technical,Billing,Account,Feedback,General',
            'status' => 'nullable|in:Open,In Progress,Resolved,Closed',
            'assigned_to_id' => 'nullable|exists:users,id',
        ]);

        DB::transaction(function () use ($request, $ticket) {
            $user = Auth::user();

            // Status Update
            if ($request->filled('status') && $request->status !== $ticket->status) {
                $this->helpdeskService->updateStatus($ticket, $request->status, $user);
            }

            // Assignee Update
            if ($request->has('assigned_to_id') && (int)$request->assigned_to_id !== (int)$ticket->assigned_to_id) {
                if ($request->filled('assigned_to_id')) {
                    $agent = User::find($request->assigned_to_id);
                    $this->helpdeskService->assignAgent($ticket, $agent, $user);
                } else {
                    $oldAgentName = $ticket->assignedTo?->name ?? 'Unassigned';
                    $ticket->update(['assigned_to_id' => null]);
                    \App\Models\TicketLog::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $user->id,
                        'action' => 'Agent Unassigned',
                        'old_value' => $oldAgentName,
                        'new_value' => 'Unassigned',
                    ]);
                }
            }

            // Priority Update
            if ($request->filled('priority') && $request->priority !== $ticket->priority) {
                $oldPriority = $ticket->priority;
                $ticket->update(['priority' => $request->priority]);
                \App\Models\TicketLog::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'action' => 'Priority Updated',
                    'old_value' => $oldPriority,
                    'new_value' => $request->priority,
                ]);
            }

            // Category Update
            if ($request->filled('category') && $request->category !== $ticket->category) {
                $oldCategory = $ticket->category;
                $ticket->update(['category' => $request->category]);
                \App\Models\TicketLog::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'action' => 'Category Updated',
                    'old_value' => $oldCategory,
                    'new_value' => $request->category,
                ]);
            }
        });

        return redirect()->back()->with('success', 'Ticket settings updated successfully.');
    }
}
