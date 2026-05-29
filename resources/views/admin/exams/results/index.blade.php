@extends('admin.layouts.app')

@section('title', 'Results')
@section('page_title', 'Results')

@section('content')
<style>
    .toolbar { display:grid; grid-template-columns:minmax(0,1fr) 260px auto; gap:12px; margin-bottom:18px; }
    .pill { display:inline-flex; padding:7px 11px; border-radius:999px; font-size:12px; font-weight:900; border:1px solid #bbf7d0; background:#dcfce7; color:#166534; }
    .pill.fail { border-color:#fecaca; background:#fee2e2; color:#991b1b; }
    .actions { display:flex; gap:8px; flex-wrap:wrap; }
    @media(max-width:800px){ .toolbar{grid-template-columns:1fr;} }
</style>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Results</h3>
            <p style="margin:6px 0 0;color:#64748b;">Enter marks, calculate percentage, grade and pass or fail status.</p>
        </div>
        <a href="{{ route('admin.results.create') }}" class="btn btn-primary">+ Add Result</a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">{{ session('success') }}</div>
    @endif

    <form method="GET" class="toolbar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search student...">
        <select name="exam_id"><option value="">All Exams</option>@foreach($exams as $exam)<option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>{{ $exam->title }}</option>@endforeach</select>
        <button class="btn btn-primary" type="submit">Search</button>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Student</th><th>Exam</th><th>Marks</th><th>%</th><th>Grade</th><th>Rank</th><th>Status</th><th width="210">Action</th></tr></thead>
            <tbody>
                @forelse($results as $result)
                    <tr>
                        <td><strong>{{ $result->student->name ?? '-' }}</strong><br><small style="color:#64748b;">{{ $result->student->student_code ?? '' }}</small></td>
                        <td>{{ $result->exam->title ?? '-' }}</td>
                        <td>{{ $result->marks_obtained }} / {{ $result->total_marks }}</td>
                        <td>{{ $result->percentage }}%</td>
                        <td>{{ $result->grade ?: '-' }}</td>
                        <td>{{ $result->rank ?: '-' }}</td>
                        <td><span class="pill {{ $result->result_status === 'fail' ? 'fail' : '' }}">{{ ucfirst($result->result_status) }}</span></td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.results.edit', $result) }}" class="btn btn-primary">Edit</a>
                                <form method="POST" action="{{ route('admin.results.destroy', $result) }}" onsubmit="return confirm('Delete this result?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center;padding:35px;color:#64748b;">No results found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:22px;">{{ $results->links() }}</div>
</div>
@endsection
