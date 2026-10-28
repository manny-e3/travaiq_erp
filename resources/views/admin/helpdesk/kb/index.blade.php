@extends('layouts.admin')

@section('title', 'Knowledge Base Manager')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Knowledge Base Articles</h3>
                            <div class="nk-block-des text-soft">
                                <p>Manage self-service FAQs, resolution guides, and how-to articles for customers and staff.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('admin.kb-articles.create') }}" class="btn btn-primary">
                                <em class="icon ni ni-plus"></em><span>Add KB Article</span>
                            </a>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-icon alert-dismissible">
                        <em class="icon ni ni-check-circle"></em> <strong>Success!</strong> {{ session('success') }}
                        <button class="close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card card-bordered card-preview">
                    <div class="card-inner">
                        <table class="table table-tranx">
                            <thead>
                                <tr class="tb-tnx-head">
                                    <th>Article Title</th>
                                    <th>Category</th>
                                    <th>Tags</th>
                                    <th>Views</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th class="tb-tnx-action">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($articles as $article)
                                    <tr class="tb-tnx-item">
                                        <td>
                                            <span class="lead-text font-weight-bold">{{ $article->title }}</span>
                                            <span class="sub-text text-soft">/kb/{{ $article->slug }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-outline-primary">{{ $article->category }}</span>
                                        </td>
                                        <td>
                                            @if(!empty($article->tags))
                                                @foreach($article->tags as $tag)
                                                    <span class="badge bg-light text-dark">#{{ $tag }}</span>
                                                @endforeach
                                            @else
                                                <span class="sub-text text-soft">No tags</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="sub-text font-weight-bold text-dark">{{ $article->views }}</span>
                                        </td>
                                        <td>
                                            @if($article->is_published)
                                                <span class="badge bg-success">Published</span>
                                            @else
                                                <span class="badge bg-light">Draft</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="sub-text">{{ $article->created_at->format('M d, Y') }}</span>
                                        </td>
                                        <td class="tb-tnx-action">
                                            <div class="dropdown">
                                                <a class="text-soft dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <ul class="link-list-opt no-bdr">
                                                        <li>
                                                            <a href="{{ route('admin.kb-articles.edit', $article) }}">
                                                                <em class="icon ni ni-edit"></em><span>Edit Article</span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ route('kb.show', $article->slug) }}" target="_blank">
                                                                <em class="icon ni ni-eye"></em><span>View Public</span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('admin.kb-articles.destroy', $article) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this article?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger border-0 bg-transparent">
                                                                    <em class="icon ni ni-trash"></em><span>Delete Article</span>
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center p-4">No Knowledge Base articles created. Click 'Add KB Article' to create one.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
