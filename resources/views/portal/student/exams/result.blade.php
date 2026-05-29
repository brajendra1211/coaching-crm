@extends('portal.layouts.app', ['portalRole' => 'student'])

@section('title', 'Exam Result')
@section('page_title', 'Exam Result')
@section('page_subtitle', $attempt->exam->title)

@section('content')
<div class="grid-4">
    <div class="card stat"><small>Marks</small><strong>{{ $attempt->marks_obtained }} / {{ $attempt->total_marks }}</strong></div>
    <div class="card stat"><small>Percentage</small><strong>{{ $attempt->percentage }}%</strong></div>
    <div class="card stat"><small>Submitted</small><strong style="font-size:18px;">{{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y h:i A') : '-' }}</strong></div>
    <div class="card stat"><small>Status</small><strong>{{ $attempt->marks_obtained >= ($attempt->exam->passing_marks ?? 0) ? 'Pass' : 'Fail' }}</strong></div>
</div>

<section class="card">
    <div class="card-header" style="display:flex;justify-content:space-between;gap:14px;align-items:center;flex-wrap:wrap;">
        <h3>Answer Review</h3>
        <a href="{{ route('student.dashboard') }}" class="btn btn-light">Back</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Question</th><th>Your Answer</th><th>Correct</th><th>Marks</th></tr></thead>
            <tbody>
                @foreach($attempt->answers as $answer)
                    <tr>
                        <td>{!! \Illuminate\Support\Str::limit(strip_tags($answer->question->question ?? ''), 100) !!}</td>
                        <td>{{ $answer->answer ?: '-' }}</td>
                        <td><span class="pill {{ $answer->is_correct ? 'green' : 'red' }}">{{ $answer->is_correct ? 'Yes' : 'No' }}</span></td>
                        <td>{{ $answer->marks }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection
