@extends('layouts.admin')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">User Profile</h3>
                            <div class="nk-block-des text-soft">
                                <p>Viewing details for {{ $user->name }}.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                                <em class="icon ni ni-arrow-left"></em><span>Back</span>
                            </a>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-icon">
                        <em class="icon ni ni-check-circle"></em> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-icon">
                        <em class="icon ni ni-cross-circle"></em> {{ session('error') }}
                    </div>
                @endif

                <div class="nk-block">
                    <div class="row g-gs">
                        <div class="col-lg-4">
                            <div class="card card-bordered">
                                <div class="card-inner text-center">
                                    <div class="user-avatar lg bg-primary mb-3" style="width: 100px; height: 100px; border-radius: 50%; overflow: hidden; margin: 0 auto;">
                                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <h4 class="title mb-1">{{ $user->name }}</h4>
                                    <span class="sub-text">{{ $user->email }}</span>
                                    
                                    <div class="mt-3">
                                        @if($user->is_active)
                                            <span class="badge badge-dot bg-success">Active</span>
                                        @else
                                            <span class="badge badge-dot bg-danger">Inactive</span>
                                        @endif
                                        @foreach($user->roles as $role)
                                            <span class="badge badge-dim bg-outline-primary text-uppercase">{{ $role->name }}</span>
                                        @endforeach
                                    </div>
                                    
                                    <div class="mt-4">
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary"><em class="icon ni ni-edit"></em><span>Edit Profile</span></a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card card-bordered mt-4">
                                <div class="card-inner">
                                    <h6 class="title mb-3">Additional Details</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><strong>Employee ID:</strong> {{ $user->employee_id ?? 'N/A' }}</li>
                                        <li class="mb-2"><strong>Department:</strong> {{ $user->department->name ?? 'N/A' }}</li>
                                        <li class="mb-2"><strong>Designation:</strong> {{ $user->designation->name ?? $user->job_title ?? 'N/A' }}</li>
                                        <li class="mb-2"><strong>Phone:</strong> {{ $user->phone ?? 'N/A' }}</li>
                                        <li class="mb-2"><strong>Joined At:</strong> {{ $user->created_at->format('d M Y, h:i A') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-8">
                            <div class="card card-bordered card-stretch">
                                <div class="card-inner-group">
                                    <div class="card-inner">
                                        <div class="card-title-group">
                                            <div class="card-title">
                                                <h5 class="title">Login History</h5>
                                                <div class="sub-text">Recent 50 login attempts.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-inner p-0">
                                        <table class="table table-tranx">
                                            <thead>
                                                <tr class="tb-tnx-head">
                                                    <th class="tb-tnx-info">IP Address</th>
                                                    <th class="tb-tnx-info">User Agent</th>
                                                    <th class="tb-tnx-date">Date & Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($user->loginHistories as $history)
                                                <tr class="tb-tnx-item">
                                                    <td class="tb-tnx-info">
                                                        <span class="title">{{ $history->ip_address }}</span>
                                                    </td>
                                                    <td class="tb-tnx-info">
                                                        <span class="sub-text">{{ Str::limit($history->user_agent, 40) }}</span>
                                                    </td>
                                                    <td class="tb-tnx-date">
                                                        <span class="date">{{ $history->login_at->format('d M Y') }}</span>
                                                        <span class="time">{{ $history->login_at->format('h:i A') }}</span>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="3" class="text-center p-3 text-muted">No login history recorded yet.</td>
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
        </div>
    </div>
</div>
@endsection
