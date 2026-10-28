<?php

namespace App\Http\Controllers;

use App\Services\HelpdeskService;
use Illuminate\Http\Request;

class CustomerTicketController extends Controller
{
    protected $helpdeskService;

    public function __construct(HelpdeskService $helpdeskService)
    {
        $this->helpdeskService = $helpdeskService;
    }

    /**
     * Show the form for customers to log a new support ticket.
     */
    public function create()
    {
        return view('portal.create_ticket');
    }

    /**
     * Store a customer-submitted support ticket.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_company' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'category' => 'required|in:Technical,Billing,Account,Feedback,General',
        ]);

        // Customers submit via 'Portal' channel, set creator to null since they are guest/client
        $ticket = $this->helpdeskService->createTicket(
            array_merge($validated, [
                'channel' => 'Portal',
                'creator_id' => null,
            ])
        );

        return redirect()->route('tickets.create')
            ->with('success', "Ticket {$ticket->ticket_key} successfully created and queued for review. Our team will contact you shortly.");
    }
}
