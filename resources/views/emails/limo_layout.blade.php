<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Denver Limo Cars' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Georgia', 'Times New Roman', serif; background-color: #0d0d0d; color: #c9a84c; }
        .wrapper { width: 100%; background-color: #0d0d0d; padding: 40px 20px; }
        .main { background-color: #111111; width: 100%; max-width: 620px; margin: 0 auto; border-radius: 2px; overflow: hidden; border: 1px solid #2a2a2a; }
        .header { padding: 36px 40px; text-align: center; background-color: #0a0a0a; border-bottom: 1px solid #c9a84c; }
        .header-wordmark { font-size: 22px; font-weight: 700; letter-spacing: 0.18em; text-transform: uppercase; color: #c9a84c; font-family: 'Georgia', serif; }
        .header-tagline { font-size: 10px; letter-spacing: 0.3em; text-transform: uppercase; color: #7a6935; margin-top: 6px; font-family: 'Arial', sans-serif; }
        .content { padding: 40px; line-height: 1.7; font-family: 'Arial', sans-serif; color: #d4d4d4; }
        .alert-banner { background-color: #1a1500; border-left: 3px solid #c9a84c; padding: 14px 18px; margin-bottom: 28px; border-radius: 1px; }
        .alert-banner p { font-size: 13px; color: #c9a84c; margin: 0; letter-spacing: 0.05em; }
        .section-title { font-size: 10px; letter-spacing: 0.25em; text-transform: uppercase; color: #7a6935; font-family: 'Arial', sans-serif; margin-bottom: 14px; padding-bottom: 8px; border-bottom: 1px solid #222; }
        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
        .detail-table td { padding: 10px 0; border-bottom: 1px solid #1e1e1e; font-size: 14px; vertical-align: top; }
        .detail-table td:first-child { color: #7a6935; width: 42%; font-size: 12px; letter-spacing: 0.05em; text-transform: uppercase; padding-right: 12px; }
        .detail-table td:last-child { color: #e8e8e8; font-weight: 500; }
        .fare-box { background: linear-gradient(135deg, #1a1500 0%, #0f0f00 100%); border: 1px solid #c9a84c; border-radius: 2px; padding: 20px 24px; margin: 24px 0; text-align: center; }
        .fare-label { font-size: 10px; letter-spacing: 0.3em; text-transform: uppercase; color: #7a6935; margin-bottom: 6px; }
        .fare-amount { font-size: 32px; font-weight: 700; color: #c9a84c; letter-spacing: 0.05em; font-family: 'Georgia', serif; }
        .btn { display: inline-block; padding: 13px 32px; background-color: #c9a84c; color: #0a0a0a; text-decoration: none; font-size: 12px; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; font-family: 'Arial', sans-serif; margin-top: 8px; }
        .btn:hover { background-color: #e2c068; }
        .status-badge { display: inline-block; padding: 4px 14px; background-color: #1a2a1a; border: 1px solid #4caf50; color: #4caf50; font-size: 11px; letter-spacing: 0.15em; text-transform: uppercase; border-radius: 1px; }
        .status-badge.pending { background-color: #1a1500; border-color: #c9a84c; color: #c9a84c; }
        .divider { border: none; border-top: 1px solid #1e1e1e; margin: 28px 0; }
        .note-box { background-color: #0f0f0f; border: 1px solid #2a2a2a; border-radius: 1px; padding: 16px 20px; margin: 20px 0; font-size: 13px; color: #8a8a8a; }
        .footer { padding: 24px 40px; text-align: center; background-color: #0a0a0a; border-top: 1px solid #1e1e1e; }
        .footer p { font-size: 11px; color: #444; letter-spacing: 0.05em; line-height: 1.8; font-family: 'Arial', sans-serif; }
        .footer a { color: #7a6935; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="main">
            <div class="header">
                <div class="header-wordmark">Denver Limo Cars</div>
                <div class="header-tagline">Premium Chauffeured Transportation · Colorado</div>
            </div>
            <div class="content">
                @yield('content')
            </div>
            <div class="footer">
                <p>
                    &copy; {{ date('Y') }} Denver Limo Cars. All rights reserved.<br>
                    Denver International Airport &amp; Greater Colorado Area<br>
                    <a href="mailto:hello@denverlimocars.com">hello@denverlimocars.com</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
