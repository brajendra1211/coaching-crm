@extends('portal.layouts.app', ['portalRole' => 'student'])

@section('title', $exam->title)
@section('page_title', $exam->title)
@section('page_subtitle', 'Read carefully and submit your answers')

@section('content')
@if($errors->any())
    <div class="card" style="margin-bottom:18px;background:#fef2f2;color:#991b1b;border-color:#fecaca;">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('student.exams.submit', $exam) }}">
    @csrf
    <div class="card" style="margin-bottom:18px;">
        <div class="card-header" style="display:flex;justify-content:space-between;gap:14px;align-items:center;flex-wrap:wrap;">
            <div>
                <h3>{{ $exam->title }}</h3>
                <p style="margin:6px 0 0;color:#64748b;">{{ $exam->subject->name ?? 'General' }} | {{ $exam->duration_minutes }} minutes | {{ $exam->total_marks }} marks</p>
            </div>
            <span class="pill">Attempt {{ $attemptsCount + 1 }} / {{ $exam->attempt_limit ?: 1 }}</span>
        </div>
        @if($exam->instructions)
            <div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:14px;padding:14px;color:#334155;line-height:1.7;">{!! $exam->instructions !!}</div>
        @endif
    </div>

    @forelse($exam->questions as $index => $question)
        <div class="card" style="margin-bottom:16px;">
            <h3>Q{{ $index + 1 }}. {!! $question->question !!}</h3>
            <p style="margin:0 0 12px;color:#64748b;font-weight:900;">Marks: {{ $question->pivot->marks ?? $question->marks }}</p>

            @if($question->question_type === 'mcq')
                @foreach(['option_a' => 'A', 'option_b' => 'B', 'option_c' => 'C', 'option_d' => 'D'] as $key => $label)
                    @if($question->$key)
                        <label style="display:flex;gap:10px;align-items:flex-start;margin:10px 0;padding:12px;border:1px solid #e5e7eb;border-radius:14px;cursor:pointer;">
                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $key }}" style="width:auto;margin-top:4px;">
                            <span><strong>{{ $label }}.</strong> {!! $question->$key !!}</span>
                        </label>
                    @endif
                @endforeach
            @elseif($question->question_type === 'numeric')
                <input type="number" step="0.01" name="answers[{{ $question->id }}]" placeholder="Enter numeric answer">
            @else
                <textarea name="answers[{{ $question->id }}]" placeholder="Write your answer"></textarea>
            @endif
        </div>
    @empty
        <div class="card" style="text-align:center;color:#64748b;">No questions attached to this exam.</div>
    @endforelse

    @if($exam->questions->count())
        <div class="card" style="display:flex;gap:12px;justify-content:flex-end;flex-wrap:wrap;">
            <a href="{{ route('student.dashboard') }}" class="btn btn-light">Cancel</a>
            <button type="submit" class="btn btn-primary" onclick="return confirm('Submit exam now?')">Submit Exam</button>
        </div>
    @endif
</form>
@endsection
