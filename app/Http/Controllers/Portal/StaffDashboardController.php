<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Certificate;
use App\Models\Exam;
use App\Models\FeePayment;
use App\Models\Lead;
use App\Models\Student;
use App\Models\StudentFeeAssignment;
use App\Models\StudyMaterial;

class StaffDashboardController extends Controller
{
    public function index()
    {
        return view('portal.staff.dashboard', [
            'leads' => Lead::latest()->take(8)->get(),
            'admissions' => Admission::with('course')->latest()->take(8)->get(),
            'students' => Student::latest()->take(8)->get(),
            'payments' => FeePayment::with(['student', 'batch'])->latest('payment_date')->take(8)->get(),
            'pendingFees' => StudentFeeAssignment::where('balance_amount', '>', 0)->sum('balance_amount'),
            'todayCollection' => FeePayment::whereDate('payment_date', today())->sum('amount'),
            'leadsCount' => Lead::count(),
            'admissionsCount' => Admission::count(),
            'studentsCount' => Student::count(),
            'examsCount' => Exam::count(),
        ]);
    }

    public function leads()
    {
        $leads = Lead::with('course')->latest()->paginate(20);

        return view('portal.staff.leads.index', compact('leads'));
    }

    public function admissions()
    {
        $admissions = Admission::with('course')->latest()->paginate(20);

        return view('portal.staff.admissions.index', compact('admissions'));
    }

    public function students()
    {
        $students = Student::with('course')->latest()->paginate(20);

        return view('portal.staff.students.index', compact('students'));
    }

    public function fees()
    {
        $assignments = StudentFeeAssignment::with(['student', 'batch'])->latest()->paginate(20);
        $payments = FeePayment::with(['student', 'batch'])->latest('payment_date')->take(12)->get();

        return view('portal.staff.fees.index', [
            'assignments' => $assignments,
            'payments' => $payments,
            'totalPending' => StudentFeeAssignment::where('balance_amount', '>', 0)->sum('balance_amount'),
            'totalPaid' => StudentFeeAssignment::sum('paid_amount'),
        ]);
    }

    public function exams()
    {
        $exams = Exam::with(['batch', 'subject'])->latest()->paginate(20);

        return view('portal.staff.exams.index', compact('exams'));
    }

    public function materials()
    {
        $materials = StudyMaterial::with(['batch', 'subject', 'teacher'])->latest()->paginate(20);

        return view('portal.staff.materials.index', compact('materials'));
    }

    public function certificates()
    {
        $certificates = Certificate::with('student')->latest('issue_date')->paginate(20);

        return view('portal.staff.certificates.index', compact('certificates'));
    }
}
