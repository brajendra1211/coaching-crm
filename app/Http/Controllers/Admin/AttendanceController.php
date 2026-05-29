<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
public function index(Request $request)
    {
        $selectedDate = $request->get('attendance_date', now()->format('Y-m-d'));
        $selectedBatchId = $request->get('batch_id');

        $batches = Batch::where('status', 'active')
            ->orderBy('name')
            ->get();

        $selectedBatch = null;

        if ($selectedBatchId) {
            $selectedBatch = Batch::with('students.course')->find($selectedBatchId);

            $students = $selectedBatch
                ? $selectedBatch->students()
                    ->where('students.status', 'active')
                    ->orderBy('students.name')
                    ->get()
                : collect();
        } else {
            $students = Student::where('status', 'active')
                ->orderBy('name')
                ->get();
        }

        $attendanceRecords = StudentAttendance::whereDate('attendance_date', $selectedDate)
            ->when($selectedBatchId, function ($q) use ($selectedBatchId) {
                $q->where('batch_id', $selectedBatchId);
            })
            ->get()
            ->keyBy('student_id');

        return view('admin.academics.attendance', compact(
            'batches',
            'students',
            'attendanceRecords',
            'selectedDate',
            'selectedBatchId',
            'selectedBatch'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'attendance_date' => ['required', 'date'],
            'batch_id' => ['nullable', 'integer'],
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', 'in:present,absent,late,leave'],
            'attendance.*.note' => ['nullable', 'string'],
        ]);

        foreach ($data['attendance'] as $studentId => $row) {
            StudentAttendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'batch_id' => $data['batch_id'] ?: null,
                    'attendance_date' => $data['attendance_date'],
                ],
                [
                    'status' => $row['status'],
                    'note' => $row['note'] ?? null,
                ]
            );
        }

        return redirect()
            ->route('admin.attendance.index', [
                'attendance_date' => $data['attendance_date'],
                'batch_id' => $data['batch_id'] ?? null,
            ])
            ->with('success', 'Attendance saved successfully.');
    }
}