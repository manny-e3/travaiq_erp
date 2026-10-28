@extends('layouts.admin')
@section('title', 'Asset Profile')
@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between g-3">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Asset: <strong class="text-primary small">{{ $asset->asset_tag }}</strong></h3>
                            <div class="nk-block-des text-soft">
                                <ul class="list-inline">
                                    <li>Registered At: <span class="text-base">{{ $asset->created_at->format('d M, Y') }}</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('admin.assets.index') }}" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
                            <a href="{{ route('admin.assets.edit', $asset) }}" class="btn btn-primary"><em class="icon ni ni-edit"></em><span>Edit Details</span></a>
                        </div>
                    </div>
                </div>

                <div class="nk-block">
                    <div class="row g-gs">
                        <!-- Left Column: Details -->
                        <div class="col-lg-4 col-xl-4 col-xxl-3">
                            <div class="card card-bordered">
                                <div class="card-inner-group">
                                    <div class="card-inner">
                                        <div class="user-card user-card-s2">
                                            <div class="d-flex justify-content-center mb-4">
                                                <!-- Dynamic QR Code generated from API -->
                                                <img src="{{ $qrCodeUrl }}" alt="QR Code for {{ $asset->asset_tag }}" class="img-thumbnail" style="width: 150px; height: 150px;">
                                            </div>
                                            <div class="user-info text-center">
                                                <h4>{{ $asset->name }}</h4>
                                                <span class="sub-text">{{ $asset->category->name ?? 'No Category' }}</span>
                                                <div class="mt-2">
                                                    @if($asset->status === 'Active')
                                                        <span class="badge badge-dot bg-success">Active</span>
                                                    @elseif($asset->status === 'Maintenance')
                                                        <span class="badge badge-dot bg-warning">Maintenance</span>
                                                    @elseif($asset->status === 'Disposed')
                                                        <span class="badge badge-dot bg-secondary">Disposed</span>
                                                    @else
                                                        <span class="badge badge-dot bg-danger">Written-Off</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-inner card-inner-sm">
                                        <ul class="btn-toolbar justify-center gx-1">
                                            <li><button class="btn btn-trigger btn-icon" data-bs-toggle="modal" data-bs-target="#recordEventModal"><em class="icon ni ni-activity-round"></em></button></li>
                                            <li><a href="#" class="btn btn-trigger btn-icon text-danger" onclick="alert('Print functionality mock')"><em class="icon ni ni-printer"></em></a></li>
                                        </ul>
                                    </div>
                                    <div class="card-inner">
                                        <h6 class="overline-title mb-2">Location & Custodian</h6>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <span class="sub-text">Location:</span>
                                                <span>{{ $asset->location->name ?? 'N/A' }}</span>
                                                @if($asset->location && $asset->location->address)
                                                    <div class="text-soft small">{{ $asset->location->address }}</div>
                                                @endif
                                            </div>
                                            <div class="col-12">
                                                <span class="sub-text">Assigned Custodian:</span>
                                                <span>{{ $asset->custodian->name ?? 'Unassigned' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-inner">
                                        <h6 class="overline-title mb-2">Financial Snapshot</h6>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <span class="sub-text">Purchase Cost:</span>
                                                <span class="fw-bold">@currency($asset->purchase_cost)</span>
                                            </div>
                                            <div class="col-12">
                                                <span class="sub-text">Current Value (Depreciated):</span>
                                                <span class="fw-bold text-primary">@currency($asset->current_value)</span>
                                            </div>
                                            <div class="col-12">
                                                <span class="sub-text">Salvage Value:</span>
                                                <span>@currency($asset->salvage_value)</span>
                                            </div>
                                            <div class="col-12">
                                                <span class="sub-text">Depreciation Method:</span>
                                                <span class="text-capitalize">{{ str_replace('_', ' ', $asset->depreciation_method) }}</span>
                                                <span class="small text-soft">({{ $asset->useful_life_years }} Years)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Tabs -->
                        <div class="col-lg-8 col-xl-8 col-xxl-9">
                            <div class="card card-bordered">
                                <ul class="nav nav-tabs nav-tabs-mb-icon nav-tabs-card">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#specs"><em class="icon ni ni-file-text"></em><span>Specifications</span></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#lifecycle"><em class="icon ni ni-repeat"></em><span>Lifecycle Events</span></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#documents"><em class="icon ni ni-folder"></em><span>Document Vault</span></a>
                                    </li>
                                </ul>
                                <div class="tab-content mt-0">
                                    
                                    <!-- Specifications Tab -->
                                    <div class="tab-pane active" id="specs">
                                        <div class="card-inner">
                                            <h5 class="title mb-4">Technical Specifications</h5>
                                            @if($asset->specifications)
                                                <div class="entry">
                                                    {!! nl2br(e($asset->specifications)) !!}
                                                </div>
                                            @else
                                                <p class="text-soft">No specifications provided for this asset.</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Lifecycle Tab -->
                                    <div class="tab-pane" id="lifecycle">
                                        <div class="card-inner">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h5 class="title">Asset History</h5>
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#recordEventModal"><em class="icon ni ni-plus"></em><span>Record Event</span></button>
                                            </div>
                                            
                                            <div class="timeline">
                                                <ul class="timeline-list">
                                                    @foreach($asset->lifecycleEvents as $event)
                                                    <li class="timeline-item">
                                                        <div class="timeline-status bg-{{ $event->event_type == 'Acquisition' ? 'success' : ($event->event_type == 'Maintenance' ? 'warning' : ($event->event_type == 'Revaluation' ? 'info' : 'danger')) }}"></div>
                                                        <div class="timeline-date">{{ $event->event_date->format('d M Y') }} <em class="icon ni ni-alarm-alt"></em></div>
                                                        <div class="timeline-data">
                                                            <h6 class="timeline-title">{{ $event->event_type }}</h6>
                                                            <div class="timeline-des">
                                                                <p>{{ $event->notes }}</p>
                                                                @if($event->value_change > 0 && $event->event_type != 'Acquisition')
                                                                    <span class="badge badge-dim bg-outline-info mt-1">Value Change: @currency($event->value_change)</span>
                                                                @endif
                                                                <span class="time text-soft mt-1 d-block"><em class="icon ni ni-user"></em> Recorded by {{ $event->recordedBy->name ?? 'System' }}</span>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Documents Tab -->
                                    <div class="tab-pane" id="documents">
                                        <div class="card-inner">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h5 class="title">Document Vault</h5>
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal"><em class="icon ni ni-upload"></em><span>Upload Document</span></button>
                                            </div>
                                            
                                            @if($asset->documents && $asset->documents->count() > 0)
                                                <table class="table table-borderless table-responsive">
                                                    <thead>
                                                        <tr class="tb-head">
                                                            <th>Document Title</th>
                                                            <th>Type</th>
                                                            <th>Uploaded By</th>
                                                            <th>Date</th>
                                                            <th class="text-end">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($asset->documents as $doc)
                                                            <tr>
                                                                <td>
                                                                    <div class="user-card">
                                                                        <div class="user-avatar sm bg-light text-primary">
                                                                            <em class="icon ni ni-file-docs"></em>
                                                                        </div>
                                                                        <div class="user-info">
                                                                            <span class="tb-lead">{{ $doc->title }}</span>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td><span class="badge badge-dim bg-secondary">{{ $doc->document_type }}</span></td>
                                                                <td>{{ $doc->uploader->name ?? 'System' }}</td>
                                                                <td>{{ $doc->created_at->format('M d, Y') }}</td>
                                                                <td class="text-end">
                                                                    <ul class="nk-tb-actions gx-1">
                                                                        <li>
                                                                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn btn-trigger btn-icon text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="View/Download">
                                                                                <em class="icon ni ni-download-cloud"></em>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <form action="{{ route('admin.asset-documents.destroy', $doc) }}" method="POST" class="d-inline">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" class="btn btn-trigger btn-icon text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" onclick="return confirm('Are you sure you want to delete this document?')">
                                                                                    <em class="icon ni ni-trash"></em>
                                                                                </button>
                                                                            </form>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <div class="text-center p-4">
                                                    <em class="icon ni ni-folder text-soft" style="font-size: 48px;"></em>
                                                    <p class="text-soft mt-3">No documents attached yet.</p>
                                                </div>
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
</div>

<!-- Modal: Record Lifecycle Event -->
<div class="modal fade" id="recordEventModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Lifecycle Event</h5>
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.assets.lifecycle.store', $asset) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="event_type">Event Type</label>
                        <div class="form-control-wrap">
                            <select class="form-select" id="event_type" name="event_type" required>
                                <option value="Maintenance">Maintenance / Repair</option>
                                <option value="Revaluation">Financial Revaluation</option>
                                <option value="Disposal">Disposal (Sold/Scrapped)</option>
                                <option value="Write-off">Write-off (Lost/Destroyed)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="event_date">Event Date</label>
                        <div class="form-control-wrap">
                            <input type="date" class="form-control" id="event_date" name="event_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="value_change">New Value Baseline (@currencySymbol) <span class="small text-soft">(Only for Revaluation)</span></label>
                        <div class="form-control-wrap">
                            <input type="number" step="0.01" class="form-control" id="value_change" name="value_change" placeholder="e.g. 5000.00">
                        </div>
                        <div class="form-note">Leave blank if not revaluing the asset.</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="notes">Notes / Reason</label>
                        <div class="form-control-wrap">
                            <textarea class="form-control" id="notes" name="notes" rows="3" required placeholder="Describe the event..."></textarea>
                        </div>
                    </div>
                    <div class="form-group mb-0 text-end">
                        <button type="submit" class="btn btn-primary">Save Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Upload Document -->
<div class="modal fade" id="uploadDocumentModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Asset Document</h5>
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.assets.documents.store', $asset) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="document_type">Document Type <span class="text-danger">*</span></label>
                        <div class="form-control-wrap">
                            <select class="form-select" id="document_type" name="document_type" required>
                                <option value="">Select Type...</option>
                                <option value="Warranty">Warranty</option>
                                <option value="Insurance Certificate">Insurance Certificate</option>
                                <option value="User Manual">User Manual</option>
                                <option value="Inspection Report">Inspection Report</option>
                                <option value="Invoice / Receipt">Invoice / Receipt</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="title">Document Title <span class="text-danger">*</span></label>
                        <div class="form-control-wrap">
                            <input type="text" class="form-control" id="title" name="title" required placeholder="e.g., 2024 Dell Warranty">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="document_file">Upload File <span class="text-danger">*</span></label>
                        <div class="form-control-wrap">
                            <div class="form-file">
                                <input type="file" class="form-file-input" id="document_file" name="document_file" required>
                                <label class="form-file-label" for="document_file">Choose file</label>
                            </div>
                        </div>
                        <div class="form-note">Max file size: 10MB.</div>
                    </div>
                    <div class="form-group mb-0 text-end">
                        <button type="submit" class="btn btn-primary">Upload Document</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
