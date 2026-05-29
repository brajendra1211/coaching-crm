@extends('portal.layouts.app', ['portalRole' => 'teacher'])

@section('title', 'Exam Builder')
@section('page_title', 'Exam Builder')
@section('page_subtitle', $exam->title)

@section('content')
<style>
    .builder-grid { display:grid; grid-template-columns:310px minmax(0,1fr); gap:18px; align-items:start; }
    .builder-panel { background:#fff; border:1px solid #e5e7eb; border-radius:18px; padding:16px; }
    .question-row { display:grid; grid-template-columns:auto minmax(0,1fr) 96px; gap:12px; padding:12px; border:1px solid #e5e7eb; border-radius:14px; margin-bottom:10px; align-items:start; }
    .question-row:hover { background:#f8fafc; }
    .question-row input[type="checkbox"] { width:auto; margin-top:5px; }
    .question-row input[type="number"] { padding:9px 10px; border-radius:10px; width:100%; }
    .meta { display:flex; gap:7px; flex-wrap:wrap; margin-top:7px; }
    .tag { display:inline-flex; padding:5px 8px; border-radius:999px; background:#eff6ff; color:#1d4ed8; font-size:12px; font-weight:900; }
    @media(max-width:900px){ .builder-grid{grid-template-columns:1fr;} }
</style>
@php $selected = $exam->questions->pluck('pivot.marks', 'id')->toArray(); @endphp
<div class="card">
    <div class="card-header" style="display:flex;justify-content:space-between;gap:14px;align-items:center;flex-wrap:wrap;">
        <div><h3>{{ $exam->title }}</h3><p class="muted" style="margin:6px 0 0;">Reusable question bank se paper build karein.</p></div>
        <a href="{{ route('teacher.exams.index') }}" class="btn btn-light">Back</a>
    </div>
    @if(session('success'))<div class="card" style="margin-bottom:15px;background:#dcfce7;color:#166534;border-color:#bbf7d0;">{{ session('success') }}</div>@endif
    <form method="POST" action="{{ route('teacher.exams.builder.update', $exam) }}">
        @csrf @method('PUT')
        <div class="builder-grid">
            <aside class="builder-panel">
                <strong>Exam Summary</strong>
                <div class="muted" style="font-size:14px;line-height:1.7;margin-top:8px;">
                    <div>Label: {{ $exam->label ?: ucfirst($exam->difficulty) }}</div>
                    <div>Access: {{ ucfirst($exam->access_type) }}</div>
                    <div>Duration: {{ $exam->duration_minutes }} minutes</div>
                    <div>Current marks: {{ $exam->total_marks }}</div>
                </div>
                <hr style="border:0;border-top:1px solid #e5e7eb;margin:14px 0;">
                <strong>Auto Select Mix</strong>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:10px;">
                    <input type="number" min="0" name="auto_easy" placeholder="Easy">
                    <input type="number" min="0" name="auto_medium" placeholder="Medium">
                    <input type="number" min="0" name="auto_hard" placeholder="Hard">
                    <input type="number" min="0" name="auto_advanced" placeholder="Advanced">
                </div>
                <input type="number" step="0.01" min="0" name="auto_marks" placeholder="Marks per question" style="width:100%;margin-top:8px;border:1px solid #cbd5e1;border-radius:12px;padding:10px;">
                <input name="auto_label" placeholder="Optional label filter" style="width:100%;margin-top:8px;border:1px solid #cbd5e1;border-radius:12px;padding:10px;">
                <select name="auto_subject_id" style="width:100%;margin-top:8px;border:1px solid #cbd5e1;border-radius:12px;padding:10px;">
                    <option value="">Any Subject</option>
                    @foreach(\App\Models\Subject::orderBy('name')->get() as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-light" name="auto_generate" value="1" type="submit" style="width:100%;margin-top:10px;">Auto Select Questions</button>
                <button class="btn btn-primary" type="submit" style="width:100%;margin-top:14px;">Save Builder</button>
            </aside>
            <section>
                @forelse($questions as $question)
                    <label class="question-row">
                        <input type="checkbox" name="question_ids[]" value="{{ $question->id }}" {{ array_key_exists($question->id, $selected) ? 'checked' : '' }}>
                        <span>
                            <strong>{!! \Illuminate\Support\Str::limit(strip_tags($question->question), 150) !!}</strong>
                            <span class="meta">
                                <span class="tag">{{ $question->subject->name ?? 'General' }}</span>
                                <span class="tag">{{ $question->label ?: ucfirst($question->difficulty) }}</span>
                                <span class="tag">{{ $question->topic ?: 'No Topic' }}</span>
                                <span class="tag">{{ strtoupper($question->question_type) }}</span>
                            </span>
                        </span>
                        <input type="number" step="0.01" min="0" name="question_marks[{{ $question->id }}]" value="{{ $selected[$question->id] ?? $question->marks }}">
                    </label>
                @empty
                    <div style="text-align:center;padding:35px;color:#64748b;">No active questions found.</div>
                @endforelse
            </section>
        </div>
    </form>
</div>
@endsection
