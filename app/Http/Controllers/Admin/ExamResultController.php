<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ExamResultController extends Controller
{
    public function index(Request $request)
    {
        $query = ExamResult::with(['exam', 'student'])->latest();

        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        if ($request->filled('search')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('student_code', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $results = $query->paginate(15)->withQueryString();
        $exams = Exam::orderByDesc('exam_date')->orderBy('title')->get();

        return view('admin.exams.results.index', compact('results', 'exams'));
    }

    public function create()
    {
        return view('admin.exams.results.form', [
            'result' => new ExamResult(['published_at' => now()->toDateString(), 'result_status' => 'pass']),
            'exams' => Exam::orderByDesc('exam_date')->orderBy('title')->get(),
            'students' => Student::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->prepareData($request);

        ExamResult::updateOrCreate(
            ['exam_id' => $data['exam_id'], 'student_id' => $data['student_id']],
            $data
        );

        return redirect()->route('admin.results.index')->with('success', 'Result saved successfully.');
    }

    public function edit(ExamResult $result)
    {
        return view('admin.exams.results.form', [
            'result' => $result,
            'exams' => Exam::orderByDesc('exam_date')->orderBy('title')->get(),
            'students' => Student::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, ExamResult $result)
    {
        $result->update($this->prepareData($request));

        return redirect()->route('admin.results.index')->with('success', 'Result updated successfully.');
    }

    public function destroy(ExamResult $result)
    {
        $result->delete();

        return redirect()->route('admin.results.index')->with('success', 'Result deleted successfully.');
    }

    private function prepareData(Request $request): array
    {
        $data = $request->validate([
            'exam_id' => ['required', 'integer', 'exists:exams,id'],
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'marks_obtained' => ['required', 'numeric', 'min:0'],
            'total_marks' => ['nullable', 'numeric', 'min:0'],
            'grade' => ['nullable', 'string', 'max:50'],
            'rank' => ['nullable', 'integer', 'min:1'],
            'remarks' => ['nullable', 'string'],
            'published_at' => ['nullable', 'date'],
        ]);

        $exam = Exam::find($data['exam_id']);
        $totalMarks = (float) ($exam->total_marks ?? 0);

        if ($totalMarks <= 0 && !empty($data['total_marks'])) {
            $totalMarks = (float) $data['total_marks'];
        }

        $marks = (float) $data['marks_obtained'];

        if ($totalMarks <= 0) {
            throw ValidationException::withMessages([
                'total_marks' => 'Selected exam has no total marks. Please update the exam builder or enter valid total marks.',
            ]);
        }

        if ($marks > $totalMarks) {
            throw ValidationException::withMessages([
                'marks_obtained' => 'Marks obtained cannot be greater than total marks (' . $totalMarks . ').',
            ]);
        }

        $percentage = $totalMarks > 0 ? round(($marks / $totalMarks) * 100, 2) : 0;
        $passingMarks = (float) ($exam->passing_marks ?? 0);

        $data['total_marks'] = $totalMarks;
        $data['percentage'] = $percentage;
        $data['result_status'] = $marks >= $passingMarks ? 'pass' : 'fail';
        $data['grade'] = $data['grade'] ?: $this->gradeFor($percentage);

        return $data;
    }

    private function gradeFor(float $percentage): string
    {
        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B+',
            $percentage >= 60 => 'B',
            $percentage >= 50 => 'C',
            default => 'D',
        };
    }
}
