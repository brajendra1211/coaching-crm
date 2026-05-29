@extends('admin.layouts.app')

@section('title', 'Question Bank')
@section('page_title', 'Question Bank')

@section('content')
<style>
    .toolbar { display:grid; grid-template-columns: minmax(0,1fr) 220px 220px auto; gap:12px; margin-bottom:18px; }
    .pill { display:inline-flex; padding:7px 11px; border-radius:999px; font-size:12px; font-weight:900; border:1px solid #bfdbfe; background:#eff6ff; color:#1d4ed8; }
    .actions { display:flex; gap:8px; flex-wrap:wrap; }
    @media(max-width:800px){ .toolbar{grid-template-columns:1fr;} }
</style>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Question Bank</h3>
            <p style="margin:6px 0 0;color:#64748b;">Create MCQ, short and long questions for online exams.</p>
        </div>
        <a href="{{ route('admin.questions.create') }}" class="btn btn-primary">+ Add Question</a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">{{ session('success') }}</div>
    @endif

    <form method="GET" class="toolbar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search question, class or difficulty...">
        <select name="subject_id">
            <option value="">All Subjects</option>
            @foreach($subjects as $subject)
                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
            @endforeach
        </select>
        <select name="label">
            <option value="">All Labels</option>
            @foreach($labels as $label)
                <option value="{{ $label }}" {{ request('label') === $label ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button class="btn btn-primary" type="submit">Search</button>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Question</th>
                    <th>Subject</th>
                    <th>Class</th>
                    <th>Label</th>
                    <th>Topic</th>
                    <th>Type</th>
                    <th>Marks</th>
                    <th>Difficulty</th>
                    <th width="210">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $question)
                    <tr>
                        <td>{{ \Illuminate\Support\Str::limit(strip_tags($question->question), 95) }}</td>
                        <td>{{ $question->subject->name ?? '-' }}</td>
                        <td>{{ $question->class_level ?: '-' }}</td>
                        <td>{{ $question->label ?: '-' }}</td>
                        <td>{{ $question->topic ?: '-' }}</td>
                        <td><span class="pill">{{ strtoupper($question->question_type) }}</span></td>
                        <td>{{ $question->marks }}</td>
                        <td>{{ ucfirst($question->difficulty) }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.questions.edit', $question) }}" class="btn btn-primary">Edit</a>
                                <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" onsubmit="return confirm('Delete this question?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" style="text-align:center;padding:35px;color:#64748b;">No questions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:22px;">{{ $questions->links() }}</div>
</div>
@endsection
