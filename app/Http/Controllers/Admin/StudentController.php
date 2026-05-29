<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Student;
use App\Models\StudentParent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['admission', 'parent', 'course'])
            ->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('student_code', 'like', '%' . $request->search . '%')
                    ->orWhere('name', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('course_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $students = $query->paginate(15)->withQueryString();

        $courses = Course::orderBy('title')->get();

        return view('admin.students.index', compact('students', 'courses'));
    }

    public function create()
    {
        $student = new Student([
            'student_code' => $this->generateStudentCode(),
            'joining_date' => now(),
            'status' => 'active',
        ]);

        $parent = new StudentParent([
            'relation' => 'Father',
            'status' => 'active',
        ]);

        $courses = Course::where('status', 'active')
            ->orderBy('title')
            ->get();

        return view('admin.students.create', compact('student', 'parent', 'courses'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($request, $data) {
            $data['student_code'] = $data['student_code'] ?: $this->generateStudentCode();

            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('students', 'public');
            }

            $student = Student::create($data);

            if ($request->filled('parent_name') || $request->filled('parent_phone')) {
                StudentParent::create([
                    'student_id' => $student->id,
                    'admission_id' => $student->admission_id,
                    'name' => $request->parent_name ?: 'Parent of ' . $student->name,
                    'relation' => $request->parent_relation,
                    'phone' => $request->parent_phone,
                    'alternate_phone' => $request->parent_alternate_phone,
                    'email' => $request->parent_email,
                    'occupation' => $request->parent_occupation,
                    'address' => $request->parent_address ?: $student->address,
                    'status' => 'active',
                ]);
            }
        });

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student created successfully.');
    }

    public function show(Student $student)
    {
        $student->load(['admission', 'parent', 'course']);

        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $student->load('parent');

        $parent = $student->parent ?: new StudentParent([
            'relation' => 'Father',
            'status' => 'active',
        ]);

        $courses = Course::where('status', 'active')
            ->orderBy('title')
            ->get();

        return view('admin.students.edit', compact('student', 'parent', 'courses'));
    }

    public function update(Request $request, Student $student)
    {
        $data = $this->validatedData($request, $student->id);

        DB::transaction(function () use ($request, $student, $data) {
            $data['photo'] = $student->photo;

            if ($request->has('remove_photo')) {
                $this->deletePhoto($student->photo);
                $data['photo'] = null;
            }

            if ($request->hasFile('photo')) {
                $this->deletePhoto($student->photo);
                $data['photo'] = $request->file('photo')->store('students', 'public');
            }

            $student->update($data);

            if ($request->filled('parent_name') || $request->filled('parent_phone')) {
                StudentParent::updateOrCreate(
                    ['student_id' => $student->id],
                    [
                        'admission_id' => $student->admission_id,
                        'name' => $request->parent_name ?: 'Parent of ' . $student->name,
                        'relation' => $request->parent_relation,
                        'phone' => $request->parent_phone,
                        'alternate_phone' => $request->parent_alternate_phone,
                        'email' => $request->parent_email,
                        'occupation' => $request->parent_occupation,
                        'address' => $request->parent_address ?: $student->address,
                        'status' => 'active',
                    ]
                );
            }
        });

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $this->deletePhoto($student->photo);

        if ($student->parent) {
            $student->parent->delete();
        }

        $student->delete();

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    private function validatedData(Request $request, ?int $ignoreId = null): array
    {
        $studentCodeRule = 'unique:students,student_code';

        if ($ignoreId) {
            $studentCodeRule .= ',' . $ignoreId;
        }

        return $request->validate([
            'student_code' => ['nullable', 'string', 'max:255', $studentCodeRule],
            'course_id' => ['nullable', 'integer'],
            'course_name' => ['nullable', 'string', 'max:255'],

            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'dob' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:30'],

            'class_level' => ['nullable', 'string', 'max:255'],

            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'pincode' => ['nullable', 'string', 'max:20'],

            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            'status' => ['required', 'in:active,inactive,passed_out,left'],
            'joining_date' => ['nullable', 'date'],

            'parent_name' => ['nullable', 'string', 'max:255'],
            'parent_relation' => ['nullable', 'string', 'max:100'],
            'parent_phone' => ['nullable', 'string', 'max:30'],
            'parent_alternate_phone' => ['nullable', 'string', 'max:30'],
            'parent_email' => ['nullable', 'email', 'max:255'],
            'parent_occupation' => ['nullable', 'string', 'max:255'],
            'parent_address' => ['nullable', 'string'],
        ]);
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

    private function deletePhoto(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}