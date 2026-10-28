@extends('layouts.admin')
@section('title', 'Edit Asset')
@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Edit Asset: {{ $asset->asset_tag }}</h3>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('admin.assets.show', $asset) }}" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
                        </div>
                    </div>
                </div>

                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <form action="{{ route('admin.assets.update', $asset) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row g-4">
                                    <div class="col-12">
                                        <h5 class="title">General Information</h5>
                                        <hr>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Asset Name <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" name="name" value="{{ old('name', $asset->name) }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Category <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <select class="form-select form-control form-control-lg" name="category_id" required>
                                                    <option value="">Select Category</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" {{ old('category_id', $asset->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
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
                                                        <option value="{{ $location->id }}" {{ old('location_id', $asset->location_id) == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
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
                                                        <option value="{{ $user->id }}" {{ old('custodian_id', $asset->custodian_id) == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Status <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <select class="form-select form-control form-control-lg" name="status" required>
                                                    <option value="Active" {{ old('status', $asset->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                                    <option value="Maintenance" {{ old('status', $asset->status) == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                                                    <option value="Disposed" {{ old('status', $asset->status) == 'Disposed' ? 'selected' : '' }}>Disposed</option>
                                                    <option value="Written-Off" {{ old('status', $asset->status) == 'Written-Off' ? 'selected' : '' }}>Written-Off</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label">Specifications (Serial number, model, brand, etc.)</label>
                                            <div class="form-control-wrap">
                                                <textarea class="form-control" name="specifications" rows="2">{{ old('specifications', $asset->specifications) }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-4">
                                        <div class="alert alert-warning">
                                            <div class="alert-text">
                                                <h6>Financial Data Locked</h6>
                                                <p class="mb-0">Financial fields like purchase cost, salvage value, and depreciation method are locked after initial registration. To adjust the asset's value, please record a <strong>Revaluation</strong> event from the asset profile.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-4">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-lg">Update Asset Details</button>
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
