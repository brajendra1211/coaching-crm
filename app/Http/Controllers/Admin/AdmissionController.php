<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Course;
use App\Models\Student;
use App\Models\StudentParent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Admission::with(['course', 'student'])
            ->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('admission_no', 'like', '%' . $request->search . '%')
                    ->orWhere('student_name', 'like', '%' . $request->search . '%')
                    ->orWhere('student_phone', 'like', '%' . $request->search . '%')
                    ->orWhere('parent_name', 'like', '%' . $request->search . '%')
                    ->orWhere('parent_phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $admissions = $query->paginate(15)->withQueryString();

        return view('admin.admissions.index', compact('admissions'));
    }

    public function create()
    {
        $admission = new Admission([
            'admission_no' => $this->generateAdmissionNo(),
            'admission_date' => now(),
            'status' => 'new',
        ]);

        $courses = Course::where('status', 'active')->orderBy('title')->get();

        return view('admin.admissions.create', compact('admission', 'courses'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($data, &$admission) {
            $data['admission_no'] = $data['admission_no'] ?: $this->generateAdmissionNo();

            $admission = Admission::create($data);

            if ($admission->status === 'admitted') {
                $this->createStudentAndParent($admission);
            }
        });

        return redirect()
            ->route('admin.admissions.index')
            ->with('success', 'Admission created successfully.');
    }

    public function show(Admission $admission)
    {
        $admission->load(['course', 'student.parent']);

        return view('admin.admissions.show', compact('admission'));
    }

    public function edit(Admission $admission)
    {
        $courses = Course::where('status', 'active')->orderBy('title')->get();

        return view('admin.admissions.edit', compact('admission', 'courses'));
    }

    public function update(Request $request, Admission $admission)
    {
        $data = $this->validatedData($request, $admission->id);

        DB::transaction(function () use ($data, $admission) {
            $oldStatus = $admission->status;

            $admission->update($data);

            if ($oldStatus !== 'admitted' && $admission->status === 'admitted') {
                $this->createStudentAndParent($admission);
            }

            if ($admission->student) {
                $this->syncStudentAndParent($admission);
            }
        });

        return redirect()
            ->route('admin.admissions.index')
            ->with('success', 'Admission updated successfully.');
    }

    public function destroy(Admission $admission)
    {
        $admission->delete();

        return redirect()
            ->route('admin.admissions.index')
            ->with('success', 'Admission deleted successfully.');
    }

    private function validatedData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'admission_no' => ['nullable', 'string', 'max:255'],
            'admission_date' => ['nullable', 'date'],

            'course_id' => ['nullable', 'integer'],
            'course_name' => ['nullable', 'string', 'max:255'],
            'class_level' => ['nullable', 'string', 'max:255'],

            'student_name' => ['required', 'string', 'max:255'],
            'student_phone' => ['nullable', 'string', 'max:30'],
            'student_email' => ['nullable', 'email', 'max:255'],
            'dob' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:30'],

            'parent_name' => ['nullable', 'string', 'max:255'],
            'parent_relation' => ['nullable', 'string', 'max:100'],
            'parent_phone' => ['nullable', 'string', 'max:30'],
            'parent_email' => ['nullable', 'email', 'max:255'],

            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'pincode' => ['nullable', 'string', 'max:20'],

            'previous_school' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:120'],

            'registration_fee' => ['nullable', 'numeric', 'min:0'],
            'admission_fee' => ['nullable', 'numeric', 'min:0'],

            'status' => ['required', 'in:new,counselling,document_pending,admitted,rejected,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function generateAdmissionNo(): string
    {
        $prefix = 'ADM-' . date('Y') . '-';

        $last = Admission::where('admission_no', 'like', $prefix . '%')
            ->latest('id')
            ->first();

        $next = 1;

        if ($last) {
            $number = (int) str_replace($prefix, '', $last->admission_no);
            $next = $number + 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    private function generateStudentCode(): string
    {
        $prefix = 'STU-' . date('Y') . '-';

        $last = Student::where('student_code', 'like', $prefix . '%')
            ->latest('id')
            ->first();

        $next = 1;

        if ($last) {
            $number = (int) str_replace($prefix, '', $last->student_code);
            $next = $number + 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    private function createStudentAndParent(Admission $admission): void
    {
        if ($admission->student) {
            return;
        }

        $student = Student::create([
            'admission_id' => $admission->id,
            'course_id' => $admission->course_id,
            'student_code' => $this->generateStudentCode(),
            'name' => $admission->student_name,
            'phone' => $admission->student_phone,
            'email' => $admission->student_email,
            'dob' => $admission->dob,
            'gender' => $admission->gender,
            'class_level' => $admission->class_level,
            'course_name' => $admission->course_name,
            'address' => $admission->address,
            'city' => $admission->city,
            'state' => $admission->state,
            'pincode' => $admission->pincode,
            'status' => 'active',
            'joining_date' => $admission->admission_date ?: now(),
        ]);

        if ($admission->parent_name || $admission->parent_phone) {
            StudentParent::create([
                'student_id' => $student->id,
                'admission_id' => $admission->id,
                'name' => $admission->parent_name ?: 'Parent of ' . $admission->student_name,
                'relation' => $admission->parent_relation,
                'phone' => $admission->parent_phone,
                'email' => $admission->parent_email,
                'address' => $admission->address,
                'status' => 'active',
            ]);
        }
    }

    private function syncStudentAndParent(Admission $admission): void
    {
        $student = $admission->student;

        $student->update([
            'course_id' => $admission->course_id,
            'name' => $admission->student_name,
            'phone' => $admission->student_phone,
            'email' => $admission->student_email,
            'dob' => $admission->dob,
            'gender' => $admission->gender,
            'class_level' => $admission->class_level,
            'course_name' => $admission->course_name,
            'address' => $admission->address,
            'city' => $admission->city,
            'state' => $admission->state,
            'pincode' => $admission->pincode,
        ]);

        if ($student->parent) {
            $student->parent->update([
                'name' => $admission->parent_name ?: $student->parent->name,
                'relation' => $admission->parent_relation,
                'phone' => $admission->parent_phone,
                'email' => $admission->parent_email,
                'address' => $admission->address,
            ]);
        }
    }
}