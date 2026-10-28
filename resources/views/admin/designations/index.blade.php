@extends('layouts.admin')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Designations</h3>
                            <div class="nk-block-des text-soft">
                                <p>Manage official designation titles assigned to employees.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('admin.designations.create') }}" class="btn btn-primary">
                                <em class="icon ni ni-plus"></em><span>Add Designation</span>
                            </a>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-icon">
                        <em class="icon ni ni-check-circle"></em> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-icon">
                        <em class="icon ni ni-cross-circle"></em> {{ session('error') }}
                    </div>
                @endif

                <div class="nk-block">
                    <div class="card card-bordered card-stretch">
                        <div class="card-inner">
                            <table class="datatable-init nk-tb-list nk-tb-ulist table">
                                <thead>
                                    <tr class="nk-tb-item nk-tb-head">
                                        <th class="nk-tb-col"><span class="sub-text">Name</span></th>
                                        <th class="nk-tb-col"><span class="sub-text">Description</span></th>
                                        <th class="nk-tb-col"><span class="sub-text">Employees Count</span></th>
                                        <th class="nk-tb-col nk-tb-col-tools text-end"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($designations as $desig)
                                    <tr class="nk-tb-item">
                                        <td class="nk-tb-col">
                                            <span class="tb-lead">{{ $desig->name }}</span>
                                        </td>
                                        <td class="nk-tb-col">
                                            <span>{{ Str::limit($desig->description, 75) }}</span>
                                        </td>
                                        <td class="nk-tb-col">
                                            <span class="badge badge-dim bg-outline-primary">{{ $desig->users_count }}</span>
                                        </td>
                                        <td class="nk-tb-col nk-tb-col-tools">
                                            <ul class="nk-tb-actions gx-1">
                                                <li>
                                                    <div class="dropdown">
                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <ul class="link-list-opt no-bdr">
                                                                <li><a href="{{ route('admin.designations.edit', $desig->id) }}"><em class="icon ni ni-edit"></em><span>Edit</span></a></li>
                                                                <li>
                                                                    <form action="{{ route('admin.designations.destroy', $desig->id) }}" method="POST" id="del-{{ $desig->id }}">
                                                                        @csrf @method('DELETE')
                                                                        <a href="#" onclick="event.preventDefault(); if(confirm('Are you sure?')) document.getElementById('del-{{ $desig->id }}').submit();" class="text-danger">
                                                                            <em class="icon ni ni-trash"></em><span>Delete</span>
                                                                        </a>
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
@endsection
