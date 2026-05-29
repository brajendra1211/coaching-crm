@extends('admin.layouts.app')

@section('title', $question->exists ? 'Edit Question' : 'Add Question')
@section('page_title', $question->exists ? 'Edit Question' : 'Add Question')

@section('content')
<style>
    .exam-form { max-width:980px; }
    .grid-2 { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; }
    .form-group { margin-bottom:18px; }
    .form-group label { display:block; font-weight:900; color:#334155; margin-bottom:8px; }
    .form-group textarea { min-height:110px; resize:vertical; }
    .ck.ck-editor { width:100%; }
    .ck.ck-editor__main > .ck-editor__editable {
        min-height:120px;
        border-radius:0 0 14px 14px;
    }
    .question-editor .ck.ck-editor__main > .ck-editor__editable {
        min-height:190px;
    }
    .ck.ck-toolbar {
        border-radius:14px 14px 0 0;
        border-color:#cbd5e1;
    }
    .answer-help {
        display:block;
        margin-top:7px;
        color:#64748b;
        font-size:12px;
        font-weight:800;
    }
    @media(max-width:800px){ .grid-2{grid-template-columns:1fr;} }
</style>

@if($errors->any())
    <div style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;padding:14px;border-radius:14px;margin-bottom:18px;">
        <strong>Please fix errors:</strong>
        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ $question->exists ? route('admin.questions.update', $question) : route('admin.questions.store') }}" class="exam-form">
    @csrf
    @if($question->exists) @method('PUT') @endif

    <div class="card">
        <div class="card-header"><h3>{{ $question->exists ? 'Edit Question' : 'Add Question' }}</h3></div>
        <div class="grid-2">
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
            <div class="form-group"><label>Topic / Chapter</label><input name="topic" value="{{ old('topic', $question->topic) }}" placeholder="Algebra, Motion, Grammar"></div>
            <div class="form-group"><label>Source</label><input name="source" value="{{ old('source', $question->source) }}" placeholder="NCERT, Module 1, Previous Year"></div>
            <div class="form-group"><label>Tags</label><input name="tags" value="{{ old('tags', $question->tags) }}" placeholder="formula, concept, pyq"></div>
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
            @php
                $correctAnswerValue = old('correct_answer', $question->correct_answer);
                $isMcqCorrect = in_array($correctAnswerValue, ['option_a', 'option_b', 'option_c', 'option_d'], true);
            @endphp
            <div class="form-group mcq-field">
                <label>Correct Option</label>
                <select name="correct_answer" id="mcqCorrectAnswer">
                    <option value="">Select Correct Option</option>
                    <option value="option_a" {{ $correctAnswerValue === 'option_a' ? 'selected' : '' }}>Option A</option>
                    <option value="option_b" {{ $correctAnswerValue === 'option_b' ? 'selected' : '' }}>Option B</option>
                    <option value="option_c" {{ $correctAnswerValue === 'option_c' ? 'selected' : '' }}>Option C</option>
                    <option value="option_d" {{ $correctAnswerValue === 'option_d' ? 'selected' : '' }}>Option D</option>
                </select>
                <span class="answer-help">Auto marking ke liye yahi value use hogi.</span>
            </div>
            <div class="form-group non-mcq-field">
                <label>Model / Correct Answer</label>
                <input name="correct_answer" id="textCorrectAnswer" value="{{ $isMcqCorrect ? '' : $correctAnswerValue }}" placeholder="Short, long ya numeric answer">
            </div>
            <div class="form-group" style="grid-column:1/-1;">
                <label>Explanation</label>
                <textarea name="explanation" class="rich-question-editor">{{ old('explanation', $question->explanation) }}</textarea>
            </div>
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button class="btn btn-primary" type="submit">{{ $question->exists ? 'Update' : 'Save' }}</button>
            <a href="{{ route('admin.questions.index') }}" class="btn btn-light">Cancel</a>
        </div>
    </div>
</form>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

<script>
    const questionEditors = [];

    document.querySelectorAll('.rich-question-editor').forEach(function (textarea) {
        ClassicEditor
            .create(textarea, {
                toolbar: [
                    'heading',
                    '|',
                    'bold',
                    'italic',
                    'link',
                    '|',
                    'bulletedList',
                    'numberedList',
                    'blockQuote',
                    '|',
                    'insertTable',
                    'undo',
                    'redo'
                ],
                table: {
                    contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
                }
            })
            .then(function (editor) {
                questionEditors.push({ textarea: textarea, editor: editor });
            })
            .catch(function (error) {
                console.error(error);
            });
    });

    document.querySelector('.exam-form')?.addEventListener('submit', function () {
        questionEditors.forEach(function (item) {
            item.textarea.value = item.editor.getData();
        });
    });

    const questionTypeSelect = document.getElementById('questionTypeSelect');
    const mcqCorrectAnswer = document.getElementById('mcqCorrectAnswer');
    const textCorrectAnswer = document.getElementById('textCorrectAnswer');

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

        if (textCorrectAnswer) {
            textCorrectAnswer.required = false;
        }
    }

    questionTypeSelect?.addEventListener('change', syncQuestionTypeFields);
    syncQuestionTypeFields();
</script>
@endsection
