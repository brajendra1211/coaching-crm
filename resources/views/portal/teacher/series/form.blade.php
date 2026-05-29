@extends('portal.layouts.app', ['portalRole' => 'teacher'])

@section('title', $examSeries->exists ? 'Edit Series' : 'Create Series')
@section('page_title', $examSeries->exists ? 'Edit Series' : 'Create Series')
@section('page_subtitle', 'Create reusable free or paid exam series')

@section('content')
<style>
    .series-form { max-width:980px; }
    .form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; }
    .form-group { margin-bottom:18px; }
    .form-group label { display:block; font-weight:900; color:#334155; margin-bottom:8px; }
    input, select, textarea { width:100%; border:1px solid #cbd5e1; border-radius:14px; padding:12px 13px; font-size:14px; outline:none; font-family:inherit; }
    textarea { min-height:110px; resize:vertical; }
    @media(max-width:800px){ .form-grid{grid-template-columns:1fr;} }
</style>
@if($errors->any())
    <div class="card" style="margin-bottom:18px;background:#fef2f2;color:#991b1b;border-color:#fecaca;"><strong>Please fix errors:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
<form method="POST" action="{{ $examSeries->exists ? route('teacher.series.update', $examSeries) : route('teacher.series.store') }}" class="series-form">
    @csrf
    @if($examSeries->exists) @method('PUT') @endif
    <div class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;gap:14px;align-items:center;flex-wrap:wrap;"><h3>{{ $examSeries->exists ? 'Edit Series' : 'Create Series' }}</h3><a href="{{ route('teacher.series.index') }}" class="btn btn-light">Back</a></div>
        <div class="form-grid">
            <div class="form-group"><label>Series Title</label><input name="title" value="{{ old('title', $examSeries->title) }}" required></div>
            <div class="form-group"><label>Series Code</label><input name="series_code" value="{{ old('series_code', $examSeries->series_code) }}"></div>
            <div class="form-group"><label>Batch</label><select name="batch_id"><option value="">All Assigned</option>@foreach($batches as $batch)<option value="{{ $batch->id }}" {{ old('batch_id', $examSeries->batch_id)==$batch->id?'selected':'' }}>{{ $batch->name }}</option>@endforeach</select></div>
            <div class="form-group"><label>Series Label</label><input name="label" value="{{ old('label', $examSeries->label) }}" placeholder="Foundation, Advanced, Strong Text Book"></div>
            <div class="form-group"><label>Difficulty</label><select name="difficulty" required>@foreach(['easy'=>'Easy','medium'=>'Medium','hard'=>'Hard','advanced'=>'Advanced'] as $value=>$label)<option value="{{ $value }}" {{ old('difficulty', $examSeries->difficulty)==$value?'selected':'' }}>{{ $label }}</option>@endforeach</select></div>
            <div class="form-group"><label>Access</label><select name="access_type" required><option value="free" {{ old('access_type', $examSeries->access_type)=='free'?'selected':'' }}>Free</option><option value="paid" {{ old('access_type', $examSeries->access_type)=='paid'?'selected':'' }}>Paid</option></select></div>
            <div class="form-group"><label>Price</label><input type="number" step="0.01" min="0" name="price" value="{{ old('price', $examSeries->price) }}"></div>
            <div class="form-group"><label>Start Date</label><input type="date" name="start_date" value="{{ old('start_date', $examSeries->start_date ? $examSeries->start_date->format('Y-m-d') : '') }}"></div>
            <div class="form-group"><label>End Date</label><input type="date" name="end_date" value="{{ old('end_date', $examSeries->end_date ? $examSeries->end_date->format('Y-m-d') : '') }}"></div>
            <div class="form-group"><label>Status</label><select name="status" required>@foreach(['draft'=>'Draft','published'=>'Published','archived'=>'Archived'] as $value=>$label)<option value="{{ $value }}" {{ old('status', $examSeries->status)==$value?'selected':'' }}>{{ $label }}</option>@endforeach</select></div>
            <div class="form-group" style="grid-column:1/-1;"><label>Description</label><textarea name="description">{{ old('description', $examSeries->description) }}</textarea></div>
            <div class="form-group" style="grid-column:1/-1;"><label>Instructions</label><textarea name="instructions">{{ old('instructions', $examSeries->instructions) }}</textarea></div>
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;"><button class="btn btn-primary" type="submit">{{ $examSeries->exists ? 'Update Series' : 'Save Series' }}</button>@if($examSeries->exists)<a href="{{ route('teacher.series.builder', $examSeries) }}" class="btn btn-light">Open Builder</a>@endif<a href="{{ route('teacher.series.index') }}" class="btn btn-light">Cancel</a></div>
    </div>
</form>
@endsection
