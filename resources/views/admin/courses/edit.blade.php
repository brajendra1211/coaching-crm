@extends('admin.layouts.app')

@section('title', 'Edit Course')
@section('page_title', 'Edit Course')

@section('content')

<div class="card">
    <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
            <div>
                <label>Course Title *</label>
                <input type="text" name="title" value="{{ old('title', $course->title) }}" required>
            </div>

            <div>
                <label>Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $course->slug) }}">
            </div>
        </div>

        <br>

        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:18px;">
            <div>
                <label>Course Type</label>
                <input type="text" name="course_type" value="{{ old('course_type', $course->course_type) }}">
            </div>

            <div>
                <label>Class Level</label>
                <input type="text" name="class_level" value="{{ old('class_level', $course->class_level) }}">
            </div>

            <div>
                <label>Duration</label>
                <input type="text" name="duration" value="{{ old('duration', $course->duration) }}">
            </div>
        </div>

        <br>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
            <div>
                <label>Course Fee</label>
                <input type="number" step="0.01" name="fee" value="{{ old('fee', $course->fee) }}">
            </div>

            <div>
                <label>Offer Fee</label>
                <input type="number" step="0.01" name="offer_fee" value="{{ old('offer_fee', $course->offer_fee) }}">
            </div>
        </div>

        <br>

        <div>
            <label>Current Image</label><br>
            @if($course->image)
                <img src="{{ asset('storage/' . $course->image) }}" style="width:160px;height:100px;object-fit:cover;border-radius:12px;">
            @else
                <p>No image uploaded.</p>
            @endif
        </div>

        <br>

        <div>
            <label>Change Image</label>
            <input type="file" name="image" accept="image/*">
        </div>

        <br>

        <div>
            <label>Short Description</label>
            <textarea name="short_description">{{ old('short_description', $course->short_description) }}</textarea>
        </div>

        <br>

        <div>
            <label>Full Description</label>
            <textarea name="description" style="min-height:150px;">{{ old('description', $course->description) }}</textarea>
        </div>

        <br>

        <div>
            <label>Subjects</label>
            <textarea name="subjects">{{ old('subjects', $course->subjects) }}</textarea>
        </div>

        <br>

        <div>
            <label>Eligibility</label>
            <textarea name="eligibility">{{ old('eligibility', $course->eligibility) }}</textarea>
        </div>

        <br>

        <div>
            <label>Features</label>
            <textarea name="features">{{ old('features', $course->features) }}</textarea>
        </div>

        <hr style="border:0;border-top:1px solid #e5e7eb;margin:25px 0;">

        <h3>SEO Settings</h3>

        <div>
            <label>SEO Title</label>
            <input type="text" name="seo_title" value="{{ old('seo_title', $course->seo_title) }}">
        </div>

        <br>

        <div>
            <label>SEO Description</label>
            <textarea name="seo_description">{{ old('seo_description', $course->seo_description) }}</textarea>
        </div>

        <br>

        <div>
            <label>SEO Keywords</label>
            <textarea name="seo_keywords">{{ old('seo_keywords', $course->seo_keywords) }}</textarea>
        </div>

        <br>

        <label style="display:flex;gap:8px;align-items:center;">
            <input type="checkbox" name="status" value="active" {{ $course->status === 'active' ? 'checked' : '' }} style="width:auto;">
            Active
        </label>

        <br>

        <button type="submit" class="btn btn-primary">Update Course</button>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-light">Back</a>
    </form>
</div>

@endsection