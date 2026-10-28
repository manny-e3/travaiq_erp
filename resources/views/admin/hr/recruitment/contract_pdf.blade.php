<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contract of Engagement - {{ $application->full_name }}</title>
    <style>
        @page {
            margin: 80px 60px 80px 60px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #1a1a1a;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: right;
            margin-bottom: 40px;
            border-bottom: 2px solid #2e3a8c;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #2e3a8c;
        }
        .company-sub {
            font-size: 10px;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .contract-date {
            text-align: right;
            font-size: 13px;
            margin-bottom: 20px;
        }
        .recipient-block {
            margin-bottom: 30px;
            font-size: 13px;
        }
        .recipient-name {
            font-weight: bold;
            font-size: 14px;
        }
        .contract-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-top: 30px;
            margin-bottom: 30px;
            text-transform: uppercase;
            border-bottom: 1px solid #1a1a1a;
            padding-bottom: 5px;
        }
        .intro-text {
            margin-bottom: 25px;
            text-align: justify;
        }
        .clause {
            margin-bottom: 20px;
            text-align: justify;
        }
        .clause-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .clause-content {
            padding-left: 20px;
        }
        .bullet-list {
            margin-top: 5px;
            margin-bottom: 5px;
            padding-left: 20px;
        }
        .bullet-list li {
            margin-bottom: 5px;
        }
        .sub-clause {
            margin-top: 5px;
            margin-bottom: 5px;
            padding-left: 20px;
        }
        .sub-clause-item {
            margin-bottom: 10px;
        }
        .page-break {
            page-break-before: always;
        }
        .signatures {
            margin-top: 50px;
            width: 100%;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .signature-table td {
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
        }
        .footer {
            position: fixed;
            bottom: -50px;
            left: 0;
            right: 0;
            height: 40px;
            font-size: 9px;
            color: #777;
            border-top: 1px solid #ccc;
            padding-top: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    @php
        $type = $contractType ?? ($application->jobPosting->employment_type ?? 'Full-time');
        $duration = $contractDuration ?? 12;
        $isContractor = in_array($type, ['Contract', 'Consulting', 'Internship']);
    @endphp

    <!-- Footer for all pages -->
    <div class="footer">
        Directors: Stephen Jennings, Chairman | Kamal Boushi | Funsho Akinwumi | Kieran Donnelly | Adeolu Ademola | Simon Edwards | Kolawole Durojaiye | Frank Mosier | Robert Reid<br>
        23, Awolowo Road, Ikoyi, Lagos, Nigeria | +234 (01) 6310700 | www.renmoney.com | TIN: 13330875-0001 | RC: 1031927
    </div>

    <div class="header">
        <div class="company-name">{{ config('app.name', 'Renmoney Microfinance Bank') }}</div>
        <div class="company-sub">{{ $isContractor ? 'Independent Contractor Agreement' : 'Contract of Engagement' }}</div>
    </div>

    <div class="contract-date">
        {{ date('F d, Y') }}
    </div>

    <div class="recipient-block">
        <span class="recipient-name">{{ $application->full_name }}</span><br>
        {{ $application->phone }}<br>
        {{ $application->email }}
    </div>

    <p>Dear {{ $application->first_name }},</p>

    <div class="contract-title">{{ $isContractor ? 'Independent Contractor Agreement' : 'Contract of Engagement' }}</div>

    <div class="intro-text">
        <strong>{{ config('app.name', 'Renmoney Microfinance Bank Limited') }}</strong> (hereinafter referred to as the "{{ $isContractor ? 'Company' : 'Bank' }}", "Our", "We", "Us") is pleased to engage your services as a <strong>{{ $application->jobPosting->designation->name ?? $application->jobPosting->title }}</strong> to render the services set out in this Agreement (the "Services") under the following terms and conditions (the "Agreement").
    </div>

    <!-- Clause 1 -->
    <div class="clause">
        <div class="clause-title">1. Duration of Contract:</div>
        <div class="clause-content">
            @if($isContractor)
                This Agreement shall become effective on <strong>{{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }}</strong>, and remain in force for a period of {{ $duration }} months, unless terminated earlier by either party or extended by the written agreement of both parties.
            @else
                This Agreement shall become effective on <strong>{{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }}</strong>, and remain in force for a period of {{ $duration }} months, with the first three (3) months serving as a trial period, unless terminated earlier by the Bank or extended by the written agreement of both parties.
            @endif
        </div>
    </div>

    <!-- Clause 2 -->
    <div class="clause">
        <div class="clause-title">2. Reporting:</div>
        <div class="clause-content">
            In the course of your engagement with the Bank, you will report to the Department Head or Engineering Manager within the <strong>{{ $application->jobPosting->department->name ?? 'Engineering' }} Department</strong>.
        </div>
    </div>

    <!-- Clause 3 -->
    <div class="clause">
        <div class="clause-title">3. Job Functions:</div>
        <div class="clause-content">
            You shall provide services including, but not limited to, the following functions:
            <ul class="bullet-list">
                <li>Develop new user facing features.</li>
                <li>Write reusable code and libraries.</li>
                <li>Enhance application for maximum speed and scalability.</li>
                <li>Validate input before submitting to back end.</li>
                <li>Collaborate with team members and stakeholders.</li>
                <li>Bridge the gap between graphic design and technical implementation.</li>
                <li>Translate UI/UX design wireframes to actual code.</li>
            </ul>
        </div>
    </div>

    <!-- Clause 4 -->
    <div class="clause">
        <div class="clause-title">4. Remuneration:</div>
        <div class="clause-content">
            You shall be paid a monthly gross of <strong>&#8358; {{ number_format($salary, 2) }}</strong> monthly in arrears, which is subject to five percent (5%) withholding tax and other statutory deductions.
        </div>
    </div>

    <div class="page-break"></div>

    <!-- Clause 5 -->
    <div class="clause">
        <div class="clause-title">5. Service Hours and Location:</div>
        <div class="clause-content">
            You will provide the Services at the Bank's head office or other place(s) designated by the Bank. You will provide the Services during the hours of 8:00am - 5:00pm on weekdays (excluding public holidays), except otherwise directed by the Bank.
        </div>
    </div>

    <!-- Clause 6 -->
    <div class="clause">
        <div class="clause-title">6. Confidentiality Clause:</div>
        <div class="clause-content">
            <div class="sub-clause">
                <div class="sub-clause-item">
                    6.1 You agree not to disclose, use, or repackage in any form, any strategy, internal communication, business plan, policies, documents, templates, intellectual property, or other proprietary information, whether written or communicated verbally, that you become privy to in the course of the discharge of your duties in the Bank during your term of employment and thereafter, without the Company's prior written consent.
                </div>
                <div class="sub-clause-item">
                    6.2 You understand and agree that the terms of your employment (including your compensation) are confidential; as such, you shall not disclose the details of the terms of your employment as contained in this Agreement or any other document to any person (within or outside the Company) or entity without the Company's prior written approval.
                </div>
                <div class="sub-clause-item">
                    6.3 You understand and agree that the confidentiality obligations herein shall continue indefinitely (surviving the termination of this Agreement) and that if you breach these obligations the Company will be at liberty to take any disciplinary actions against you (including the termination of your employment) and/or seek compensation for any damages or losses suffered by the Company as a result of your breach of confidentiality.
                </div>
            </div>
        </div>
    </div>

    <!-- Clause 7 -->
    <div class="clause">
        <div class="clause-title">7. Intellectual Property:</div>
        <div class="clause-content">
            You hereby assign the following to the Bank:
            <div class="sub-clause">
                <div class="sub-clause-item">
                    i. All existing or future intellectual property rights (including without limitation, patents, copyright, designs, trademarks, and related rights and inventions) arising from any work made wholly or partially by you during the term of this Agreement and forming part of the Services performed with, to, and for the Bank; and
                </div>
                <div class="sub-clause-item">
                    ii. All moral rights (as provided under the Copyright Act, 2022 or under any other similar legislation) accruing to you from all such creations.
                </div>
            </div>
            You shall not use any intellectual property right belonging to the Bank for any purpose without our prior written consent of the Bank.
        </div>
    </div>

    <!-- Clause 8 -->
    <div class="clause">
        <div class="clause-title">8. Representations and Warranties:</div>
        <div class="clause-content">
            You hereby represent and warrant that:
            <ul class="bullet-list">
                <li>You have the requisite qualifications and expertise to provide the Services;</li>
                <li>Your performance of the Services shall not breach any existing obligation under any agreement, whether written or oral, legislation or intellectual property rights; and</li>
                <li>You are not aware, as at date hereof, of any matter which might or will adversely affect your ability to perform the Services.</li>
            </ul>
        </div>
    </div>

    <!-- Clause 9 -->
    <div class="clause">
        <div class="clause-title">9. Authority:</div>
        <div class="clause-content">
            You shall not have any authority over any employees in the performance of the Services and shall not commit the Bank to any payments or legally binding commitments.
        </div>
    </div>

    <!-- Clause 10 -->
    <div class="clause">
        <div class="clause-title">10. Assignment:</div>
        <div class="clause-content">
            You shall not assign the performance of any part of the Services to any third-party without the prior written consent of the Bank.
        </div>
    </div>

    <!-- Clause 11 -->
    <div class="clause">
        <div class="clause-title">11. Background Verification:</div>
        <div class="clause-content">
            As part of the requirements for your engagement, you are expected to provide the Bank with evidence validating your educational and experience claims in the form of verifiable credentials and other relevant documents. This includes your educational and professional certificates, valid means of identification, birth certificate, and two (2) completed reference forms (with reference valid means of identification).
        </div>
    </div>

    <!-- Signatures -->
    <table class="signature-table">
        <tr>
            <td>
                For: <strong>{{ config('app.name', 'Renmoney Microfinance Bank') }}</strong>
                <div class="signature-line">
                    Authorized Representative
                </div>
            </td>
            <td>
                For: <strong>{{ $isContractor ? 'Contractor' : 'Employee' }}</strong>
                <div class="signature-line">
                    {{ $application->full_name }}
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
