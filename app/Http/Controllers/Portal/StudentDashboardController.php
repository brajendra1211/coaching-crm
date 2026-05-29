<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CoachingSetting;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamResult;
use App\Models\FeePayment;
use App\Models\OnlineClass;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentFeeAssignment;
use App\Models\StudyMaterial;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $student = $this->student();
        $batchIds = $this->batchIds($student);
        $feeAssignments = $this->feeAssignments($student)->get();
        $payments = $this->paymentQuery($student)->take(8)->get();
        $results = $this->resultQuery($student)->take(8)->get();
        $attendance = $this->attendanceQuery($student)->take(10)->get();
        $materials = $this->materialQuery($student, $batchIds)->take(8)->get();
        $exams = $this->examQuery($student, $batchIds)->take(8)->get();
        $classes = $this->classQuery($student, $batchIds)->take(5)->get();
        $attempts = $student
            ? ExamAttempt::with('exam')->where('student_id', $student->id)->latest()->take(5)->get()
            : collect();

        return view('portal.student.dashboard', [
            'student' => $student,
            'batches' => $student?->batches()->get() ?? collect(),
            'feeAssignments' => $feeAssignments,
            'payments' => $payments,
            'results' => $results,
            'attendance' => $attendance,
            'materials' => $materials,
            'exams' => $exams,
            'classes' => $classes,
            'attempts' => $attempts,
            'totalPaid' => $feeAssignments->sum('paid_amount'),
            'totalPending' => $feeAssignments->sum('balance_amount'),
        ]);
    }

    public function exams()
    {
        $student = $this->student();
        $batchIds = $this->batchIds($student);
        $exams = $this->examQuery($student, $batchIds)->paginate(15);
        $attemptsByExam = $student
            ? ExamAttempt::where('student_id', $student->id)->get()->groupBy('exam_id')
            : collect();

        return view('portal.student.exams.index', compact('student', 'exams', 'attemptsByExam'));
    }

    public function results()
    {
        $student = $this->student();
        $results = $this->resultQuery($student)->paginate(15);
        $attempts = $student
            ? ExamAttempt::with('exam.subject')->where('student_id', $student->id)->latest()->paginate(15, ['*'], 'attempt_page')
            : collect();

        return view('portal.student.results.index', compact('student', 'results', 'attempts'));
    }

    public function fees()
    {
        $student = $this->student();
        $feeAssignments = $this->feeAssignments($student)->get();
        $payments = $this->paymentQuery($student)->paginate(15);

        return view('portal.student.fees.index', [
            'student' => $student,
            'feeAssignments' => $feeAssignments,
            'payments' => $payments,
            'totalPayable' => $feeAssignments->sum('total_amount'),
            'totalPaid' => $feeAssignments->sum('paid_amount'),
            'totalPending' => $feeAssignments->sum('balance_amount'),
        ]);
    }

    public function attendance()
    {
        $student = $this->student();
        $attendance = $this->attendanceQuery($student)->paginate(25);
        $present = $student ? StudentAttendance::where('student_id', $student->id)->where('status', 'present')->count() : 0;
        $absent = $student ? StudentAttendance::where('student_id', $student->id)->where('status', 'absent')->count() : 0;
        $total = $student ? StudentAttendance::where('student_id', $student->id)->count() : 0;

        return view('portal.student.attendance.index', compact('student', 'attendance', 'present', 'absent', 'total'));
    }

    public function materials()
    {
        $student = $this->student();
        $batchIds = $this->batchIds($student);
        $materials = $this->materialQuery($student, $batchIds)->paginate(15);

        return view('portal.student.materials.index', compact('student', 'materials'));
    }

    public function classes()
    {
        $student = $this->student();
        $batchIds = $this->batchIds($student);
        $classes = $this->classQuery($student, $batchIds)->paginate(15);

        return view('portal.student.classes.index', compact('student', 'classes'));
    }

    public function profile()
    {
        $student = $this->student();
        $batches = $student?->batches()->with(['course', 'subject', 'teacher'])->get() ?? collect();

        return view('portal.student.profile.index', compact('student', 'batches'));
    }

    public function certificates()
    {
        $student = $this->student();
        $certificates = Certificate::query()
            ->when(!$student, fn ($q) => $q->whereRaw('1 = 0'))
            ->when($student, fn ($q) => $q->where('student_id', $student->id))
            ->where('status', 'active')
            ->latest('issue_date')
            ->paginate(15);

        return view('portal.student.certificates.index', compact('student', 'certificates'));
    }

    public function certificatePdf(Certificate $certificate)
    {
        $student = $this->student();
        abort_unless($student && (int) $certificate->student_id === (int) $student->id && $certificate->status === 'active', 403);

        $setting = CoachingSetting::current();

        return Pdf::setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 120,
            ])
            ->loadView('admin.certificates.pdf', [
                'certificate' => $certificate->load('student'),
                'setting' => $setting,
                'template' => $certificate->template ?: 'premium',
                'logoDataUri' => null,
            ])
            ->setPaper('A4', 'landscape')
            ->download('CERTIFICATE-' . $certificate->certificate_no . '.pdf');
    }

    private function batchIds(?Student $student): array
    {
        return $student?->batches()->pluck('batches.id')->all() ?? [];
    }

    private function examQuery(?Student $student, array $batchIds)
    {
        return Exam::with(['batch', 'subject'])
            ->when(!$student, fn ($q) => $q->whereRaw('1 = 0'))
            ->whereIn('status', ['scheduled', 'completed'])
            ->where(function ($q) use ($batchIds) {
                $q->whereNull('batch_id')->orWhereIn('batch_id', $batchIds);
            })
            ->orderByDesc('exam_date');
    }

    private function resultQuery(?Student $student)
    {
        return ExamResult::with('exam.subject')
            ->when(!$student, fn ($q) => $q->whereRaw('1 = 0'))
            ->when($student, fn ($q) => $q->where('student_id', $student->id))
            ->latest();
    }

    private function feeAssignments(?Student $student)
    {
        return StudentFeeAssignment::with(['batch', 'feePlan'])
            ->when(!$student, fn ($q) => $q->whereRaw('1 = 0'))
            ->when($student, fn ($q) => $q->where('student_id', $student->id))
            ->latest();
    }

    private function paymentQuery(?Student $student)
    {
        return FeePayment::with('batch')
            ->when(!$student, fn ($q) => $q->whereRaw('1 = 0'))
            ->when($student, fn ($q) => $q->where('student_id', $student->id))
            ->latest('payment_date');
    }

    private function attendanceQuery(?Student $student)
    {
        return StudentAttendance::with('batch')
            ->when(!$student, fn ($q) => $q->whereRaw('1 = 0'))
            ->when($student, fn ($q) => $q->where('student_id', $student->id))
            ->latest('attendance_date');
    }

    private function materialQuery(?Student $student, array $batchIds)
    {
        return StudyMaterial::with(['batch', 'subject', 'teacher'])
            ->when(!$student, fn ($q) => $q->whereRaw('1 = 0'))
            ->where('status', 'active')
            ->where(function ($q) use ($batchIds, $student) {
                $q->whereNull('batch_id')
                    ->orWhereIn('batch_id', $batchIds);

                if ($student?->class_level) {
                    $q->orWhere('class_level', $student->class_level);
                }
            })
            ->latest();
    }

    private function classQuery(?Student $student, array $batchIds)
    {
        return OnlineClass::with(['batch', 'subject', 'teacher'])
            ->when(!$student, fn ($q) => $q->whereRaw('1 = 0'))
            ->whereIn('status', ['scheduled', 'completed'])
            ->where(function ($q) use ($batchIds) {
                $q->whereNull('batch_id')->orWhereIn('batch_id', $batchIds);
            })
            ->orderByDesc('class_date');
    }

    private function student(): ?Student
    {
        $user = Auth::user();

        if ($user->student_id) {
            return Student::with('batches')->find($user->student_id);
        }

        return Student::with('batches')
            ->where(function ($query) use ($user) {
                $query->where('email', $user->email);

                if ($user->phone) {
                    $query->orWhere('phone', $user->phone);
                }
            })
            ->first();
    }
}
