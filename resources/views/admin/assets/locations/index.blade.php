@extends('layouts.admin')
@section('title', 'Asset Locations')
@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Asset Locations</h3>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('admin.asset-locations.create') }}" class="btn btn-primary"><em class="icon ni ni-plus"></em><span>Add Location</span></a>
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
                                            <th class="nk-tb-col"><span class="sub-text">Name</span></th>
                                            <th class="nk-tb-col tb-col-md"><span class="sub-text">Address</span></th>
                                            <th class="nk-tb-col tb-col-md"><span class="sub-text">Assets Count</span></th>
                                            <th class="nk-tb-col nk-tb-col-tools text-end"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($locations as $location)
                                        <tr class="nk-tb-item">
                                            <td class="nk-tb-col">
                                                <span class="tb-lead">{{ $location->name }}</span>
                                            </td>
                                            <td class="nk-tb-col tb-col-md">
                                                <span>{{ Str::limit($location->address, 50) }}</span>
                                            </td>
                                            <td class="nk-tb-col tb-col-md">
                                                <span class="badge badge-dim bg-outline-primary">{{ $location->assets_count }}</span>
                                            </td>
                                            <td class="nk-tb-col nk-tb-col-tools">
                                                <ul class="nk-tb-actions gx-1">
                                                    <li>
                                                        <div class="drodown">
                                                            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <ul class="link-list-opt no-bdr">
                                                                    <li><a href="{{ route('admin.asset-locations.edit', $location) }}"><em class="icon ni ni-edit"></em><span>Edit</span></a></li>
                                                                    <li>
                                                                        <form action="{{ route('admin.asset-locations.destroy', $location) }}" method="POST">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
