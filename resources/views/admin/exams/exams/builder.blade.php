@extends('admin.layouts.app')

@section('title', 'Exam Builder')
@section('page_title', 'Exam Builder')

@section('content')
<style>
    .builder-grid { display:grid; grid-template-columns:310px minmax(0,1fr); gap:18px; align-items:start; }
    .builder-panel { background:#fff; border:1px solid #e5e7eb; border-radius:18px; padding:16px; }
    .question-row { display:grid; grid-template-columns:auto minmax(0,1fr) 96px; gap:12px; padding:12px; border:1px solid #e5e7eb; border-radius:14px; margin-bottom:10px; align-items:start; }
    .question-row:hover { background:#f8fafc; }
    .question-row input[type="checkbox"] { width:auto; margin-top:5px; }
    .question-row input[type="number"] { padding:9px 10px; border-radius:10px; }
    .meta { display:flex; gap:7px; flex-wrap:wrap; margin-top:7px; }
    .tag { display:inline-flex; padding:5px 8px; border-radius:999px; background:#eff6ff; color:#1d4ed8; font-size:12px; font-weight:900; }
    .builder-search { display:grid; gap:10px; position:sticky; top:96px; }
    @media(max-width:900px){ .builder-grid{grid-template-columns:1fr;} .builder-search{position:static;} }
</style>

@php
    $selected = $exam->questions->pluck('pivot.marks', 'id')->toArray();
@endphp

<div class="card">
    <div class="card-header">
        <div>
            <h3>{{ $exam->title }}</h3>
            <p style="margin:6px 0 0;color:#64748b;">Reusable question bank se questions select karke exam paper build karein.</p>
        </div>
        <a href="{{ route('admin.exams.index') }}" class="btn btn-light">Back</a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.exams.builder.update', $exam) }}">
        @csrf
        @method('PUT')

        <div class="builder-grid">
            <aside class="builder-panel builder-search">
                <strong>Exam Summary</strong>
                <div style="color:#64748b;font-size:14px;line-height:1.7;">
                    <div>Label: {{ $exam->label ?: ucfirst($exam->difficulty) }}</div>
                    <div>Access: {{ ucfirst($exam->access_type) }} {{ $exam->access_type === 'paid' ? '(Rs. ' . number_format((float) $exam->price, 2) . ')' : '' }}</div>
                    <div>Duration: {{ $exam->duration_minutes }} minutes</div>
                    <div>Current marks: {{ $exam->total_marks }}</div>
                </div>

                <button class="btn btn-primary" type="submit" style="width:100%;">Save Builder</button>
            </aside>

            <section>
                @forelse($questions as $question)
                    <label class="question-row">
                        <input type="checkbox" name="question_ids[]" value="{{ $question->id }}" {{ array_key_exists($question->id, $selected) ? 'checked' : '' }}>
                        <span>
                            <strong>{{ \Illuminate\Support\Str::limit(strip_tags($question->question), 150) }}</strong>
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
