@extends('portal.layouts.app', ['portalRole' => 'student'])

@section('title', 'My Results')
@section('page_title', 'Results')
@section('page_subtitle', 'Published marks, grades and exam attempt history')

@section('content')
<section class="card" style="margin-bottom:18px;">
    <h3>Published Results</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Exam</th><th>Subject</th><th>Marks</th><th>%</th><th>Grade</th><th>Rank</th><th>Status</th><th>Published</th></tr></thead>
            <tbody>
                @forelse($results as $result)
                    <tr>
                        <td><strong>{{ $result->exam->title ?? '-' }}</strong></td>
                        <td>{{ $result->exam->subject->name ?? 'General' }}</td>
                        <td>{{ $result->marks_obtained }} / {{ $result->total_marks }}</td>
                        <td>{{ $result->percentage }}%</td>
                        <td>{{ $result->grade ?: '-' }}</td>
                        <td>{{ $result->rank ?: '-' }}</td>
                        <td><span class="pill {{ $result->result_status === 'fail' ? 'red' : 'green' }}">{{ ucfirst($result->result_status) }}</span></td>
                        <td>{{ $result->published_at ? $result->published_at->format('d M Y') : '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center;color:#64748b;">No results published.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $results->links() }}
</section>

<section class="card">
    <h3>Attempt History</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Exam</th><th>Subject</th><th>Submitted</th><th>Marks</th><th>%</th><th>Review</th></tr></thead>
            <tbody>
                @forelse($attempts as $attempt)
                    <tr>
                        <td>{{ $attempt->exam->title ?? '-' }}</td>
                        <td>{{ $attempt->exam->subject->name ?? 'General' }}</td>
                        <td>{{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y h:i A') : '-' }}</td>
                        <td>{{ $attempt->marks_obtained }} / {{ $attempt->total_marks }}</td>
                        <td>{{ $attempt->percentage }}%</td>
                        <td><a href="{{ route('student.exams.result', $attempt) }}" class="btn btn-light">Review</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;color:#64748b;">No exam attempts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ method_exists($attempts, 'links') ? $attempts->links() : '' }}
</section>
@endsection
