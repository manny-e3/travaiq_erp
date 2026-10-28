@extends('layouts.admin')

@section('title', 'Leave Management')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Leave Management</h3>
                            <div class="nk-block-des text-soft">
                                <p>Track leave balances, submit leave requests, and manage approval workflows in real time.</p>
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

                @if(!Auth::user()->hasRole('admin'))
                    <!-- Regular Employee Leave Balances Cards -->
                    <div class="row g-gs mb-4">
                        @foreach($myBalances as $bal)
                            @php
                                $color = 'primary';
                                if ($bal->leave_type === 'Annual') $color = 'success';
                                elseif ($bal->leave_type === 'Sick') $color = 'warning';
                                elseif ($bal->leave_type === 'Maternity') $color = 'info';
                                elseif ($bal->leave_type === 'Paternity') $color = 'pink';
                            @endphp
                            <div class="col-sm-6 col-md-3">
                                <div class="card card-bordered border-t-{{ $color }} border-t-3">
                                    <div class="card-inner text-center">
                                        <h6 class="title text-soft mb-2">{{ $bal->leave_type }} Leave</h6>
                                        <div class="amount mb-1 text-{{ $color }}">{{ $bal->remaining_days }} / {{ $bal->allocated_days }}</div>
                                        <span class="sub-text text-soft">Days Remaining</span>
                                        <div class="progress progress-sm mt-3">
                                            @php
                                                $pct = $bal->allocated_days > 0 ? ($bal->remaining_days / $bal->allocated_days) * 100 : 0;
                                            @endphp
                                            <div class="progress-bar bg-{{ $color }}" style="width: {{ $pct }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Regular Employee Tabs -->
                    <div class="card card-bordered">
                        <ul class="nav nav-tabs nav-tabs-card nav-tabs-xs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#request-leave">Request Leave</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#my-history">My Leave History</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <!-- Request Form -->
                            <div class="tab-pane active p-4" id="request-leave">
                                <form action="{{ route('admin.hr.leaves.store') }}" method="POST">
                                    @csrf
                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Leave Type</label>
                                                <div class="form-control-wrap">
                                                    <select class="form-select form-control" name="leave_type" required>
                                                        <option value="">Select Leave Type</option>
                                                        @foreach($myBalances as $bal)
                                                            <option value="{{ $bal->leave_type }}">{{ $bal->leave_type }} Leave ({{ $bal->remaining_days }} days remaining)</option>
                                                        @endforeach
                                                        <option value="Unpaid">Unpaid Leave</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Start Date</label>
                                                <input type="date" class="form-control" name="start_date" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">End Date</label>
                                                <input type="date" class="form-control" name="end_date" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Reason</label>
                                                <textarea class="form-control" name="reason" rows="3" placeholder="Describe the reason for your leave request..." required></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">Submit Leave Request</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Leave History -->
                            <div class="tab-pane p-0" id="my-history">
                                <table class="table table-tranx">
                                    <thead>
                                        <tr class="tb-tnx-head">
                                            <th>Leave Type</th>
                                            <th>Duration</th>
                                            <th>Total Days</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                            <th>Action/Approver Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($myRequests as $req)
                                            <tr class="tb-tnx-item">
                                                <td><span class="lead-text">{{ $req->leave_type }}</span></td>
                                                <td>
                                                    <span class="sub-text">{{ $req->start_date->format('M d, Y') }} to {{ $req->end_date->format('M d, Y') }}</span>
                                                </td>
                                                <td><span class="sub-text">{{ $req->total_days }} working days</span></td>
                                                <td><span class="sub-text">{{ $req->reason }}</span></td>
                                                <td>
                                                    @if($req->status === 'Pending')
                                                        <span class="badge badge-dim bg-warning text-warning">Pending</span>
                                                    @elseif($req->status === 'Approved')
                                                        <span class="badge badge-dim bg-success text-success">Approved</span>
                                                    @else
                                                        <span class="badge badge-dim bg-danger text-danger">Rejected</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($req->status === 'Approved')
                                                        <span class="sub-text text-soft">Approved by: {{ $req->approvedBy->name ?? 'Admin' }}</span>
                                                    @elseif($req->status === 'Rejected')
                                                        <span class="sub-text text-danger">Reason: {{ $req->rejection_reason }}</span>
                                                    @else
                                                        <span class="sub-text text-soft">Awaiting approval</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center p-4">No leave requests found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Admin View Dashboard -->
                    <div class="card card-bordered">
                        <ul class="nav nav-tabs nav-tabs-card nav-tabs-xs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#pending-leaves">Pending Requests</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#processed-leaves">Processed Leaves</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#staff-balances">Staff Leave Balances</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <!-- Pending Requests -->
                            <div class="tab-pane active p-0" id="pending-leaves">
                                <table class="table table-tranx">
                                    <thead>
                                        <tr class="tb-tnx-head">
                                            <th>Employee</th>
                                            <th>Leave Type</th>
                                            <th>Duration</th>
                                            <th>Days</th>
                                            <th>Reason</th>
                                            <th class="tb-tnx-action">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pendingRequests as $req)
                                            <tr class="tb-tnx-item">
                                                <td>
                                                    <div class="user-card">
                                                        <div class="user-avatar bg-primary">
                                                            <span>{{ strtoupper(substr($req->user->name, 0, 2)) }}</span>
                                                        </div>
                                                        <div class="user-info">
                                                            <span class="lead-text">{{ $req->user->name }}</span>
                                                            <span class="sub-text">{{ $req->user->designation->name ?? 'Employee' }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span class="lead-text">{{ $req->leave_type }}</span></td>
                                                <td>
                                                    <span class="sub-text">{{ $req->start_date->format('M d, Y') }} to {{ $req->end_date->format('M d, Y') }}</span>
                                                </td>
                                                <td><span class="lead-text">{{ $req->total_days }} days</span></td>
                                                <td><span class="sub-text">{{ $req->reason }}</span></td>
                                                <td class="tb-tnx-action">
                                                    <div class="d-flex align-items-center">
                                                        <form action="{{ route('admin.hr.leaves.approve', $req) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success me-2">Approve</button>
                                                        </form>
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $req->id }}">Reject</button>
                                                    </div>

                                                    <!-- Rejection Modal -->
                                                    <div class="modal fade" id="rejectModal-{{ $req->id }}" tabindex="-1" role="dialog">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <form action="{{ route('admin.hr.leaves.reject', $req) }}" method="POST">
                                                                    @csrf
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Reject Leave Request</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <label class="form-label">Reason for Rejection</label>
                                                                            <textarea class="form-control" name="rejection_reason" rows="3" required placeholder="Type rejection reason..."></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-outline-light btn-white" data-bs-dismiss="modal">Close</button>
                                                                        <button type="submit" class="btn btn-danger">Reject Request</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center p-4">No pending leave requests.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Processed History -->
                            <div class="tab-pane p-0" id="processed-leaves">
                                <table class="table table-tranx">
                                    <thead>
                                        <tr class="tb-tnx-head">
                                            <th>Employee</th>
                                            <th>Leave Type</th>
                                            <th>Duration</th>
                                            <th>Days</th>
                                            <th>Status</th>
                                            <th>Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($processedRequests as $req)
                                            <tr class="tb-tnx-item">
                                                <td>
                                                    <div class="user-card">
                                                        <div class="user-info">
                                                            <span class="lead-text">{{ $req->user->name }}</span>
                                                            <span class="sub-text">{{ $req->user->email }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span class="lead-text">{{ $req->leave_type }}</span></td>
                                                <td>
                                                    <span class="sub-text">{{ $req->start_date->format('M d, Y') }} to {{ $req->end_date->format('M d, Y') }}</span>
                                                </td>
                                                <td><span class="sub-text">{{ $req->total_days }} days</span></td>
                                                <td>
                                                    @if($req->status === 'Approved')
                                                        <span class="badge badge-dim bg-success text-success">Approved</span>
                                                    @else
                                                        <span class="badge badge-dim bg-danger text-danger">Rejected</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($req->status === 'Approved')
                                                        <span class="sub-text text-soft">Approved by {{ $req->approvedBy->name ?? 'Admin' }} on {{ $req->approved_at ? $req->approved_at->format('Y-m-d') : '' }}</span>
                                                    @else
                                                        <span class="sub-text text-danger">Rejection note: {{ $req->rejection_reason }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center p-4">No processed leave history.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Staff Leave Balances & Allocation Manager -->
                            <div class="tab-pane p-0" id="staff-balances">
                                <table class="table table-tranx">
                                    <thead>
                                        <tr class="tb-tnx-head">
                                            <th>Employee Name</th>
                                            <th>Department</th>
                                            <th>Annual</th>
                                            <th>Sick </th>
                                            <th>Maternity/Paternity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($activeUsers as $activeUser)
                                            @php
                                                $userBals = $allBalances->get($activeUser->id) ?? collect();
                                                $annual = $userBals->where('leave_type', 'Annual')->first();
                                                $sick = $userBals->where('leave_type', 'Sick')->first();
                                                $maternity = $userBals->where('leave_type', 'Maternity')->first();
                                                $paternity = $userBals->where('leave_type', 'Paternity')->first();
                                            @endphp
                                            <tr class="tb-tnx-item">
                                                <td>
                                                    <span class="lead-text">{{ $activeUser->name }}</span>
                                                    <span class="sub-text text-soft">ID: {{ $activeUser->employee_id ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <span class="sub-text">{{ $activeUser->department->name ?? 'N/A' }}</span>
                                                </td>
                                                <!-- Annual -->
                                                <td>
                                                    @if($annual)
                                                        <div class="d-flex align-items-center">
                                                            <span class="me-2 text-success font-weight-bold">{{ $annual->remaining_days }}</span>
                                                            
                                                        </div>
                                                    @else
                                                        <span class="text-soft">-</span>
                                                    @endif
                                                </td>
                                                <!-- Sick -->
                                                <td>
                                                    @if($sick)
                                                        <div class="d-flex align-items-center">
                                                            <span class="me-2 text-warning font-weight-bold">{{ $sick->remaining_days }}</span>
                                                            
                                                        </div>
                                                    @else
                                                        <span class="text-soft">-</span>
                                                    @endif
                                                </td>
                                                <!-- Maternity / Paternity -->
                                                <td>
                                                    @if($maternity)
                                                        <div class="d-flex align-items-center">
                                                            <span class="me-2 text-info font-weight-bold">{{ $maternity->remaining_days }} (Mat)</span>
                                                            
                                                        </div>
                                                    @elseif($paternity)
                                                        <div class="d-flex align-items-center">
                                                            <span class="me-2 text-pink font-weight-bold">{{ $paternity->remaining_days }} (Pat)</span>
                                                            <form action="{{ route('admin.hr.leaves.updateBalance', $paternity) }}" method="POST" class="d-flex align-items-center">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="number" class="form-control form-control-xs text-end me-1" name="allocated_days" value="{{ $paternity->allocated_days }}" style="width: 55px;" min="0">
                                                                <button type="submit" class="btn btn-xs btn-primary"><em class="icon ni ni-check"></em></button>
                                                            </form>
                                                        </div>
                                                    @else
                                                        <span class="text-soft">No gender-based leave</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center p-4">No staff members found.</td>
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
