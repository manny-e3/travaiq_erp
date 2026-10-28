<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Update on your application</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f4f5f7; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; border: 1px solid #ddd; border-bottom: none; }
        .header h2 { color: #333; margin: 0; }
        .content { padding: 20px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 5px 5px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Update on your application</h2>
        </div>
        <div class="content">
            <p>Dear {{ $application->first_name }},</p>
            
            <p>Thank you for taking the time to apply for the <strong>{{ $application->jobPosting->title }}</strong> position at {{ config('app.name') }}.</p>
            
            <p>We wanted to let you know that we have reviewed your application. While your background is impressive, we have decided to move forward with other candidates who more closely align with the current needs of this specific role.</p>
            
            <p>We genuinely appreciate your interest in our company and the time you invested in the application process. We will keep your resume on file and may reach out if a future opportunity matches your skill set.</p>
            
            <p>We wish you every success in your job search and your future career endeavors.</p>
            
            <p>Best regards,<br>
            The Hiring Team<br>
            {{ config('app.name') }}</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
