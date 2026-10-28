<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Job Offer: {{ $application->jobPosting->title }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #28a745; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 5px 5px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Congratulations!</h2>
        </div>
        <div class="content">
            <p>Dear {{ $application->first_name }},</p>
            
            <p>We are absolutely thrilled to officially offer you the position of <strong>{{ $application->jobPosting->title }}</strong> at {{ config('app.name') }}!</p>
            
            <p>Our team was incredibly impressed with your skills, experience, and the great conversations we've had during the interview process. We believe you will be a fantastic addition to our company.</p>
            
            <p><strong>Please find your official offer letter attached to this email.</strong> It contains all the important details regarding your compensation, start date, and terms of employment.</p>
            
            <p>If you choose to accept this offer, please sign the attached document and reply to this email with the signed copy by the date specified in the letter.</p>
            
            <p>If you have any questions at all, please do not hesitate to reach out.</p>
            
            <p>We can't wait to welcome you to the team!</p>
            
            <p>Warm regards,<br>
            The Hiring Team<br>
            {{ config('app.name') }}</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
