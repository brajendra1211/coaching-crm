@extends('portal.layouts.app', ['portalRole' => 'teacher'])
@section('title', 'Question Bank')
@section('page_title', 'Question Bank')
@section('page_subtitle', 'Reusable questions for exams')
@section('content')
@if(session('success'))
    <div class="card" style="margin-bottom:18px;background:#dcfce7;color:#166534;border-color:#bbf7d0;">{{ session('success') }}</div>
@endif
@if(session('import_errors'))
    <div class="card" style="margin-bottom:18px;background:#fff7ed;color:#9a3412;border-color:#fed7aa;">
        <strong>Skipped rows:</strong>
        <ul>@foreach(session('import_errors') as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
<section class="card" style="margin-bottom:18px;">
    <div class="card-header" style="display:flex;justify-content:space-between;gap:14px;align-items:center;flex-wrap:wrap;">
        <div>
            <h3>Question Tools</h3>
            <p class="muted" style="margin:6px 0 0;">Excel/CSV se bulk questions upload karein ya manually question add karein.</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('teacher.questions.import') }}" class="btn btn-primary">Import Excel</a>
            <a href="{{ route('teacher.questions.import.sample') }}" class="btn btn-light">Download Sample</a>
            <a href="{{ route('teacher.questions.create') }}" class="btn btn-light">Add Question</a>
        </div>
    </div>
</section>
<section class="card">
    <h3>Questions</h3>
    <div class="table-wrap"><table>
        <thead><tr><th>Question</th><th>Label</th><th>Type</th><th>Difficulty</th><th>Marks</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($questions as $question)
            <tr>
                <td>{!! \Illuminate\Support\Str::limit(strip_tags($question->question), 120) !!}</td>
                <td>{{ $question->label ?: '-' }}</td>
                <td>{{ strtoupper($question->question_type) }}</td>
                <td>{{ ucfirst($question->difficulty ?? '-') }}</td>
                <td>{{ $question->marks }}</td>
                <td><span class="pill">{{ ucfirst($question->status) }}</span></td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;color:#64748b;">No questions found.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    {{ $questions->links() }}
</section>
@endsection
