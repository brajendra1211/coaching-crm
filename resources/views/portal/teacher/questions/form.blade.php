@extends('portal.layouts.app', ['portalRole' => 'teacher'])

@section('title', 'Add Question')
@section('page_title', 'Add Question')
@section('page_subtitle', 'Create reusable questions without opening the admin panel')

@section('content')
<style>
    .question-form { max-width:980px; }
    .form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; }
    .form-group { margin-bottom:18px; }
    .form-group label { display:block; font-weight:900; color:#334155; margin-bottom:8px; }
    input, select, textarea { width:100%; border:1px solid #cbd5e1; border-radius:14px; padding:12px 13px; font-size:14px; outline:none; }
    textarea { min-height:110px; resize:vertical; }
    input:focus, select:focus, textarea:focus { border-color:#2563eb; box-shadow:0 0 0 4px rgba(37,99,235,.10); }
    .ck.ck-editor { width:100%; }
    .ck.ck-editor__main > .ck-editor__editable { min-height:120px; border-radius:0 0 14px 14px; }
    .question-editor .ck.ck-editor__main > .ck-editor__editable { min-height:190px; }
    .ck.ck-toolbar { border-radius:14px 14px 0 0; border-color:#cbd5e1; }
    .answer-help { display:block; margin-top:7px; color:#64748b; font-size:12px; font-weight:800; }
    @media(max-width:800px){ .form-grid{grid-template-columns:1fr;} }
</style>

@if($errors->any())
    <div class="card" style="margin-bottom:18px;background:#fef2f2;color:#991b1b;border-color:#fecaca;">
        <strong>Please fix errors:</strong>
        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

@if(session('success'))
    <div class="card" style="margin-bottom:18px;background:#dcfce7;color:#166534;border-color:#bbf7d0;">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('teacher.questions.store') }}" class="question-form">
    @csrf
    <div class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;gap:14px;align-items:center;flex-wrap:wrap;">
            <h3>Add Question</h3>
            <a href="{{ route('teacher.questions.index') }}" class="btn btn-light">Back to Bank</a>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Subject</label>
                <select name="subject_id">
                    <option value="">Select Subject</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id', $question->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group"><label>Class / Level</label><input name="class_level" value="{{ old('class_level', $question->class_level) }}"></div>
            <div class="form-group"><label>Question Label</label><input name="label" value="{{ old('label', $question->label) }}" placeholder="Easy, Medium, Hard, Strong Text Book"></div>
            <div class="form-group"><label>Topic / Chapter</label><input name="topic" value="{{ old('topic', $question->topic) }}"></div>
            <div class="form-group"><label>Source</label><input name="source" value="{{ old('source', $question->source) }}"></div>
            <div class="form-group"><label>Tags</label><input name="tags" value="{{ old('tags', $question->tags) }}"></div>
            <div class="form-group">
                <label>Question Type</label>
                <select name="question_type" id="questionTypeSelect" required>
                    @foreach(['mcq' => 'MCQ', 'short' => 'Short Answer', 'long' => 'Long Answer', 'numeric' => 'Numeric'] as $value => $label)
                        <option value="{{ $value }}" {{ old('question_type', $question->question_type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <span class="answer-help">MCQ par options aur correct option dropdown active hoga.</span>
            </div>
            <div class="form-group"><label>Marks</label><input type="number" step="0.01" min="0" name="marks" value="{{ old('marks', $question->marks) }}" required></div>
            <div class="form-group">
                <label>Difficulty</label>
                <select name="difficulty" required>
                    @foreach(['easy' => 'Easy', 'medium' => 'Medium', 'hard' => 'Hard', 'advanced' => 'Advanced'] as $value => $label)
                        <option value="{{ $value }}" {{ old('difficulty', $question->difficulty) == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="active" {{ old('status', $question->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $question->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="form-group question-editor" style="grid-column:1/-1;">
                <label>Question</label>
                <textarea name="question" class="rich-question-editor">{{ old('question', $question->question) }}</textarea>
            </div>
            @foreach(['option_a' => 'Option A', 'option_b' => 'Option B', 'option_c' => 'Option C', 'option_d' => 'Option D'] as $name => $label)
                <div class="form-group mcq-field">
                    <label>{{ $label }}</label>
                    <textarea name="{{ $name }}" class="rich-question-editor">{{ old($name, $question->$name) }}</textarea>
                </div>
            @endforeach
            <div class="form-group mcq-field">
                <label>Correct Option</label>
                <select name="correct_answer" id="mcqCorrectAnswer">
                    <option value="">Select Correct Option</option>
                    @foreach(['option_a' => 'Option A', 'option_b' => 'Option B', 'option_c' => 'Option C', 'option_d' => 'Option D'] as $value => $label)
                        <option value="{{ $value }}" {{ old('correct_answer', $question->correct_answer) === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <span class="answer-help">Auto marking ke liye yahi value use hogi.</span>
            </div>
            <div class="form-group non-mcq-field">
                <label>Model / Correct Answer</label>
                <input name="correct_answer" id="textCorrectAnswer" value="{{ in_array(old('correct_answer', $question->correct_answer), ['option_a','option_b','option_c','option_d'], true) ? '' : old('correct_answer', $question->correct_answer) }}">
            </div>
            <div class="form-group" style="grid-column:1/-1;">
                <label>Explanation</label>
                <textarea name="explanation" class="rich-question-editor">{{ old('explanation', $question->explanation) }}</textarea>
            </div>
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button class="btn btn-primary" type="submit">Save Question</button>
            <a href="{{ route('teacher.questions.index') }}" class="btn btn-light">Cancel</a>
        </div>
    </div>
</form>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
    const questionEditors = [];
    document.querySelectorAll('.rich-question-editor').forEach(function (textarea) {
        ClassicEditor.create(textarea, {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', '|', 'bulletedList', 'numberedList', 'blockQuote', '|', 'insertTable', 'undo', 'redo'],
            table: { contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'] }
        }).then(function (editor) {
            questionEditors.push({ textarea: textarea, editor: editor });
        }).catch(function (error) {
            console.error(error);
        });
    });

    document.querySelector('.question-form')?.addEventListener('submit', function () {
        questionEditors.forEach(function (item) {
            item.textarea.value = item.editor.getData();
        });
    });

    const questionTypeSelect = document.getElementById('questionTypeSelect');
    const mcqCorrectAnswer = document.getElementById('mcqCorrectAnswer');

    function syncQuestionTypeFields() {
        const isMcq = questionTypeSelect?.value === 'mcq';

        document.querySelectorAll('.mcq-field').forEach(function (field) {
            field.style.display = isMcq ? '' : 'none';
            field.querySelectorAll('textarea, select, input').forEach(function (input) {
                input.disabled = !isMcq;
            });
        });

        document.querySelectorAll('.non-mcq-field').forEach(function (field) {
            field.style.display = isMcq ? 'none' : '';
            field.querySelectorAll('textarea, select, input').forEach(function (input) {
                input.disabled = isMcq;
            });
        });

        if (mcqCorrectAnswer) {
            mcqCorrectAnswer.required = isMcq;
        }
    }

    questionTypeSelect?.addEventListener('change', syncQuestionTypeFields);
    syncQuestionTypeFields();
</script>
@endsection
