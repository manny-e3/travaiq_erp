@extends('layouts.admin')

@section('title', 'Payroll Runs')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Payroll Processing</h3>
                            <div class="nk-block-des text-soft">
                                <p>Manage monthly payroll initialization, allowances, statutory deductions, and automated GL entries.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('admin.hr.payroll.create') }}" class="btn btn-primary d-none d-md-inline-flex"><em class="icon ni ni-plus"></em><span>Initialize Payroll Run</span></a>
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

                <!-- Stats Grid -->
                <div class="row g-gs mb-4">
                    <div class="col-sm-6 col-xxl-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title text-soft">Total Runs</h6>
                                    </div>
                                </div>
                                <div class="align-end gy-1">
                                    <div class="amount">{{ $stats['total_runs'] }}</div>
                                    <div class="subtitle text-soft">Initialized Runs</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xxl-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title text-soft">Approved Runs</h6>
                                    </div>
                                </div>
                                <div class="align-end gy-1">
                                    <div class="amount text-success">{{ $stats['total_approved'] }}</div>
                                    <div class="subtitle text-soft">Processed & Balanced</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xxl-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title text-soft">Gross Processed</h6>
                                    </div>
                                </div>
                                <div class="align-end gy-1">
                                    <div class="amount text-primary">@currency($stats['total_gross_processed'])</div>
                                    <div class="subtitle text-soft">Cumulative Gross Salary</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xxl-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title text-soft">Total Net Paid</h6>
                                    </div>
                                </div>
                                <div class="align-end gy-1">
                                    <div class="amount text-info">@currency($stats['total_net_paid'])</div>
                                    <div class="subtitle text-soft">Transferred to Staff</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payroll runs list -->
                <div class="card card-bordered card-stretch">
                    <div class="card-inner-group">
                        <div class="card-inner p-0">
                            <table class="table table-tranx">
                                <thead>
                                    <tr class="tb-tnx-head">
                                        <th class="tb-tnx-id">Period</th>
                                        <th class="tb-tnx-info">Status</th>
                                        <th class="tb-tnx-info text-end">Total Gross</th>
                                        <th class="tb-tnx-info text-end">Total Deductions</th>
                                        <th class="tb-tnx-info text-end">Total Net</th>
                                        <th class="tb-tnx-info">Processed By</th>
                                        <th class="tb-tnx-info">Processed At</th>
                                        <th class="tb-tnx-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payrollRuns as $run)
                                    @php
                                        $parsedPeriod = \Carbon\Carbon::createFromFormat('Y-m', $run->period);
                                    @endphp
                                    <tr class="tb-tnx-item">
                                        <td class="tb-tnx-id">
                                            <span class="lead-text">{{ $parsedPeriod->format('F, Y') }}</span>
                                            <span class="sub-text">{{ $run->period }}</span>
                                        </td>
                                        <td class="tb-tnx-info">
                                            @if($run->status === 'Draft')
                                                <span class="badge badge-dim bg-warning text-warning">Draft</span>
                                            @else
                                                <span class="badge badge-dim bg-success text-success">Approved & Posted</span>
                                            @endif
                                        </td>
                                        <td class="tb-tnx-info text-end">
                                            <span class="lead-text">@currency($run->total_gross)</span>
                                        </td>
                                        <td class="tb-tnx-info text-end text-danger">
                                            <span class="lead-text">-@currency($run->total_deductions)</span>
                                        </td>
                                        <td class="tb-tnx-info text-end text-success">
                                            <span class="lead-text">@currency($run->total_net)</span>
                                        </td>
                                        <td class="tb-tnx-info">
                                            <span class="sub-text">{{ $run->processedBy->name ?? 'System' }}</span>
                                        </td>
                                        <td class="tb-tnx-info">
                                            <span class="sub-text">{{ $run->processed_at ? $run->processed_at->format('M d, Y H:i') : 'Pending Approval' }}</span>
                                        </td>
                                        <td class="tb-tnx-action">
                                            <a href="{{ route('admin.hr.payroll.show', $run) }}" class="btn btn-sm btn-outline-light btn-white">View Details</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center p-4">
                                            <div class="text-muted">No payroll runs found. Click "Initialize Payroll Run" to start.</div>
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
