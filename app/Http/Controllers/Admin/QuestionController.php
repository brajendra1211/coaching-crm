<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with('subject')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('question', 'like', '%' . $request->search . '%')
                    ->orWhere('class_level', 'like', '%' . $request->search . '%')
                    ->orWhere('difficulty', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('label')) {
            $query->where('label', $request->label);
        }

        $questions = $query->paginate(15)->withQueryString();
        $subjects = Subject::orderBy('name')->get();
        $labels = Question::query()
            ->whereNotNull('label')
            ->where('label', '!=', '')
            ->distinct()
            ->orderBy('label')
            ->pluck('label');

        return view('admin.exams.questions.index', compact('questions', 'subjects', 'labels'));
    }

    public function create()
    {
        return view('admin.exams.questions.form', [
            'question' => new Question(['question_type' => 'mcq', 'marks' => 1, 'difficulty' => 'medium', 'status' => 'active']),
            'subjects' => Subject::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Question::create($this->validateData($request));

        return redirect()->route('admin.questions.index')->with('success', 'Question added successfully.');
    }

    public function edit(Question $question)
    {
        return view('admin.exams.questions.form', [
            'question' => $question,
            'subjects' => Subject::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Question $question)
    {
        $question->update($this->validateData($request));

        return redirect()->route('admin.questions.index')->with('success', 'Question updated successfully.');
    }

    public function destroy(Question $question)
    {
        $question->delete();

        return redirect()->route('admin.questions.index')->with('success', 'Question deleted successfully.');
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'class_level' => ['nullable', 'string', 'max:255'],
            'label' => ['nullable', 'string', 'max:255'],
            'topic' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'string'],
            'question_type' => ['required', 'in:mcq,short,long,numeric'],
            'question' => ['required', 'string'],
            'option_a' => ['required_if:question_type,mcq', 'nullable', 'string'],
            'option_b' => ['required_if:question_type,mcq', 'nullable', 'string'],
            'option_c' => ['nullable', 'string'],
            'option_d' => ['nullable', 'string'],
            'correct_answer' => ['required_if:question_type,mcq', 'nullable', 'string', 'max:255'],
            'marks' => ['required', 'numeric', 'min:0'],
            'difficulty' => ['required', 'in:easy,medium,hard,advanced'],
            'explanation' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        if (($data['question_type'] ?? null) === 'mcq' && !in_array($data['correct_answer'] ?? null, ['option_a', 'option_b', 'option_c', 'option_d'], true)) {
            throw ValidationException::withMessages([
                'correct_answer' => 'Please select the correct option for MCQ questions.',
            ]);
        }

        if (($data['question_type'] ?? null) !== 'mcq') {
            $data['option_a'] = null;
            $data['option_b'] = null;
            $data['option_c'] = null;
            $data['option_d'] = null;
        }

        return $data;
    }
}
