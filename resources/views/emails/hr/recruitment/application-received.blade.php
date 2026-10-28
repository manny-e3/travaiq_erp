<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Application Received</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #0056b3; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 5px 5px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #0056b3; color: white; text-decoration: none; border-radius: 4px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Application Received</h2>
        </div>
        <div class="content">
            <p>Dear {{ $application->first_name }},</p>
            
            <p>Thank you for submitting your application for the <strong>{{ $application->jobPosting->title }}</strong> position at {{ config('app.name') }}.</p>
            
            <p>We have successfully received your application and resume. Our hiring team will review your qualifications and experience. If your profile matches our requirements, we will contact you to schedule an interview.</p>
            
            <p>We appreciate your interest in joining our team and wish you the best of luck!</p>
            
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
