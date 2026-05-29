<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::with(['batch', 'subject'])->withCount(['questions', 'results', 'series'])->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('exam_code', 'like', '%' . $request->search . '%')
                    ->orWhere('label', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('access_type')) {
            $query->where('access_type', $request->access_type);
        }

        $exams = $query->paginate(15)->withQueryString();
        $labels = Exam::query()
            ->whereNotNull('label')
            ->where('label', '!=', '')
            ->distinct()
            ->orderBy('label')
            ->pluck('label');

        return view('admin.exams.exams.index', compact('exams', 'labels'));
    }

    public function create()
    {
        return view('admin.exams.exams.form', $this->formData(new Exam([
            'exam_date' => now()->toDateString(),
            'duration_minutes' => 60,
            'exam_type' => 'single',
            'difficulty' => 'medium',
            'access_type' => 'free',
            'attempt_limit' => 1,
            'show_result_immediately' => true,
            'status' => 'draft',
        ])));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $questionIds = $data['question_ids'] ?? [];
        unset($data['question_ids']);

        $exam = Exam::create($data);
        $this->syncQuestions($exam, $questionIds);

        return redirect()->route('admin.exams.index')->with('success', 'Exam created successfully.');
    }

    public function edit(Exam $exam)
    {
        $exam->load('questions');

        return view('admin.exams.exams.form', $this->formData($exam));
    }

    public function update(Request $request, Exam $exam)
    {
        $data = $this->validateData($request, $exam->id);
        $questionIds = $data['question_ids'] ?? [];
        unset($data['question_ids']);

        $exam->update($data);
        $this->syncQuestions($exam, $questionIds);

        return redirect()->route('admin.exams.index')->with('success', 'Exam updated successfully.');
    }

    public function builder(Exam $exam)
    {
        $exam->load(['questions.subject']);

        $questions = Question::with('subject')
            ->where('status', 'active')
            ->orderBy('label')
            ->orderBy('difficulty')
            ->orderBy('question')
            ->get();

        $subjects = Subject::orderBy('name')->get();
        $labels = Question::query()
            ->whereNotNull('label')
            ->where('label', '!=', '')
            ->distinct()
            ->orderBy('label')
            ->pluck('label');

        return view('admin.exams.exams.builder', compact('exam', 'questions', 'subjects', 'labels'));
    }

    public function updateBuilder(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'question_ids' => ['nullable', 'array'],
            'question_ids.*' => ['integer', 'exists:questions,id'],
            'question_marks' => ['nullable', 'array'],
            'question_marks.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $questionIds = $data['question_ids'] ?? [];
        $marks = $data['question_marks'] ?? [];
        $questions = Question::whereIn('id', $questionIds)->get()->keyBy('id');
        $syncData = [];
        $totalMarks = 0;

        foreach (array_values($questionIds) as $index => $questionId) {
            if (!$questions->has($questionId)) {
                continue;
            }

            $questionMarks = (float) ($marks[$questionId] ?? $questions[$questionId]->marks ?? 1);
            $totalMarks += $questionMarks;

            $syncData[$questionId] = [
                'marks' => $questionMarks,
                'sort_order' => $index + 1,
            ];
        }

        $exam->questions()->sync($syncData);
        $exam->update(['total_marks' => $totalMarks]);

        return redirect()->route('admin.exams.builder', $exam)->with('success', 'Exam builder saved successfully.');
    }

    public function destroy(Exam $exam)
    {
        $exam->questions()->detach();
        $exam->results()->delete();
        $exam->delete();

        return redirect()->route('admin.exams.index')->with('success', 'Exam deleted successfully.');
    }

    private function formData(Exam $exam): array
    {
        return [
            'exam' => $exam,
            'batches' => Batch::orderBy('name')->get(),
            'subjects' => Subject::orderBy('name')->get(),
            'questions' => Question::where('status', 'active')->orderBy('question')->get(),
        ];
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        $codeRule = 'unique:exams,exam_code';

        if ($ignoreId) {
            $codeRule .= ',' . $ignoreId;
        }

        $data = $request->validate([
            'batch_id' => ['nullable', 'integer', 'exists:batches,id'],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'exam_code' => ['nullable', 'string', 'max:255', $codeRule],
            'exam_type' => ['required', 'in:single,mock,practice,series_part'],
            'label' => ['nullable', 'string', 'max:255'],
            'difficulty' => ['required', 'in:easy,medium,hard,advanced'],
            'access_type' => ['required', 'in:free,paid'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'exam_date' => ['nullable', 'date'],
            'start_time' => ['nullable'],
            'end_time' => ['nullable'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'total_marks' => ['required', 'numeric', 'min:0'],
            'passing_marks' => ['required', 'numeric', 'min:0'],
            'negative_marks' => ['nullable', 'numeric', 'min:0'],
            'attempt_limit' => ['required', 'integer', 'min:1'],
            'show_result_immediately' => ['nullable', 'boolean'],
            'instructions' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,scheduled,completed,cancelled'],
            'question_ids' => ['nullable', 'array'],
            'question_ids.*' => ['integer', 'exists:questions,id'],
        ]);

        $data['price'] = (float) ($data['price'] ?? 0);
        $data['negative_marks'] = (float) ($data['negative_marks'] ?? 0);
        $data['show_result_immediately'] = (bool) ($data['show_result_immediately'] ?? false);

        if (($data['access_type'] ?? 'free') === 'free') {
            $data['price'] = 0;
        }

        return $data;
    }

    private function syncQuestions(Exam $exam, array $questionIds): void
    {
        $questions = Question::whereIn('id', $questionIds)->get()->keyBy('id');
        $syncData = [];

        foreach (array_values($questionIds) as $index => $questionId) {
            if (!$questions->has($questionId)) {
                continue;
            }

            $syncData[$questionId] = [
                'marks' => $questions[$questionId]->marks,
                'sort_order' => $index + 1,
            ];
        }

        $exam->questions()->sync($syncData);
    }
}
