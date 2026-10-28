@extends('emails.layout')

@section('content')
<h2 class="h1">Ticket Received</h2>
<p>Dear {{ $ticket->customer->name }},</p>
<p>Thank you for reaching out to our support team. We have received your request and logged a support ticket for you.</p>

<div style="background-color: #f8f9fa; border: 1px solid #e9ebee; border-radius: 6px; padding: 20px; margin: 20px 0;">
    <h3 style="margin-top: 0; color: #364a63; font-size: 16px;">Ticket Details</h3>
    <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
        <tr>
            <td style="padding: 6px 0; width: 120px; font-weight: bold; color: #8094ae;">Ticket ID:</td>
            <td style="padding: 6px 0; font-weight: bold; color: #364a63;">{{ $ticket->ticket_key }}</td>
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
            <td style="padding: 6px 0; color: #526484;">{{ $ticket->priority }}</td>
        </tr>
        @if($ticket->response_deadline)
        <tr>
            <td style="padding: 6px 0; font-weight: bold; color: #8094ae;">SLA Target:</td>
            <td style="padding: 6px 0; color: #ff9800; font-weight: 600;">Response expected by {{ $ticket->response_deadline->format('M d, Y h:i A') }}</td>
        </tr>
        @endif
    </table>
</div>

<p>A support agent has been assigned to your ticket and will follow up with you shortly. If you need to add any additional comments, simply reply to this email.</p>

<p>Best regards,<br>Support Team</p>
@endsection
