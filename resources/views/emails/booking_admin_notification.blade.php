@extends('emails.limo_layout')

@section('content')

{{-- Alert Banner --}}
<div class="alert-banner">
    <p>&#128663; New ride reservation received — action may be required.</p>
</div>

{{-- Greeting --}}
<p style="font-size:15px; margin-bottom:24px;">
    Hello Admin,<br><br>
    A new booking has been submitted on <strong style="color:#c9a84c">Denver Limo Cars</strong>. Full reservation details are listed below.
</p>

{{-- Reference & Status --}}
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:10px;">
    <div>
        <div class="section-title" style="margin-bottom:4px; border:none;">Booking Reference</div>
        <div style="font-size:22px; font-weight:700; color:#c9a84c; letter-spacing:0.1em; font-family:'Georgia',serif;">
            {{ $booking->reference }}
        </div>
    </div>
    <div>
        <span class="status-badge pending">{{ ucfirst($booking->status) }}</span>
    </div>
</div>

<hr class="divider">

{{-- Customer Details --}}
<p class="section-title">Customer Information</p>
<table class="detail-table">
    <tr>
        <td>Full Name</td>
        <td>{{ $booking->customer->full_name }}</td>
    </tr>
    <tr>
        <td>Email</td>
        <td>{{ $booking->customer->email }}</td>
    </tr>
    <tr>
        <td>Phone</td>
        <td>{{ $booking->customer->phone ?? 'Not provided' }}</td>
    </tr>
</table>

{{-- Trip Details --}}
<p class="section-title">Trip Details</p>
<table class="detail-table">
    <tr>
        <td>Service Type</td>
        <td>{{ $booking->service_type === 'airport' ? '✈ Airport Transfer' : '⏱ Hourly Directed' }}</td>
    </tr>
    <tr>
        <td>Vehicle</td>
        <td>{{ $booking->vehicle_name }}</td>
    </tr>
    <tr>
        <td>Pickup Location</td>
        <td>{{ $booking->pickup }}</td>
    </tr>
    <tr>
        <td>Drop-off Location</td>
        <td>{{ $booking->dropoff ?? 'As-Directed Hourly' }}</td>
    </tr>
    <tr>
        <td>Date &amp; Time</td>
        <td>{{ $booking->date->format('l, F j, Y') }} at {{ $booking->time }}</td>
    </tr>
    <tr>
        <td>Passengers</td>
        <td>{{ $booking->passengers }}</td>
    </tr>
    <tr>
        <td>Luggage</td>
        <td>{{ $booking->luggage }} bag(s)</td>
    </tr>
    @if($booking->duration)
    <tr>
        <td>Duration</td>
        <td>{{ $booking->duration }} hour(s)</td>
    </tr>
    @endif
    @if($booking->distance_miles)
    <tr>
        <td>Distance</td>
        <td>{{ $booking->distance_miles }} miles</td>
    </tr>
    @endif
    @if($booking->flight_number)
    <tr>
        <td>Flight Number</td>
        <td>{{ $booking->flight_number }}</td>
    </tr>
    @endif
    @if($booking->notes)
    <tr>
        <td>Customer Notes</td>
        <td>{{ $booking->notes }}</td>
    </tr>
    @endif
</table>

{{-- Fare Box --}}
<div class="fare-box">
    <div class="fare-label">Total Fare</div>
    <div class="fare-amount">@currency($booking->total_fare)</div>
    <div style="font-size:12px; color:#7a6935; margin-top:8px; font-family:'Arial',sans-serif; letter-spacing:0.08em; text-transform:uppercase;">
        Payment Method:
        @php
            $label = match($booking->payment_method) {
                'none'          => 'Offline — Manual Follow-Up Required',
                'bank_transfer' => 'Bank Transfer',
                default         => ucfirst(str_replace('_', ' ', $booking->payment_method)),
            };
        @endphp
        {{ $label }}
    </div>
</div>

{{-- Action Required Box (only for none/offline) --}}
@if($booking->payment_method === 'none')
<div class="note-box" style="border-color:#c9a84c; color:#c9a84c; background-color:#1a1500;">
    <strong>&#9888; Action Required:</strong> No payment gateway was active when this booking was submitted. Please contact the customer at <strong>{{ $booking->customer->email }}</strong> to arrange payment manually.
</div>
@else
<div class="note-box">
    The customer has been directed to complete payment via <strong>{{ $label }}</strong>. Monitor the booking in the admin panel.
</div>
@endif

{{-- CTA Button --}}
<div style="text-align:center; margin-top:28px;">
    <a href="{{ url('/admin/rides') }}" class="btn">View in Admin Panel</a>
</div>

@endsection
