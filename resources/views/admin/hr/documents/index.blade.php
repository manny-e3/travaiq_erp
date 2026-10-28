@extends('layouts.admin')

@section('title', 'Document Vault')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                
                <!-- Head -->
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Document Vault</h3>
                            <div class="nk-block-des text-soft">
                                <p>Central security vault for all credentials, contracts, and profiles.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Overview -->
                <div class="row g-gs mb-4">
                    <div class="col">
                        <div class="card card-bordered">
                            <div class="card-inner py-3 text-center">
                                <div class="h6 text-muted mb-1">Total Files</div>
                                <div class="h3">{{ $stats['total'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card card-bordered">
                            <div class="card-inner py-3 text-center">
                                <div class="h6 text-info mb-1">Resumes/CVs</div>
                                <div class="h3 text-info">{{ $stats['cv'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card card-bordered">
                            <div class="card-inner py-3 text-center">
                                <div class="h6 text-success mb-1">IDs & Passports</div>
                                <div class="h3 text-success">{{ $stats['id_card'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card card-bordered">
                            <div class="card-inner py-3 text-center">
                                <div class="h6 text-warning mb-1">Certificates</div>
                                <div class="h3 text-warning">{{ $stats['certificate'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card card-bordered">
                            <div class="card-inner py-3 text-center">
                                <div class="h6 text-secondary mb-1">Others</div>
                                <div class="h3">{{ $stats['other'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card card-bordered mb-4">
                    <div class="card-inner">
                        <form method="GET" action="{{ route('admin.hr.documents.index') }}">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="form-control-wrap">
                                            <div class="form-icon form-icon-left"><em class="icon ni ni-search"></em></div>
                                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search by document name or employee...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select class="form-select form-control" name="document_type">
                                            <option value="">All Document Types</option>
                                            <option value="CV" {{ request('document_type') === 'CV' ? 'selected' : '' }}>CV / Resume</option>
                                            <option value="ID Card" {{ request('document_type') === 'ID Card' ? 'selected' : '' }}>ID / Passport</option>
                                            <option value="Certificate" {{ request('document_type') === 'Certificate' ? 'selected' : '' }}>Qualification Certificate</option>
                                            <option value="Contract" {{ request('document_type') === 'Contract' ? 'selected' : '' }}>Signed Contract</option>
                                            <option value="Other" {{ request('document_type') === 'Other' ? 'selected' : '' }}>Other Document</option>
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

                <!-- Documents Table List -->
                <div class="card card-bordered card-stretch">
                    <div class="card-inner-group">
                        <div class="card-inner p-0">
                            <table class="table table-tranx">
                                <thead>
                                    <tr class="tb-tnx-head">
                                        <th>Document Name</th>
                                        <th>Category</th>
                                        <th>Employee</th>
                                        <th>Uploaded Date</th>
                                        <th class="tb-tnx-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($documents as $doc)
                                    <tr class="tb-tnx-item">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <em class="icon ni ni-file-text text-primary fs-28px me-2"></em>
                                                <div>
                                                    <span class="lead-text">{{ $doc->document_name }}</span>
                                                    @if($doc->notes)
                                                        <span class="sub-text text-muted fs-11px">{{ $doc->notes }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-dim bg-info">{{ $doc->document_type }}</span>
                                        </td>
                                        <td>
                                            @if($doc->user)
                                                <div class="user-info">
                                                    <span class="lead-text">{{ $doc->user->name }}</span>
                                                    <span class="sub-text text-muted fs-11px">{{ $doc->user->employee_id ?? 'N/A' }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted">Deleted User</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span>{{ $doc->created_at->format('M d, Y, h:i A') }}</span>
                                        </td>
                                        <td class="tb-tnx-action">
                                            <div class="d-flex gap-1 justify-content-end">
                                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-xs btn-icon btn-dim btn-primary" title="Download"><em class="icon ni ni-download"></em></a>
                                                @if($doc->user)
                                                    <a href="{{ route('admin.hr.employees.show', ['user' => $doc->user->id]) }}#vault" class="btn btn-xs btn-dim btn-primary">Vault</a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center p-4 text-muted">No vault documents matching the selected filters.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($documents->hasPages())
                        <div class="card-inner">
                            {{ $documents->links() }}
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
