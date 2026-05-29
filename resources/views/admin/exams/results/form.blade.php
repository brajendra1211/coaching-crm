@extends('admin.layouts.app')

@section('title', $result->exists ? 'Edit Result' : 'Add Result')
@section('page_title', $result->exists ? 'Edit Result' : 'Add Result')

@section('content')
<style>
    .exam-form { max-width:920px; }
    .grid-2 { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; }
    .form-group { margin-bottom:18px; }
    .form-group label { display:block; font-weight:900; color:#334155; margin-bottom:8px; }
    .form-group textarea { min-height:110px; resize:vertical; }
    .input-help { display:block; margin-top:7px; color:#64748b; font-size:12px; font-weight:800; }
    @media(max-width:800px){ .grid-2{grid-template-columns:1fr;} }
</style>

@if($errors->any())
    <div style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;padding:14px;border-radius:14px;margin-bottom:18px;">
        <strong>Please fix errors:</strong>
        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ $result->exists ? route('admin.results.update', $result) : route('admin.results.store') }}" class="exam-form">
    @csrf
    @if($result->exists) @method('PUT') @endif
    <div class="card">
        <div class="card-header"><h3>{{ $result->exists ? 'Edit Result' : 'Add Result' }}</h3></div>
        <div class="grid-2">
            <div class="form-group">
                <label>Exam</label>
                <select name="exam_id" id="examSelect" required>
                    <option value="">Select Exam</option>
                    @foreach($exams as $exam)
                        <option
                            value="{{ $exam->id }}"
                            data-total-marks="{{ $exam->total_marks }}"
                            data-passing-marks="{{ $exam->passing_marks }}"
                            {{ old('exam_id', $result->exam_id) == $exam->id ? 'selected' : '' }}
                        >
                            {{ $exam->title }} ({{ $exam->total_marks }} marks)
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group"><label>Student</label><select name="student_id" required><option value="">Select Student</option>@foreach($students as $student)<option value="{{ $student->id }}" {{ old('student_id', $result->student_id) == $student->id ? 'selected' : '' }}>{{ $student->name }} {{ $student->student_code ? '(' . $student->student_code . ')' : '' }}</option>@endforeach</select></div>
            <div class="form-group">
                <label>Marks Obtained</label>
                <input type="number" step="0.01" min="0" name="marks_obtained" id="marksObtainedInput" value="{{ old('marks_obtained', $result->marks_obtained) }}" required>
                <span class="input-help" id="marksHelp">Select exam to see total marks.</span>
            </div>
            <div class="form-group">
                <label>Total Marks</label>
                <input type="number" step="0.01" min="0" name="total_marks" id="totalMarksInput" value="{{ old('total_marks', $result->total_marks) }}" readonly>
                <span class="input-help">Auto from selected exam. Update exam builder if marks are wrong.</span>
            </div>
            <div class="form-group"><label>Grade</label><input name="grade" value="{{ old('grade', $result->grade) }}" placeholder="Auto if blank"></div>
            <div class="form-group"><label>Rank</label><input type="number" min="1" name="rank" value="{{ old('rank', $result->rank) }}"></div>
            <div class="form-group"><label>Publish Date</label><input type="date" name="published_at" value="{{ old('published_at', $result->published_at ? $result->published_at->format('Y-m-d') : '') }}"></div>
            <div class="form-group" style="grid-column:1/-1;"><label>Remarks</label><textarea name="remarks">{{ old('remarks', $result->remarks) }}</textarea></div>
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button class="btn btn-primary" type="submit">{{ $result->exists ? 'Update' : 'Save' }}</button>
            <a href="{{ route('admin.results.index') }}" class="btn btn-light">Cancel</a>
        </div>
    </div>
</form>

<script>
    const examSelect = document.getElementById('examSelect');
    const totalMarksInput = document.getElementById('totalMarksInput');
    const marksObtainedInput = document.getElementById('marksObtainedInput');
    const marksHelp = document.getElementById('marksHelp');

    function syncExamMarks() {
        const selected = examSelect?.selectedOptions?.[0];
        const totalMarks = selected?.dataset?.totalMarks || '';

        if (totalMarksInput && totalMarks) {
            totalMarksInput.value = totalMarks;
        }

        if (marksObtainedInput) {
            marksObtainedInput.max = totalMarks || '';
        }

        if (marksHelp) {
            marksHelp.textContent = totalMarks
                ? 'Maximum marks allowed: ' + totalMarks
                : 'Select exam to see total marks.';
        }
    }

    examSelect?.addEventListener('change', syncExamMarks);
    syncExamMarks();
</script>
@endsection
