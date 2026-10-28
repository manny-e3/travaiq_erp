@extends('layouts.admin')

@section('title', 'Create Job Posting')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Create Job Posting</h3>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('admin.hr.recruitment.jobs.index') }}" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
                        </div>
                    </div>
                </div>
                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <form action="{{ route('admin.hr.recruitment.jobs.store') }}" method="POST">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Designation</label>
                                            <div class="form-control-wrap">
                                                <select class="form-select js-select2" name="designation_id" required>
                                                    @foreach($designations as $desig)
                                                        <option value="{{ $desig->id }}" {{ old('designation_id') == $desig->id ? 'selected' : '' }}>{{ $desig->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Department</label>
                                            <div class="form-control-wrap">
                                                <select class="form-select" name="department_id" required>
                                                    @foreach($departments as $dept)
                                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label">Location</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" name="location" required value="{{ old('location', 'Remote/HQ') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label">Employment Type</label>
                                            <div class="form-control-wrap">
                                                <select class="form-select" name="employment_type" required>
                                                    <option value="Full-time">Full-time</option>
                                                    <option value="Part-time">Part-time</option>
                                                    <option value="Contract">Contract</option>
                                                    <option value="Internship">Internship</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label">Status</label>
                                            <div class="form-control-wrap">
                                                <select class="form-select" name="status" required>
                                                    <option value="Draft">Draft</option>
                                                    <option value="Published">Published</option>
                                                    <option value="Closed">Closed</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label">Description & Requirements</label>
                                            <div class="form-control-wrap">
                                                <textarea class="form-control summernote-basic" name="description" rows="5" required>{{ old('description') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label">Closing Date</label>
                                            <div class="form-control-wrap">
                                                <input type="date" class="form-control" name="closing_date" value="{{ old('closing_date') }}">
                                                <div class="form-note">Leave empty for "Open until filled"</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label">AI Passing Score</label>
                                            <div class="form-control-wrap">
                                                <input type="number" class="form-control" name="ai_pass_score" min="0" max="100" value="{{ old('ai_pass_score', 40) }}" required>
                                                <div class="form-note">Candidates below this score are auto-rejected when closed.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Save Job Posting</button>
                                        </div>
                                    </div>
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

@push('scripts')
    <link rel="stylesheet" href="{{ asset('assets/css/editors/summernote.css') }}">
    <script src="{{ asset('assets/js/libs/editors/summernote.js') }}"></script>
    <script src="{{ asset('assets/js/editors.js') }}"></script>
@endpush
