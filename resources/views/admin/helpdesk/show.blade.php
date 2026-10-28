@extends('layouts.admin')

@section('title', 'Manage Ticket ' . $ticket->ticket_key)

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <a href="{{ route('admin.helpdesk.index') }}" class="back-to"><em class="icon ni ni-arrow-left"></em><span>Back to Queue</span></a>
                            <h3 class="nk-block-title page-title">Ticket: {{ $ticket->ticket_key }}</h3>
                            <div class="nk-block-des text-soft">
                                <p>Opened on {{ $ticket->created_at->format('M d, Y \a\t h:i A') }}</p>
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

                <div class="row g-gs">
                    <!-- Left Side: Ticket Content & Thread -->
                    <div class="col-lg-8">
                        <div class="card card-bordered mb-4">
                            <div class="card-inner">
                                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                    <h5 class="title">{{ $ticket->subject }}</h5>
                                    <span class="sub-text text-soft">Category: <strong class="text-dark">{{ $ticket->category }}</strong></span>
                                </div>
                                <div class="text-dark mb-4" style="white-space: pre-line;">
                                    {{ $ticket->description }}
                                </div>
                                <div class="bg-light p-3 rounded">
                                    <h6 class="title text-soft fs-12px mb-2">Customer Details</h6>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <span class="sub-text text-soft">Name</span>
                                            <span class="lead-text">{{ $ticket->customer->name }}</span>
                                        </div>
                                        <div class="col-sm-4">
                                            <span class="sub-text text-soft">Email</span>
                                            <span class="lead-text">{{ $ticket->customer->email }}</span>
                                        </div>
                                        <div class="col-sm-4">
                                            <span class="sub-text text-soft">Company / Phone</span>
                                            <span class="lead-text">{{ $ticket->customer->company ?? 'N/A' }} / {{ $ticket->customer->phone ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Conversation Thread -->
                        <div class="card card-bordered mb-4">
                            <div class="card-inner border-bottom">
                                <h6 class="title mb-0">Conversation Thread</h6>
                            </div>
                            <div class="card-inner p-0" style="max-height: 400px; overflow-y: auto;">
                                <div class="p-4">
                                    @forelse($ticket->replies as $reply)
                                        <div class="mb-3 d-flex align-start {{ $reply->user_id ? 'justify-content-end' : '' }}">
                                            <div class="card card-bordered p-3 {{ $reply->user_id ? 'bg-primary-dim border-primary' : 'bg-light' }}" style="max-width: 80%;">
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                    <span class="lead-text fs-12px">
                                                        {{ $reply->user ? $reply->user->name . ' (Support Agent)' : $ticket->customer->name . ' (Customer)' }}
                                                    </span>
                                                    <span class="sub-text text-soft fs-11px ms-3">
                                                        {{ $reply->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <div class="text-dark fs-13px">
                                                    {{ $reply->message }}
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center text-soft p-4">No replies logged for this ticket yet.</div>
                                    @endforelse
                                </div>
                            </div>
                            <div class="card-inner border-top">
                                <form action="{{ route('admin.helpdesk.reply', $ticket) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <label class="form-label mb-0">Write Response Message</label>
                                            @if($articles->isNotEmpty())
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-xs btn-outline-primary dropdown-toggle py-1 px-2" data-bs-toggle="dropdown" style="font-size: 11px;">
                                                        <em class="icon ni ni-file-text me-1"></em><span>Insert KB Article</span>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end" style="max-height: 250px; overflow-y: auto; width: 280px;">
                                                        <ul class="link-list-opt no-bdr p-2" style="display: flex; flex-direction: column; gap: 4px;">
                                                            @foreach($articles as $art)
                                                                <li>
                                                                    <a href="#" class="kb-insert-link py-1 px-2 rounded text-dark fs-12px" data-content="{{ $art->content }}" style="display: block; text-decoration: none; transition: background 0.15s;">
                                                                        {{ $art->title }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <textarea class="form-control" id="reply-message-area" name="message" rows="4" placeholder="Compose support message to reply..." required></textarea>
                                    </div>
                                    <div class="form-group text-end mt-2">
                                        <button type="submit" class="btn btn-primary">Send Message</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Metadata / Audit Trail -->
                    <div class="col-lg-4">
                        <!-- SLA & Status Panel -->
                        <div class="card card-bordered mb-4">
                            <div class="card-inner">
                                <h6 class="title mb-1">SLA Targets & Control</h6>
                                <p class="sub-text text-soft mb-3">
                                    SLA Policy: <strong>{{ $ticket->slaPolicy ? $ticket->slaPolicy->name : 'Default System SLA' }}</strong> (Customer: {{ $ticket->customer->tier }} Tier)
                                </p>

                                <!-- Response SLA Target -->
                                <div class="card card-bordered p-2 mb-3 bg-light sla-clock-container"
                                     data-deadline="{{ $ticket->response_deadline ? $ticket->response_deadline->toISOString() : '' }}"
                                     data-created="{{ $ticket->created_at->toISOString() }}"
                                     data-completed="{{ $ticket->first_responded_at ? 'true' : 'false' }}">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="sub-text font-weight-bold text-dark">First Response Target</span>
                                        <span class="badge sla-badge @if($ticket->first_responded_at) @if($ticket->first_responded_at->lte($ticket->response_deadline)) bg-success @else bg-danger @endif @else @if(\Carbon\Carbon::now()->gt($ticket->response_deadline)) bg-danger @else @php $timeToResponse = \Carbon\Carbon::now()->diffInMinutes($ticket->response_deadline, false); @endphp @if($timeToResponse < 60) bg-warning text-dark @else bg-info @endif @endif @endif">
                                            @if($ticket->first_responded_at)
                                                @if($ticket->first_responded_at->lte($ticket->response_deadline)) Met @else Breached @endif
                                            @else
                                                @if(\Carbon\Carbon::now()->gt($ticket->response_deadline)) Breached @else @if($timeToResponse < 60) Breach Alert ({{ $timeToResponse }}m) @else Active @endif @endif
                                            @endif
                                        </span>
                                    </div>
                                    <span class="sub-text fs-11px text-soft">
                                        Target: {{ $ticket->response_deadline->format('M d, Y h:i A') }}
                                    </span>
                                    @if($ticket->first_responded_at)
                                        <span class="sub-text fs-11px text-soft">
                                            Actual: {{ $ticket->first_responded_at->format('M d, Y h:i A') }}
                                        </span>
                                    @else
                                        <span class="sub-text fs-11px text-soft sla-timer-text">
                                            @if(\Carbon\Carbon::now()->lte($ticket->response_deadline))
                                                Timer: <strong class="text-primary">{{ \Carbon\Carbon::now()->diffForHumans($ticket->response_deadline, ['parts' => 1, 'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW]) }}</strong>
                                            @else
                                                Timer: <strong class="text-danger">Breached</strong>
                                            @endif
                                        </span>
                                    @endif
                                </div>

                                <!-- Resolution SLA Target -->
                                <div class="card card-bordered p-2 mb-3 bg-light sla-clock-container"
                                     data-deadline="{{ $ticket->sla_deadline ? $ticket->sla_deadline->toISOString() : '' }}"
                                     data-created="{{ $ticket->created_at->toISOString() }}"
                                     data-completed="{{ ($ticket->status === 'Resolved' || $ticket->status === 'Closed') ? 'true' : 'false' }}">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="sub-text font-weight-bold text-dark">Resolution Target</span>
                                        <span class="badge sla-badge @if($ticket->status === 'Resolved' || $ticket->status === 'Closed') @if($ticket->resolved_at && $ticket->resolved_at->lte($ticket->sla_deadline)) bg-success @else bg-danger @endif @else @if(\Carbon\Carbon::now()->gt($ticket->sla_deadline)) bg-danger @else @php $timeToResolve = \Carbon\Carbon::now()->diffInMinutes($ticket->sla_deadline, false); @endphp @if($timeToResolve < 180) bg-warning text-dark @else bg-info @endif @endif @endif">
                                            @if($ticket->status === 'Resolved' || $ticket->status === 'Closed')
                                                @if($ticket->resolved_at && $ticket->resolved_at->lte($ticket->sla_deadline)) Met @else Breached @endif
                                            @else
                                                @if(\Carbon\Carbon::now()->gt($ticket->sla_deadline)) Breached @else @if($timeToResolve < 180) Breach Alert ({{ round($timeToResolve / 60, 1) }}h) @else Active @endif @endif
                                            @endif
                                        </span>
                                    </div>
                                    <span class="sub-text fs-11px text-soft">
                                        Target: {{ $ticket->sla_deadline->format('M d, Y h:i A') }}
                                    </span>
                                    @if($ticket->status === 'Resolved' || $ticket->status === 'Closed')
                                        <span class="sub-text fs-11px text-soft">
                                            Actual: {{ $ticket->resolved_at ? $ticket->resolved_at->format('M d, Y h:i A') : 'N/A' }}
                                        </span>
                                    @else
                                        <span class="sub-text fs-11px text-soft sla-timer-text">
                                            @if(\Carbon\Carbon::now()->lte($ticket->sla_deadline))
                                                Timer: <strong class="text-primary">{{ \Carbon\Carbon::now()->diffForHumans($ticket->sla_deadline, ['parts' => 1, 'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW]) }}</strong>
                                            @else
                                                Timer: <strong class="text-danger">Breached</strong>
                                            @endif
                                        </span>
                                    @endif
                                </div>

                                <form action="{{ route('admin.helpdesk.updateTicket', $ticket) }}" method="POST" class="gy-3">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label class="form-label">Ticket Status</label>
                                        <select class="form-select form-control" name="status">
                                            <option value="Open" {{ $ticket->status === 'Open' ? 'selected' : '' }}>Open</option>
                                            <option value="In Progress" {{ $ticket->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="Resolved" {{ $ticket->status === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                            <option value="Closed" {{ $ticket->status === 'Closed' ? 'selected' : '' }}>Closed</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Assigned Support Agent</label>
                                        <select class="form-select form-control" name="assigned_to_id">
                                            <option value="">-- Unassigned --</option>
                                            @foreach($agents as $agent)
                                                <option value="{{ $agent->id }}" {{ (int)$ticket->assigned_to_id === (int)$agent->id ? 'selected' : '' }}>
                                                    {{ $agent->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Priority Level</label>
                                        <select class="form-select form-control" name="priority">
                                            <option value="Low" {{ $ticket->priority === 'Low' ? 'selected' : '' }}>Low</option>
                                            <option value="Medium" {{ $ticket->priority === 'Medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="High" {{ $ticket->priority === 'High' ? 'selected' : '' }}>High</option>
                                            <option value="Urgent" {{ $ticket->priority === 'Urgent' ? 'selected' : '' }}>Urgent</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Category</label>
                                        <select class="form-select form-control" name="category">
                                            <option value="General" {{ $ticket->category === 'General' ? 'selected' : '' }}>General</option>
                                            <option value="Technical" {{ $ticket->category === 'Technical' ? 'selected' : '' }}>Technical</option>
                                            <option value="Billing" {{ $ticket->category === 'Billing' ? 'selected' : '' }}>Billing</option>
                                            <option value="Account" {{ $ticket->category === 'Account' ? 'selected' : '' }}>Account</option>
                                            <option value="Feedback" {{ $ticket->category === 'Feedback' ? 'selected' : '' }}>Feedback</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-block btn-success mt-2">Update Settings</button>
                                </form>
                            </div>
                        </div>

                        <!-- Audit Trail Logs -->
                        <div class="card card-bordered">
                            <div class="card-inner border-bottom">
                                <h6 class="title mb-0">Audit Trail Log</h6>
                            </div>
                            <div class="card-inner p-0" style="max-height: 300px; overflow-y: auto;">
                                <div class="timeline p-3">
                                    @forelse($ticket->logs->sortByDesc('created_at') as $log)
                                        <div class="mb-3 border-bottom pb-2">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <strong class="fs-12px text-dark">{{ $log->action }}</strong>
                                                <span class="sub-text fs-10px">{{ $log->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="fs-11px text-soft">
                                                @if($log->old_value)
                                                    Changed from <span class="text-danger">{{ $log->old_value }}</span> to <span class="text-success">{{ $log->new_value }}</span>
                                                @else
                                                    Value: <span class="text-success">{{ $log->new_value }}</span>
                                                @endif
                                            </div>
                                            @if($log->user)
                                                <div class="fs-10px text-soft mt-1">Performed by: {{ $log->user->name }}</div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="text-center text-soft p-3">No log records recorded.</div>
                                    @endforelse
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const containers = document.querySelectorAll('.sla-clock-container');
        
        function updateClocks() {
            const now = new Date();
            
            containers.forEach(container => {
                const completed = container.getAttribute('data-completed') === 'true';
                if (completed) return;
                
                const deadlineStr = container.getAttribute('data-deadline');
                if (!deadlineStr) return;
                
                const createdStr = container.getAttribute('data-created');
                const deadline = new Date(deadlineStr);
                const created = new Date(createdStr);
                
                const diffMs = deadline - now;
                const totalMs = deadline - created;
                
                const badge = container.querySelector('.sla-badge');
                const timerText = container.querySelector('.sla-timer-text');
                
                if (!badge || !timerText) return;
                
                if (diffMs <= 0) {
                    badge.className = 'badge sla-badge bg-danger';
                    badge.textContent = 'Breached';
                    timerText.innerHTML = 'Timer: <strong class="text-danger">Breached</strong>';
                    return;
                }
                
                const totalSeconds = Math.floor(diffMs / 1000);
                const hours = Math.floor(totalSeconds / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;
                
                const percentRemaining = (diffMs / totalMs) * 100;
                const hoursRemaining = diffMs / 3600000;
                
                let badgeClass = 'bg-success';
                let badgeText = 'Active';
                
                if (hoursRemaining < 2.0 || percentRemaining < 25.0) {
                    badgeClass = 'bg-warning text-dark';
                    badgeText = 'Breach Alert';
                    if (hoursRemaining < 1.0) {
                        badgeText += ` (${minutes}m)`;
                    } else {
                        badgeText += ` (${hoursRemaining.toFixed(1)}h)`;
                    }
                }
                
                badge.className = `badge sla-badge ${badgeClass}`;
                badge.textContent = badgeText;
                
                timerText.innerHTML = `Timer: <strong class="text-primary">${hours}h ${minutes}m ${seconds}s remaining</strong>`;
            });
        }
        
        updateClocks();
        setInterval(updateClocks, 1000);

        // Agent KB Quick-Insert script
        const kbLinks = document.querySelectorAll('.kb-insert-link');
        const replyTextarea = document.getElementById('reply-message-area');
        
        kbLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const content = this.getAttribute('data-content');
                if (replyTextarea) {
                    const startPos = replyTextarea.selectionStart;
                    const endPos = replyTextarea.selectionEnd;
                    const text = replyTextarea.value;
                    
                    if (startPos || startPos === 0) {
                        replyTextarea.value = text.substring(0, startPos) + content + text.substring(endPos, text.length);
                        replyTextarea.selectionStart = startPos + content.length;
                        replyTextarea.selectionEnd = startPos + content.length;
                    } else {
                        replyTextarea.value += content;
                    }
                    replyTextarea.focus();
                }
            });
        });
    });
</script>
@endpush
