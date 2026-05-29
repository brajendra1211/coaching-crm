@extends('admin.layouts.app')

@section('title', $exam->exists ? 'Edit Exam' : 'Add Exam')
@section('page_title', $exam->exists ? 'Edit Exam' : 'Add Exam')

@section('content')
<style>
    .exam-form { max-width:1050px; }
    .grid-2 { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; }
    .form-group { margin-bottom:18px; }
    .form-group label { display:block; font-weight:900; color:#334155; margin-bottom:8px; }
    .form-group textarea { min-height:110px; resize:vertical; }
    .question-list { max-height:300px; overflow:auto; border:1px solid #e5e7eb; border-radius:14px; padding:10px; }
    .question-row { display:flex; gap:10px; align-items:flex-start; padding:9px; border-radius:12px; }
    .question-row:hover { background:#f8fafc; }
    .question-row input { width:auto; margin-top:3px; }
    @media(max-width:800px){ .grid-2{grid-template-columns:1fr;} }
</style>

@if($errors->any())
    <div style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;padding:14px;border-radius:14px;margin-bottom:18px;">
        <strong>Please fix errors:</strong>
        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

@php $selectedQuestions = old('question_ids', $exam->questions->pluck('id')->toArray()); @endphp

<form method="POST" action="{{ $exam->exists ? route('admin.exams.update', $exam) : route('admin.exams.store') }}" class="exam-form">
    @csrf
    @if($exam->exists) @method('PUT') @endif

    <div class="card">
        <div class="card-header"><h3>{{ $exam->exists ? 'Edit Exam' : 'Add Exam' }}</h3></div>
        <div class="grid-2">
            <div class="form-group"><label>Exam Title</label><input name="title" value="{{ old('title', $exam->title) }}" required></div>
            <div class="form-group"><label>Exam Code</label><input name="exam_code" value="{{ old('exam_code', $exam->exam_code) }}"></div>
            <div class="form-group">
                <label>Exam Type</label>
                <select name="exam_type" required>
                    @foreach(['single' => 'Single Exam', 'mock' => 'Mock Test', 'practice' => 'Practice Test', 'series_part' => 'Series Part'] as $value => $label)
                        <option value="{{ $value }}" {{ old('exam_type', $exam->exam_type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group"><label>Exam Label</label><input name="label" value="{{ old('label', $exam->label) }}" placeholder="Strong Text Book, Easy, Medium, Hard"></div>
            <div class="form-group">
                <label>Difficulty</label>
                <select name="difficulty" required>
                    @foreach(['easy' => 'Easy', 'medium' => 'Medium', 'hard' => 'Hard', 'advanced' => 'Advanced'] as $value => $label)
                        <option value="{{ $value }}" {{ old('difficulty', $exam->difficulty) == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Access</label>
                <select name="access_type" required>
                    <option value="free" {{ old('access_type', $exam->access_type) == 'free' ? 'selected' : '' }}>Free</option>
                    <option value="paid" {{ old('access_type', $exam->access_type) == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div class="form-group"><label>Price</label><input type="number" step="0.01" min="0" name="price" value="{{ old('price', $exam->price) }}"></div>
            <div class="form-group">
                <label>Batch</label>
                <select name="batch_id"><option value="">Select Batch</option>@foreach($batches as $batch)<option value="{{ $batch->id }}" {{ old('batch_id', $exam->batch_id) == $batch->id ? 'selected' : '' }}>{{ $batch->name }}</option>@endforeach</select>
            </div>
            <div class="form-group">
                <label>Subject</label>
                <select name="subject_id"><option value="">Select Subject</option>@foreach($subjects as $subject)<option value="{{ $subject->id }}" {{ old('subject_id', $exam->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>@endforeach</select>
            </div>
            <div class="form-group"><label>Exam Date</label><input type="date" name="exam_date" value="{{ old('exam_date', $exam->exam_date ? $exam->exam_date->format('Y-m-d') : '') }}"></div>
            <div class="form-group"><label>Duration Minutes</label><input type="number" min="1" name="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" required></div>
            <div class="form-group"><label>Start Time</label><input type="time" name="start_time" value="{{ old('start_time', $exam->start_time) }}"></div>
            <div class="form-group"><label>End Time</label><input type="time" name="end_time" value="{{ old('end_time', $exam->end_time) }}"></div>
            <div class="form-group"><label>Total Marks</label><input type="number" step="0.01" min="0" name="total_marks" value="{{ old('total_marks', $exam->total_marks) }}" required></div>
            <div class="form-group"><label>Passing Marks</label><input type="number" step="0.01" min="0" name="passing_marks" value="{{ old('passing_marks', $exam->passing_marks) }}" required></div>
            <div class="form-group"><label>Negative Marks</label><input type="number" step="0.01" min="0" name="negative_marks" value="{{ old('negative_marks', $exam->negative_marks) }}"></div>
            <div class="form-group"><label>Attempt Limit</label><input type="number" min="1" name="attempt_limit" value="{{ old('attempt_limit', $exam->attempt_limit ?: 1) }}" required></div>
            <div class="form-group">
                <label>Show Result Immediately</label>
                <select name="show_result_immediately">
                    <option value="1" {{ old('show_result_immediately', $exam->show_result_immediately) ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ !old('show_result_immediately', $exam->show_result_immediately) ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    @foreach(['draft' => 'Draft', 'scheduled' => 'Scheduled', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                        <option value="{{ $value }}" {{ old('status', $exam->status) == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="grid-column:1/-1;"><label>Instructions</label><textarea name="instructions">{{ old('instructions', $exam->instructions) }}</textarea></div>
            <div class="form-group" style="grid-column:1/-1;">
                <label>Attach Questions</label>
                <p style="margin:0 0 10px;color:#64748b;font-size:13px;">For advanced filtering and marks control, save the exam and open Builder.</p>
                <div class="question-list">
                    @forelse($questions as $question)
                        <label class="question-row">
                            <input type="checkbox" name="question_ids[]" value="{{ $question->id }}" {{ in_array($question->id, $selectedQuestions) ? 'checked' : '' }}>
                            <span>{{ \Illuminate\Support\Str::limit($question->question, 120) }} <small style="color:#64748b;">({{ $question->marks }} marks)</small></span>
                        </label>
                    @empty
                        <p style="color:#64748b;margin:8px;">No active questions. Add questions first from Question Bank.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button class="btn btn-primary" type="submit">{{ $exam->exists ? 'Update' : 'Save' }}</button>
            <a href="{{ route('admin.exams.index') }}" class="btn btn-light">Cancel</a>
        </div>
    </div>
</form>
@endsection
