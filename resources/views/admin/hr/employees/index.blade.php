@extends('layouts.admin')

@section('title', 'Employee Profiles')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Employee Profiles</h3>
                            <div class="nk-block-des text-soft">
                                <p>Manage detailed records, qualifications, contracts, and history of employees.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary d-none d-md-inline-flex"><em class="icon ni ni-plus"></em><span>Add Employee</span></a>
                        </div>
                    </div>
                </div>

                <!-- Search & Filters -->
                <div class="card card-bordered mb-4">
                    <div class="card-inner">
                        <form method="GET" action="{{ route('admin.hr.employees.index') }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="form-control-wrap">
                                            <div class="form-icon form-icon-left"><em class="icon ni ni-search"></em></div>
                                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search by name, ID, title, or email...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select class="form-select form-control" name="department_id">
                                            <option value="">All Departments</option>
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select class="form-select form-control" name="status">
                                            <option value="">All Statuses</option>
                                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Employee List -->
                <div class="card card-bordered card-stretch">
                    <div class="card-inner-group">
                        <div class="card-inner p-0">
                            <table class="table table-tranx">
                                <thead>
                                    <tr class="tb-tnx-head">
                                        <th class="tb-tnx-id">Employee ID</th>
                                        <th class="tb-tnx-info">Employee</th>
                                        <th class="tb-tnx-info">Job Title & Dept</th>
                                        <th class="tb-tnx-info">Status</th>
                                        <th class="tb-tnx-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employees as $employee)
                                    <tr class="tb-tnx-item">
                                        <td class="tb-tnx-id">
                                            <span class="badge bg-light text-primary">{{ $employee->employee_id ?? 'N/A' }}</span>
                                        </td>
                                        <td class="tb-tnx-info">
                                            <div class="user-card">
                                                <div class="user-avatar bg-primary">
                                                    <span>{{ strtoupper(substr($employee->name, 0, 2)) }}</span>
                                                </div>
                                                <div class="user-info">
                                                    <span class="lead-text">{{ $employee->name }}</span>
                                                    <span class="sub-text">{{ $employee->email }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="tb-tnx-info">
                                            <span class="title">{{ $employee->designation->name ?? $employee->job_title ?? 'N/A' }}</span>
                                            <span class="sub-text text-muted">{{ $employee->department->name ?? 'Unassigned' }}</span>
                                        </td>
                                        <td class="tb-tnx-info">
                                            @if($employee->is_active)
                                                <span class="badge badge-dim bg-success">Active</span>
                                            @else
                                                <span class="badge badge-dim bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="tb-tnx-action">
                                            <a href="{{ route('admin.hr.employees.show', $employee->id) }}" class="btn btn-sm btn-dim btn-primary"><em class="icon ni ni-eye"></em> View Records</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center p-4 text-muted">No employee profiles found matching your search.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($employees->hasPages())
                        <div class="card-inner">
                            <div class="nk-block-between-md g-3">
                                <div class="g">
                                    {{ $employees->links() }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
