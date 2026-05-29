<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchFeePlan;
use App\Models\Course;
use App\Models\OnlineClass;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentFeeAssignment;
use App\Models\StudyMaterial;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        $query = Batch::with(['course', 'teacher', 'subject'])
            ->withCount(['students', 'teachers'])
            ->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%')
                    ->orWhere('class_level', 'like', '%' . $request->search . '%')
                    ->orWhere('days', 'like', '%' . $request->search . '%')
                    ->orWhere('room_no', 'like', '%' . $request->search . '%');
            });
        }

        $items = $query->paginate(15)->withQueryString();

        return view('admin.academics.index', [
            'title' => 'Batches',
            'description' => 'Manage batches, timing, teachers, subjects and capacity.',
            'items' => $items,
            'routePrefix' => 'batches',
            'columns' => [
                'name' => 'Batch Name',
                'code' => 'Code',
                'class_level' => 'Class / Level',
                'days' => 'Days',
                'room_no' => 'Room',
                'status' => 'Status',
            ],
        ]);
    }

    public function create()
    {
        return view('admin.academics.form', [
            'title' => 'Add Batch',
            'routePrefix' => 'batches',
            'item' => new Batch(['status' => 'active']),
            'fields' => $this->fields(),
        ]);
    }

    public function store(Request $request)
    {
        Batch::create($this->validateData($request));

        return redirect()
            ->route('admin.batches.index')
            ->with('success', 'Batch created successfully.');
    }

    public function show(Batch $batch)
    {
        $batch->load([
            'course',
            'teacher',
            'subject',
            'students.course',
            'students.parent',
            'teachers',
            'activeFeePlan',
            'feeAssignments',
        ]);

        $subjectsMap = Subject::pluck('name', 'id');

        $onlineClasses = OnlineClass::with(['teacher', 'subject'])
            ->where('batch_id', $batch->id)
            ->latest('class_date')
            ->take(10)
            ->get();

        $studyMaterials = StudyMaterial::with(['teacher', 'subject'])
            ->where('batch_id', $batch->id)
            ->latest()
            ->take(10)
            ->get();

        $attendanceSummary = StudentAttendance::where('batch_id', $batch->id)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $lastAttendanceDate = StudentAttendance::where('batch_id', $batch->id)
            ->max('attendance_date');

        $assignedStudentsCount = $batch->students->count();
        $capacity = (int) ($batch->capacity ?? 0);

        $capacityPercent = 0;

        if ($capacity > 0) {
            $capacityPercent = min(100, round(($assignedStudentsCount / $capacity) * 100));
        }

        $feeSummary = [
            'active_plan' => $batch->activeFeePlan,
            'total_assigned' => StudentFeeAssignment::where('batch_id', $batch->id)
                ->where('status', 'active')
                ->count(),
            'total_due' => StudentFeeAssignment::where('batch_id', $batch->id)
                ->where('status', 'active')
                ->sum('balance_amount'),
            'total_paid' => StudentFeeAssignment::where('batch_id', $batch->id)
                ->sum('paid_amount'),
        ];

        return view('admin.batches.show', compact(
            'batch',
            'subjectsMap',
            'onlineClasses',
            'studyMaterials',
            'attendanceSummary',
            'lastAttendanceDate',
            'assignedStudentsCount',
            'capacity',
            'capacityPercent',
            'feeSummary'
        ));
    }

    public function edit(Batch $batch)
    {
        return view('admin.academics.form', [
            'title' => 'Edit Batch',
            'routePrefix' => 'batches',
            'item' => $batch,
            'fields' => $this->fields(),
        ]);
    }

    public function update(Request $request, Batch $batch)
    {
        $batch->update($this->validateData($request, $batch->id));

        return redirect()
            ->route('admin.batches.index')
            ->with('success', 'Batch updated successfully.');
    }

    public function destroy(Batch $batch)
    {
        DB::transaction(function () use ($batch) {
            $batch->students()->detach();
            $batch->teachers()->detach();

            StudentFeeAssignment::where('batch_id', $batch->id)
                ->where('status', 'active')
                ->update(['status' => 'inactive']);

            $batch->delete();
        });

        return redirect()
            ->route('admin.batches.index')
            ->with('success', 'Batch deleted successfully.');
    }

    public function builder(Batch $batch)
    {
        $batch->load(['course', 'teacher', 'subject', 'students', 'teachers', 'activeFeePlan']);

        $students = Student::with('course')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $teachers = Teacher::where('status', 'active')
            ->orderBy('name')
            ->get();

        $subjects = Subject::where('status', 'active')
            ->orderBy('name')
            ->get();

        $assignedStudentIds = $batch->students
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->toArray();

        $teacherRows = $batch->teachers->map(function ($teacher) {
            return [
                'teacher_id' => $teacher->id,
                'subject_id' => $teacher->pivot->subject_id,
                'role' => $teacher->pivot->role ?: 'assistant',
            ];
        })->values();

        if ($teacherRows->isEmpty() && $batch->teacher_id) {
            $teacherRows = collect([
                [
                    'teacher_id' => $batch->teacher_id,
                    'subject_id' => $batch->subject_id,
                    'role' => 'primary',
                ],
            ]);
        }

        return view('admin.batches.builder', compact(
            'batch',
            'students',
            'teachers',
            'subjects',
            'assignedStudentIds',
            'teacherRows'
        ));
    }

    public function syncStudents(Request $request, Batch $batch)
    {
        $data = $request->validate([
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['integer', 'exists:students,id'],
        ]);

        $studentIds = array_values(array_unique($data['student_ids'] ?? []));

        $capacity = (int) ($batch->capacity ?? 0);

        if ($capacity > 0 && count($studentIds) > $capacity) {
            return back()
                ->withErrors([
                    'student_ids' => 'Batch capacity is only ' . $capacity . ' students. You selected ' . count($studentIds) . ' students.',
                ])
                ->withInput();
        }

        DB::transaction(function () use ($batch, $studentIds) {
            $syncData = [];

            foreach ($studentIds as $studentId) {
                $syncData[$studentId] = [
                    'status' => 'active',
                    'assigned_at' => now()->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $batch->students()->sync($syncData);

            $this->syncStudentFeeAssignments($batch, $studentIds);
        });

        return redirect()
            ->route('admin.batches.builder', $batch)
            ->with('success', 'Students assigned to batch successfully. Fee assignment has been updated automatically.');
    }

    public function syncTeachers(Request $request, Batch $batch)
    {
        $data = $request->validate([
            'teacher_ids' => ['nullable', 'array'],
            'teacher_ids.*' => ['nullable', 'integer', 'exists:teachers,id'],

            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => ['nullable', 'integer', 'exists:subjects,id'],

            'roles' => ['nullable', 'array'],
            'roles.*' => ['nullable', 'in:primary,assistant'],
        ]);

        $teacherIds = $data['teacher_ids'] ?? [];
        $subjectIds = $data['subject_ids'] ?? [];
        $roles = $data['roles'] ?? [];

        $syncData = [];
        $firstTeacherId = null;
        $firstSubjectId = null;

        foreach ($teacherIds as $index => $teacherId) {
            if (!$teacherId) {
                continue;
            }

            $subjectId = $subjectIds[$index] ?? null;
            $role = $roles[$index] ?? 'assistant';

            $syncData[$teacherId] = [
                'subject_id' => $subjectId ?: null,
                'role' => $role,
                'status' => 'active',
                'assigned_at' => now()->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (!$firstTeacherId) {
                $firstTeacherId = $teacherId;
                $firstSubjectId = $subjectId ?: null;
            }
        }

        DB::transaction(function () use ($batch, $syncData, $firstTeacherId, $firstSubjectId) {
            $batch->teachers()->sync($syncData);

            $batch->update([
                'teacher_id' => $firstTeacherId,
                'subject_id' => $firstSubjectId,
            ]);
        });

        return redirect()
            ->route('admin.batches.builder', $batch)
            ->with('success', 'Teachers assigned to batch successfully.');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        $codeRule = 'unique:batches,code';

        if ($ignoreId) {
            $codeRule .= ',' . $ignoreId;
        }

        return $request->validate([
            'course_id' => ['nullable', 'integer'],
            'teacher_id' => ['nullable', 'integer'],
            'subject_id' => ['nullable', 'integer'],

            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:100', $codeRule],
            'class_level' => ['nullable', 'string', 'max:255'],

            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'start_time' => ['nullable'],
            'end_time' => ['nullable'],

            'days' => ['nullable', 'string', 'max:255'],
            'room_no' => ['nullable', 'string', 'max:100'],
            'capacity' => ['nullable', 'integer', 'min:0'],

            'status' => ['required', 'in:active,inactive,completed'],
        ]);
    }

    private function fields(): array
    {
        $courses = Course::orderBy('title')->pluck('title', 'id')->toArray();
        $teachers = Teacher::orderBy('name')->pluck('name', 'id')->toArray();
        $subjects = Subject::orderBy('name')->pluck('name', 'id')->toArray();

        return [
            ['name' => 'name', 'label' => 'Batch Name', 'type' => 'text', 'required' => true],
            ['name' => 'code', 'label' => 'Batch Code', 'type' => 'text'],
            ['name' => 'course_id', 'label' => 'Course', 'type' => 'select', 'options' => $courses],
            ['name' => 'teacher_id', 'label' => 'Default Teacher', 'type' => 'select', 'options' => $teachers],
            ['name' => 'subject_id', 'label' => 'Default Subject', 'type' => 'select', 'options' => $subjects],
            ['name' => 'class_level', 'label' => 'Class / Level', 'type' => 'text'],
            ['name' => 'start_date', 'label' => 'Start Date', 'type' => 'date'],
            ['name' => 'end_date', 'label' => 'End Date', 'type' => 'date'],
            ['name' => 'start_time', 'label' => 'Start Time', 'type' => 'time'],
            ['name' => 'end_time', 'label' => 'End Time', 'type' => 'time'],
            ['name' => 'days', 'label' => 'Days', 'type' => 'text'],
            ['name' => 'room_no', 'label' => 'Room No', 'type' => 'text'],
            ['name' => 'capacity', 'label' => 'Capacity', 'type' => 'number'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => [
                'active' => 'Active',
                'inactive' => 'Inactive',
                'completed' => 'Completed',
            ]],
        ];
    }

    private function syncStudentFeeAssignments(Batch $batch, array $studentIds): void
    {
        $activePlan = BatchFeePlan::where('batch_id', $batch->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        StudentFeeAssignment::where('batch_id', $batch->id)
            ->whereNotIn('student_id', $studentIds ?: [0])
            ->where('status', 'active')
            ->update(['status' => 'inactive']);

        if (!$activePlan) {
            return;
        }

        foreach ($studentIds as $studentId) {
            $this->createOrUpdateStudentFeeAssignment($studentId, $batch->id, $activePlan);
        }
    }

    private function createOrUpdateStudentFeeAssignment(int $studentId, int $batchId, BatchFeePlan $plan): void
    {
        $total = (float) $plan->registration_fee
            + (float) $plan->admission_fee
            + (float) $plan->tuition_fee
            + (float) $plan->exam_fee
            + (float) $plan->material_fee
            + (float) $plan->other_fee;

        $assignment = StudentFeeAssignment::firstOrNew([
            'student_id' => $studentId,
            'batch_id' => $batchId,
            'batch_fee_plan_id' => $plan->id,
        ]);

        $paidAmount = (float) ($assignment->paid_amount ?? 0);
        $discountAmount = (float) ($assignment->discount_amount ?? 0);
        $fineAmount = (float) ($assignment->fine_amount ?? 0);

        $balanceAmount = max(0, ($total + $fineAmount) - ($paidAmount + $discountAmount));

        $assignment->fill([
            'billing_type' => $plan->billing_type,

            'registration_fee' => $plan->registration_fee ?? 0,
            'admission_fee' => $plan->admission_fee ?? 0,
            'tuition_fee' => $plan->tuition_fee ?? 0,
            'exam_fee' => $plan->exam_fee ?? 0,
            'material_fee' => $plan->material_fee ?? 0,
            'other_fee' => $plan->other_fee ?? 0,

            'total_amount' => $total,
            'paid_amount' => $paidAmount,
            'discount_amount' => $discountAmount,
            'fine_amount' => $fineAmount,
            'balance_amount' => $balanceAmount,

            'due_day' => $plan->due_day,
            'next_due_date' => $assignment->next_due_date ?: $this->calculateNextDueDate($plan->due_day),
            'assigned_at' => $assignment->assigned_at ?: now()->format('Y-m-d'),
            'status' => 'active',
        ]);

        $assignment->save();
    }

    private function calculateNextDueDate(?int $dueDay): ?string
    {
        if (!$dueDay) {
            return null;
        }

        $today = now();
        $day = min($dueDay, $today->daysInMonth);

        $date = Carbon::create($today->year, $today->month, $day);

        if ($date->isPast() && !$date->isToday()) {
            $nextMonth = $today->copy()->addMonth();
            $day = min($dueDay, $nextMonth->daysInMonth);

            $date = Carbon::create($nextMonth->year, $nextMonth->month, $day);
        }

        return $date->format('Y-m-d');
    }
}