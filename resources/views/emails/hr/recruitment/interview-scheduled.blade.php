<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Interview Scheduled</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #0056b3; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 5px 5px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
        .details-box { background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin: 20px 0; border-left: 4px solid #0056b3; }
        .details-box p { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Interview Scheduled</h2>
        </div>
        <div class="content">
            <p>Dear {{ $interview->application->first_name }},</p>
            
            <p>We are pleased to invite you to an interview for the <strong>{{ $interview->application->jobPosting->title }}</strong> position at {{ config('app.name') }}.</p>
            
            <p>Here are the details of your upcoming interview:</p>
            
            <div class="details-box">
                @if($interview->interview_stage === 'Assessment')
                    <p><strong>Stage:</strong> Assessment</p>
                    <p><strong>Due Date & Time:</strong> {{ $interview->scheduled_at->format('l, F j, Y \a\t g:i A') }}</p>
                    <p><strong>Reviewer(s):</strong> {{ $interview->interviewers->pluck('name')->join(', ') }}</p>
                    @if($interview->assessment_details)
                        <p><strong>Instructions:</strong> {{ $interview->assessment_details }}</p>
                    @endif
                    @if($interview->assessment_file)
                        <p><strong>Attachment:</strong> An assessment file has been attached to this email.</p>
                    @endif
                @else
                    <p><strong>Stage:</strong> {{ $interview->interview_stage ?? 'Interview' }}</p>
                    <p><strong>Date & Time:</strong> {{ $interview->scheduled_at->format('l, F j, Y \a\t g:i A') }}</p>
                    <p><strong>Interviewer(s):</strong> {{ $interview->interviewers->pluck('name')->join(', ') }}</p>
                    <p><strong>Location/Link:</strong> {{ $interview->location_or_link }}</p>
                @endif
            </div>
            
            <p>Please let us know as soon as possible if you are unable to make this time, and we will do our best to reschedule.</p>
            
            <p>We look forward to speaking with you!</p>
            
            <p>Best regards,<br>
            The HR Team<br>
            {{ config('app.name') }}</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
