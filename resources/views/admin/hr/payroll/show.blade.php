@extends('layouts.admin')

@section('title', 'Payroll Run Details')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                @php
                    $parsedPeriod = \Carbon\Carbon::createFromFormat('Y-m', $payroll->period);
                @endphp

                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <a href="{{ route('admin.hr.payroll.index') }}" class="back-to"><em class="icon ni ni-arrow-left"></em><span>Back to Payroll Runs</span></a>
                            <h3 class="nk-block-title page-title">Payroll Run: {{ $parsedPeriod->format('F, Y') }}</h3>
                            <div class="nk-block-des text-soft">
                                <p>Period: <strong>{{ $payroll->period }}</strong> | Status: 
                                    @if($payroll->status === 'Draft')
                                        <span class="badge badge-dim bg-warning text-warning">Draft</span>
                                    @else
                                        <span class="badge badge-dim bg-success text-success">Approved & GL Posted</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            @if($payroll->status === 'Draft')
                                <form action="{{ route('admin.hr.payroll.approve', $payroll) }}" method="POST" onsubmit="return confirm('Are you sure you want to approve this payroll run? This will lock calculations and post entries to the General Ledger.');">
                                    @csrf
                                    <button type="submit" class="btn btn-success"><em class="icon ni ni-check-circle"></em><span>Approve & Process Payroll</span></button>
                                </form>
                            @else
                                <a href="{{ route('admin.hr.payroll.gl') }}" class="btn btn-outline-primary"><em class="icon ni ni-file-text"></em><span>View GL entries</span></a>
                            @endif
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-icon alert-dismissible">
                        <em class="icon ni ni-check-circle"></em> <strong>Success!</strong> {{ session('success') }}
                        <button class="close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-icon alert-dismissible">
                        <em class="icon ni ni-cross-circle"></em> <strong>Error!</strong> {{ session('error') }}
                        <button class="close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Stats summary card -->
                <div class="row g-gs mb-4">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title text-soft mb-1">Total Gross Salary</div>
                                <div class="amount text-primary">@currency($payroll->total_gross)</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title text-soft mb-1">Total Deductions</div>
                                <div class="amount text-danger">@currency($payroll->total_deductions)</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title text-soft mb-1">Total Net Salaries</div>
                                <div class="amount text-success">@currency($payroll->total_net)</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title text-soft mb-1">Active Staff Count</div>
                                <div class="amount">{{ $payroll->entries->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payroll Entries Table -->
                <div class="card card-bordered card-stretch">
                    <div class="card-inner-group">
                        <div class="card-inner p-0">
                            <table class="table table-tranx">
                                <thead>
                                    <tr class="tb-tnx-head">
                                        <th class="tb-tnx-id">Employee ID</th>
                                        <th class="tb-tnx-info">Employee Name</th>
                                        <th class="tb-tnx-info text-end">Basic Salary</th>
                                        <th class="tb-tnx-info text-end" style="width: 220px;">Allowance</th>
                                        <th class="tb-tnx-info text-end">Pension (8%)</th>
                                        <th class="tb-tnx-info text-end">NHF (2.5%)</th>
                                        <th class="tb-tnx-info text-end">PAYE Tax</th>
                                        <th class="tb-tnx-info text-end">Net Pay</th>
                                        <th class="tb-tnx-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payroll->entries as $entry)
                                    <tr class="tb-tnx-item">
                                        <td class="tb-tnx-id">
                                            <span class="badge bg-light text-primary">{{ $entry->user->employee_id ?? 'N/A' }}</span>
                                        </td>
                                        <td class="tb-tnx-info">
                                            <div class="user-card">
                                                <div class="user-avatar bg-primary">
                                                    <span>{{ strtoupper(substr($entry->user->name, 0, 2)) }}</span>
                                                </div>
                                                <div class="user-info">
                                                    <span class="lead-text">{{ $entry->user->name }}</span>
                                                    <span class="sub-text">{{ $entry->user->designation->name ?? $entry->user->job_title ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="tb-tnx-info text-end">
                                            <span class="lead-text">@currency($entry->basic_salary)</span>
                                        </td>
                                        <td class="tb-tnx-info text-end">
                                            @if($payroll->status === 'Draft')
                                                <form action="{{ route('admin.hr.payroll-entry.allowance', $entry) }}" method="POST" class="d-flex align-items-center justify-content-end">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="input-group input-group-sm" style="width: 140px;">
                                                        <span class="input-group-text">@currencySymbol</span>
                                                        <input type="number" step="0.01" class="form-control form-control-sm text-end" name="allowances" value="{{ $entry->allowances }}" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-xs btn-primary ms-1"><em class="icon ni ni-check"></em></button>
                                                </form>
                                            @else
                                                <span class="lead-text">@currency($entry->allowances)</span>
                                            @endif
                                        </td>
                                        <td class="tb-tnx-info text-end text-danger">
                                            <span class="sub-text">-@currency($entry->pension_deduction)</span>
                                        </td>
                                        <td class="tb-tnx-info text-end text-danger">
                                            <span class="sub-text">-@currency($entry->nhf_deduction)</span>
                                        </td>
                                        <td class="tb-tnx-info text-end text-danger">
                                            <span class="sub-text">-@currency($entry->paye_tax)</span>
                                        </td>
                                        <td class="tb-tnx-info text-end text-success">
                                            <span class="lead-text">@currency($entry->net_salary)</span>
                                        </td>
                                        <td class="tb-tnx-action">
                                            @if($payroll->status === 'Approved')
                                                <a href="{{ route('admin.hr.payroll-entry.payslip', $entry) }}" class="btn btn-xs btn-outline-primary"><em class="icon ni ni-download"></em><span>Payslip</span></a>
                                            @else
                                                <span class="text-soft font-italic">Approve to download</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center p-4">
                                            <div class="text-muted">No employees registered on this payroll run.</div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
