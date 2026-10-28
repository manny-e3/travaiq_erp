@extends('emails.limo_layout')

@section('content')

{{-- Greeting --}}
<p style="font-size:15px; margin-bottom:8px; color:#c9a84c; letter-spacing:0.05em;">
    Dear {{ $booking->customer->first_name }},
</p>
<p style="font-size:15px; margin-bottom:28px; line-height:1.8;">
    Thank you for choosing <strong style="color:#c9a84c">Denver Limo Cars</strong>. Your reservation has been received and is now being processed. Below are the full details of your upcoming journey.
</p>

{{-- Reference & Status --}}
<div style="text-align:center; margin-bottom:32px;">
    <div style="font-size:11px; letter-spacing:0.3em; text-transform:uppercase; color:#7a6935; margin-bottom:8px; font-family:'Arial',sans-serif;">Your Booking Reference</div>
    <div style="font-size:28px; font-weight:700; color:#c9a84c; letter-spacing:0.15em; font-family:'Georgia',serif;">
        {{ $booking->reference }}
    </div>
    <div style="margin-top:10px;">
        <span style="display:inline-block; padding:5px 18px; border:1px solid #c9a84c; background-color:#1a1500; color:#c9a84c; font-size:11px; letter-spacing:0.15em; text-transform:uppercase; font-family:'Arial',sans-serif;">
            Reservation Received
        </span>
    </div>
</div>

<hr style="border:none; border-top:1px solid #1e1e1e; margin:24px 0;">

{{-- Journey Summary --}}
<p class="section-title">Your Journey</p>
<table class="detail-table">
    <tr>
        <td>Service</td>
        <td>{{ $booking->service_type === 'airport' ? '✈ Airport Transfer' : '⏱ Hourly Directed' }}</td>
    </tr>
    <tr>
        <td>Date</td>
        <td>{{ $booking->date->format('l, F j, Y') }}</td>
    </tr>
    <tr>
        <td>Time</td>
        <td>{{ $booking->time }}</td>
    </tr>
    <tr>
        <td>Pickup</td>
        <td>{{ $booking->pickup }}</td>
    </tr>
    <tr>
        <td>Drop-off</td>
        <td>{{ $booking->dropoff ?? 'As-Directed by You' }}</td>
    </tr>
    @if($booking->flight_number)
    <tr>
        <td>Flight Number</td>
        <td>{{ $booking->flight_number }}</td>
    </tr>
    @endif
    <tr>
        <td>Passengers</td>
        <td>{{ $booking->passengers }}</td>
    </tr>
    <tr>
        <td>Luggage</td>
        <td>{{ $booking->luggage }} bag(s)</td>
    </tr>
</table>

{{-- Vehicle --}}
<p class="section-title">Your Vehicle</p>
<table class="detail-table">
    <tr>
        <td>Vehicle</td>
        <td>{{ $booking->vehicle_name }}</td>
    </tr>
    @if($booking->duration)
    <tr>
        <td>Duration</td>
        <td>{{ $booking->duration }} hour(s)</td>
    </tr>
    @endif
</table>

{{-- Fare Box --}}
<div class="fare-box">
    <div class="fare-label">Total Fare</div>
    <div class="fare-amount">@currency($booking->total_fare)</div>
    <div style="font-size:12px; color:#7a6935; margin-top:8px; font-family:'Arial',sans-serif; letter-spacing:0.05em;">
        @if($booking->payment_method === 'none')
            Our concierge team will be in touch shortly to arrange payment.
        @elseif($booking->payment_method === 'bank_transfer')
            Payment via bank transfer — our team will send you transfer details.
        @else
            Payment to be completed via {{ ucfirst(str_replace('_', ' ', $booking->payment_method)) }}.
        @endif
    </div>
</div>

{{-- What Happens Next --}}
<p class="section-title" style="margin-top:28px;">What Happens Next</p>
<table style="width:100%; border-collapse:collapse;">
    <tr>
        <td style="padding:10px 0; vertical-align:top; width:28px;">
            <span style="display:inline-block; width:22px; height:22px; background-color:#c9a84c; color:#0a0a0a; font-size:11px; font-weight:700; text-align:center; line-height:22px; border-radius:50%; font-family:'Arial',sans-serif;">1</span>
        </td>
        <td style="padding:10px 0 10px 12px; font-size:13px; color:#aaa; border-bottom:1px solid #1e1e1e;">
            Our team reviews your reservation and assigns your chauffeur.
        </td>
    </tr>
    <tr>
        <td style="padding:10px 0; vertical-align:top; width:28px;">
            <span style="display:inline-block; width:22px; height:22px; background-color:#c9a84c; color:#0a0a0a; font-size:11px; font-weight:700; text-align:center; line-height:22px; border-radius:50%; font-family:'Arial',sans-serif;">2</span>
        </td>
        <td style="padding:10px 0 10px 12px; font-size:13px; color:#aaa; border-bottom:1px solid #1e1e1e;">
            @if($booking->payment_method === 'none')
                A team member will contact you within 24 hours to confirm payment.
            @else
                You will receive a booking confirmation once payment is processed.
            @endif
        </td>
    </tr>
    <tr>
        <td style="padding:10px 0; vertical-align:top; width:28px;">
            <span style="display:inline-block; width:22px; height:22px; background-color:#c9a84c; color:#0a0a0a; font-size:11px; font-weight:700; text-align:center; line-height:22px; border-radius:50%; font-family:'Arial',sans-serif;">3</span>
        </td>
        <td style="padding:10px 0 10px 12px; font-size:13px; color:#aaa;">
            Your chauffeur will be ready at {{ $booking->pickup }} on {{ $booking->date->format('F j') }} at {{ $booking->time }}.
        </td>
    </tr>
</table>

{{-- Notes --}}
@if($booking->notes)
<div class="note-box" style="margin-top:24px;">
    <strong style="color:#c9a84c;">Your Notes:</strong> {{ $booking->notes }}
</div>
@endif

{{-- Contact --}}
<hr style="border:none; border-top:1px solid #1e1e1e; margin:28px 0;">
<p style="font-size:13px; color:#888; text-align:center; line-height:1.8;">
    Questions about your reservation?<br>
    Contact us at <a href="mailto:hello@denverlimocars.com" style="color:#c9a84c; text-decoration:none;">hello@denverlimocars.com</a><br>
    or call us anytime — we're available 24/7.
</p>

@endsection
