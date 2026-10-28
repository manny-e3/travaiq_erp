<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Job Offer - {{ $application->full_name }}</title>
    <style>
        @page {
            margin: 0px; 
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 15px;
            color: #222;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }
        
        .left-border-blue {
            position: fixed;
            top: 40px;
            bottom: 40px;
            left: 20px;
            width: 10px;
            background-color: #2e3a8c; 
        }
        .left-border-orange {
            position: fixed;
            top: 250px;
            bottom: 40px;
            left: 30px;
            width: 10px;
            background-color: #ef8b33; 
        }
        
        .content {
            margin-top: 50px;
            margin-bottom: 50px;
            margin-left: 80px; 
            margin-right: 50px;
            position: relative;
            z-index: 2;
        }
        
        .company-address {
            text-align: right;
            color: #444;
            margin-bottom: 50px;
            font-size: 14px;
        }
        
        .confidential {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 30px;
        }
        
        .applicant-info {
            margin-bottom: 30px;
        }
        
        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 30px;
            font-size: 16px;
        }
        
        .body-text p {
            margin-bottom: 18px;
            text-align: justify;
        }
        
        .signature-block {
            margin-top: 40px;
        }
        
        .signature-img {
            font-family: 'Brush Script MT', 'Comic Sans MS', cursive;
            font-size: 28px;
            color: #333;
            margin-bottom: 5px;
            border-bottom: 1px solid transparent; 
        }
        
        .watermark {
            position: fixed;
            bottom: -150px;
            right: -150px;
            z-index: 0;
            opacity: 0.08;
            width: 600px;
            height: 600px;
        }
        
    </style>
</head>
<body>

    <div class="watermark">
        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAwIiBoZWlnaHQ9IjUwMCIgdmlld0JveD0iMCAwIDUwMCA1MDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iNTAwIiBjeT0iNTAwIiByPSIxMDAiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLXdpZHRoPSI0MCIvPjxjaXJjbGUgY3g9IjUwMCIgY3k9IjUwMCIgcj0iMTgwIiBmaWxsPSJub25lIiBzdHJva2U9IiMwMDAiIHN0cm9rZS13aWR0aD0iNDAiLz48Y2lyY2xlIGN4PSI1MDAiIGN5PSI1MDAiIHI9IjI2MCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utd2lkdGg9IjQwIi8+PGNpcmNsZSBjeD0iNTAwIiBjeT0iNTAwIiByPSIzNDAiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLXdpZHRoPSI0MCIvPjxjaXJjbGUgY3g9IjUwMCIgY3k9IjUwMCIgcj0iNDIwIiBmaWxsPSJub25lIiBzdHJva2U9IiMwMDAiIHN0cm9rZS13aWR0aD0iNDAiLz48L3N2Zz4=" alt="watermark">
    </div>

    <div class="left-border-blue"></div>
    <div class="left-border-orange"></div>

    <div class="content">
        <div class="company-address">
            BPA Enterprise LTD<br>
            123 Corporate Blvd, Business City<br>
            90210, United States
        </div>

        <div class="confidential">
            PRIVATE &amp; CONFIDENTIAL
        </div>

        <div class="applicant-info">
            {{ date('d F, Y') }}<br><br>
            {{ $application->full_name }}<br>
            {{ $application->email }}<br>
            {{ $application->phone }}
        </div>

        <div class="body-text">
            <p>Dear {{ $application->first_name }},</p>

            @if(in_array($application->jobPosting->employment_type, ['Contract', 'Consulting', 'Internship']))
                @if($application->jobPosting->employment_type === 'Internship')
                    <div class="title">OFFER OF INTERNSHIP</div>

                    <p>We are pleased to offer you an internship with BPA Enterprise (hereinafter referred to as The Company), as a <strong>{{ $application->jobPosting->title }}</strong> effective <strong>{{ \Carbon\Carbon::parse($startDate)->format('d F, Y') }}</strong> for a duration of {{ $contractDuration }} months.</p>
                @else
                    <div class="title">OFFER OF ENGAGEMENT</div>

                    <p>We are pleased to offer you an engagement with BPA Enterprise (hereinafter referred to as The Company), as a <strong>{{ $application->jobPosting->title }}</strong> on a contract basis effective <strong>{{ \Carbon\Carbon::parse($startDate)->format('d F, Y') }}</strong> for an initial duration of {{ $contractDuration }} months which is subject to renewal.</p>
                @endif
            @else
                <div class="title">OFFER OF EMPLOYMENT</div>

                <p>We are pleased to offer you a position with BPA Enterprise (hereinafter referred to as The Company), as a <strong>{{ $application->jobPosting->title }}</strong> on a {{ strtolower($application->jobPosting->employment_type ?? 'Full-time') }} basis effective <strong>{{ \Carbon\Carbon::parse($startDate)->format('d F, Y') }}</strong>.</p>
            @endif

            <p>Employees are the most valuable assets of The Company, and we strive to attract the most talented people who will bring the necessary skills to help us achieve our objectives and goals. Equally important is that team members at The Company share the Company's desire to build a fun place of work where team members take an ownership approach to their work and maintain a strong work ethic.</p>

            <p>Based on our discussions thus far, we believe you will make meaningful contributions to the progress of the Company and in turn, have a fulfilling career with us.</p>
            
            <p>Your starting annual salary will be <strong>@currency($salary)</strong>. Please find below terms and condition of this offer of employment.</p>

            <p>We would like to use this medium to congratulate you on this offer and welcome you to our team. We firmly believe that your association with us is for the best and mutually beneficial. Wish you the very best for the future and looking forward to working together.</p>

            <div class="signature-block">
                <p>With Best Regards,</p>
                <div class="signature-img">BPA HR</div>
                Opeyemi Shoroye<br>
                <strong>Human Resources</strong><br>
                <strong>BPA Enterprise</strong>
            </div>
        </div>
    </div>
</body>
</html>
