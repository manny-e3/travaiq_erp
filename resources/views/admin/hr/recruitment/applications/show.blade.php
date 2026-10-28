@extends('layouts.admin')

@section('title', 'Applicant Details')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">{{ $jobApplication->full_name }}</h3>
                            <div class="nk-block-des text-soft">
                                <p>Applying for: <strong>{{ $jobApplication->jobPosting->title }}</strong></p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('admin.hr.recruitment.jobs.show', $jobApplication->job_posting_id) }}" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back to Job</span></a>
                        </div>
                    </div>
                </div>
                
                <div class="row g-gs">
                    <div class="col-md-4">
                        <div class="card card-bordered">
                            <div class="card-inner-group">
                                <div class="card-inner">
                                    <div class="user-card user-card-s2">
                                        <div class="user-avatar lg bg-primary">
                                            <span>{{ substr($jobApplication->first_name, 0, 1) }}{{ substr($jobApplication->last_name, 0, 1) }}</span>
                                        </div>
                                        <div class="user-info">
                                            <h5>{{ $jobApplication->full_name }}</h5>
                                            <span class="sub-text">{{ $jobApplication->email }}</span>
                                            <span class="sub-text">{{ $jobApplication->phone }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-inner">
                                    <div class="row text-center">
                                        <div class="col-12">
                                            <div class="profile-stats">
                                                <span class="amount">{{ $jobApplication->created_at->format('M d, Y') }}</span>
                                                <span class="sub-text">Applied On</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-inner">
                                    <h6 class="overline-title mb-2">Change Status</h6>
                                    <form action="{{ route('admin.hr.recruitment.applications.updateStatus', $jobApplication) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="input-group">
                                            <select name="status" class="form-select">
                                                <option value="New" {{ $jobApplication->status == 'New' ? 'selected' : '' }}>New</option>
                                                <option value="In Review" {{ $jobApplication->status == 'In Review' ? 'selected' : '' }}>In Review</option>
                                                <option value="Interviewing" {{ $jobApplication->status == 'Interviewing' ? 'selected' : '' }}>Interviewing</option>
                                                <!-- <option value="Offered" {{ $jobApplication->status == 'Offered' ? 'selected' : '' }}>Offered</option>
                                                <option value="Hired" {{ $jobApplication->status == 'Hired' ? 'selected' : '' }}>Hired</option> -->
                                                <option value="Rejected" {{ $jobApplication->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-inner text-center">
                                    <a href="{{ asset('resumes/' . $jobApplication->resume_path) }}" target="_blank" class="btn btn-outline-secondary w-100"><em class="icon ni ni-download"></em> View Resume</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        @if($jobApplication->ai_score !== null)
                        <div class="card card-bordered mb-4">
                            <div class="card-inner">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h5 class="title mb-0"><em class="icon ni ni-cpu"></em> AI Screening Results</h5>
                                    <span class="badge badge-dim {{ $jobApplication->ai_score >= 70 ? 'bg-success' : ($jobApplication->ai_score >= 40 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ $jobApplication->ai_recommendation }}
                                    </span>
                                </div>
                                <div class="row g-gs align-items-center">
                                    <div class="col-md-3 text-center">
                                        <div class="h1 fw-bold text-{{ $jobApplication->ai_score >= 70 ? 'success' : ($jobApplication->ai_score >= 40 ? 'warning' : 'danger') }}">{{ $jobApplication->ai_score }}%</div>
                                        <span class="sub-text">Match Score</span>
                                    </div>
                                    <div class="col-md-9 border-start">
                                        <p class="text-soft">{{ $jobApplication->ai_summary }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="card card-bordered mb-4">
                            <div class="card-inner">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="title mb-1"><em class="icon ni ni-cpu"></em> AI Screening Pending</h5>
                                        <p class="text-soft mb-0">This application has not been screened by AI yet.</p>
                                    </div>
                                    <form action="{{ route('admin.hr.recruitment.applications.screen', $jobApplication) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary"><em class="icon ni ni-cpu"></em> Run AI Screening</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="card card-bordered">
                            <ul class="nav nav-tabs nav-tabs-mb-icon nav-tabs-card">
                                @if($jobApplication->cover_letter)
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#cover-letter"><em class="icon ni ni-file-docs"></em><span>Cover Letter</span></a>
                                </li>
                                @endif
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#interviews"><em class="icon ni ni-calendar"></em><span>Interviews</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#offers"><em class="icon ni ni-file-text"></em><span>Offer Letter</span></a>
                                </li>
                            </ul>
                            
                            <div class="tab-content mt-0">
                                @if($jobApplication->cover_letter)
                                <!-- Cover Letter Tab -->
                                <div class="tab-pane" id="cover-letter">
                                    <div class="card-inner">
                                        <h5 class="title mb-4">Cover Letter</h5>
                                        <div class="bg-white p-4 rounded border">
                                            {!! nl2br(e($jobApplication->cover_letter)) !!}
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Interviews Tab -->
                                <div class="tab-pane active" id="interviews">
                                    <div class="card-inner">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <h5 class="title">Interview Schedule</h5>
                                            @if($jobApplication->status == 'Interviewing')
                                                <button data-bs-toggle="modal" data-bs-target="#scheduleInterviewModal" class="btn btn-sm btn-primary">Schedule Next Round</button>
                                            @endif
                                        </div>
                                        
                                        @if($jobApplication->interviews->count() > 0)
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Stage</th>
                                                    <th>Date & Time</th>
                                                    <th>Interviewer</th>
                                                    <th>Location/Link</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($jobApplication->interviews as $interview)
                                                <tr>
                                                    <td>{{ $interview->interview_stage ?? 'N/A' }}</td>
                                                    <td>{{ $interview->scheduled_at->format('M d, Y h:i A') }}</td>
                                                    <td>{{ $interview->interviewers->pluck('name')->join(', ') }}</td>
                                                    <td>{{ $interview->location_or_link }}</td>
                                                    <td>
                                                        <span class="badge {{ $interview->status == 'Scheduled' ? 'bg-primary' : ($interview->status == 'Completed' ? 'bg-success' : 'bg-danger') }}">
                                                            {{ $interview->status }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <form action="{{ route('admin.hr.recruitment.interviews.update', $interview) }}" method="POST">
                                                            @csrf @method('PUT')
                                                            <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">
                                                                <option value="Scheduled" {{ $interview->status == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                                                <option value="Completed" {{ $interview->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                                                <option value="Cancelled" {{ $interview->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                            </select>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @else
                                        <p class="text-muted">No interviews scheduled yet.</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Offers Tab -->
                                <div class="tab-pane" id="offers">
                                    <div class="card-inner">
                                        @php
                                            $hasCompletedFinalRound = $jobApplication->interviews()->where('interview_stage', 'Final Round')->where('status', 'Completed')->exists();
                                        @endphp

                                        @if(!$hasCompletedFinalRound)
                                            <div class="alert alert-warning">
                                                <strong>Note:</strong> You must schedule and complete a "Final Round" interview before you can generate an offer letter for this candidate.
                                            </div>
                                        @elseif($jobApplication->offer)
                                            <div class="alert alert-success mb-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h5>Offer Generated</h5>
                                                         <p class="mb-0">Salary: @currency($jobApplication->offer->salary_offer) | Start Date: {{ $jobApplication->offer->expected_start_date->format('M d, Y') }}</p>
                                                    </div>
                                                    <div>
                                                        <a href="{{ asset('offer_letter/' . $jobApplication->offer->document_path) }}" target="_blank" class="btn btn-primary">Download Offer PDF</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="alert alert-info">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h5>Onboard Candidate</h5>
                                                        <p class="mb-0">Flow this applicant into the Employee Records system.</p>
                                                    </div>
                                                    <div>
                                                        @if(\App\Models\User::where('email', $jobApplication->email)->exists())
                                                            <span class="badge bg-success fs-13px">Already Onboarded</span>
                                                        @else
                                                            <form action="{{ route('admin.hr.recruitment.applications.onboard', $jobApplication) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success"><em class="icon ni ni-user-add"></em> Onboard as Employee</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <h5 class="title mb-4">Generate Offer Letter</h5>
                                            <form action="{{ route('admin.hr.recruitment.offers.store', $jobApplication) }}" method="POST">
                                                @csrf
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Salary Offer</label>
                                                             <div class="form-control-wrap">
                                                                 <div class="form-icon form-icon-left" style="font-style: normal; font-weight: bold;">@currencySymbol</div>
                                                                 <input type="number" step="0.01" class="form-control" name="salary_offer" required>
                                                             </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Expected Start Date</label>
                                                            <div class="form-control-wrap">
                                                                <input type="date" class="form-control" name="expected_start_date" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if(in_array($jobApplication->jobPosting->employment_type, ['Contract', 'Consulting', 'Internship']))
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Contract Duration (Months)</label>
                                                            <div class="form-control-wrap">
                                                                <input type="number" class="form-control" name="contract_duration_months" value="{{ $jobApplication->jobPosting->employment_type == 'Internship' ? 3 : ($jobApplication->jobPosting->employment_type == 'Contract' ? 6 : 12) }}" min="1" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    <div class="col-12 mt-4">
                                                        <button type="submit" class="btn btn-success">Generate & Save PDF</button>
                                                    </div>
                                                </div>
                                            </form>
                                        @endif
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

<!-- Schedule Interview Modal -->
<div class="modal fade" tabindex="-1" id="scheduleInterviewModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Schedule Interview</h5>
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.hr.recruitment.interviews.store', $jobApplication) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Interview Stage/Type</label>
                        <select name="interview_stage" id="interview_stage_select" class="form-select" required>
                            <option value="Phone Screen">Phone Screen</option>
                            <option value="1st Round">1st Round Interview</option>
                            <option value="Assessment">Assessment</option>
                            <option value="Culture Fit">Culture Fit</option>
                            <option value="Final Round">Final Round</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Interviewers</label>
                        <select name="interviewer_ids[]" class="form-select" multiple required size="4" data-search="on">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" id="date_label">Date & Time</label>
                        <input type="datetime-local" class="form-control" name="scheduled_at" required>
                    </div>
                    
                    <div id="standard_fields">
                        <div class="form-group">
                            <label class="form-label">Location or Video Link</label>
                            <input type="text" class="form-control" name="location_or_link" id="location_or_link" placeholder="Zoom link or Room 102">
                        </div>
                    </div>
                    
                    <div id="assessment_fields" style="display: none;">
                        <div class="form-group">
                            <label class="form-label">Assessment Details (optional)</label>
                            <textarea class="form-control" name="assessment_details" rows="3" placeholder="Instructions for the assessment..."></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Assessment Attachment (optional)</label>
                            <input type="file" class="form-control" name="assessment_file" accept=".pdf,.doc,.docx,.zip">
                        </div>
                    </div>
                    
                    <div class="form-group text-end mt-4">
                        <button type="submit" class="btn btn-primary">Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('select[name="status"]').on('change', function() {
            if ($(this).val() === 'Interviewing') {
                $('#scheduleInterviewModal').modal('show');
                // Reset select back to original value so they use the modal form to save
                $(this).val('{{ $jobApplication->status }}');
            }
        });

        $('#interview_stage_select').on('change', function() {
            var stage = $(this).val();
            if (stage === 'Assessment') {
                $('#standard_fields').hide();
                $('#location_or_link').removeAttr('required');
                $('#assessment_fields').show();
                $('#date_label').text('Due Date & Time');
            } else if (stage === 'Phone Screen') {
                $('#standard_fields').hide();
                $('#location_or_link').removeAttr('required');
                $('#assessment_fields').hide();
                $('#date_label').text('Date & Time');
            } else {
                $('#standard_fields').show();
                $('#location_or_link').attr('required', 'required');
                $('#assessment_fields').hide();
                $('#date_label').text('Date & Time');
            }
        });
        
        // Trigger on load to set correct state
        $('#interview_stage_select').trigger('change');
    });
</script>
@endpush
