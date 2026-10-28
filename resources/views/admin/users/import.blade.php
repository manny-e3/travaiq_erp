@extends('layouts.admin')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Bulk Import Users</h3>
                            <div class="nk-block-des text-soft">
                                <p>Upload a CSV file to import multiple users.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                                <em class="icon ni ni-arrow-left"></em><span>Back to Users</span>
                            </a>
                        </div>
                    </div>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger alert-icon">
                        <em class="icon ni ni-cross-circle"></em> {{ session('error') }}
                    </div>
                @endif

                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row g-gs">
                                    <div class="col-12">
                                        <div class="alert alert-info alert-icon">
                                            <em class="icon ni ni-info-fill"></em> 
                                            <strong>Required CSV Format:</strong>
                                            <p class="mb-0">The CSV must have the following headers: <code>name, email, role_name, department_name, employee_id, phone, job_title</code>.</p>
                                            <p class="mb-0 mt-1">Example: <code>John Doe, john@example.com, admin, IT, EMP123, 555-1234, Developer</code></p>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="form-label">Upload CSV File</label>
                                            <div class="form-control-wrap">
                                                <div class="form-file">
                                                    <input type="file" name="file" class="form-file-input @error('file') is-invalid @enderror" id="customFile" accept=".csv" required>
                                                    <label class="form-file-label" for="customFile">Choose file</label>
                                                </div>
                                                @error('file') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mt-auto">
                                        <button type="submit" class="btn btn-lg btn-primary w-100"><em class="icon ni ni-upload"></em><span>Import Users</span></button>
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
