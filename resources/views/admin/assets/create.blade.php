@extends('layouts.admin')
@section('title', 'Register Asset')
@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Register Asset</h3>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('admin.assets.index') }}" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
                        </div>
                    </div>
                </div>

                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <form action="{{ route('admin.assets.store') }}" method="POST">
                                @csrf
                                <div class="row g-4">
                                    
                                    <div class="col-12">
                                        <h5 class="title">General Information</h5>
                                        <hr>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Asset Tag / QR ID <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" name="asset_tag" value="{{ old('asset_tag', $suggestedTag) }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Asset Name <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Category <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <select class="form-select form-control form-control-lg" name="category_id" required>
                                                    <option value="">Select Category</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Location <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <select class="form-select form-control form-control-lg" name="location_id" required>
                                                    <option value="">Select Location</option>
                                                    @foreach($locations as $location)
                                                        <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Custodian (Assignee)</label>
                                            <div class="form-control-wrap">
                                                <select class="form-select form-control form-control-lg" name="custodian_id">
                                                    <option value="">Unassigned</option>
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}" {{ old('custodian_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label">Specifications (Serial number, model, brand, etc.)</label>
                                            <div class="form-control-wrap">
                                                <textarea class="form-control" name="specifications" rows="2">{{ old('specifications') }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-4">
                                        <h5 class="title">Financial & Depreciation Lifecycle</h5>
                                        <hr>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Purchase Date <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <input type="date" class="form-control" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Purchase Cost (@currencySymbol) <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <input type="number" step="0.01" class="form-control" name="purchase_cost" value="{{ old('purchase_cost', '0.00') }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Salvage Value (@currencySymbol) <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <input type="number" step="0.01" class="form-control" name="salvage_value" value="{{ old('salvage_value', '0.00') }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Useful Life (Years) <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <input type="number" class="form-control" name="useful_life_years" value="{{ old('useful_life_years', 5) }}" required min="1">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Depreciation Method <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <select class="form-select form-control form-control-lg" name="depreciation_method" required>
                                                    <option value="straight_line" {{ old('depreciation_method') == 'straight_line' ? 'selected' : '' }}>Straight-line</option>
                                                    <option value="reducing_balance" {{ old('depreciation_method') == 'reducing_balance' ? 'selected' : '' }}>Reducing Balance</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Initial Status <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <select class="form-select form-control form-control-lg" name="status" required>
                                                    <option value="Active">Active</option>
                                                    <option value="Maintenance">Maintenance</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-4">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-lg">Register Asset & Initialize Lifecycle</button>
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
