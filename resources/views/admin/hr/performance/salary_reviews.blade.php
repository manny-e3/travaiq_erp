@extends('layouts.admin')

@section('title', 'Salary & Promotion Reviews')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Salary & Promotion Reviews</h3>
                            <div class="nk-block-des text-soft">
                                <p>Review, approve, or reject proposed pay increases and designation promotions generated from appraisals.</p>
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

                <div class="card card-bordered">
                    <ul class="nav nav-tabs nav-tabs-card nav-tabs-xs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#pending-reviews">Pending Review approvals</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#processed-reviews">Processed History Logs</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <!-- Pending Approvals -->
                        <div class="tab-pane active p-0" id="pending-reviews">
                            <table class="table table-tranx">
                                <thead>
                                    <tr class="tb-tnx-head">
                                        <th>Employee Details</th>
                                        <th>Current Salary (@currencySymbol)</th>
                                        <th>Proposed Salary (@currencySymbol)</th>
                                        <th>Current Designation</th>
                                        <th>Proposed Designation</th>
                                        <th>Effective Date</th>
                                        <th>Notes</th>
                                        <th class="tb-tnx-action">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pendingReviews as $review)
                                        <tr class="tb-tnx-item">
                                            <td>
                                                <div class="user-card">
                                                    <div class="user-info">
                                                        <span class="lead-text">{{ $review->user->name }}</span>
                                                        <span class="sub-text">{{ $review->user->job_title ?? 'Employee' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="sub-text font-weight-bold">@currency($review->current_salary)</span></td>
                                            <td><span class="lead-text font-weight-bold text-success">@currency($review->proposed_salary)</span></td>
                                            <td><span class="sub-text">{{ $review->currentDesignation->name ?? 'None' }}</span></td>
                                            <td>
                                                @if($review->proposed_designation_id && $review->proposed_designation_id !== $review->current_designation_id)
                                                    <span class="badge badge-dim bg-success text-success">{{ $review->proposedDesignation->name }}</span>
                                                @else
                                                     <span class="sub-text text-soft">No Change</span>
                                                @endif
                                            </td>
                                            <td><span class="sub-text">{{ $review->effective_date->format('Y-m-d') }}</span></td>
                                            <td><span class="sub-text text-soft">{{ $review->notes }}</span></td>
                                            <td class="tb-tnx-action">
                                                <div class="d-flex align-items-center">
                                                    <form action="{{ route('admin.hr.salary-reviews.approve', $review) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success me-2">Approve</button>
                                                    </form>
                                                    <form action="{{ route('admin.hr.salary-reviews.reject', $review) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center p-4">No pending salary or promotion reviews.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Processed History Logs -->
                        <div class="tab-pane p-0" id="processed-reviews">
                            <table class="table table-tranx">
                                <thead>
                                    <tr class="tb-tnx-head">
                                        <th>Employee Details</th>
                                        <th>Old vs New Salary</th>
                                        <th>Old vs New Designation</th>
                                        <th>Status</th>
                                        <th>Processed By</th>
                                        <th>Effective Date</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($processedReviews as $review)
                                        <tr class="tb-tnx-item">
                                            <td>
                                                <div class="user-card">
                                                    <div class="user-info">
                                                        <span class="lead-text">{{ $review->user->name }}</span>
                                                        <span class="sub-text">{{ $review->user->email }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="sub-text">@currency($review->current_salary)</span>
                                                <em class="icon ni ni-arrow-long-right text-soft"></em>
                                                <span class="lead-text font-weight-bold text-success">@currency($review->proposed_salary)</span>
                                            </td>
                                            <td>
                                                <span class="sub-text">{{ $review->currentDesignation->name ?? 'None' }}</span>
                                                <em class="icon ni ni-arrow-long-right text-soft"></em>
                                                <span class="lead-text text-success">{{ $review->proposedDesignation->name ?? 'None' }}</span>
                                            </td>
                                            <td>
                                                @if($review->status === 'Approved')
                                                    <span class="badge badge-dim bg-success text-success">Approved</span>
                                                @else
                                                    <span class="badge badge-dim bg-danger text-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="sub-text text-soft">Processed by: {{ $review->approvedBy->name ?? 'Admin' }}</span>
                                            </td>
                                            <td><span class="sub-text">{{ $review->effective_date->format('Y-m-d') }}</span></td>
                                            <td><span class="sub-text text-soft">{{ $review->notes }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center p-4">No processed history logs available.</td>
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
