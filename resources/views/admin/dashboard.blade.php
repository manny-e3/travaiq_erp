@extends('layouts.admin')

@section('title', 'ERP Dashboard')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">

                <!-- Header / Action Bar -->
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between flex-wrap g-3">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">ERP Executive Dashboard</h3>
                            <div class="nk-block-des text-soft">
                                <p>Comprehensive real-time overview of workforce, enterprise assets, talent acquisition, and operations.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <div class="toggle-wrap nk-block-tools-toggle">
                                <ul class="nk-block-tools g-3">
                                    <li>
                                        <a href="{{ route('admin.users.create') }}" class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                                            <em class="icon ni ni-user-add"></em>
                                            <span>Add Employee</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.assets.create') }}" class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                                            <em class="icon ni ni-plus-circle"></em>
                                            <span>Deploy Asset</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.hr.recruitment.jobs.create') }}" class="btn btn-primary">
                                            <em class="icon ni ni-briefcase"></em>
                                            <span>Post Job Opening</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KPI Metric Cards -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <!-- Total Workforce -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="sub-text fw-bold text-uppercase" style="letter-spacing: 0.05em; font-size: 11px;">Total Workforce</span>
                                        <div class="user-avatar bg-primary-dim text-primary sm">
                                            <em class="icon ni ni-users-fill"></em>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-baseline mb-2">
                                        <h2 class="amount mb-0 fw-bold">{{ number_format($totalUsers) }}</h2>
                                        <span class="fs-12px text-success ms-2 fw-medium">
                                            <em class="icon ni ni-arrow-up-right"></em> {{ $activeUsers }} Active
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center fs-12px text-soft pt-1 border-top">
                                        <span>{{ $totalDepartments }} Departments</span>
                                        <a href="{{ route('admin.users.index') }}" class="link link-primary fw-medium">View Staff &rarr;</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enterprise Assets -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="sub-text fw-bold text-uppercase" style="letter-spacing: 0.05em; font-size: 11px;">Corporate Assets</span>
                                        <div class="user-avatar bg-info-dim text-info sm">
                                            <em class="icon ni ni-box"></em>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-baseline mb-2">
                                        <h2 class="amount mb-0 fw-bold">{{ number_format($totalAssets) }}</h2>
                                        <span class="fs-12px text-soft ms-2 fw-medium">
                                            {{ $currencySymbol }}{{ number_format($totalAssetValue / 1000000, 1) }}M Book Value
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center fs-12px text-soft pt-1 border-top">
                                        <span>{{ $activeAssets }} Active | {{ $maintenanceAssets }} Maint.</span>
                                        <a href="{{ route('admin.assets.index') }}" class="link link-info fw-medium">Asset Hub &rarr;</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Talent Pipeline -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="sub-text fw-bold text-uppercase" style="letter-spacing: 0.05em; font-size: 11px;">Talent Pipeline</span>
                                        <div class="user-avatar bg-warning-dim text-warning sm">
                                            <em class="icon ni ni-briefcase"></em>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-baseline mb-2">
                                        <h2 class="amount mb-0 fw-bold">{{ number_format($totalApplications) }}</h2>
                                        <span class="fs-12px text-warning ms-2 fw-medium">
                                            {{ $openJobsCount }} Open Roles
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center fs-12px text-soft pt-1 border-top">
                                        <span>{{ $interviewingApplications }} In Interview | {{ $offeredApplications }} Offered</span>
                                        <a href="{{ route('admin.hr.recruitment.jobs.index') }}" class="link link-warning fw-medium">Careers &rarr;</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Operations & Helpdesk -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="sub-text fw-bold text-uppercase" style="letter-spacing: 0.05em; font-size: 11px;">Operations Helpdesk</span>
                                        <div class="user-avatar bg-danger-dim text-danger sm">
                                            <em class="icon ni ni-headphone-fill"></em>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-baseline mb-2">
                                        <h2 class="amount mb-0 fw-bold">{{ number_format($openTickets) }}</h2>
                                        <span class="fs-12px text-danger ms-2 fw-medium">
                                            {{ $urgentTickets }} Urgent Tickets
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center fs-12px text-soft pt-1 border-top">
                                        <span>{{ $resolvedTickets }} Resolved Total</span>
                                        <a href="{{ route('admin.helpdesk.index') }}" class="link link-danger fw-medium">Tickets &rarr;</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mid Section: Analytics & Breakdown -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <!-- Department Workforce Distribution -->
                        <div class="col-lg-6 col-xxl-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="card-title-group mb-3">
                                        <div class="card-title">
                                            <h6 class="title">Workforce by Department</h6>
                                            <p class="text-soft fs-12px">Distribution of active human capital across organizational units.</p>
                                        </div>
                                        <div class="card-tools">
                                            <a href="{{ route('admin.departments.index') }}" class="link link-sm">View All</a>
                                        </div>
                                    </div>

                                    <div class="department-list mt-3">
                                        @forelse($departments as $dept)
                                            @php
                                                $percent = $totalUsers > 0 ? round(($dept->users_count / $totalUsers) * 100) : 0;
                                            @endphp
                                            <div class="dept-item mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-bold fs-13px text-dark">{{ $dept->name }}</span>
                                                    <span class="fs-12px text-soft">{{ $dept->users_count }} members ({{ $percent }}%)</span>
                                                </div>
                                                <div class="progress progress-sm" style="height: 6px; border-radius: 3px;">
                                                    <div class="progress-bar bg-primary" style="width: {{ $percent }}%"></div>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-soft text-center py-3">No departmental data available.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Asset Categories & Infrastructure Health -->
                        <div class="col-lg-6 col-xxl-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="card-title-group mb-3">
                                        <div class="card-title">
                                            <h6 class="title">Asset Infrastructure Allocation</h6>
                                            <p class="text-soft fs-12px">Hardware, networking, and facility inventory summary.</p>
                                        </div>
                                        <div class="card-tools">
                                            <a href="{{ route('admin.assets.index') }}" class="link link-sm">Inventory Hub</a>
                                        </div>
                                    </div>

                                    <div class="row g-3 mt-1">
                                        @forelse($assetCategories as $cat)
                                            <div class="col-6">
                                                <div class="p-3 bg-light rounded border border-light">
                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                        <span class="fs-11px text-uppercase fw-bold text-soft">{{ $cat->name }}</span>
                                                        <span class="badge badge-dim badge-primary">{{ $cat->assets_count }} items</span>
                                                    </div>
                                                    <div class="fs-18px fw-bold text-dark">{{ $cat->assets_count }} Assets</div>
                                                    <div class="fs-11px text-soft text-truncate">{{ $cat->description ?? 'Standard Category' }}</div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <p class="text-soft text-center py-3">No asset categories registered.</p>
                                            </div>
                                        @endforelse
                                    </div>

                                    <div class="alert alert-light alert-icon mt-4 mb-0 bg-lighter border">
                                        <em class="icon ni ni-shield-check text-success"></em>
                                        <div class="alert-text fs-12px">
                                            <strong>Enterprise Maintenance Schedule:</strong> All critical systems and data center backup switches are functioning within operational thresholds.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Operational Tables: Leaves & Tickets -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <!-- Pending Leave Requests -->
                        <div class="col-lg-6 col-xxl-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner border-bottom py-3">
                                    <div class="card-title-group">
                                        <div class="card-title">
                                            <h6 class="title">Recent Leave Requests</h6>
                                            <span class="sub-text">{{ $pendingLeaves }} requests awaiting executive review</span>
                                        </div>
                                        <div class="card-tools">
                                            <a href="{{ route('admin.hr.leaves.index') }}" class="btn btn-sm btn-outline-primary">Manage Leaves</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-tranx is-compact fs-13px">
                                        <thead>
                                            <tr class="tb-tnx-head bg-light">
                                                <th>Employee</th>
                                                <th>Leave Type</th>
                                                <th>Duration</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentLeaves as $leave)
                                                <tr>
                                                    <td>
                                                        <div class="user-card">
                                                            <div class="user-avatar sm bg-primary-dim text-primary">
                                                                <span>{{ strtoupper(substr($leave->user->name ?? 'U', 0, 2)) }}</span>
                                                            </div>
                                                            <div class="user-info">
                                                                <span class="tb-lead">{{ $leave->user->name ?? 'Staff Member' }}</span>
                                                                <span class="sub-text fs-11px">{{ $leave->user->job_title ?? 'Employee' }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="tb-sub fw-medium">{{ $leave->leave_type }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="tb-sub">{{ $leave->total_days }} day(s)</span>
                                                        <span class="sub-text fs-11px">{{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }}</span>
                                                    </td>
                                                    <td>
                                                        @if($leave->status === 'Approved')
                                                            <span class="badge badge-dim badge-success">Approved</span>
                                                        @elseif($leave->status === 'Pending')
                                                            <span class="badge badge-dim badge-warning">Pending</span>
                                                        @else
                                                            <span class="badge badge-dim badge-danger">{{ $leave->status }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-soft py-4">No recent leave requests.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Active Service & Operations Tickets -->
                        <div class="col-lg-6 col-xxl-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner border-bottom py-3">
                                    <div class="card-title-group">
                                        <div class="card-title">
                                            <h6 class="title">Helpdesk Operations</h6>
                                            <span class="sub-text">{{ $openTickets }} open operational service tickets</span>
                                        </div>
                                        <div class="card-tools">
                                            <a href="{{ route('admin.helpdesk.index') }}" class="btn btn-sm btn-outline-danger">All Tickets</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-tranx is-compact fs-13px">
                                        <thead>
                                            <tr class="tb-tnx-head bg-light">
                                                <th>Ticket Key & Subject</th>
                                                <th>Priority</th>
                                                <th>Client / Organization</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentTickets as $ticket)
                                                <tr>
                                                    <td>
                                                        <span class="tb-lead text-truncate d-block" style="max-width: 220px;">
                                                            <a href="{{ route('admin.helpdesk.show', $ticket->id) }}" class="text-dark">{{ $ticket->subject }}</a>
                                                        </span>
                                                        <span class="sub-text fs-11px text-primary font-monospace">{{ $ticket->ticket_key }} &bull; {{ $ticket->category }}</span>
                                                    </td>
                                                    <td>
                                                        @if($ticket->priority === 'Urgent')
                                                            <span class="badge badge-danger">Urgent</span>
                                                        @elseif($ticket->priority === 'High')
                                                            <span class="badge badge-warning">High</span>
                                                        @elseif($ticket->priority === 'Medium')
                                                            <span class="badge badge-info">Medium</span>
                                                        @else
                                                            <span class="badge badge-light">Low</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="tb-sub">{{ $ticket->customer->name ?? 'External Client' }}</span>
                                                    </td>
                                                    <td>
                                                        @if($ticket->status === 'Resolved')
                                                            <span class="badge badge-dim badge-success">Resolved</span>
                                                        @elseif($ticket->status === 'In Progress')
                                                            <span class="badge badge-dim badge-info">In Progress</span>
                                                        @else
                                                            <span class="badge badge-dim badge-warning">Open</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-soft py-4">No support tickets found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lower Section: Recent Assets & Recruitment Applications -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <!-- Corporate Assets Snapshot -->
                        <div class="col-lg-6 col-xxl-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner border-bottom py-3">
                                    <div class="card-title-group">
                                        <div class="card-title">
                                            <h6 class="title">Corporate Assets Snapshot</h6>
                                            <span class="sub-text">Latest deployed and cataloged enterprise equipment</span>
                                        </div>
                                        <div class="card-tools">
                                            <a href="{{ route('admin.assets.index') }}" class="link link-sm">View All Assets</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-tranx is-compact fs-13px">
                                        <thead>
                                            <tr class="tb-tnx-head bg-light">
                                                <th>Asset Tag & Device</th>
                                                <th>Category</th>
                                                <th>Cost</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentAssets as $ast)
                                                <tr>
                                                    <td>
                                                        <span class="tb-lead">{{ $ast->name }}</span>
                                                        <span class="sub-text fs-11px text-primary font-monospace">{{ $ast->asset_tag }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="tb-sub">{{ $ast->category->name ?? 'General IT' }}</span>
                                                        <span class="sub-text fs-11px">{{ $ast->location->name ?? 'Headquarters' }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="tb-sub fw-bold">{{ $currencySymbol }}{{ number_format($ast->purchase_cost, 0) }}</span>
                                                    </td>
                                                    <td>
                                                        @if($ast->status === 'Active')
                                                            <span class="badge badge-dim badge-success">Active</span>
                                                        @elseif($ast->status === 'Maintenance')
                                                            <span class="badge badge-dim badge-warning">Maintenance</span>
                                                        @else
                                                            <span class="badge badge-dim badge-secondary">{{ $ast->status }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-soft py-4">No assets registered yet.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Recruitment Candidates -->
                        <div class="col-lg-6 col-xxl-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner border-bottom py-3">
                                    <div class="card-title-group">
                                        <div class="card-title">
                                            <h6 class="title">Recruitment Pipeline (Candidates)</h6>
                                            <span class="sub-text">{{ $pendingApplications }} candidates awaiting screening</span>
                                        </div>
                                        <div class="card-tools">
                                            <a href="{{ route('admin.hr.recruitment.jobs.index') }}" class="link link-sm">Recruitment Hub</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-tranx is-compact fs-13px">
                                        <thead>
                                            <tr class="tb-tnx-head bg-light">
                                                <th>Candidate</th>
                                                <th>Target Position</th>
                                                <th>Applied Date</th>
                                                <th>Stage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentApplications as $candidate)
                                                <tr>
                                                    <td>
                                                        <div class="user-card">
                                                            <div class="user-avatar sm bg-warning-dim text-warning">
                                                                <span>{{ strtoupper(substr($candidate->first_name, 0, 1) . substr($candidate->last_name, 0, 1)) }}</span>
                                                            </div>
                                                            <div class="user-info">
                                                                <span class="tb-lead">{{ $candidate->first_name }} {{ $candidate->last_name }}</span>
                                                                <span class="sub-text fs-11px">{{ $candidate->email }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="tb-sub fw-medium">{{ $candidate->jobPosting->title ?? 'Open Position' }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="tb-sub">{{ $candidate->created_at->format('M d, Y') }}</span>
                                                    </td>
                                                    <td>
                                                        @if($candidate->status === 'Offered' || $candidate->status === 'Hired')
                                                            <span class="badge badge-dim badge-success">{{ $candidate->status }}</span>
                                                        @elseif($candidate->status === 'Interviewing')
                                                            <span class="badge badge-dim badge-info">Interviewing</span>
                                                        @elseif($candidate->status === 'In Review')
                                                            <span class="badge badge-dim badge-warning">In Review</span>
                                                        @else
                                                            <span class="badge badge-dim badge-light">{{ $candidate->status }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-soft py-4">No candidates in pipeline.</td>
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
    </div>
</div>
@endsection