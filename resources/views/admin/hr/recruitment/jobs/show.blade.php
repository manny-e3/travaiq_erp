@extends('layouts.admin')

@section('title', 'Applicant Tracking Board')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">ATS: {{ $jobPosting->title }}</h3>
                            <div class="nk-block-des text-soft">
                                <p>{{ $jobPosting->applications->count() }} Applicants | {{ $jobPosting->location }} | {{ $jobPosting->department->name ?? 'N/A' }} | AI Pass Score: {{ $jobPosting->ai_pass_score }}%</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <button data-bs-toggle="modal" data-bs-target="#addApplicantModal" class="btn btn-primary"><em class="icon ni ni-plus"></em><span>Add Applicant</span></button>
                            <a href="{{ route('admin.hr.recruitment.jobs.index') }}" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Applicant List -->
                <div class="nk-block">
                    <div class="card card-bordered">
                        <form action="{{ route('admin.hr.recruitment.applications.bulkReject') }}" method="POST" id="bulkRejectForm">
                            @csrf
                            <div class="card-inner border-bottom">
                                <button type="button" class="btn btn-danger" onclick="if(confirm('Are you sure you want to reject the selected applicants?')) document.getElementById('bulkRejectForm').submit();"><em class="icon ni ni-cross-circle"></em><span>Bulk Reject Selected</span></button>
                            </div>
                            <table class="table table-tranx table-hover">
                            <thead>
                                <tr>
                                    <th class="w-50px">
                                        <div class="custom-control custom-control-sm custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="selectAll">
                                            <label class="custom-control-label" for="selectAll"></label>
                                        </div>
                                    </th>
                                    <th>Applicant Name</th>
                                    <th>Email</th>
                                    <th>Applied On</th>
                                    <th>AI Score</th>
                                    <th>Status</th>
                                    <th>Resume</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jobPosting->applications->sortByDesc('ai_score') as $app)
                                <tr>
                                    <td>
                                        <div class="custom-control custom-control-sm custom-checkbox">
                                            <input type="checkbox" class="custom-control-input app-checkbox" id="app_{{ $app->id }}" name="application_ids[]" value="{{ $app->id }}">
                                            <label class="custom-control-label" for="app_{{ $app->id }}"></label>
                                        </div>
                                    </td>
                                    <td><a href="{{ route('admin.hr.recruitment.applications.show', $app) }}">{{ $app->full_name }}</a></td>
                                    <td>{{ $app->email }}</td>
                                    <td>{{ $app->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @if($app->ai_score !== null)
                                            <span class="badge {{ $app->ai_score >= 70 ? 'bg-success' : ($app->ai_score >= 40 ? 'bg-warning' : 'bg-danger') }}">
                                                {{ $app->ai_score }}% - {{ $app->ai_recommendation }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-dark">Pending AI</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge 
                                            {{ $app->status === 'New' ? 'bg-primary' : '' }}
                                            {{ $app->status === 'In Review' ? 'bg-warning' : '' }}
                                            {{ $app->status === 'Interviewing' ? 'bg-info' : '' }}
                                            {{ $app->status === 'Offered' ? 'bg-success' : '' }}
                                            {{ $app->status === 'Hired' ? 'bg-success' : '' }}
                                            {{ $app->status === 'Rejected' ? 'bg-danger' : '' }}
                                        ">{{ $app->status }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ asset('resumes/' . $app->resume_path) }}" target="_blank" class="btn btn-sm btn-outline-primary"><em class="icon ni ni-download"></em> CV</a>
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.hr.recruitment.applications.screen', $app) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-icon btn-light" title="Re-Screen AI"><em class="icon ni ni-cpu"></em></button>
                                        </form>
                                        <a href="{{ route('admin.hr.recruitment.applications.show', $app) }}" class="btn btn-sm btn-icon btn-light" title="View"><em class="icon ni ni-eye"></em></a>
                                    </td>
                                </tr>
                                @endforeach
                                @if($jobPosting->applications->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No applicants yet.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                        </form>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<!-- Add Applicant Modal -->
<div class="modal fade" tabindex="-1" id="addApplicantModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manually Add Applicant</h5>
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.hr.recruitment.applications.store', $jobPosting) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone (optional)</label>
                        <input type="text" class="form-control" name="phone">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Resume (PDF, DOCX)</label>
                        <input type="file" class="form-control" name="resume_file" required accept=".pdf,.doc,.docx">
                    </div>
                    <div class="form-group text-end mt-4">
                        <button type="submit" class="btn btn-primary">Add Applicant</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('selectAll').addEventListener('change', function(e) {
        let checkboxes = document.querySelectorAll('.app-checkbox');
        checkboxes.forEach(cb => cb.checked = e.target.checked);
    });
</script>
@endpush
@endsection
