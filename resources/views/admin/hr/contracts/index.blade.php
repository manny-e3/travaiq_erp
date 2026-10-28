@extends('layouts.admin')

@section('title', 'Contract Management')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                
                <!-- Head -->
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Contract Management</h3>
                            <div class="nk-block-des text-soft">
                                <p>Monitor all employee contracts, salaries, and tracking timelines.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Overview -->
                <div class="row g-gs mb-4">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title text-muted">Total Contracts</h6>
                                    </div>
                                </div>
                                <div class="align-end gy-1">
                                    <div class="amount">{{ $totalContracts }}</div>
                                    <span class="sub-text text-soft">All-time generated</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered text-white bg-success">
                            <div class="card-inner text-white">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title text-white opacity-70">Active Contracts</h6>
                                    </div>
                                </div>
                                <div class="align-end gy-1">
                                    <div class="amount text-white">{{ $activeContracts }}</div>
                                    <span class="sub-text text-white opacity-75">Currently in force</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered {{ $expiringSoon > 0 ? 'bg-danger text-white' : '' }}">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title {{ $expiringSoon > 0 ? 'text-white' : 'text-muted' }}">Expiring < 30 Days</h6>
                                    </div>
                                </div>
                                <div class="align-end gy-1">
                                    <div class="amount {{ $expiringSoon > 0 ? 'text-white' : 'text-danger' }}">{{ $expiringSoon }}</div>
                                    <span class="sub-text {{ $expiringSoon > 0 ? 'text-white opacity-75' : 'text-soft' }}">Requires renewal actions</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title text-muted">Terminated</h6>
                                    </div>
                                </div>
                                <div class="align-end gy-1">
                                    <div class="amount text-soft">{{ $terminatedContracts }}</div>
                                    <span class="sub-text text-soft">Ended contracts</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card card-bordered mb-4">
                    <div class="card-inner">
                        <form method="GET" action="{{ route('admin.hr.contracts.index') }}">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="form-control-wrap">
                                            <div class="form-icon form-icon-left"><em class="icon ni ni-search"></em></div>
                                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search employee...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select class="form-select form-control" name="contract_type">
                                            <option value="">All Contract Types</option>
                                            <option value="Full-time" {{ request('contract_type') === 'Full-time' ? 'selected' : '' }}>Full-time</option>
                                            <option value="Part-time" {{ request('contract_type') === 'Part-time' ? 'selected' : '' }}>Part-time</option>
                                            <option value="Contract" {{ request('contract_type') === 'Contract' ? 'selected' : '' }}>Contract (Fixed-term)</option>
                                            <option value="Internship" {{ request('contract_type') === 'Internship' ? 'selected' : '' }}>Internship</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select class="form-select form-control" name="status">
                                            <option value="">All Statuses</option>
                                            <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                                            <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="Expired" {{ request('status') === 'Expired' ? 'selected' : '' }}>Expired</option>
                                            <option value="Terminated" {{ request('status') === 'Terminated' ? 'selected' : '' }}>Terminated</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <div class="custom-control custom-control-sm custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="expiring-check" name="expiring" value="1" {{ request('expiring') ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="expiring-check">Expiring Soon</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Contracts List -->
                <div class="card card-bordered card-stretch">
                    <div class="card-inner-group">
                        <div class="card-inner p-0">
                            <table class="table table-tranx">
                                <thead>
                                    <tr class="tb-tnx-head">
                                        <th>Employee</th>
                                        <th>Contract Type</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Basic Salary</th>
                                        <th>Status</th>
                                        <th class="tb-tnx-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($contracts as $contract)
                                    <tr class="tb-tnx-item">
                                        <td>
                                            @if($contract->user)
                                                <div class="user-card">
                                                    <div class="user-info">
                                                        <span class="lead-text">{{ $contract->user->name }}</span>
                                                        <span class="sub-text">{{ $contract->user->department->name ?? 'Unassigned' }}</span>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">Deleted User</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="title">{{ $contract->contract_type }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $contract->start_date->format('M d, Y') }}</span>
                                        </td>
                                        <td>
                                            @if($contract->end_date)
                                                @php
                                                    $isExpiring = $contract->status === 'Active' && $contract->end_date->isFuture() && $contract->end_date->diffInDays(now()) <= 30;
                                                @endphp
                                                <span class="{{ $isExpiring ? 'text-danger fw-bold' : '' }}">
                                                    {{ $contract->end_date->format('M d, Y') }}
                                                    @if($isExpiring)
                                                        <span class="d-block fs-10px badge bg-dim bg-danger mt-1">Expiring in {{ $contract->end_date->diffInDays(now()) }} Days</span>
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-soft">Indefinite</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>@currency($contract->basic_salary)</strong>
                                        </td>
                                        <td>
                                            <span class="badge {{ $contract->status === 'Active' ? 'bg-success' : ($contract->status === 'Pending' ? 'bg-warning' : 'bg-danger') }}">
                                                {{ $contract->status }}
                                            </span>
                                        </td>
                                        <td class="tb-tnx-action">
                                            <div class="d-flex gap-1 justify-content-end">
                                                @if($contract->document_path)
                                                    <a href="{{ asset('storage/' . $contract->document_path) }}" target="_blank" class="btn btn-xs btn-icon btn-dim btn-primary" title="Download Document"><em class="icon ni ni-download"></em></a>
                                                @endif
                                                @if($contract->user)
                                                    <a href="{{ route('admin.hr.employees.show', ['user' => $contract->user->id]) }}#contracts" class="btn btn-xs btn-dim btn-primary">Manage</a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center p-4 text-muted">No contracts matching the selected filters.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($contracts->hasPages())
                        <div class="card-inner">
                            {{ $contracts->links() }}
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
