<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Interview Assignment</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #343a40; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 5px 5px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
        .details-box { background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin: 20px 0; border-left: 4px solid #343a40; }
        .details-box p { margin: 5px 0; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #0056b3; color: white; text-decoration: none; border-radius: 4px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Interview Assignment</h2>
        </div>
        <div class="content">
            <p>Hi {{ $interviewer->name }},</p>
            
            <p>You have been assigned to conduct an interview for the <strong>{{ $interview->application->jobPosting->title }}</strong> position.</p>
            
            <p><strong>Candidate Details:</strong></p>
            <ul>
                <li><strong>Name:</strong> {{ $interview->application->first_name }} {{ $interview->application->last_name }}</li>
                <li><strong>Email:</strong> {{ $interview->application->email }}</li>
                <li><strong>Phone:</strong> {{ $interview->application->phone ?? 'N/A' }}</li>
            </ul>

            <p><strong>Interview Details:</strong></p>
            <div class="details-box">
                <p><strong>Date & Time:</strong> {{ $interview->scheduled_at->format('l, F j, Y \a\t g:i A') }}</p>
                <p><strong>Other Interviewers:</strong> {{ $interview->interviewers->where('id', '!=', $interviewer->id)->pluck('name')->join(', ') ?: 'None' }}</p>
                <p><strong>Location/Link:</strong> {{ $interview->location_or_link }}</p>
            </div>
            
            <p>Please log in to the {{ config('app.name') }} dashboard to review the candidate's resume and application details prior to the interview.</p>
            
            <p>Best regards,<br>
            HR Recruitment System<br>
            {{ config('app.name') }}</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
