@extends('layouts.admin')

@section('title', 'Performance Appraisals')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Performance Appraisals & 360° Feedbacks</h3>
                            <div class="nk-block-des text-soft">
                                <p>Manage company appraisal cycles, gather peer feedback, and perform rating calibrations.</p>
                            </div>
                        </div>
                        @if(Auth::user()->hasRole('admin'))
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <div class="toggle-expand-content">
                                        <ul class="nk-block-tools g-3">
                                            <li>
                                                <button type="button" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#createCycleModal">
                                                    <em class="icon ni ni-calendar-booking"></em><span>Create Cycle</span>
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#inviteEmployeeModal">
                                                    <em class="icon ni ni-plus"></em><span>Invite Staff</span>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
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

                @if(Auth::user()->hasRole('admin'))
                    <!-- Admin Panel View -->
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <ul class="nav nav-tabs nav-tabs-card nav-tabs-xs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#active-appraisals">Staff Appraisals</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#appraisal-cycles">Appraisal Cycles</a>
                                </li>
                            </ul>
                            <div class="tab-content mt-3">
                                <!-- Appraisals list -->
                                <div class="tab-pane active" id="active-appraisals">
                                    <table class="table table-tranx">
                                        <thead>
                                            <tr class="tb-tnx-head">
                                                <th>Staff Employee</th>
                                                <th>Cycle</th>
                                                <th>Manager</th>
                                                <th>Ratings (Self / Mgr / Calib)</th>
                                                <th>Peer 360 Feedbacks</th>
                                                <th>Status</th>
                                                <th class="tb-tnx-action">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($appraisals as $appraisal)
                                                <tr class="tb-tnx-item">
                                                    <td>
                                                        <div class="user-card">
                                                            <div class="user-info">
                                                                <span class="lead-text">{{ $appraisal->user->name }}</span>
                                                                <span class="sub-text">{{ $appraisal->user->designation->name ?? 'Staff' }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><span class="sub-text">{{ $appraisal->cycle->title }}</span></td>
                                                    <td><span class="sub-text">{{ $appraisal->manager->name }}</span></td>
                                                    <td>
                                                        <span class="lead-text">
                                                            {{ $appraisal->self_rating ?? '-' }} / {{ $appraisal->manager_rating ?? '-' }} / 
                                                            <strong class="text-success">{{ $appraisal->calibrated_rating ?? '-' }}</strong>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $subPeer = $appraisal->peerFeedbacks->where('status', 'Submitted');
                                                            $avgRating = $subPeer->avg('rating');
                                                        @endphp
                                                        @if($subPeer->count() > 0)
                                                            <span class="sub-text text-primary">{{ $subPeer->count() }} submitted (Avg: {{ number_format($avgRating, 1) }}★)</span>
                                                        @else
                                                            <span class="sub-text text-soft">0 reviews</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($appraisal->status === 'Pending Self')
                                                            <span class="badge badge-dim bg-warning text-warning">Self Review</span>
                                                        @elseif($appraisal->status === 'Pending Manager')
                                                            <span class="badge badge-dim bg-primary text-primary">Manager Review</span>
                                                        @elseif($appraisal->status === 'Pending Calibration')
                                                            <span class="badge badge-dim bg-info text-info">Calibration</span>
                                                        @else
                                                            <span class="badge badge-dim bg-success text-success">Completed</span>
                                                        @endif
                                                    </td>
                                                    <td class="tb-tnx-action">
                                                        <div class="d-flex align-items-center">
                                                            @if($appraisal->status === 'Pending Calibration')
                                                                <button type="button" class="btn btn-sm btn-success me-2" data-bs-toggle="modal" data-bs-target="#calibrateModal{{ $appraisal->id }}">
                                                                    Calibrate
                                                                </button>
                                                            @endif

                                                            <button type="button" class="btn btn-sm btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#requestPeerModal{{ $appraisal->id }}">
                                                                Request Peer
                                                            </button>

                                                            @if($appraisal->status === 'Completed' && !$appraisal->salaryReview)
                                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#linkSalaryModal{{ $appraisal->id }}">
                                                                    Propose Salary Review
                                                                </button>
                                                            @elseif($appraisal->salaryReview)
                                                                <span class="badge badge-dim bg-outline-success">Review Linked</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Peer 360 Request Modal -->
                                                <div class="modal fade" id="requestPeerModal{{ $appraisal->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Request 360 Feedback for {{ $appraisal->user->name }}</h5>
                                                                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                                            </div>
                                                            <form action="{{ route('admin.hr.appraisals.requestPeer', $appraisal) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label class="form-label">Reviewer</label>
                                                                        <select class="form-select form-control" name="reviewer_id" required>
                                                                            @foreach($staff as $employee)
                                                                                @if($employee->id !== $appraisal->user_id)
                                                                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                                                                @endif
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="form-label">Relationship to Appraisee</label>
                                                                        <select class="form-select form-control" name="relationship" required>
                                                                            <option value="Peer">Peer / Colleague</option>
                                                                            <option value="Subordinate">Subordinate / Team Member</option>
                                                                            <option value="Stakeholder">External / Department Stakeholder</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-primary">Send Feedback Request</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Calibration Modal -->
                                                <div class="modal fade" id="calibrateModal{{ $appraisal->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                 <h5 class="modal-title">Calibrate Rating: {{ $appraisal->user->name }}</h5>
                                                                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                                            </div>
                                                            <form action="{{ route('admin.hr.appraisals.submitCalibration', $appraisal) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="card bg-light p-3 mb-3">
                                                                        <p class="mb-1"><strong>Self Rating:</strong> {{ $appraisal->self_rating }}★</p>
                                                                        <p class="text-soft mb-2">"{{ $appraisal->self_comment }}"</p>
                                                                        <hr class="my-2">
                                                                        <p class="mb-1"><strong>Manager Rating:</strong> {{ $appraisal->manager_rating }}★</p>
                                                                        <p class="text-soft mb-0">"{{ $appraisal->manager_comment }}"</p>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="form-label">Final Calibrated Rating (1.00 - 5.00)</label>
                                                                        <input type="number" step="0.1" class="form-control" name="calibrated_rating" min="1.0" max="5.0" placeholder="e.g. 4.3" required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="form-label">Calibration Justification & Feedback</label>
                                                                        <textarea class="form-control" name="calibrated_comment" rows="3" placeholder="Provide final remarks on this appraisal decision..." required></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-success">Finalize Calibration</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Propose Salary Review & Promotion Modal -->
                                                <div class="modal fade" id="linkSalaryModal{{ $appraisal->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Propose Salary & Promotion Review</h5>
                                                                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                                            </div>
                                                            <form action="{{ route('admin.hr.salary-reviews.store', $appraisal) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="alert alert-info">
                                                                        <strong>Appraisal Calibrated Rating:</strong> {{ $appraisal->calibrated_rating }}★
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="form-label">Proposed New Basic Monthly Salary (₦)</label>
                                                                        <input type="number" step="0.01" class="form-control" name="proposed_salary" placeholder="e.g. 250000" required>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="form-label">Proposed New Designation</label>
                                                                        <select class="form-select form-control" name="proposed_designation_id">
                                                                            <option value="">-- No Change --</option>
                                                                            @foreach($designations as $desg)
                                                                                <option value="{{ $desg->id }}">{{ $desg->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="form-label">Proposed Job Title</label>
                                                                        <input type="text" class="form-control" name="proposed_job_title" placeholder="e.g. Senior Backend Developer">
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="form-label">Effective Date</label>
                                                                        <input type="date" class="form-control" name="effective_date" value="{{ date('Y-m-d') }}" required>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="form-label">Admin Notes</label>
                                                                        <textarea class="form-control" name="notes" rows="2" placeholder="Justification for salary review..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-primary">Propose & Submit</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center p-4">No active staff appraisals invited yet.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Appraisal Cycles -->
                                <div class="tab-pane" id="appraisal-cycles">
                                    <table class="table table-tranx">
                                        <thead>
                                            <tr class="tb-tnx-head">
                                                <th>Cycle Title</th>
                                                <th>Period Start</th>
                                                <th>Period End</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cycles as $cycle)
                                                <tr class="tb-tnx-item">
                                                    <td><span class="lead-text">{{ $cycle->title }}</span></td>
                                                    <td><span class="sub-text">{{ $cycle->start_date->format('Y-m-d') }}</span></td>
                                                    <td><span class="sub-text">{{ $cycle->end_date->format('Y-m-d') }}</span></td>
                                                    <td>
                                                        @if($cycle->status === 'Draft')
                                                            <span class="badge badge-dim bg-outline-light">Draft</span>
                                                        @elseif($cycle->status === 'Active')
                                                            <span class="badge badge-dim bg-outline-success">Active</span>
                                                        @else
                                                            <span class="badge badge-dim bg-outline-primary">Completed</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Regular Employee View -->
                    <div class="card card-bordered">
                        <ul class="nav nav-tabs nav-tabs-card nav-tabs-xs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#my-appraisals">My Appraisals</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#my-manager-reviews">Staff Reviews</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#peer-requests">Peer Feedbacks</a>
                            </li>
                        </ul>
                        <div class="tab-content mt-3">
                            <!-- My Appraisals -->
                            <div class="tab-pane active" id="my-appraisals">
                                <table class="table table-tranx">
                                    <thead>
                                        <tr class="tb-tnx-head">
                                            <th>Appraisal Cycle</th>
                                            <th>Manager</th>
                                            <th>Self Assessment</th>
                                            <th>Manager Rating</th>
                                            <th>Final Calibrated</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($mySelfAppraisals as $appr)
                                            <tr class="tb-tnx-item">
                                                <td><span class="lead-text">{{ $appr->cycle->title }}</span></td>
                                                <td><span class="sub-text">{{ $appr->manager->name }}</span></td>
                                                <td>
                                                    @if($appr->status === 'Pending Self')
                                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#selfAppraiseModal{{ $appr->id }}">
                                                            Complete Self Assessment
                                                        </button>
                                                    @else
                                                        <span class="sub-text">{{ $appr->self_rating }}★ (Completed)</span>
                                                    @endif
                                                </td>
                                                <td><span class="sub-text">{{ $appr->manager_rating ? $appr->manager_rating . '★' : 'Awaiting Mgr' }}</span></td>
                                                <td><span class="lead-text text-success">{{ $appr->calibrated_rating ? $appr->calibrated_rating . '★' : '-' }}</span></td>
                                                <td>
                                                    @if($appr->status === 'Pending Self')
                                                        <span class="badge badge-dim bg-warning text-warning">Pending Self</span>
                                                    @elseif($appr->status === 'Pending Manager')
                                                        <span class="badge badge-dim bg-primary text-primary">Pending Manager</span>
                                                    @elseif($appr->status === 'Pending Calibration')
                                                        <span class="badge badge-dim bg-info text-info">In Calibration</span>
                                                    @else
                                                        <span class="badge badge-dim bg-success text-success">Completed</span>
                                                    @endif
                                                </td>
                                            </tr>

                                            <!-- Self Assessment Modal -->
                                            <div class="modal fade" id="selfAppraiseModal{{ $appr->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Submit Self Appraisal: {{ $appr->cycle->title }}</h5>
                                                            <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                                        </div>
                                                        <form action="{{ route('admin.hr.appraisals.submitSelf', $appr) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label class="form-label">Self Rating (1 - 5 stars)</label>
                                                                    <select class="form-select form-control" name="self_rating" required>
                                                                        <option value="5">5★ (Excellent, Outperformed goals)</option>
                                                                        <option value="4">4★ (Very Good, Exceeded expectations)</option>
                                                                        <option value="3" selected>3★ (Good, Met expectations)</option>
                                                                        <option value="2">2★ (Fair, Needs minor development)</option>
                                                                        <option value="1">1★ (Needs urgent improvement)</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="form-label">Self Evaluation Comments</label>
                                                                    <textarea class="form-control" name="self_comment" rows="4" placeholder="Detail your accomplishments and performance highlights during this period..." required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Submit Self Assessment</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center p-4">No pending appraisal invitations.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Managed Appraisals -->
                            <div class="tab-pane" id="my-manager-reviews">
                                <table class="table table-tranx">
                                    <thead>
                                        <tr class="tb-tnx-head">
                                            <th>Staff Member</th>
                                            <th>Cycle</th>
                                            <th>Self Rating</th>
                                            <th>Manager Assessment</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($myManagerReviews as $appr)
                                            <tr class="tb-tnx-item">
                                                <td><span class="lead-text">{{ $appr->user->name }}</span></td>
                                                <td><span class="sub-text">{{ $appr->cycle->title }}</span></td>
                                                <td><span class="sub-text">{{ $appr->self_rating ? $appr->self_rating . '★' : 'Pending self' }}</span></td>
                                                <td>
                                                    @if($appr->status === 'Pending Manager')
                                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#managerAppraiseModal{{ $appr->id }}">
                                                            Submit Manager Review
                                                        </button>
                                                    @elseif($appr->status === 'Pending Self')
                                                        <span class="sub-text text-soft">Awaiting employee self-rating</span>
                                                    @else
                                                        <span class="sub-text">{{ $appr->manager_rating }}★ (Submitted)</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($appr->status === 'Pending Self')
                                                        <span class="badge badge-dim bg-warning text-warning">Awaiting Self</span>
                                                    @elseif($appr->status === 'Pending Manager')
                                                        <span class="badge badge-dim bg-primary text-primary">Pending Mgr</span>
                                                    @elseif($appr->status === 'Pending Calibration')
                                                        <span class="badge badge-dim bg-info text-info">Calibrating</span>
                                                    @else
                                                        <span class="badge badge-dim bg-success text-success">Completed</span>
                                                    @endif
                                                </td>
                                            </tr>

                                            <!-- Manager Review Modal -->
                                            <div class="modal fade" id="managerAppraiseModal{{ $appr->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Submit Manager Review: {{ $appr->user->name }}</h5>
                                                            <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                                        </div>
                                                        <form action="{{ route('admin.hr.appraisals.submitManager', $appr) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="card bg-light p-3 mb-3">
                                                                    <p class="mb-1"><strong>Employee Self Rating:</strong> {{ $appr->self_rating }}★</p>
                                                                    <p class="text-soft mb-0">"{{ $appr->self_comment }}"</p>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label class="form-label">Manager Rating (1 - 5 stars)</label>
                                                                    <select class="form-select form-control" name="manager_rating" required>
                                                                        <option value="5">5★ (Excellent, Outperformed goals)</option>
                                                                        <option value="4">4★ (Very Good, Exceeded expectations)</option>
                                                                        <option value="3" selected>3★ (Good, Met expectations)</option>
                                                                        <option value="2">2★ (Fair, Needs minor development)</option>
                                                                        <option value="1">1★ (Needs urgent improvement)</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="form-label">Manager Comments & Growth Feedback</label>
                                                                    <textarea class="form-control" name="manager_comment" rows="4" placeholder="Detail employee strengths, key accomplishments, and areas of growth..." required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Submit Manager Review</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center p-4">You have no staff members assigned to review.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Peer Feedback Requests -->
                            <div class="tab-pane" id="peer-requests">
                                <table class="table table-tranx">
                                    <thead>
                                        <tr class="tb-tnx-head">
                                            <th>Colleague</th>
                                            <th>Cycle</th>
                                            <th>Your Relationship</th>
                                            <th>Feedback Rating</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($myPeerFeedbackRequests as $req)
                                            <tr class="tb-tnx-item">
                                                <td><span class="lead-text">{{ $req->appraisal->user->name }}</span></td>
                                                <td><span class="sub-text">{{ $req->appraisal->cycle->title }}</span></td>
                                                <td><span class="badge badge-dim bg-outline-info">{{ $req->relationship }}</span></td>
                                                <td>
                                                    @if($req->status === 'Pending')
                                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#peerFeedbackModal{{ $req->id }}">
                                                            Submit Feedback
                                                        </button>
                                                    @else
                                                        <span class="sub-text">{{ $req->rating }}★ (Submitted)</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($req->status === 'Pending')
                                                        <span class="badge badge-dim bg-warning text-warning">Pending</span>
                                                    @else
                                                        <span class="badge badge-dim bg-success text-success">Submitted</span>
                                                    @endif
                                                </td>
                                            </tr>

                                            <!-- Peer Feedback Submission Modal -->
                                            <div class="modal fade" id="peerFeedbackModal{{ $req->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Submit Peer Feedback: {{ $req->appraisal->user->name }}</h5>
                                                            <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                                        </div>
                                                        <form action="{{ route('admin.hr.peer-feedback.submit', $req) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label class="form-label">Review Rating (1 - 5 stars)</label>
                                                                    <select class="form-select form-control" name="rating" required>
                                                                        <option value="5">5★ (Outstanding Colleague)</option>
                                                                        <option value="4">4★ (Great to work with)</option>
                                                                        <option value="3" selected>3★ (Good and collaborative)</option>
                                                                        <option value="2">2★ (Needs more collaboration)</option>
                                                                        <option value="1">1★ (Difficult to work with)</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="form-label">360° Review Comments</label>
                                                                    <textarea class="form-control" name="feedback" rows="4" placeholder="Share professional comments on their collaboration, delivery, or communication..." required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Submit Feedback</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center p-4">No peer feedback requests active.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Admin Modals -->
                @if(Auth::user()->hasRole('admin'))
                    <!-- Create Cycle Modal -->
                    <div class="modal fade" id="createCycleModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Create Appraisal Cycle</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                </div>
                                <form action="{{ route('admin.hr.appraisals.storeCycle') }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label class="form-label">Cycle Title</label>
                                            <input type="text" class="form-control" name="title" placeholder="e.g. End of Year 2026 Appraisal" required>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label class="form-label">Cycle Start Date</label>
                                                    <input type="date" class="form-control" name="start_date" value="{{ date('Y-m-d') }}" required>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label class="form-label">Cycle End Date</label>
                                                    <input type="date" class="form-control" name="end_date" value="{{ date('Y-12-31') }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label class="form-label">Cycle Status</label>
                                            <select class="form-select form-control" name="status" required>
                                                <option value="Draft">Draft (Internal planning)</option>
                                                <option value="Active" selected>Active (Open for submissions)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Create Cycle</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Invite Employee Modal -->
                    <div class="modal fade" id="inviteEmployeeModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Invite Employee to Appraisal</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                </div>
                                <form action="{{ route('admin.hr.appraisals.invite') }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label class="form-label">Appraisal Cycle</label>
                                            <select class="form-select form-control" name="appraisal_cycle_id" required>
                                                @foreach($cycles->where('status', 'Active') as $cyc)
                                                    <option value="{{ $cyc->id }}">{{ $cyc->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Target Employee</label>
                                            <select class="form-select form-control" name="user_id" required>
                                                @foreach($staff as $employee)
                                                    <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->designation->name ?? 'Staff' }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Assigned Appraising Manager</label>
                                            <select class="form-select form-control" name="manager_id" required>
                                                @foreach($staff as $mgr)
                                                    <option value="{{ $mgr->id }}">{{ $mgr->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Invite Staff</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
