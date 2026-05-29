@extends('portal.layouts.app', ['portalRole' => 'teacher'])

@section('title', 'Import Questions')
@section('page_title', 'Import Questions')
@section('page_subtitle', 'Upload 100+ questions at once from Excel or CSV')

@section('content')
<style>
    .import-form { max-width:920px; }
    input[type="file"] { width:100%; border:1px solid #cbd5e1; border-radius:14px; padding:12px 13px; font-size:14px; background:#fff; }
    .sample-table td, .sample-table th { font-size:12px; white-space:nowrap; }
</style>

@if($errors->any())
    <div class="card" style="margin-bottom:18px;background:#fef2f2;color:#991b1b;border-color:#fecaca;">{{ $errors->first() }}</div>
@endif

<section class="card import-form" style="margin-bottom:18px;">
    <div class="card-header" style="display:flex;justify-content:space-between;gap:14px;align-items:center;flex-wrap:wrap;">
        <div>
            <h3>Bulk Upload</h3>
            <p class="muted" style="margin:6px 0 0;">Upload `.xlsx` or `.csv`. First row must be column headers.</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('teacher.questions.import.sample') }}" class="btn btn-primary">Download Sample</a>
            <a href="{{ route('teacher.questions.index') }}" class="btn btn-light">Back</a>
        </div>
    </div>
    <form method="POST" action="{{ route('teacher.questions.import.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="question_file" accept=".xlsx,.csv,.txt" required>
        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:16px;">
            <button class="btn btn-primary" type="submit">Upload Questions</button>
            <a href="{{ route('teacher.questions.import.sample') }}" class="btn btn-light">Sample CSV</a>
            <a href="{{ route('teacher.questions.create') }}" class="btn btn-light">Add Manually</a>
        </div>
    </form>
</section>

<section class="card">
    <h3>Excel Columns</h3>
    <p class="muted">Recommended headers: `question_type`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `marks`, `difficulty`, `label`, `subject`, `class_level`, `topic`, `source`, `tags`, `explanation`, `status`.</p>
    <div class="table-wrap">
        <table class="sample-table">
            <thead><tr><th>question_type</th><th>question</th><th>option_a</th><th>option_b</th><th>option_c</th><th>option_d</th><th>correct_answer</th><th>marks</th><th>difficulty</th><th>label</th><th>subject</th></tr></thead>
            <tbody>
                <tr><td>mcq</td><td>2 + 2 = ?</td><td>3</td><td>4</td><td>5</td><td>6</td><td>B</td><td>1</td><td>easy</td><td>Arithmetic Easy</td><td>Math</td></tr>
                <tr><td>numeric</td><td>Square root of 49</td><td></td><td></td><td></td><td></td><td>7</td><td>1</td><td>medium</td><td>Concept</td><td>Math</td></tr>
            </tbody>
        </table>
    </div>
</section>
@endsection
