@extends('admin.layouts.app')

@section('title', 'Online Exams')
@section('page_title', 'Online Exams')

@section('content')
<style>
    .toolbar { display:grid; grid-template-columns:minmax(0,1fr) 180px auto; gap:12px; margin-bottom:18px; }
    .pill { display:inline-flex; padding:7px 11px; border-radius:999px; font-size:12px; font-weight:900; border:1px solid #bfdbfe; background:#eff6ff; color:#1d4ed8; }
    .actions { display:flex; gap:8px; flex-wrap:wrap; }
    @media(max-width:800px){ .toolbar{grid-template-columns:1fr;} }
</style>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Online Exams</h3>
            <p style="margin:6px 0 0;color:#64748b;">Schedule tests, attach questions and manage exam settings.</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('admin.exam-series.index') }}" class="btn btn-light">Series</a>
            <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">+ Add Exam</a>
        </div>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">{{ session('success') }}</div>
    @endif

    <form method="GET" class="toolbar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search exam title or code...">
        <select name="access_type">
            <option value="">Free + Paid</option>
            <option value="free" {{ request('access_type') === 'free' ? 'selected' : '' }}>Free</option>
            <option value="paid" {{ request('access_type') === 'paid' ? 'selected' : '' }}>Paid</option>
        </select>
        <button class="btn btn-primary" type="submit">Search</button>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Exam</th>
                    <th>Batch</th>
                    <th>Subject</th>
                    <th>Label</th>
                    <th>Access</th>
                    <th>Date</th>
                    <th>Marks</th>
                    <th>Questions</th>
                    <th>Series</th>
                    <th>Results</th>
                    <th>Status</th>
                    <th width="210">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($exams as $exam)
                    <tr>
                        <td><strong>{{ $exam->title }}</strong><br><small style="color:#64748b;">{{ $exam->exam_code ?: '-' }}</small></td>
                        <td>{{ $exam->batch->name ?? '-' }}</td>
                        <td>{{ $exam->subject->name ?? '-' }}</td>
                        <td>{{ $exam->label ?: ucfirst($exam->difficulty) }}</td>
                        <td><span class="pill">{{ ucfirst($exam->access_type ?? 'free') }}</span><br><small style="color:#64748b;">{{ ($exam->access_type ?? 'free') === 'paid' ? 'Rs. ' . number_format((float) $exam->price, 2) : 'No fee' }}</small></td>
                        <td>{{ $exam->exam_date ? $exam->exam_date->format('d M Y') : '-' }}</td>
                        <td>{{ $exam->total_marks }} / Pass {{ $exam->passing_marks }}</td>
                        <td>{{ $exam->questions_count }}</td>
                        <td>{{ $exam->series_count }}</td>
                        <td>{{ $exam->results_count }}</td>
                        <td><span class="pill">{{ ucfirst($exam->status) }}</span></td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.exams.builder', $exam) }}" class="btn btn-dark">Builder</a>
                                <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-primary">Edit</a>
                                <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}" onsubmit="return confirm('Delete this exam?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="12" style="text-align:center;padding:35px;color:#64748b;">No exams found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:22px;">{{ $exams->links() }}</div>
</div>
@endsection
