@extends('portal.layouts.app', ['portalRole' => 'student'])

@section('title', 'My Exams')
@section('page_title', 'My Exams')
@section('page_subtitle', 'Assigned tests, practice exams and online attempts')

@section('content')
@if(!$student)
    <div class="card"><h3>Profile Not Linked</h3><p class="muted" style="margin:0;">Ask admin to link this login with a student profile.</p></div>
@else
    <section class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;gap:14px;align-items:center;flex-wrap:wrap;">
            <h3>Available Exams</h3>
            <a href="{{ route('student.results.index') }}" class="btn btn-light">View Results</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Exam</th><th>Batch</th><th>Subject</th><th>Date</th><th>Marks</th><th>Attempts</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($exams as $exam)
                        @php
                            $attempts = $attemptsByExam->get($exam->id, collect());
                            $limit = (int) ($exam->attempt_limit ?: 1);
                            $locked = $attempts->count() >= $limit;
                        @endphp
                        <tr>
                            <td><strong>{{ $exam->title }}</strong><br><small class="muted">{{ $exam->label ?: ucfirst($exam->difficulty ?? 'medium') }} | {{ ucfirst($exam->access_type ?? 'free') }}</small></td>
                            <td>{{ $exam->batch->name ?? 'All Batches' }}</td>
                            <td>{{ $exam->subject->name ?? 'General' }}</td>
                            <td>{{ $exam->exam_date ? $exam->exam_date->format('d M Y') : '-' }}</td>
                            <td>{{ $exam->total_marks }}</td>
                            <td>{{ $attempts->count() }} / {{ $limit }}</td>
                            <td><span class="pill {{ $locked ? 'orange' : 'green' }}">{{ $locked ? 'Limit Reached' : ucfirst($exam->status) }}</span></td>
                            <td>
                                @if($locked)
                                    <a href="{{ route('student.results.index') }}" class="btn btn-light">Review</a>
                                @else
                                    <a href="{{ route('student.exams.show', $exam) }}" class="btn btn-primary">Attempt</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" style="text-align:center;color:#64748b;">No exams assigned.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $exams->links() }}
    </section>
@endif
@endsection
