@extends('layouts.admin')

@section('title', 'Goals & OKRs')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Performance Goals & OKRs</h3>
                            <div class="nk-block-des text-soft">
                                <p>Set key objectives and track progress towards business process automation targets.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#setGoalModal">
                                <em class="icon ni ni-plus"></em><span>Set New Goal</span>
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

                <!-- Goals Grid/List -->
                <div class="nk-block">
                    <div class="row g-gs">
                        @forelse($goals as $goal)
                            <div class="col-md-6 col-xxl-4">
                                <div class="card card-bordered h-100">
                                    <div class="card-inner">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <span class="badge badge-dim bg-outline-primary mb-2">Weight: {{ $goal->weight }}x</span>
                                                <h5 class="title">{{ $goal->title }}</h5>
                                                @if(Auth::user()->hasRole('admin'))
                                                    <span class="sub-text text-primary">Owner: {{ $goal->user->name }}</span>
                                                @endif
                                            </div>
                                            <div>
                                                @if($goal->status === 'Achieved')
                                                    <span class="badge badge-dim bg-success text-success">Achieved</span>
                                                @elseif($goal->status === 'Missed')
                                                    <span class="badge badge-dim bg-danger text-danger">Missed</span>
                                                @else
                                                    <span class="badge badge-dim bg-warning text-warning">In Progress</span>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="text-soft">{{ $goal->description }}</p>
                                        
                                        <!-- Progress Bar -->
                                        @php
                                            $percentage = min(100, round(($goal->current_value / $goal->target_value) * 100));
                                        @endphp
                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="sub-text text-soft">Progress</span>
                                                <span class="lead-text font-weight-bold">{{ $goal->current_value }} / {{ $goal->target_value }} {{ $goal->unit }} ({{ $percentage }}%)</span>
                                            </div>
                                            <div class="progress progress-md">
                                                <div class="progress-bar {{ $percentage >= 100 ? 'bg-success' : 'bg-primary' }}" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                                            <span class="sub-text text-soft">Ends: {{ $goal->end_date->format('Y-m-d') }}</span>
                                            @if(Auth::id() === $goal->user_id || Auth::user()->hasRole('admin'))
                                                <button type="button" class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#updateGoalProgressModal{{ $goal->id }}">
                                                    Update Progress
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Update Goal Progress Modal -->
                            <div class="modal fade" id="updateGoalProgressModal{{ $goal->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Update Progress: {{ $goal->title }}</h5>
                                            <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                        </div>
                                        <form action="{{ route('admin.hr.goals.updateProgress', $goal) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label class="form-label">Current Achieved Value ({{ $goal->unit }})</label>
                                                    <input type="number" class="form-control" name="current_value" value="{{ $goal->current_value }}" min="0" required>
                                                    <span class="sub-text text-soft mt-1">Target target is: {{ $goal->target_value }} {{ $goal->unit }}</span>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Save Progress</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="card card-bordered">
                                    <div class="card-inner text-center p-5">
                                        <em class="icon ni ni-info-fill fs-1 text-soft mb-3"></em>
                                        <h4>No active goals set.</h4>
                                        <p class="text-soft">Set clear OKRs or KPI goals for yourself or staff to begin tracking target achievements.</p>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Set Goal Modal -->
                <div class="modal fade" id="setGoalModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Set Performance Goal (OKR)</h5>
                                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                            </div>
                            <form action="{{ route('admin.hr.goals.store') }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    @if(Auth::user()->hasRole('admin'))
                                        <div class="form-group">
                                            <label class="form-label">Assign to Employee</label>
                                            <select class="form-select form-control" name="user_id" required>
                                                @foreach($staff ?? [] as $employee)
                                                    <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->designation->name ?? 'Staff' }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <label class="form-label">Objective Title</label>
                                        <input type="text" class="form-control" name="title" placeholder="e.g. Increase monthly lead count" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Description & Key Results</label>
                                        <textarea class="form-control" name="description" rows="3" placeholder="Detail the results used to measure achievement..."></textarea>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label class="form-label">Target Metric Value</label>
                                                <input type="number" class="form-control" name="target_value" value="100" min="1" required>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label class="form-label">Metric Unit</label>
                                                <input type="text" class="form-control" name="unit" value="%" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 mt-1">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label class="form-label">Start Date</label>
                                                <input type="date" class="form-control" name="start_date" value="{{ date('Y-m-d') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label class="form-label">End Date</label>
                                                <input type="date" class="form-control" name="end_date" value="{{ date('Y-12-31') }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label class="form-label">Importance Weight</label>
                                        <select class="form-select form-control" name="weight" required>
                                            <option value="1">1x (Low Weight)</option>
                                            <option value="2" selected>2x (Medium Weight)</option>
                                            <option value="3">3x (High Weight)</option>
                                            <option value="5">5x (Critical Goal)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Create Goal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
