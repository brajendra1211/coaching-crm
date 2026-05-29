@extends('portal.layouts.app', ['portalRole' => 'teacher'])

@section('title', 'Add Study Material')
@section('page_title', 'Add Study Material')
@section('page_subtitle', 'Share notes, PDFs, links and assignments for assigned classes or batches')

@section('content')
<style>
    .teacher-form { max-width:920px; }
    .form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; }
    .form-group { margin-bottom:18px; }
    .form-group label { display:block; font-weight:900; color:#334155; margin-bottom:8px; }
    input, select, textarea { width:100%; border:1px solid #cbd5e1; border-radius:14px; padding:12px 13px; font-size:14px; outline:none; font-family:inherit; }
    textarea { min-height:115px; resize:vertical; }
    input:focus, select:focus, textarea:focus { border-color:#2563eb; box-shadow:0 0 0 4px rgba(37,99,235,.10); }
    @media(max-width:800px){ .form-grid{grid-template-columns:1fr;} }
</style>

@if(!$teacher)
    <div class="card"><h3>Profile Not Linked</h3><p class="muted" style="margin:0;">Ask admin to link this user with a teacher profile.</p></div>
@else
    @if($errors->any())
        <div class="card" style="margin-bottom:18px;background:#fef2f2;color:#991b1b;border-color:#fecaca;">
            <strong>Please fix errors:</strong>
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('teacher.materials.store') }}" enctype="multipart/form-data" class="teacher-form">
        @csrf
        <div class="card">
            <div class="card-header" style="display:flex;justify-content:space-between;gap:14px;align-items:center;flex-wrap:wrap;">
                <h3>Material Details</h3>
                <a href="{{ route('teacher.materials.index') }}" class="btn btn-light">Back</a>
            </div>

            <div class="form-grid">
                <div class="form-group" style="grid-column:1/-1;">
                    <label>Title</label>
                    <input name="title" value="{{ old('title') }}" required placeholder="Chapter notes, assignment, worksheet">
                </div>

                <div class="form-group">
                    <label>Type</label>
                    <select name="type">
                        <option value="">Select Type</option>
                        @foreach(['PDF','Notes','Video','Assignment','Question Paper','Other'] as $type)
                            <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Class / Level</label>
                    <select name="class_level">
                        <option value="">Select Class</option>
                        @foreach($classLevels as $level)
                            <option value="{{ $level }}" {{ old('class_level') == $level ? 'selected' : '' }}>{{ $level }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Assigned Batch</label>
                    <select name="batch_id">
                        <option value="">Class-wide / No specific batch</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" {{ old('batch_id') == $batch->id ? 'selected' : '' }}>
                                {{ $batch->name }}{{ $batch->class_level ? ' - ' . $batch->class_level : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Subject</label>
                    <select name="subject_id">
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Upload File</label>
                    <input type="file" name="file_path">
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" required>
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="form-group" style="grid-column:1/-1;">
                    <label>External Link / YouTube Link</label>
                    <textarea name="external_link" placeholder="Paste video, drive or meeting resource link">{{ old('external_link') }}</textarea>
                </div>

                <div class="form-group" style="grid-column:1/-1;">
                    <label>Description</label>
                    <textarea name="description" placeholder="Short instruction for students">{{ old('description') }}</textarea>
                </div>
            </div>

            <div style="display:flex;gap:12px;flex-wrap:wrap;">
                <button type="submit" class="btn btn-primary">Save Material</button>
                <a href="{{ route('teacher.materials.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </div>
    </form>
@endif
@endsection
