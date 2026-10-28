@extends('layouts.admin')
@section('title', 'Asset Registry')
@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Asset Registry</h3>
                            <div class="nk-block-des text-soft">
                                <p>You have total {{ $assets->total() }} assets.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <div class="toggle-wrap nk-block-tools-toggle">
                                <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                                <div class="toggle-expand-content" data-content="pageMenu">
                                    <ul class="nk-block-tools g-3">
                                        <li>
                                            <div class="drodown">
                                                <a href="#" class="dropdown-toggle btn btn-white btn-dim btn-outline-light" data-bs-toggle="dropdown"><em class="d-none d-sm-inline icon ni ni-filter-alt"></em><span>Filtered By</span><em class="dd-indc icon ni ni-chevron-right"></em></a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <ul class="link-list-opt no-bdr">
                                                        <li><a href="{{ route('admin.assets.index') }}"><span>All Assets</span></a></li>
                                                        <li><a href="{{ route('admin.assets.index', ['status' => 'Active']) }}"><span>Active</span></a></li>
                                                        <li><a href="{{ route('admin.assets.index', ['status' => 'Maintenance']) }}"><span>In Maintenance</span></a></li>
                                                        <li><a href="{{ route('admin.assets.index', ['status' => 'Disposed']) }}"><span>Disposed</span></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="nk-block-tools-opt">
                                            <a href="{{ route('admin.assets.create') }}" class="btn btn-primary">
                                                <em class="icon ni ni-plus"></em><span>Register Asset</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="nk-block">
                    <div class="card card-bordered card-stretch">
                        <div class="card-inner-group">
                            <div class="card-inner p-0">
                                <table class="nk-tb-list nk-tb-ulist">
                                    <thead>
                                        <tr class="nk-tb-item nk-tb-head">
                                            <th class="nk-tb-col"><span class="sub-text">Asset Tag</span></th>
                                            <th class="nk-tb-col"><span class="sub-text">Name & Category</span></th>
                                            <th class="nk-tb-col tb-col-md"><span class="sub-text">Location</span></th>
                                            <th class="nk-tb-col tb-col-lg"><span class="sub-text">Custodian</span></th>
                                            <th class="nk-tb-col tb-col-lg"><span class="sub-text">Current Value</span></th>
                                            <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                                            <th class="nk-tb-col nk-tb-col-tools text-end"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assets as $asset)
                                        <tr class="nk-tb-item">
                                            <td class="nk-tb-col">
                                                <a href="{{ route('admin.assets.show', $asset) }}" class="fw-bold">{{ $asset->asset_tag }}</a>
                                            </td>
                                            <td class="nk-tb-col">
                                                <span class="tb-lead">{{ $asset->name }}</span>
                                                <span class="sub-text">{{ $asset->category->name ?? 'N/A' }}</span>
                                            </td>
                                            <td class="nk-tb-col tb-col-md">
                                                <span>{{ $asset->location->name ?? 'N/A' }}</span>
                                            </td>
                                            <td class="nk-tb-col tb-col-lg">
                                                <span>{{ $asset->custodian->name ?? 'Unassigned' }}</span>
                                            </td>
                                            <td class="nk-tb-col tb-col-lg">
                                                <span>@currency($asset->current_value)</span>
                                            </td>
                                            <td class="nk-tb-col">
                                                @if($asset->status === 'Active')
                                                    <span class="badge badge-dot bg-success">Active</span>
                                                @elseif($asset->status === 'Maintenance')
                                                    <span class="badge badge-dot bg-warning">Maintenance</span>
                                                @elseif($asset->status === 'Disposed')
                                                    <span class="badge badge-dot bg-secondary">Disposed</span>
                                                @else
                                                    <span class="badge badge-dot bg-danger">Written-Off</span>
                                                @endif
                                            </td>
                                            <td class="nk-tb-col nk-tb-col-tools">
                                                <ul class="nk-tb-actions gx-1">
                                                    <li>
                                                        <div class="drodown">
                                                            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <ul class="link-list-opt no-bdr">
                                                                    <li><a href="{{ route('admin.assets.show', $asset) }}"><em class="icon ni ni-eye"></em><span>View Details</span></a></li>
                                                                    <li><a href="{{ route('admin.assets.edit', $asset) }}"><em class="icon ni ni-edit"></em><span>Edit</span></a></li>
                                                                    <li>
                                                                        <form action="{{ route('admin.assets.destroy', $asset) }}" method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <a href="#" onclick="event.preventDefault(); this.closest('form').submit();" class="text-danger"><em class="icon ni ni-trash"></em><span>Delete</span></a>
                                                                        </form>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-inner">
                                {{ $assets->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
