@extends('layouts.admin')

@section('title', 'Helpdesk Dashboard')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Customer Ops Helpdesk</h3>
                            <div class="nk-block-des text-soft">
                                <p>Track support intake channels, monitor SLA targets, and manage ticket assignments.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                                <em class="icon ni ni-plus"></em><span>Log New Ticket</span>
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

                <!-- Analytics Cards -->
                <div class="row g-gs mb-4">
                    <div class="col-sm-6 col-xxl-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title text-soft">Total Tickets Intake</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="icon ni ni-ticket text-primary fs-3"></em>
                                    </div>
                                </div>
                                <div class="align-end gy-1">
                                    <div class="amount fs-2">{{ $analytics['total_tickets'] }}</div>
                                    <span class="sub-text text-soft">Logged across all intake channels</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xxl-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title text-soft">Active / Open Tickets</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="icon ni ni-clock text-warning fs-3"></em>
                                    </div>
                                </div>
                                <div class="align-end gy-1">
                                    <div class="amount fs-2 text-warning">{{ $analytics['open_tickets'] }}</div>
                                    <span class="sub-text text-soft">Awaiting resolution or update</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xxl-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title text-soft">SLA Compliance Rate</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="icon ni ni-activity-round text-success fs-3"></em>
                                    </div>
                                </div>
                                <div class="align-end gy-1">
                                    <div class="amount fs-2 text-success">{{ $analytics['sla_compliance_rate'] }}%</div>
                                    <span class="sub-text text-soft">Tickets resolved within SLA target</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xxl-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title text-soft">Avg Resolution Time</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="icon ni ni-check-circle text-info fs-3"></em>
                                    </div>
                                </div>
                                <div class="align-end gy-1">
                                    <div class="amount fs-2 text-info">{{ $analytics['avg_resolution_hours'] }} hrs</div>
                                    <span class="sub-text text-soft">From intake logging to final closure</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Section -->
                <div class="card card-bordered card-preview">
                    <div class="card-inner">
                        <div class="card-title-group mb-3">
                            <div class="card-title">
                                <h6 class="title">Support Tickets Queue</h6>
                            </div>
                            <div class="card-tools">
                                <form action="{{ route('admin.helpdesk.index') }}" method="GET" class="form-inline g-3">
                                    <div class="form-group me-2">
                                        <select name="status" class="form-select form-control form-control-sm" onchange="this.form.submit()">
                                            <option value="">-- All Statuses --</option>
                                            <option value="Open" {{ request('status') === 'Open' ? 'selected' : '' }}>Open</option>
                                            <option value="In Progress" {{ request('status') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="Resolved" {{ request('status') === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                            <option value="Closed" {{ request('status') === 'Closed' ? 'selected' : '' }}>Closed</option>
                                        </select>
                                    </div>
                                    <div class="form-group me-2">
                                        <select name="priority" class="form-select form-control form-control-sm" onchange="this.form.submit()">
                                            <option value="">-- All Priorities --</option>
                                            <option value="Low" {{ request('priority') === 'Low' ? 'selected' : '' }}>Low</option>
                                            <option value="Medium" {{ request('priority') === 'Medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="High" {{ request('priority') === 'High' ? 'selected' : '' }}>High</option>
                                            <option value="Urgent" {{ request('priority') === 'Urgent' ? 'selected' : '' }}>Urgent</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <select name="category" class="form-select form-control form-control-sm" onchange="this.form.submit()">
                                            <option value="">-- All Categories --</option>
                                            <option value="Technical" {{ request('category') === 'Technical' ? 'selected' : '' }}>Technical</option>
                                            <option value="Billing" {{ request('category') === 'Billing' ? 'selected' : '' }}>Billing</option>
                                            <option value="Account" {{ request('category') === 'Account' ? 'selected' : '' }}>Account</option>
                                            <option value="Feedback" {{ request('category') === 'Feedback' ? 'selected' : '' }}>Feedback</option>
                                            <option value="General" {{ request('category') === 'General' ? 'selected' : '' }}>General</option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <table class="table table-tranx">
                            <thead>
                                <tr class="tb-tnx-head">
                                    <th>Ticket Key</th>
                                    <th>Customer</th>
                                    <th>Category & Channel</th>
                                    <th>Priority</th>
                                    <th>Assigned Agent</th>
                                    <th>SLA Target</th>
                                    <th>Status</th>
                                    <th class="tb-tnx-action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                    <tr class="tb-tnx-item">
                                        <td>
                                            <span class="lead-text">{{ $ticket->ticket_key }}</span>
                                            <span class="sub-text text-soft text-truncate" style="max-width: 180px;">{{ $ticket->subject }}</span>
                                        </td>
                                        <td>
                                            <div class="user-info">
                                                <span class="lead-text">{{ $ticket->customer->name }}</span>
                                                <span class="sub-text">{{ $ticket->customer->email }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $ticket->category }}</span>
                                            <span class="sub-text text-soft">{{ $ticket->channel }}</span>
                                        </td>
                                        <td>
                                            @if($ticket->priority === 'Urgent')
                                                <span class="badge bg-danger">Urgent</span>
                                            @elseif($ticket->priority === 'High')
                                                <span class="badge bg-warning text-dark">High</span>
                                            @elseif($ticket->priority === 'Medium')
                                                <span class="badge bg-primary">Medium</span>
                                            @else
                                                <span class="badge bg-secondary">Low</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ticket->assignedTo)
                                                <span class="sub-text font-weight-bold text-dark">{{ $ticket->assignedTo->name }}</span>
                                            @else
                                                <span class="badge bg-outline-danger">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ticket->status === 'Resolved' || $ticket->status === 'Closed')
                                                <span class="text-success sub-text">Resolved</span>
                                            @else
                                                @php
                                                    $isBreached = \Carbon\Carbon::now()->gt($ticket->sla_deadline);
                                                @endphp
                                                @if($isBreached)
                                                    <span class="text-danger font-weight-bold sub-text">
                                                        <em class="icon ni ni-alert-circle"></em> Breached
                                                    </span>
                                                @else
                                                    <span class="text-soft sub-text">
                                                        {{ \Carbon\Carbon::now()->diffForHumans($ticket->sla_deadline, ['parts' => 1, 'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW]) }}
                                                    </span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($ticket->status === 'Open')
                                                <span class="badge badge-dim bg-danger text-danger">Open</span>
                                            @elseif($ticket->status === 'In Progress')
                                                <span class="badge badge-dim bg-warning text-warning">In Progress</span>
                                            @elseif($ticket->status === 'Resolved')
                                                <span class="badge badge-dim bg-success text-success">Resolved</span>
                                            @else
                                                <span class="badge badge-dim bg-secondary text-secondary">Closed</span>
                                            @endif
                                        </td>
                                        <td class="tb-tnx-action">
                                            <a href="{{ route('admin.helpdesk.show', $ticket) }}" class="btn btn-sm btn-outline-light">
                                                <span>Manage</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center p-4">No support tickets found in this queue.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $tickets->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Ticket Modal -->
<div class="modal fade" id="createTicketModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Customer Ticket</h5>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('admin.helpdesk.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Customer Name</label>
                                <input type="text" class="form-control" name="customer_name" placeholder="John Doe" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Customer Email</label>
                                <input type="email" class="form-control" name="customer_email" placeholder="john@example.com" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Customer Phone</label>
                                <input type="text" class="form-control" name="customer_phone" placeholder="e.g. +234...">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Company Name</label>
                                <input type="text" class="form-control" name="customer_company" placeholder="ACME Corp">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Customer SLA Tier</label>
                                <select class="form-select form-control" name="customer_tier" required>
                                    <option value="Standard" selected>Standard Tier</option>
                                    <option value="Gold">Gold Tier</option>
                                    <option value="Platinum">Platinum Tier</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Intake Channel</label>
                                <select class="form-select form-control" name="channel" required>
                                    <option value="Portal" selected>Portal Intake</option>
                                    <option value="Email">Email Message</option>
                                    <option value="Phone">Phone Support Call</option>
                                    <option value="API">API Integration</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Ticket Category</label>
                                <select class="form-select form-control" name="category" required>
                                    <option value="General" selected>General Request</option>
                                    <option value="Technical">Technical Issue</option>
                                    <option value="Billing">Billing Inquiry</option>
                                    <option value="Account">Account Security</option>
                                    <option value="Feedback">Customer Feedback</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Priority Level</label>
                                <select class="form-select form-control" name="priority" required>
                                    <option value="Low">Low Priority</option>
                                    <option value="Medium" selected>Medium Priority</option>
                                    <option value="High">High Priority</option>
                                    <option value="Urgent">Urgent Priority (2hr SLA)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Ticket Subject</label>
                                <input type="text" class="form-control" name="subject" placeholder="Brief summary of request..." required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Description / Details</label>
                                <textarea class="form-control" name="description" rows="4" placeholder="Detail the complaint or request..." required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Log & Route Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
