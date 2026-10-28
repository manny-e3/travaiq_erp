@extends('emails.layout')

@section('content')
<h2 class="h1">New Ticket Assigned</h2>
<p>Dear {{ $ticket->assignedTo->name }},</p>
<p>A new support ticket has been assigned to you. Please review the details below and handle it in accordance with the SLA targets.</p>

<div style="background-color: #f8f9fa; border: 1px solid #e9ebee; border-radius: 6px; padding: 20px; margin: 20px 0;">
    <h3 style="margin-top: 0; color: #364a63; font-size: 16px;">Ticket Details</h3>
    <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
        <tr>
            <td style="padding: 6px 0; width: 120px; font-weight: bold; color: #8094ae;">Ticket ID:</td>
            <td style="padding: 6px 0; font-weight: bold; color: #364a63;">{{ $ticket->ticket_key }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; font-weight: bold; color: #8094ae;">Customer:</td>
            <td style="padding: 6px 0; color: #526484;">{{ $ticket->customer->name }} ({{ $ticket->customer->tier }} Tier)</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; font-weight: bold; color: #8094ae;">Subject:</td>
            <td style="padding: 6px 0; color: #526484;">{{ $ticket->subject }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; font-weight: bold; color: #8094ae;">Category:</td>
            <td style="padding: 6px 0; color: #526484;"><span style="background-color: #e3e7fc; color: #3f51b5; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">{{ $ticket->category }}</span></td>
        </tr>
        <tr>
            <td style="padding: 6px 0; font-weight: bold; color: #8094ae;">Priority:</td>
            <td style="padding: 6px 0; color: #526484;">
                @if($ticket->priority === 'Urgent')
                    <span style="color: #e85347; font-weight: bold;">Urgent</span>
                @elseif($ticket->priority === 'High')
                    <span style="color: #f4bd0e; font-weight: bold;">High</span>
                @else
                    <span>{{ $ticket->priority }}</span>
                @endif
            </td>
        </tr>
        @if($ticket->response_deadline)
        <tr>
            <td style="padding: 6px 0; font-weight: bold; color: #8094ae;">Response Due:</td>
            <td style="padding: 6px 0; color: #e85347; font-weight: 600;">{{ $ticket->response_deadline->format('M d, Y h:i A') }}</td>
        </tr>
        @endif
        @if($ticket->sla_deadline)
        <tr>
            <td style="padding: 6px 0; font-weight: bold; color: #8094ae;">Resolution Due:</td>
            <td style="padding: 6px 0; color: #e85347; font-weight: 600;">{{ $ticket->sla_deadline->format('M d, Y h:i A') }}</td>
        </tr>
        @endif
    </table>
</div>

<p>You can access the ticket management page directly via the link below to post a response or update ticket settings:</p>

<div style="text-align: center;">
    <a href="{{ route('admin.helpdesk.show', $ticket) }}" class="btn">View Ticket Details</a>
</div>
@endsection
