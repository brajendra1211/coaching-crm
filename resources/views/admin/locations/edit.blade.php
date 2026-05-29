@extends('admin.layouts.app')

@section('title', 'Edit Location SEO Page')
@section('page_title', 'Edit Location SEO Page')

@section('content')

<div class="card">
    <form method="POST" action="{{ route('admin.locations.update', $location) }}">
        @csrf
        @method('PUT')

        <div class="form-grid-2">
            <div class="form-group">
                <label>Location Name *</label>
                <input type="text" name="location_name" value="{{ old('location_name', $location->location_name) }}" required>
            </div>

            <div class="form-group">
                <label>Focus Keyword</label>
                <input type="text" name="focus_keyword" value="{{ old('focus_keyword', $location->focus_keyword) }}">
            </div>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label>Page Title *</label>
                <input type="text" name="page_title" value="{{ old('page_title', $location->page_title) }}" required>
            </div>

            <div class="form-group">
                <label>URL Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $location->slug) }}">
            </div>
        </div>

        <div class="form-group">
            <label>Short Description</label>
            <textarea name="short_description">{{ old('short_description', $location->short_description) }}</textarea>
        </div>

        <div class="form-group">
            <label>Page Content</label>
            <textarea name="content" style="min-height:180px;">{{ old('content', $location->content) }}</textarea>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label>CTA Title</label>
                <input type="text" name="cta_title" value="{{ old('cta_title', $location->cta_title) }}">
            </div>

            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $location->sort_order) }}">
            </div>
        </div>

        <div class="form-group">
            <label>CTA Description</label>
            <textarea name="cta_description">{{ old('cta_description', $location->cta_description) }}</textarea>
        </div>

        <hr style="border:0;border-top:1px solid #e5e7eb;margin:24px 0;">

        <h3>SEO Settings</h3>

        <div class="form-group">
            <label>SEO Title</label>
            <input type="text" name="seo_title" value="{{ old('seo_title', $location->seo_title) }}">
        </div>

        <div class="form-group">
            <label>SEO Meta Description</label>
            <textarea name="seo_description">{{ old('seo_description', $location->seo_description) }}</textarea>
        </div>

        <div class="form-group">
            <label>SEO Keywords</label>
            <textarea name="seo_keywords">{{ old('seo_keywords', $location->seo_keywords) }}</textarea>
        </div>

        <label style="display:flex;gap:8px;align-items:center;margin:18px 0;">
            <input type="checkbox" name="status" value="active" {{ $location->status === 'active' ? 'checked' : '' }} style="width:auto;">
            Active
        </label>

        <button type="submit" class="btn btn-primary">Update Location Page</button>
        <a href="{{ route('admin.locations.index') }}" class="btn btn-light">Back</a>
    </form>
</div>

@endsection