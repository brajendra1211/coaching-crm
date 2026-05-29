@extends('admin.layouts.app')

@section('title', 'Add Location SEO Page')
@section('page_title', 'Add Location SEO Page')

@section('content')

<div class="card">
    <form method="POST" action="{{ route('admin.locations.store') }}">
        @csrf

        <div class="form-grid-2">
            <div class="form-group">
                <label>Location Name *</label>
                <input type="text" name="location_name" value="{{ old('location_name') }}" placeholder="Greater Noida" required>
            </div>

            <div class="form-group">
                <label>Focus Keyword</label>
                <input type="text" name="focus_keyword" value="{{ old('focus_keyword') }}" placeholder="NEET Coaching">
            </div>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label>Page Title *</label>
                <input type="text" name="page_title" value="{{ old('page_title') }}" placeholder="Best NEET Coaching in Greater Noida" required>
            </div>

            <div class="form-group">
                <label>URL Slug</label>
                <input type="text" name="slug" value="{{ old('slug') }}" placeholder="neet-coaching-in-greater-noida">
            </div>
        </div>

        <div class="form-group">
            <label>Short Description</label>
            <textarea name="short_description" placeholder="Short page intro for hero section">{{ old('short_description') }}</textarea>
        </div>

        <div class="form-group">
            <label>Page Content</label>
            <textarea name="content" style="min-height:180px;" placeholder="Write local SEO content for this location page">{{ old('content') }}</textarea>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label>CTA Title</label>
                <input type="text" name="cta_title" value="{{ old('cta_title') }}" placeholder="Book Free Counselling">
            </div>

            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}">
            </div>
        </div>

        <div class="form-group">
            <label>CTA Description</label>
            <textarea name="cta_description" placeholder="CTA text for enquiry section">{{ old('cta_description') }}</textarea>
        </div>

        <hr style="border:0;border-top:1px solid #e5e7eb;margin:24px 0;">

        <h3>SEO Settings</h3>

        <div class="form-group">
            <label>SEO Title</label>
            <input type="text" name="seo_title" value="{{ old('seo_title') }}" placeholder="Best NEET Coaching in Greater Noida">
        </div>

        <div class="form-group">
            <label>SEO Meta Description</label>
            <textarea name="seo_description" placeholder="Meta description for search result">{{ old('seo_description') }}</textarea>
        </div>

        <div class="form-group">
            <label>SEO Keywords</label>
            <textarea name="seo_keywords" placeholder="neet coaching greater noida, best coaching near me">{{ old('seo_keywords') }}</textarea>
        </div>

        <label style="display:flex;gap:8px;align-items:center;margin:18px 0;">
            <input type="checkbox" name="status" value="active" checked style="width:auto;">
            Active
        </label>

        <button type="submit" class="btn btn-primary">Save Location Page</button>
        <a href="{{ route('admin.locations.index') }}" class="btn btn-light">Back</a>
    </form>
</div>

@endsection