@extends('layouts.admin')

@section('title', 'SLA Policies Manager')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Service Level Agreements (SLAs)</h3>
                            <div class="nk-block-des text-soft">
                                <p>Manage and adjust response and resolution target rules by customer tier, priority, or category.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPolicyModal">
                                <em class="icon ni ni-plus"></em><span>Add SLA Policy</span>
                            </button>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-icon alert-dismissible">
                        <em class="icon ni ni-check-circle"></em> <strong>Success!</strong> {{ session('success') }}
                        <button class="close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card card-bordered card-preview">
                    <div class="card-inner">
                        <table class="table table-tranx">
                            <thead>
                                <tr class="tb-tnx-head">
                                    <th>Policy Name</th>
                                    <th>Customer Tier</th>
                                    <th>Category Mapping</th>
                                    <th>Priority Matching</th>
                                    <th>Response Target</th>
                                    <th>Resolution Deadline</th>
                                    <th>Status</th>
                                    <th class="tb-tnx-action">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($policies as $policy)
                                    <tr class="tb-tnx-item">
                                        <td>
                                            <span class="lead-text font-weight-bold">{{ $policy->name }}</span>
                                        </td>
                                        <td>
                                            @if($policy->customer_tier === 'Platinum')
                                                <span class="badge bg-dark">Platinum</span>
                                            @elseif($policy->customer_tier === 'Gold')
                                                <span class="badge bg-warning text-dark">Gold</span>
                                            @else
                                                <span class="badge bg-light text-dark">Standard</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="sub-text text-dark">{{ $policy->category ?? 'Any Category' }}</span>
                                        </td>
                                        <td>
                                            @if($policy->priority === 'Urgent')
                                                <span class="badge bg-dim bg-danger text-danger">Urgent</span>
                                            @elseif($policy->priority === 'High')
                                                <span class="badge bg-dim bg-warning text-warning">High</span>
                                            @elseif($policy->priority === 'Medium')
                                                <span class="badge bg-dim bg-primary text-primary">Medium</span>
                                            @else
                                                <span class="badge bg-dim bg-secondary text-secondary">Low</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="sub-text font-weight-bold text-dark">{{ $policy->response_time_hours }} hrs</span>
                                        </td>
                                        <td>
                                            <span class="sub-text font-weight-bold text-dark">{{ $policy->resolution_time_hours }} hrs</span>
                                        </td>
                                        <td>
                                            @if($policy->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-light">Disabled</span>
                                            @endif
                                        </td>
                                        <td class="tb-tnx-action">
                                            <div class="dropdown">
                                                <a class="text-soft dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <ul class="link-list-opt no-bdr">
                                                        <li>
                                                            <a href="#" data-bs-toggle="modal" data-bs-target="#editPolicyModal{{ $policy->id }}">
                                                                <em class="icon ni ni-edit"></em><span>Edit Policy</span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('admin.sla-policies.destroy', $policy) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this policy?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger border-0 bg-transparent">
                                                                    <em class="icon ni ni-trash"></em><span>Delete Policy</span>
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Edit Policy Modal -->
                                    <div class="modal fade" id="editPolicyModal{{ $policy->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit SLA Policy</h5>
                                                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                                </div>
                                                <form action="{{ route('admin.sla-policies.update', $policy) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body text-start">
                                                        <div class="row g-3">
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <label class="form-label">Policy Name</label>
                                                                    <input type="text" class="form-control" name="name" value="{{ $policy->name }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label class="form-label">Customer Tier</label>
                                                                    <select class="form-select form-control" name="customer_tier" required>
                                                                        <option value="Standard" {{ $policy->customer_tier === 'Standard' ? 'selected' : '' }}>Standard Tier</option>
                                                                        <option value="Gold" {{ $policy->customer_tier === 'Gold' ? 'selected' : '' }}>Gold Tier</option>
                                                                        <option value="Platinum" {{ $policy->customer_tier === 'Platinum' ? 'selected' : '' }}>Platinum Tier</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label class="form-label">Category (Optional)</label>
                                                                    <select class="form-select form-control" name="category">
                                                                        <option value="">-- Any Category --</option>
                                                                        <option value="Technical" {{ $policy->category === 'Technical' ? 'selected' : '' }}>Technical</option>
                                                                        <option value="Billing" {{ $policy->category === 'Billing' ? 'selected' : '' }}>Billing</option>
                                                                        <option value="Account" {{ $policy->category === 'Account' ? 'selected' : '' }}>Account</option>
                                                                        <option value="Feedback" {{ $policy->category === 'Feedback' ? 'selected' : '' }}>Feedback</option>
                                                                        <option value="General" {{ $policy->category === 'General' ? 'selected' : '' }}>General</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label class="form-label">Priority Matching</label>
                                                                    <select class="form-select form-control" name="priority" required>
                                                                        <option value="Low" {{ $policy->priority === 'Low' ? 'selected' : '' }}>Low Priority</option>
                                                                        <option value="Medium" {{ $policy->priority === 'Medium' ? 'selected' : '' }}>Medium Priority</option>
                                                                        <option value="High" {{ $policy->priority === 'High' ? 'selected' : '' }}>High Priority</option>
                                                                        <option value="Urgent" {{ $policy->priority === 'Urgent' ? 'selected' : '' }}>Urgent Priority</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label class="form-label">Is Policy Active?</label>
                                                                    <select class="form-select form-control" name="is_active" required>
                                                                        <option value="1" {{ $policy->is_active ? 'selected' : '' }}>Active</option>
                                                                        <option value="0" {{ !$policy->is_active ? 'selected' : '' }}>Disabled</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label class="form-label">Response Time Target (Hours)</label>
                                                                    <input type="number" step="0.01" class="form-control" name="response_time_hours" value="{{ $policy->response_time_hours }}" required>
                                                                    <span class="sub-text text-soft">e.g. 0.5 = 30 mins, 4 = 4 hours</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label class="form-label">Resolution Time Target (Hours)</label>
                                                                    <input type="number" step="0.01" class="form-control" name="resolution_time_hours" value="{{ $policy->resolution_time_hours }}" required>
                                                                    <span class="sub-text text-soft">e.g. 2 = 2 hours, 24 = 24 hours</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-success">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center p-4">No custom SLA policies defined. Falls back to default response (8h) and resolution (24h) targets.</td>
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

<!-- Create Policy Modal -->
<div class="modal fade" id="createPolicyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Define Custom SLA Policy</h5>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('admin.sla-policies.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Policy Name</label>
                                <input type="text" class="form-control" name="name" placeholder="e.g. Platinum Tech Urgent Target" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Customer Tier</label>
                                <select class="form-select form-control" name="customer_tier" required>
                                    <option value="Standard" selected>Standard Tier</option>
                                    <option value="Gold">Gold Tier</option>
                                    <option value="Platinum">Platinum Tier</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Category (Optional)</label>
                                <select class="form-select form-control" name="category">
                                    <option value="" selected>-- Any Category --</option>
                                    <option value="Technical">Technical</option>
                                    <option value="Billing">Billing</option>
                                    <option value="Account">Account</option>
                                    <option value="Feedback">Feedback</option>
                                    <option value="General">General</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Priority Match</label>
                                <select class="form-select form-control" name="priority" required>
                                    <option value="Low">Low Priority</option>
                                    <option value="Medium" selected>Medium Priority</option>
                                    <option value="High">High Priority</option>
                                    <option value="Urgent">Urgent Priority</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Response Target (Hours)</label>
                                <input type="number" step="0.01" class="form-control" name="response_time_hours" placeholder="e.g. 0.5" required>
                                <span class="sub-text text-soft">e.g. 0.25 = 15m, 1 = 1 hour</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Resolution Target (Hours)</label>
                                <input type="number" step="0.01" class="form-control" name="resolution_time_hours" placeholder="e.g. 4.00" required>
                                <span class="sub-text text-soft">e.g. 2 = 2 hours, 48 = 48 hours</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Define SLA Policy</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
