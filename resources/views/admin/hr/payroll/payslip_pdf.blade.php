<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip - {{ $entry->user->name }} - {{ $entry->payrollRun->period }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            font-size: 13px;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #6576ff;
            padding-bottom: 10px;
        }
        .header table {
            width: 100%;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #6576ff;
        }
        .document-title {
            font-size: 18px;
            text-align: right;
            color: #555555;
            text-transform: uppercase;
        }
        .details-box {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        .details-box td {
            padding: 6px 10px;
            vertical-align: top;
            width: 50%;
        }
        .details-label {
            font-weight: bold;
            color: #777777;
            display: inline-block;
            width: 120px;
        }
        .details-value {
            color: #111111;
        }
        .pay-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .pay-table th {
            background-color: #f4f6fd;
            border: 1px solid #dbdfea;
            padding: 10px;
            font-weight: bold;
            text-align: left;
            color: #6576ff;
        }
        .pay-table td {
            border: 1px solid #dbdfea;
            padding: 10px;
            vertical-align: top;
        }
        .inner-table {
            width: 100%;
            border-collapse: collapse;
        }
        .inner-table td {
            border: none;
            padding: 5px 0;
        }
        .amount-col {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            border-top: 1px solid #dbdfea;
            padding-top: 10px;
            margin-top: 10px;
        }
        .net-pay-section {
            background-color: #f4f6fd;
            border: 1px solid #6576ff;
            padding: 15px;
            text-align: center;
            margin-bottom: 40px;
            border-radius: 4px;
        }
        .net-pay-label {
            font-size: 14px;
            text-transform: uppercase;
            color: #555555;
            margin-bottom: 5px;
        }
        .net-pay-amount {
            font-size: 24px;
            font-weight: bold;
            color: #6576ff;
        }
        .footer {
            text-align: center;
            font-size: 11px;
            color: #888888;
            border-top: 1px solid #dbdfea;
            padding-top: 10px;
            margin-top: 50px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <span class="company-name">Business Process Automation</span><br>
                    <span style="color:#777777;">Staff Payroll & Compensation Vault</span>
                </td>
                <td class="document-title">
                    Payslip
                </td>
            </tr>
        </table>
    </div>

    @php
        $parsedPeriod = \Carbon\Carbon::createFromFormat('Y-m', $entry->payrollRun->period);
    @endphp

    <table class="details-box">
        <tr>
            <td>
                <div><span class="details-label">Employee Name:</span><span class="details-value">{{ $entry->user->name }}</span></div>
                <div><span class="details-label">Employee ID:</span><span class="details-value">{{ $entry->user->employee_id ?? 'N/A' }}</span></div>
                <div><span class="details-label">Email:</span><span class="details-value">{{ $entry->user->email }}</span></div>
            </td>
            <td>
                <div><span class="details-label">Pay Period:</span><span class="details-value">{{ $parsedPeriod->format('F, Y') }}</span></div>
                <div><span class="details-label">Department:</span><span class="details-value">{{ $entry->user->department->name ?? 'N/A' }}</span></div>
                <div><span class="details-label">Designation:</span><span class="details-value">{{ $entry->user->designation->name ?? $entry->user->job_title ?? 'N/A' }}</span></div>
            </td>
        </tr>
    </table>

    <table class="pay-table">
        <tr>
            <th style="width: 50%;">Earnings</th>
            <th style="width: 50%;">Deductions</th>
        </tr>
        <tr>
            <td>
                <table class="inner-table">
                    <tr>
                        <td>Basic Salary</td>
                        <td class="amount-col">@currency($entry->basic_salary)</td>
                    </tr>
                    <tr>
                        <td>Allowances</td>
                        <td class="amount-col">@currency($entry->allowances)</td>
                    </tr>
                    <tr class="total-row">
                        <td>Gross Salary</td>
                        <td class="amount-col">@currency($entry->gross_salary)</td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="inner-table">
                    <tr>
                        <td>PAYE Tax</td>
                        <td class="amount-col">@currency($entry->paye_tax)</td>
                    </tr>
                    <tr>
                        <td>Pension (8%)</td>
                        <td class="amount-col">@currency($entry->pension_deduction)</td>
                    </tr>
                    <tr>
                        <td>NHF (2.5%)</td>
                        <td class="amount-col">@currency($entry->nhf_deduction)</td>
                    </tr>
                    <tr class="total-row">
                        <td>Total Deductions</td>
                        <td class="amount-col">@currency($entry->pension_deduction + $entry->nhf_deduction + $entry->paye_tax)</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="net-pay-section">
        <div class="net-pay-label">Net Pay Distribution</div>
        <div class="net-pay-amount">@currency($entry->net_salary)</div>
    </div>

    <div class="footer">
        This is a computer-generated document and does not require a signature. If you have any inquiries regarding this payslip, please contact the Human Resources / Finance Department.
    </div>

</body>
</html>
