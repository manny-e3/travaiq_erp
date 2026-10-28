@extends('emails.layout')

@section('content')
    <h1 class="h1">Manuscript Received!</h1>
    <p>Hello {{ $prospect->name }},</p>
    <p>Thank you for submitting your manuscript, <strong>"{{ $prospect->book_title }}"</strong>, to {{ config('app.name', 'Business process automation') }}. We have successfully received your files and our editorial team has been notified.</p>
    <p><strong>What happens next?</strong></p>
    <ul>
        <li>Initial Review: 3-5 business days.</li>
        <li>Feedback: You will receive an email once our editors have evaluated your work.</li>
        <li>Estimate: Your initial cost estimate is @currency($prospect->estimated_cost).</li>
    </ul>
    <p>We're excited to potentially work with you on this journey!</p>
    <p>Best Regards,<br>The {{ config('app.name', 'Business process automation') }} Team</p>
@endsection
