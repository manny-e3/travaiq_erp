@extends('layouts.admin')

@section('title', 'Overtime Tracking')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Overtime Tracking</h3>
                            <div class="nk-block-des text-soft">
                                <p>Log and approve employee overtime hours with custom multipliers for weekday and weekend work.</p>
                            </div>
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

                @if($errors->any())
                    <div class="alert alert-danger alert-icon alert-dismissible">
                        <em class="icon ni ni-cross-circle"></em> <strong>Error!</strong>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button class="close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Stats summary card -->
                <div class="row g-gs mb-4">
                    <div class="col-sm-6">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                @if(!Auth::user()->hasRole('admin'))
                                    <div class="card-title text-soft mb-1">My Approved Overtime This Month</div>
                                    <div class="amount text-primary">{{ number_format($myHoursThisMonth, 1) }} Hours</div>
                                    <span class="sub-text text-soft">Accumulated approved overtime</span>
                                @else
                                    <div class="card-title text-soft mb-1">Total Approved Staff Overtime This Month</div>
                                    <div class="amount text-success">{{ number_format($totalHoursThisMonth, 1) }} Hours</div>
                                    <span class="sub-text text-soft">Accumulated across all active staff</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if(!Auth::user()->hasRole('admin'))
                    <!-- Regular Employee Overtime Tabs -->
                    <div class="card card-bordered">
                        <ul class="nav nav-tabs nav-tabs-card nav-tabs-xs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#log-overtime">Log Overtime</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#my-overtime-history">My Logs History</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <!-- Log Overtime Form -->
                            <div class="tab-pane active p-4" id="log-overtime">
                                <form action="{{ route('admin.hr.overtime.store') }}" method="POST">
                                    @csrf
                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Date</label>
                                                <input type="date" class="form-control" name="date" max="{{ date('Y-m-d') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Overtime Hours</label>
                                                <input type="number" step="0.5" class="form-control" name="hours" min="0.5" max="16" placeholder="e.g. 3.5" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Rate Multiplier</label>
                                                <div class="form-control-wrap">
                                                    <select class="form-select form-control" name="rate_multiplier" required>
                                                        <option value="1.5">1.5x (Standard Weekday Overtime)</option>
                                                        <option value="2.0">2.0x (Weekend & Public Holiday)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Work Description</label>
                                                <textarea class="form-control" name="description" rows="3" placeholder="Describe the tasks completed during this overtime session..." required></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">Submit Overtime Log</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- History Table -->
                            <div class="tab-pane p-0" id="my-overtime-history">
                                <table class="table table-tranx">
                                    <thead>
                                        <tr class="tb-tnx-head">
                                            <th>Date</th>
                                            <th>Hours</th>
                                            <th>Multiplier</th>
                                            <th>Work Description</th>
                                            <th>Status</th>
                                            <th>Approver details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($myOvertime as $record)
                                            <tr class="tb-tnx-item">
                                                <td><span class="lead-text">{{ $record->date->format('Y-m-d') }}</span></td>
                                                <td><span class="lead-text">{{ $record->hours }} Hours</span></td>
                                                <td><span class="badge badge-dim bg-outline-primary">{{ $record->rate_multiplier }}x</span></td>
                                                <td><span class="sub-text">{{ $record->description }}</span></td>
                                                <td>
                                                    @if($record->status === 'Pending')
                                                        <span class="badge badge-dim bg-warning text-warning">Pending</span>
                                                    @elseif($record->status === 'Approved')
                                                        <span class="badge badge-dim bg-success text-success">Approved</span>
                                                    @else
                                                        <span class="badge badge-dim bg-danger text-danger">Rejected</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($record->status === 'Approved')
                                                        <span class="sub-text text-soft">Approved by: {{ $record->approvedBy->name ?? 'Admin' }}</span>
                                                    @elseif($record->status === 'Rejected')
                                                        <span class="sub-text text-danger">Rejected by: {{ $record->approvedBy->name ?? 'Admin' }}</span>
                                                    @else
                                                        <span class="sub-text text-soft">Awaiting approval</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center p-4">No overtime hours logged yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Admin View Tabs -->
                    <div class="card card-bordered">
                        <ul class="nav nav-tabs nav-tabs-card nav-tabs-xs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#pending-overtime">Pending Logs</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#processed-overtime">History Logs</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <!-- Pending Approvals -->
                            <div class="tab-pane active p-0" id="pending-overtime">
                                <table class="table table-tranx">
                                    <thead>
                                        <tr class="tb-tnx-head">
                                            <th>Employee</th>
                                            <th>Date</th>
                                            <th>Hours</th>
                                            <th>Rate</th>
                                            <th>Work Description</th>
                                            <th class="tb-tnx-action">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pendingOvertime as $record)
                                            <tr class="tb-tnx-item">
                                                <td>
                                                    <div class="user-card">
                                                        <div class="user-avatar bg-primary">
                                                            <span>{{ strtoupper(substr($record->user->name, 0, 2)) }}</span>
                                                        </div>
                                                        <div class="user-info">
                                                            <span class="lead-text">{{ $record->user->name }}</span>
                                                            <span class="sub-text">{{ $record->user->designation->name ?? 'Employee' }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span class="lead-text">{{ $record->date->format('Y-m-d') }}</span></td>
                                                <td><span class="lead-text font-weight-bold">{{ $record->hours }} Hours</span></td>
                                                <td><span class="badge badge-dim bg-outline-primary">{{ $record->rate_multiplier }}x</span></td>
                                                <td><span class="sub-text">{{ $record->description }}</span></td>
                                                <td class="tb-tnx-action">
                                                    <div class="d-flex align-items-center">
                                                        <form action="{{ route('admin.hr.overtime.approve', $record) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success me-2">Approve</button>
                                                        </form>
                                                        <form action="{{ route('admin.hr.overtime.reject', $record) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center p-4">No pending overtime approvals.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Processed Overtime Logs -->
                            <div class="tab-pane p-0" id="processed-overtime">
                                <table class="table table-tranx">
                                    <thead>
                                        <tr class="tb-tnx-head">
                                            <th>Employee</th>
                                            <th>Date</th>
                                            <th>Hours</th>
                                            <th>Rate</th>
                                            <th>Status</th>
                                            <th>Processed By</th>
                                            <th>Work Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($processedOvertime as $record)
                                            <tr class="tb-tnx-item">
                                                <td>
                                                    <div class="user-card">
                                                        <div class="user-info">
                                                            <span class="lead-text">{{ $record->user->name }}</span>
                                                            <span class="sub-text">{{ $record->user->email }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span class="sub-text">{{ $record->date->format('Y-m-d') }}</span></td>
                                                <td><span class="lead-text">{{ $record->hours }} Hours</span></td>
                                                <td><span class="badge badge-dim bg-outline-primary">{{ $record->rate_multiplier }}x</span></td>
                                                <td>
                                                    @if($record->status === 'Approved')
                                                        <span class="badge badge-dim bg-success text-success">Approved</span>
                                                    @else
                                                        <span class="badge badge-dim bg-danger text-danger">Rejected</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="sub-text text-soft">By {{ $record->approvedBy->name ?? 'Admin' }}</span>
                                                </td>
                                                <td><span class="sub-text text-soft">{{ $record->description }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center p-4">No processed overtime records.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
