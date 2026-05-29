<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Exam;
use App\Models\ExamSeries;
use App\Models\OnlineClass;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudyMaterial;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use ZipArchive;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $teacher = $this->teacher();
        $batchIds = $this->batchIds($teacher);

        $batches = $this->batchQuery($teacher, $batchIds)->get();
        $students = $this->studentQuery($teacher, $batchIds)->take(8)->get();
        $exams = $this->examQuery($teacher, $batchIds)->take(8)->get();
        $materials = $this->materialQuery($teacher, $batchIds)->take(8)->get();
        $attendance = $this->attendanceQuery($teacher, $batchIds)->take(10)->get();
        $classes = $this->classQuery($teacher, $batchIds)->take(6)->get();

        $questionsCount = Question::count();

        return view('portal.teacher.dashboard', [
            'teacher' => $teacher,
            'batches' => $batches,
            'students' => $students,
            'exams' => $exams,
            'materials' => $materials,
            'attendance' => $attendance,
            'classes' => $classes,
            'studentsCount' => $batches->sum('students_count'),
            'questionsCount' => $questionsCount,
        ]);
    }

    public function batches()
    {
        $teacher = $this->teacher();
        $batchIds = $this->batchIds($teacher);
        $batches = $this->batchQuery($teacher, $batchIds)->paginate(15);

        return view('portal.teacher.batches.index', compact('teacher', 'batches'));
    }

    public function students()
    {
        $teacher = $this->teacher();
        $batchIds = $this->batchIds($teacher);
        $students = $this->studentQuery($teacher, $batchIds)->paginate(20);

        return view('portal.teacher.students.index', compact('teacher', 'students'));
    }

    public function exams()
    {
        $teacher = $this->teacher();
        $batchIds = $this->batchIds($teacher);
        $exams = $this->examQuery($teacher, $batchIds)->paginate(15);

        return view('portal.teacher.exams.index', compact('teacher', 'exams'));
    }

    public function createExam()
    {
        $teacher = $this->teacher();
        $batchIds = $this->batchIds($teacher);

        return view('portal.teacher.exams.form', $this->examFormData($teacher, new Exam([
            'exam_date' => now()->toDateString(),
            'duration_minutes' => 60,
            'exam_type' => 'single',
            'difficulty' => 'medium',
            'access_type' => 'free',
            'attempt_limit' => 1,
            'show_result_immediately' => true,
            'status' => 'draft',
        ]), $batchIds));
    }

    public function storeExam(Request $request)
    {
        $teacher = $this->teacher();
        abort_unless($teacher, 403);

        $batchIds = $this->batchIds($teacher);
        $data = $this->validateExam($request, $batchIds);
        $questionIds = $data['question_ids'] ?? [];
        unset($data['question_ids']);
        $data['teacher_id'] = $teacher->id;

        $exam = Exam::create($data);
        $this->syncExamQuestions($exam, $questionIds);

        return redirect()->route('teacher.exams.builder', $exam)->with('success', 'Exam created. Now build the paper.');
    }

    public function editExam(Exam $exam)
    {
        $teacher = $this->teacher();
        $batchIds = $this->batchIds($teacher);
        $this->authorizeTeacherExam($exam, $batchIds);

        $exam->load('questions');

        return view('portal.teacher.exams.form', $this->examFormData($teacher, $exam, $batchIds));
    }

    public function updateExam(Request $request, Exam $exam)
    {
        $teacher = $this->teacher();
        abort_unless($teacher, 403);

        $batchIds = $this->batchIds($teacher);
        $this->authorizeTeacherExam($exam, $batchIds);

        $data = $this->validateExam($request, $batchIds, $exam->id);
        $questionIds = $data['question_ids'] ?? [];
        unset($data['question_ids']);
        $data['teacher_id'] = $exam->teacher_id ?: $teacher->id;

        $exam->update($data);
        $this->syncExamQuestions($exam, $questionIds);

        return redirect()->route('teacher.exams.index')->with('success', 'Exam updated successfully.');
    }

    public function examBuilder(Exam $exam)
    {
        $teacher = $this->teacher();
        $batchIds = $this->batchIds($teacher);
        $this->authorizeTeacherExam($exam, $batchIds);

        $exam->load(['questions.subject']);

        $questions = Question::with('subject')
            ->where('status', 'active')
            ->where(function ($q) use ($teacher) {
                $q->whereNull('teacher_id')->orWhere('teacher_id', $teacher?->id);
            })
            ->orderBy('label')
            ->orderBy('difficulty')
            ->orderBy('question')
            ->get();

        return view('portal.teacher.exams.builder', compact('teacher', 'exam', 'questions'));
    }

    public function updateExamBuilder(Request $request, Exam $exam)
    {
        $teacher = $this->teacher();
        abort_unless($teacher, 403);

        $batchIds = $this->batchIds($teacher);
        $this->authorizeTeacherExam($exam, $batchIds);

        $data = $request->validate([
            'question_ids' => ['nullable', 'array'],
            'question_ids.*' => ['integer', 'exists:questions,id'],
            'question_marks' => ['nullable', 'array'],
            'question_marks.*' => ['nullable', 'numeric', 'min:0'],
            'auto_easy' => ['nullable', 'integer', 'min:0'],
            'auto_medium' => ['nullable', 'integer', 'min:0'],
            'auto_hard' => ['nullable', 'integer', 'min:0'],
            'auto_advanced' => ['nullable', 'integer', 'min:0'],
            'auto_marks' => ['nullable', 'numeric', 'min:0'],
            'auto_label' => ['nullable', 'string', 'max:255'],
            'auto_subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'auto_generate' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('auto_generate')) {
            [$questionIds, $marks] = $this->autoSelectQuestions($teacher, $data);
            $this->syncExamQuestions($exam, $questionIds, $marks);
        } else {
            $this->syncExamQuestions($exam, $data['question_ids'] ?? [], $data['question_marks'] ?? []);
        }

        return redirect()->route('teacher.exams.builder', $exam)->with('success', 'Exam builder saved successfully.');
    }

    public function series()
    {
        $teacher = $this->teacher();
        $batchIds = $this->batchIds($teacher);
        $series = ExamSeries::with('batch')
            ->withCount('exams')
            ->when(!$teacher, fn ($q) => $q->whereRaw('1 = 0'))
            ->where(function ($q) use ($teacher, $batchIds) {
                $q->where('teacher_id', $teacher?->id)
                    ->orWhere(function ($legacy) use ($batchIds) {
                        $legacy->whereNull('teacher_id')
                            ->where(function ($scope) use ($batchIds) {
                                $scope->whereNull('batch_id')->orWhereIn('batch_id', $batchIds);
                            });
                    });
            })
            ->latest()
            ->paginate(15);

        return view('portal.teacher.series.index', compact('teacher', 'series'));
    }

    public function createSeries()
    {
        $teacher = $this->teacher();
        $batchIds = $this->batchIds($teacher);

        return view('portal.teacher.series.form', $this->seriesFormData($teacher, new ExamSeries([
            'difficulty' => 'medium',
            'access_type' => 'free',
            'status' => 'draft',
        ]), $batchIds));
    }

    public function storeSeries(Request $request)
    {
        $teacher = $this->teacher();
        abort_unless($teacher, 403);

        $batchIds = $this->batchIds($teacher);
        $data = $this->validateSeries($request, $batchIds);
        $data['teacher_id'] = $teacher->id;

        $series = ExamSeries::create($data);

        return redirect()->route('teacher.series.builder', $series)->with('success', 'Series created. Now attach exams.');
    }

    public function editSeries(ExamSeries $examSeries)
    {
        $teacher = $this->teacher();
        $batchIds = $this->batchIds($teacher);
        $this->authorizeTeacherSeries($examSeries, $batchIds);

        return view('portal.teacher.series.form', $this->seriesFormData($teacher, $examSeries, $batchIds));
    }

    public function updateSeries(Request $request, ExamSeries $examSeries)
    {
        $teacher = $this->teacher();
        abort_unless($teacher, 403);

        $batchIds = $this->batchIds($teacher);
        $this->authorizeTeacherSeries($examSeries, $batchIds);

        $data = $this->validateSeries($request, $batchIds, $examSeries->id);
        $data['teacher_id'] = $examSeries->teacher_id ?: $teacher->id;

        $examSeries->update($data);

        return redirect()->route('teacher.series.index')->with('success', 'Series updated successfully.');
    }

    public function seriesBuilder(ExamSeries $examSeries)
    {
        $teacher = $this->teacher();
        $batchIds = $this->batchIds($teacher);
        $this->authorizeTeacherSeries($examSeries, $batchIds);

        $examSeries->load(['exams.batch', 'exams.subject']);
        $exams = $this->examQuery($teacher, $batchIds)->get();

        return view('portal.teacher.series.builder', compact('teacher', 'examSeries', 'exams'));
    }

    public function updateSeriesBuilder(Request $request, ExamSeries $examSeries)
    {
        $teacher = $this->teacher();
        abort_unless($teacher, 403);

        $batchIds = $this->batchIds($teacher);
        $this->authorizeTeacherSeries($examSeries, $batchIds);

        $data = $request->validate([
            'exam_ids' => ['nullable', 'array'],
            'exam_ids.*' => ['integer', 'exists:exams,id'],
            'unlock_rules' => ['nullable', 'array'],
            'unlock_rules.*' => ['nullable', 'in:always,after_previous,scheduled_only'],
        ]);

        $allowedExamIds = $this->examQuery($teacher, $batchIds)->pluck('id')->map(fn ($id) => (int) $id)->all();
        $syncData = [];

        foreach (array_values($data['exam_ids'] ?? []) as $index => $examId) {
            if (!in_array((int) $examId, $allowedExamIds, true)) {
                continue;
            }

            $syncData[$examId] = [
                'sort_order' => $index + 1,
                'unlock_rule' => $data['unlock_rules'][$examId] ?? 'always',
            ];
        }

        $examSeries->exams()->sync($syncData);

        return redirect()->route('teacher.series.builder', $examSeries)->with('success', 'Series builder saved successfully.');
    }

    public function materials()
    {
        $teacher = $this->teacher();
        $batchIds = $this->batchIds($teacher);
        $materials = $this->materialQuery($teacher, $batchIds)->paginate(15);

        return view('portal.teacher.materials.index', compact('teacher', 'materials'));
    }

    public function createMaterial()
    {
        $teacher = $this->teacher();
        $batchIds = $this->batchIds($teacher);
        $batches = $this->batchQuery($teacher, $batchIds)->get();
        $subjects = Subject::where('status', 'active')->orderBy('name')->get();
        $classLevels = $batches->pluck('class_level')->filter()->unique()->values();

        return view('portal.teacher.materials.form', compact('teacher', 'batches', 'subjects', 'classLevels'));
    }

    public function storeMaterial(Request $request)
    {
        $teacher = $this->teacher();
        abort_unless($teacher, 403);

        $batchIds = $this->batchIds($teacher);

        $data = $request->validate([
            'batch_id' => ['nullable', 'integer'],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'class_level' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'file_path' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,webp,zip', 'max:10240'],
            'external_link' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        if (empty($data['batch_id']) && empty($data['class_level'])) {
            throw ValidationException::withMessages([
                'batch_id' => 'Please select either assigned batch or class level.',
            ]);
        }

        if (!empty($data['batch_id']) && !in_array((int) $data['batch_id'], array_map('intval', $batchIds), true)) {
            abort(403);
        }

        if ($request->hasFile('file_path')) {
            $data['file_path'] = $request->file('file_path')->store('study-materials', 'public');
        }

        $data['teacher_id'] = $teacher->id;

        StudyMaterial::create($data);

        return redirect()
            ->route('teacher.materials.index')
            ->with('success', 'Study material added successfully.');
    }

    public function attendance(Request $request)
    {
        $teacher = $this->teacher();
        $batchIds = $this->batchIds($teacher);
        $batches = $this->batchQuery($teacher, $batchIds)->get();
        $selectedDate = $request->get('attendance_date', now()->format('Y-m-d'));
        $selectedBatchId = $request->get('batch_id') ?: $batches->first()?->id;

        if ($selectedBatchId && !in_array((int) $selectedBatchId, array_map('intval', $batchIds), true)) {
            abort(403);
        }

        $selectedBatch = $selectedBatchId
            ? Batch::with('students.course')->whereIn('id', $batchIds)->find($selectedBatchId)
            : null;

        $students = $selectedBatch
            ? $selectedBatch->students()
                ->where('students.status', 'active')
                ->orderBy('students.name')
                ->get()
            : collect();

        $attendanceRecords = StudentAttendance::whereDate('attendance_date', $selectedDate)
            ->when($selectedBatchId, fn ($q) => $q->where('batch_id', $selectedBatchId))
            ->get()
            ->keyBy('student_id');

        $attendance = $this->attendanceQuery($teacher, $batchIds)->take(15)->get();

        return view('portal.teacher.attendance.index', compact(
            'teacher',
            'batches',
            'students',
            'attendanceRecords',
            'selectedDate',
            'selectedBatchId',
            'selectedBatch',
            'attendance'
        ));
    }

    public function storeAttendance(Request $request)
    {
        $teacher = $this->teacher();
        abort_unless($teacher, 403);

        $batchIds = $this->batchIds($teacher);

        $data = $request->validate([
            'attendance_date' => ['required', 'date'],
            'batch_id' => ['required', 'integer'],
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', 'in:present,absent,late,leave'],
            'attendance.*.note' => ['nullable', 'string'],
        ]);

        if (!in_array((int) $data['batch_id'], array_map('intval', $batchIds), true)) {
            abort(403);
        }

        $studentIds = Batch::findOrFail($data['batch_id'])
            ->students()
            ->pluck('students.id')
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach ($data['attendance'] as $studentId => $row) {
            if (!in_array((int) $studentId, $studentIds, true)) {
                continue;
            }

            StudentAttendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'batch_id' => $data['batch_id'],
                    'attendance_date' => $data['attendance_date'],
                ],
                [
                    'status' => $row['status'],
                    'note' => $row['note'] ?? null,
                ]
            );
        }

        return redirect()
            ->route('teacher.attendance.index', [
                'attendance_date' => $data['attendance_date'],
                'batch_id' => $data['batch_id'],
            ])
            ->with('success', 'Attendance saved successfully.');
    }

    public function classes()
    {
        $teacher = $this->teacher();
        $batchIds = $this->batchIds($teacher);
        $classes = $this->classQuery($teacher, $batchIds)->paginate(15);

        return view('portal.teacher.classes.index', compact('teacher', 'classes'));
    }

    public function questions()
    {
        $teacher = $this->teacher();
        $questions = Question::with('subject')
            ->where(function ($q) use ($teacher) {
                $q->whereNull('teacher_id')->orWhere('teacher_id', $teacher?->id);
            })
            ->latest()
            ->paginate(20);

        return view('portal.teacher.questions.index', compact('teacher', 'questions'));
    }

    public function createQuestion()
    {
        return view('portal.teacher.questions.form', [
            'question' => new Question([
                'question_type' => 'mcq',
                'marks' => 1,
                'difficulty' => 'medium',
                'status' => 'active',
            ]),
            'subjects' => Subject::orderBy('name')->get(),
        ]);
    }

    public function storeQuestion(Request $request)
    {
        $teacher = $this->teacher();
        $data = $this->validateQuestion($request);
        $data['teacher_id'] = $teacher?->id;

        Question::create($data);

        return redirect()
            ->route('teacher.questions.index')
            ->with('success', 'Question added successfully.');
    }

    public function importQuestions()
    {
        return view('portal.teacher.questions.import');
    }

    public function downloadQuestionImportSample()
    {
        $headers = [
            'question_type',
            'question',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'correct_answer',
            'marks',
            'difficulty',
            'label',
            'subject',
            'class_level',
            'topic',
            'source',
            'tags',
            'explanation',
            'status',
        ];

        $rows = [
            ['mcq', '2 + 2 = ?', '3', '4', '5', '6', 'B', '1', 'easy', 'Arithmetic Easy', 'Math', 'Class 10', 'Numbers', 'NCERT', 'basic,calculation', '2 + 2 equals 4.', 'active'],
            ['numeric', 'Square root of 49', '', '', '', '', '7', '1', 'medium', 'Concept', 'Math', 'Class 10', 'Squares', 'Module 1', 'numeric', 'The square root of 49 is 7.', 'active'],
            ['short', 'Define photosynthesis.', '', '', '', '', 'Process by which green plants prepare food using sunlight.', '2', 'medium', 'Theory', 'Science', 'Class 10', 'Life Processes', 'Textbook', 'biology,theory', 'Expected answer may vary.', 'active'],
        ];

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="question-import-sample.csv"',
        ]);
    }

    public function storeQuestionImport(Request $request)
    {
        $teacher = $this->teacher();
        abort_unless($teacher, 403);

        $request->validate([
            'question_file' => ['required', 'file', 'mimes:csv,txt,xlsx', 'max:5120'],
        ]);

        $rows = $this->parseQuestionImportFile($request->file('question_file')->getRealPath(), $request->file('question_file')->getClientOriginalExtension());
        $created = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            try {
                $data = $this->mapQuestionImportRow($row);
                $data['teacher_id'] = $teacher->id;
                Question::create($data);
                $created++;
            } catch (\Throwable $exception) {
                $errors[] = 'Row ' . ($index + 2) . ': ' . $exception->getMessage();
            }
        }

        return redirect()
            ->route('teacher.questions.index')
            ->with('success', $created . ' questions imported successfully.')
            ->with('import_errors', array_slice($errors, 0, 10));
    }

    public function profile()
    {
        $teacher = $this->teacher();
        $batches = $teacher?->batches()->with(['course', 'subject'])->get() ?? collect();

        return view('portal.teacher.profile.index', compact('teacher', 'batches'));
    }

    private function batchIds(?Teacher $teacher): array
    {
        return $teacher?->batches()->pluck('batches.id')->all() ?? [];
    }

    private function batchQuery(?Teacher $teacher, array $batchIds)
    {
        return Batch::with(['course', 'subject', 'teacher'])
            ->withCount('students')
            ->when(!$teacher, fn ($q) => $q->whereRaw('1 = 0'))
            ->whereIn('id', $batchIds)
            ->orderBy('name');
    }

    private function studentQuery(?Teacher $teacher, array $batchIds)
    {
        return Student::with('course')
            ->when(!$teacher, fn ($q) => $q->whereRaw('1 = 0'))
            ->whereHas('batches', fn ($q) => $q->whereIn('batches.id', $batchIds))
            ->latest();
    }

    private function examQuery(?Teacher $teacher, array $batchIds)
    {
        return Exam::with(['batch', 'subject'])
            ->when(!$teacher, fn ($q) => $q->whereRaw('1 = 0'))
            ->whereIn('status', ['draft', 'scheduled', 'completed'])
            ->where(function ($q) use ($teacher, $batchIds) {
                $q->where('teacher_id', $teacher?->id)
                    ->orWhere(function ($legacy) use ($batchIds) {
                        $legacy->whereNull('teacher_id')
                            ->where(function ($scope) use ($batchIds) {
                                $scope->whereNull('batch_id')->orWhereIn('batch_id', $batchIds);
                            });
                    });
            })
            ->latest();
    }

    private function materialQuery(?Teacher $teacher, array $batchIds)
    {
        return StudyMaterial::with(['batch', 'subject'])
            ->when(!$teacher, fn ($q) => $q->whereRaw('1 = 0'))
            ->where(function ($q) use ($teacher, $batchIds) {
                $q->where('teacher_id', $teacher?->id)
                    ->orWhereIn('batch_id', $batchIds);
            })
            ->latest();
    }

    private function attendanceQuery(?Teacher $teacher, array $batchIds)
    {
        return StudentAttendance::with(['student', 'batch'])
            ->when(!$teacher, fn ($q) => $q->whereRaw('1 = 0'))
            ->whereIn('batch_id', $batchIds)
            ->latest('attendance_date');
    }

    private function classQuery(?Teacher $teacher, array $batchIds)
    {
        return OnlineClass::with(['batch', 'subject', 'teacher'])
            ->when(!$teacher, fn ($q) => $q->whereRaw('1 = 0'))
            ->where(function ($q) use ($teacher, $batchIds) {
                $q->where('teacher_id', $teacher?->id)
                    ->orWhereIn('batch_id', $batchIds);
            })
            ->latest('class_date');
    }

    private function examFormData(?Teacher $teacher, Exam $exam, array $batchIds): array
    {
        return [
            'teacher' => $teacher,
            'exam' => $exam,
            'batches' => $this->batchQuery($teacher, $batchIds)->get(),
            'subjects' => Subject::where('status', 'active')->orderBy('name')->get(),
            'questions' => Question::where('status', 'active')->orderBy('question')->get(),
        ];
    }

    private function seriesFormData(?Teacher $teacher, ExamSeries $examSeries, array $batchIds): array
    {
        return [
            'teacher' => $teacher,
            'examSeries' => $examSeries,
            'batches' => $this->batchQuery($teacher, $batchIds)->get(),
        ];
    }

    private function validateExam(Request $request, array $batchIds, ?int $ignoreId = null): array
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

        if (!empty($data['batch_id']) && !in_array((int) $data['batch_id'], array_map('intval', $batchIds), true)) {
            abort(403);
        }

        $data['price'] = (float) ($data['price'] ?? 0);
        $data['negative_marks'] = (float) ($data['negative_marks'] ?? 0);
        $data['show_result_immediately'] = (bool) ($data['show_result_immediately'] ?? false);

        if (($data['access_type'] ?? 'free') === 'free') {
            $data['price'] = 0;
        }

        return $data;
    }

    private function validateSeries(Request $request, array $batchIds, ?int $ignoreId = null): array
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

        if (!empty($data['batch_id']) && !in_array((int) $data['batch_id'], array_map('intval', $batchIds), true)) {
            abort(403);
        }

        $data['price'] = (float) ($data['price'] ?? 0);
        if (($data['access_type'] ?? 'free') === 'free') {
            $data['price'] = 0;
        }

        return $data;
    }

    private function syncExamQuestions(Exam $exam, array $questionIds, array $marks = []): void
    {
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

        if ($marks) {
            $exam->update(['total_marks' => $totalMarks]);
        }
    }

    private function authorizeTeacherExam(Exam $exam, array $batchIds): void
    {
        $userTeacher = $this->teacher();

        abort_unless(
            ($exam->teacher_id && $userTeacher && (int) $exam->teacher_id === (int) $userTeacher->id)
            || (!$exam->teacher_id && (!$exam->batch_id || in_array((int) $exam->batch_id, array_map('intval', $batchIds), true))),
            403
        );
    }

    private function authorizeTeacherSeries(ExamSeries $examSeries, array $batchIds): void
    {
        $userTeacher = $this->teacher();

        abort_unless(
            ($examSeries->teacher_id && $userTeacher && (int) $examSeries->teacher_id === (int) $userTeacher->id)
            || (!$examSeries->teacher_id && (!$examSeries->batch_id || in_array((int) $examSeries->batch_id, array_map('intval', $batchIds), true))),
            403
        );
    }

    private function validateQuestion(Request $request): array
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

    private function autoSelectQuestions(?Teacher $teacher, array $data): array
    {
        $counts = [
            'easy' => (int) ($data['auto_easy'] ?? 0),
            'medium' => (int) ($data['auto_medium'] ?? 0),
            'hard' => (int) ($data['auto_hard'] ?? 0),
            'advanced' => (int) ($data['auto_advanced'] ?? 0),
        ];

        $questionIds = [];
        $marks = [];
        $defaultMarks = (float) ($data['auto_marks'] ?? 1);

        foreach ($counts as $difficulty => $count) {
            if ($count <= 0) {
                continue;
            }

            $selected = Question::where('status', 'active')
                ->where('difficulty', $difficulty)
                ->where(function ($q) use ($teacher) {
                    $q->whereNull('teacher_id')->orWhere('teacher_id', $teacher?->id);
                })
                ->when(!empty($data['auto_label']), fn ($q) => $q->where('label', $data['auto_label']))
                ->when(!empty($data['auto_subject_id']), fn ($q) => $q->where('subject_id', $data['auto_subject_id']))
                ->inRandomOrder()
                ->limit($count)
                ->get();

            foreach ($selected as $question) {
                $questionIds[] = $question->id;
                $marks[$question->id] = $defaultMarks > 0 ? $defaultMarks : (float) ($question->marks ?: 1);
            }
        }

        return [$questionIds, $marks];
    }

    private function parseQuestionImportFile(string $path, string $extension): array
    {
        $extension = strtolower($extension);

        if ($extension === 'xlsx') {
            if (!class_exists(ZipArchive::class)) {
                throw ValidationException::withMessages([
                    'question_file' => 'XLSX upload requires PHP ZipArchive. Please upload CSV if ZipArchive is not enabled.',
                ]);
            }

            return $this->parseXlsxRows($path);
        }

        return $this->parseCsvRows($path);
    }

    private function parseCsvRows(string $path): array
    {
        $handle = fopen($path, 'r');
        if (!$handle) {
            return [];
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            return [];
        }

        $headers = array_map(fn ($header) => $this->normalizeImportHeader($header), $headers);
        $rows = [];

        while (($line = fgetcsv($handle)) !== false) {
            if (!array_filter($line, fn ($value) => trim((string) $value) !== '')) {
                continue;
            }

            $rows[] = array_combine($headers, array_pad($line, count($headers), null));
        }

        fclose($handle);

        return $rows;
    }

    private function parseXlsxRows(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return [];
        }

        $sharedStrings = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml) {
            $xml = simplexml_load_string($sharedXml);
            foreach ($xml->si ?? [] as $item) {
                $parts = [];
                if (isset($item->t)) {
                    $parts[] = (string) $item->t;
                }
                foreach ($item->r ?? [] as $run) {
                    $parts[] = (string) $run->t;
                }
                $sharedStrings[] = implode('', $parts);
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if (!$sheetXml) {
            return [];
        }

        $sheet = simplexml_load_string($sheetXml);
        $table = [];

        foreach ($sheet->sheetData->row ?? [] as $row) {
            $line = [];
            foreach ($row->c ?? [] as $cell) {
                $ref = (string) $cell['r'];
                $column = preg_replace('/\d+/', '', $ref);
                $index = $this->columnNameToIndex($column);
                $value = (string) ($cell->v ?? '');

                if ((string) $cell['t'] === 's') {
                    $value = $sharedStrings[(int) $value] ?? '';
                }

                $line[$index] = $value;
            }

            if ($line) {
                ksort($line);
                $table[] = array_values(array_replace(array_fill(0, max(array_keys($line)) + 1, ''), $line));
            }
        }

        if (!$table) {
            return [];
        }

        $headers = array_map(fn ($header) => $this->normalizeImportHeader($header), array_shift($table));
        $rows = [];

        foreach ($table as $line) {
            if (!array_filter($line, fn ($value) => trim((string) $value) !== '')) {
                continue;
            }

            $rows[] = array_combine($headers, array_pad($line, count($headers), null));
        }

        return $rows;
    }

    private function mapQuestionImportRow(array $row): array
    {
        $type = strtolower(trim((string) ($row['question_type'] ?? $row['type'] ?? 'mcq')));
        $difficulty = strtolower(trim((string) ($row['difficulty'] ?? 'medium')));
        $status = strtolower(trim((string) ($row['status'] ?? 'active')));
        $correctAnswer = trim((string) ($row['correct_answer'] ?? $row['correct_option'] ?? ''));

        if (in_array(strtolower($correctAnswer), ['a', 'option a'], true)) {
            $correctAnswer = 'option_a';
        } elseif (in_array(strtolower($correctAnswer), ['b', 'option b'], true)) {
            $correctAnswer = 'option_b';
        } elseif (in_array(strtolower($correctAnswer), ['c', 'option c'], true)) {
            $correctAnswer = 'option_c';
        } elseif (in_array(strtolower($correctAnswer), ['d', 'option d'], true)) {
            $correctAnswer = 'option_d';
        }

        if (!in_array($type, ['mcq', 'short', 'long', 'numeric'], true)) {
            $type = 'mcq';
        }

        if (!in_array($difficulty, ['easy', 'medium', 'hard', 'advanced'], true)) {
            $difficulty = 'medium';
        }

        if (!in_array($status, ['active', 'inactive'], true)) {
            $status = 'active';
        }

        $subjectId = $row['subject_id'] ?? null;
        if (!$subjectId && !empty($row['subject'])) {
            $subjectId = Subject::where('name', trim((string) $row['subject']))->value('id');
        }

        $data = [
            'subject_id' => $subjectId ?: null,
            'class_level' => $row['class_level'] ?? null,
            'label' => $row['label'] ?? null,
            'topic' => $row['topic'] ?? null,
            'source' => $row['source'] ?? null,
            'tags' => $row['tags'] ?? null,
            'question_type' => $type,
            'question' => trim((string) ($row['question'] ?? '')),
            'option_a' => $row['option_a'] ?? null,
            'option_b' => $row['option_b'] ?? null,
            'option_c' => $row['option_c'] ?? null,
            'option_d' => $row['option_d'] ?? null,
            'correct_answer' => $correctAnswer ?: null,
            'marks' => is_numeric($row['marks'] ?? null) ? (float) $row['marks'] : 1,
            'difficulty' => $difficulty,
            'explanation' => $row['explanation'] ?? null,
            'status' => $status,
        ];

        validator($data, [
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'question' => ['required', 'string'],
            'option_a' => ['required_if:question_type,mcq', 'nullable', 'string'],
            'option_b' => ['required_if:question_type,mcq', 'nullable', 'string'],
            'correct_answer' => ['required_if:question_type,mcq', 'nullable', 'string'],
            'marks' => ['required', 'numeric', 'min:0'],
        ])->validate();

        if ($type === 'mcq' && !in_array($data['correct_answer'], ['option_a', 'option_b', 'option_c', 'option_d'], true)) {
            throw new \InvalidArgumentException('MCQ correct_answer must be A/B/C/D or option_a/option_b/option_c/option_d.');
        }

        if ($type !== 'mcq') {
            $data['option_a'] = null;
            $data['option_b'] = null;
            $data['option_c'] = null;
            $data['option_d'] = null;
        }

        return $data;
    }

    private function normalizeImportHeader(?string $header): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '_', (string) $header), '_'));
    }

    private function columnNameToIndex(string $column): int
    {
        $index = 0;
        foreach (str_split($column) as $letter) {
            $index = $index * 26 + (ord(strtoupper($letter)) - 64);
        }

        return max(0, $index - 1);
    }

    private function teacher(): ?Teacher
    {
        $user = Auth::user();

        if ($user->teacher_id) {
            return Teacher::with('batches')->find($user->teacher_id);
        }

        return Teacher::with('batches')
            ->where(function ($query) use ($user) {
                $query->where('email', $user->email);

                if ($user->phone) {
                    $query->orWhere('phone', $user->phone);
                }
            })
            ->first();
    }
}
