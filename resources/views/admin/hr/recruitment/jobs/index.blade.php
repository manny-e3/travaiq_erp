@extends('layouts.admin')

@section('title', 'Job Postings')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Job Postings</h3>
                            <div class="nk-block-des text-soft">
                                <p>Manage active vacancies and track applicants.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('admin.hr.recruitment.jobs.create') }}" class="btn btn-primary"><em class="icon ni ni-plus"></em><span>Add Job</span></a>
                        </div>
                    </div>
                </div>
                <div class="nk-block">
                    <div class="card card-bordered card-stretch">
                        <div class="card-inner-group">
                            <div class="card-inner p-0">
                                <table class="table table-tranx">
                                    <thead>
                                        <tr class="tb-tnx-head">
                                            <th class="tb-tnx-info">Title</th>
                                            <th class="tb-tnx-info">Department</th>
                                            <th class="tb-tnx-info">Location</th>
                                            <th class="tb-tnx-info">Type</th>
                                            <th class="tb-tnx-info">Status</th>
                                            <th class="tb-tnx-info">Closing Date</th>
                                            <th class="tb-tnx-action"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($postings as $job)
                                        <tr class="tb-tnx-item">
                                            <td class="tb-tnx-info">
                                                <div class="tb-tnx-desc">
                                                    <span class="title"><a href="{{ route('admin.hr.recruitment.jobs.show', $job) }}">{{ $job->title }}</a></span>
                                                </div>
                                            </td>
                                            <td class="tb-tnx-info"><span class="sub-text">{{ $job->department->name ?? 'N/A' }}</span></td>
                                            <td class="tb-tnx-info"><span class="sub-text">{{ $job->location }}</span></td>
                                            <td class="tb-tnx-info"><span class="sub-text">{{ $job->employment_type }}</span></td>
                                            <td class="tb-tnx-info">
                                                <span class="badge badge-dot {{ $job->status === 'Published' ? 'bg-success' : ($job->status === 'Draft' ? 'bg-warning' : 'bg-danger') }}">{{ $job->status }}</span>
                                            </td>
                                            <td class="tb-tnx-info"><span class="sub-text">{{ $job->closing_date ? $job->closing_date->format('M d, Y') : 'Open until filled' }}</span></td>
                                            <td class="tb-tnx-action">
                                                <div class="dropdown">
                                                    <a class="text-soft dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                                        <ul class="link-list-plain">
                                                            <li><a href="{{ route('admin.hr.recruitment.jobs.show', $job) }}">View Board</a></li>
                                                            <li><a href="{{ route('admin.hr.recruitment.jobs.edit', $job) }}">Edit</a></li>
                                                            <li>
                                                                <form action="{{ route('admin.hr.recruitment.jobs.destroy', $job) }}" method="POST" onsubmit="return confirm('Delete this job posting?')">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="btn btn-link text-danger p-0" style="padding-left: 1rem !important; margin-top:0.2rem; font-size:13px">Delete</button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
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
