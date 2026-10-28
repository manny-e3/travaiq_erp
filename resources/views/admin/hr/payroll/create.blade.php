@extends('layouts.admin')

@section('title', 'Initialize Payroll')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Initialize Payroll Run</h3>
                            <div class="nk-block-des text-soft">
                                <p>Select the target month and year to prepare and calculate the payroll entries for all active employees.</p>
                            </div>
                        </div>
                    </div>
                </div>

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

                <div class="card card-bordered">
                    <div class="card-inner">
                        <form action="{{ route('admin.hr.payroll.store') }}" method="POST">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Month</label>
                                        <div class="form-control-wrap">
                                            <select class="form-select form-control" name="month" required>
                                                @foreach(range(1, 12) as $m)
                                                    @php
                                                        $monthName = date('F', mktime(0, 0, 0, $m, 1));
                                                    @endphp
                                                    <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>{{ $monthName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Year</label>
                                        <div class="form-control-wrap">
                                            <select class="form-select form-control" name="year" required>
                                                @foreach(range(date('Y') - 2, date('Y') + 2) as $y)
                                                    <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group mt-2">
                                        <button type="submit" class="btn btn-primary">Initialize Draft Payroll</button>
                                        <a href="{{ route('admin.hr.payroll.index') }}" class="btn btn-outline-light btn-white ms-2">Cancel</a>
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
@endsection
