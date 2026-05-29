<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Exam;
use App\Models\ExamSeries;
use Illuminate\Http\Request;

class ExamSeriesController extends Controller
{
    public function index(Request $request)
    {
        $query = ExamSeries::with('batch')->withCount('exams')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('series_code', 'like', '%' . $request->search . '%')
                    ->orWhere('label', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('access_type')) {
            $query->where('access_type', $request->access_type);
        }

        $series = $query->paginate(15)->withQueryString();

        return view('admin.exams.series.index', compact('series'));
    }

    public function create()
    {
        return view('admin.exams.series.form', $this->formData(new ExamSeries([
            'difficulty' => 'medium',
            'access_type' => 'free',
            'status' => 'draft',
        ])));
    }

    public function store(Request $request)
    {
        ExamSeries::create($this->validateData($request));

        return redirect()->route('admin.exam-series.index')->with('success', 'Exam series created successfully.');
    }

    public function edit(ExamSeries $examSeries)
    {
        return view('admin.exams.series.form', $this->formData($examSeries));
    }

    public function update(Request $request, ExamSeries $examSeries)
    {
        $examSeries->update($this->validateData($request, $examSeries->id));

        return redirect()->route('admin.exam-series.index')->with('success', 'Exam series updated successfully.');
    }

    public function destroy(ExamSeries $examSeries)
    {
        $examSeries->exams()->detach();
        $examSeries->delete();

        return redirect()->route('admin.exam-series.index')->with('success', 'Exam series deleted successfully.');
    }

    public function builder(ExamSeries $examSeries)
    {
        $examSeries->load(['exams.batch', 'exams.subject']);

        $exams = Exam::with(['batch', 'subject'])
            ->whereIn('status', ['draft', 'scheduled', 'completed'])
            ->orderBy('label')
            ->orderBy('title')
            ->get();

        return view('admin.exams.series.builder', compact('examSeries', 'exams'));
    }

    public function updateBuilder(Request $request, ExamSeries $examSeries)
    {
        $data = $request->validate([
            'exam_ids' => ['nullable', 'array'],
            'exam_ids.*' => ['integer', 'exists:exams,id'],
            'unlock_rules' => ['nullable', 'array'],
            'unlock_rules.*' => ['nullable', 'in:always,after_previous,scheduled_only'],
        ]);

        $examIds = $data['exam_ids'] ?? [];
        $unlockRules = $data['unlock_rules'] ?? [];
        $syncData = [];

        foreach (array_values($examIds) as $index => $examId) {
            $syncData[$examId] = [
                'sort_order' => $index + 1,
                'unlock_rule' => $unlockRules[$examId] ?? 'always',
            ];
        }

        $examSeries->exams()->sync($syncData);

        return redirect()->route('admin.exam-series.builder', $examSeries)->with('success', 'Series builder saved successfully.');
    }

    private function formData(ExamSeries $examSeries): array
    {
        return [
            'examSeries' => $examSeries,
            'batches' => Batch::orderBy('name')->get(),
        ];
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        $codeRule = 'unique:exam_series,series_code';

        if ($ignoreId) {
            $codeRule .= ',' . $ignoreId;
        }

        $data = $request->validate([
            'batch_id' => ['nullable', 'integer', 'exists:batches,id'],
            'title' => ['required', 'string', 'max:255'],
            'series_code' => ['nullable', 'string', 'max:255', $codeRule],
            'label' => ['nullable', 'string', 'max:255'],
            'difficulty' => ['required', 'in:easy,medium,hard,advanced'],
            'access_type' => ['required', 'in:free,paid'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);

        $data['price'] = (float) ($data['price'] ?? 0);

        if (($data['access_type'] ?? 'free') === 'free') {
            $data['price'] = 0;
        }

        return $data;
    }
}
