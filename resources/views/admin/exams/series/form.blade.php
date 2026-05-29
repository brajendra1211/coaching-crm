@extends('admin.layouts.app')

@section('title', $examSeries->exists ? 'Edit Exam Series' : 'Add Exam Series')
@section('page_title', $examSeries->exists ? 'Edit Exam Series' : 'Add Exam Series')

@section('content')
<style>
    .series-form { max-width:980px; }
    .grid-2 { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; }
    .form-group { margin-bottom:18px; }
    .form-group label { display:block; font-weight:900; color:#334155; margin-bottom:8px; }
    .form-group textarea { min-height:110px; resize:vertical; }
    @media(max-width:800px){ .grid-2{grid-template-columns:1fr;} }
</style>

@if($errors->any())
    <div style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;padding:14px;border-radius:14px;margin-bottom:18px;">
        <strong>Please fix errors:</strong>
        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ $examSeries->exists ? route('admin.exam-series.update', $examSeries) : route('admin.exam-series.store') }}" class="series-form">
    @csrf
    @if($examSeries->exists) @method('PUT') @endif

    <div class="card">
        <div class="card-header"><h3>{{ $examSeries->exists ? 'Edit Exam Series' : 'Add Exam Series' }}</h3></div>
        <div class="grid-2">
            <div class="form-group"><label>Series Title</label><input name="title" value="{{ old('title', $examSeries->title) }}" required></div>
            <div class="form-group"><label>Series Code</label><input name="series_code" value="{{ old('series_code', $examSeries->series_code) }}"></div>
            <div class="form-group">
                <label>Batch</label>
                <select name="batch_id"><option value="">All Batches</option>@foreach($batches as $batch)<option value="{{ $batch->id }}" {{ old('batch_id', $examSeries->batch_id) == $batch->id ? 'selected' : '' }}>{{ $batch->name }}</option>@endforeach</select>
            </div>
            <div class="form-group"><label>Series Label</label><input name="label" value="{{ old('label', $examSeries->label) }}" placeholder="Strong Text Book, Foundation, Advanced"></div>
            <div class="form-group">
                <label>Difficulty</label>
                <select name="difficulty" required>
                    @foreach(['easy' => 'Easy', 'medium' => 'Medium', 'hard' => 'Hard', 'advanced' => 'Advanced'] as $value => $label)
                        <option value="{{ $value }}" {{ old('difficulty', $examSeries->difficulty) == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Access</label>
                <select name="access_type" required>
                    <option value="free" {{ old('access_type', $examSeries->access_type) == 'free' ? 'selected' : '' }}>Free</option>
                    <option value="paid" {{ old('access_type', $examSeries->access_type) == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div class="form-group"><label>Price</label><input type="number" step="0.01" min="0" name="price" value="{{ old('price', $examSeries->price) }}"></div>
            <div class="form-group"><label>Start Date</label><input type="date" name="start_date" value="{{ old('start_date', $examSeries->start_date ? $examSeries->start_date->format('Y-m-d') : '') }}"></div>
            <div class="form-group"><label>End Date</label><input type="date" name="end_date" value="{{ old('end_date', $examSeries->end_date ? $examSeries->end_date->format('Y-m-d') : '') }}"></div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    @foreach(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'] as $value => $label)
                        <option value="{{ $value }}" {{ old('status', $examSeries->status) == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="grid-column:1/-1;"><label>Description</label><textarea name="description">{{ old('description', $examSeries->description) }}</textarea></div>
            <div class="form-group" style="grid-column:1/-1;"><label>Instructions</label><textarea name="instructions">{{ old('instructions', $examSeries->instructions) }}</textarea></div>
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button class="btn btn-primary" type="submit">{{ $examSeries->exists ? 'Update' : 'Save' }}</button>
            @if($examSeries->exists)
                <a href="{{ route('admin.exam-series.builder', $examSeries) }}" class="btn btn-dark">Open Builder</a>
            @endif
            <a href="{{ route('admin.exam-series.index') }}" class="btn btn-light">Cancel</a>
        </div>
    </div>
</form>
@endsection
