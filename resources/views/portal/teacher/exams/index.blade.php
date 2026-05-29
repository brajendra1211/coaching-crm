@extends('portal.layouts.app', ['portalRole' => 'teacher'])
@section('title', 'Teacher Exams')
@section('page_title', 'Exams')
@section('page_subtitle', 'Assigned batch exams and builder shortcut')
@section('content')
@if(session('success'))
    <div class="card" style="margin-bottom:18px;background:#dcfce7;color:#166534;border-color:#bbf7d0;">{{ session('success') }}</div>
@endif
<div class="page-actions">
    <a href="{{ route('teacher.exams.create') }}" class="btn btn-primary">Create Exam</a>
    <a href="{{ route('teacher.series.index') }}" class="btn btn-light">Series Builder</a>
    <a href="{{ route('teacher.questions.create') }}" class="btn btn-light">Add Question</a>
    <a href="{{ route('teacher.questions.index') }}" class="btn btn-light">Question Bank</a>
</div>
<section class="card">
    <h3>Exams</h3>
    <div class="table-wrap"><table>
        <thead><tr><th>Exam</th><th>Batch</th><th>Subject</th><th>Date</th><th>Marks</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
        @forelse($exams as $exam)
            <tr>
                <td><strong>{{ $exam->title }}</strong><br><small class="muted">{{ $exam->label ?: ucfirst($exam->difficulty ?? 'medium') }}</small></td>
                <td>{{ $exam->batch->name ?? 'All' }}</td>
                <td>{{ $exam->subject->name ?? '-' }}</td>
                <td>{{ $exam->exam_date ? $exam->exam_date->format('d M Y') : '-' }}</td>
                <td>{{ $exam->total_marks }}</td>
                <td><span class="pill">{{ ucfirst($exam->status) }}</span></td>
                <td style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="{{ route('teacher.exams.builder', $exam) }}" class="btn btn-primary">Builder</a>
                    <a href="{{ route('teacher.exams.edit', $exam) }}" class="btn btn-light">Edit</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;color:#64748b;">No exams found.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    {{ $exams->links() }}
</section>
@endsection
