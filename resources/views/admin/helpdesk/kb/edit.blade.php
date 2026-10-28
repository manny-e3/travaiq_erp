@extends('layouts.admin')

@section('title', 'Edit KB Article')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <a href="{{ route('admin.kb-articles.index') }}" class="back-to"><em class="icon ni ni-arrow-left"></em><span>Back to Articles</span></a>
                            <h3 class="nk-block-title page-title">Edit Article: {{ $kbArticle->title }}</h3>
                        </div>
                    </div>
                </div>

                <div class="card card-bordered">
                    <div class="card-inner">
                        <form action="{{ route('admin.kb-articles.update', $kbArticle) }}" method="POST" class="gy-4">
                            @csrf
                            @method('PUT')
                            
                            <div class="row g-gs">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" for="title">Article Title</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $kbArticle->title) }}" required>
                                        </div>
                                        @error('title')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" for="slug">URL Slug (Unique)</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $kbArticle->slug) }}">
                                            <span class="sub-text text-soft mt-1">Leave empty to auto-regenerate from title. E.g. how-to-reset-password</span>
                                        </div>
                                        @error('slug')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="category">Category</label>
                                        <div class="form-control-wrap">
                                            <select class="form-select form-control" id="category" name="category" required>
                                                <option value="General" {{ old('category', $kbArticle->category) === 'General' ? 'selected' : '' }}>General</option>
                                                <option value="Technical" {{ old('category', $kbArticle->category) === 'Technical' ? 'selected' : '' }}>Technical</option>
                                                <option value="Billing" {{ old('category', $kbArticle->category) === 'Billing' ? 'selected' : '' }}>Billing</option>
                                                <option value="Account" {{ old('category', $kbArticle->category) === 'Account' ? 'selected' : '' }}>Account</option>
                                                <option value="Feedback" {{ old('category', $kbArticle->category) === 'Feedback' ? 'selected' : '' }}>Feedback</option>
                                            </select>
                                        </div>
                                        @error('category')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="is_published">Status</label>
                                        <div class="form-control-wrap">
                                            <select class="form-select form-control" id="is_published" name="is_published" required>
                                                <option value="1" {{ old('is_published', $kbArticle->is_published ? '1' : '0') === '1' ? 'selected' : '' }}>Published</option>
                                                <option value="0" {{ old('is_published', $kbArticle->is_published ? '1' : '0') === '0' ? 'selected' : '' }}>Draft</option>
                                            </select>
                                        </div>
                                        @error('is_published')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" for="tags_input">Tags (Comma-separated)</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="tags_input" name="tags_input" value="{{ old('tags_input', is_array($kbArticle->tags) ? implode(', ', $kbArticle->tags) : '') }}">
                                            <span class="sub-text text-soft mt-1">Provide keywords separated by commas to improve article searchability.</span>
                                        </div>
                                        @error('tags_input')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" for="content">Article Body Content</label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control no-resize summernote-basic" id="content" name="content" rows="10" required>{{ old('content', $kbArticle->content) }}</textarea>
                                        </div>
                                        @error('content')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group text-end">
                                        <button type="submit" class="btn btn-primary">Update Article</button>
                                        <a href="{{ route('admin.kb-articles.index') }}" class="btn btn-outline-light">Cancel</a>
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

@push('scripts')
    <link rel="stylesheet" href="{{ asset('assets/css/editors/summernote.css') }}">
    <script src="{{ asset('assets/js/libs/editors/summernote.js') }}"></script>
    <script src="{{ asset('assets/js/editors.js') }}"></script>
@endpush
