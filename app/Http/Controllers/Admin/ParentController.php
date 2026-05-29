<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentParent;
use Illuminate\Http\Request;

class ParentController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentParent::with(['student', 'admission'])
            ->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%')
                    ->orWhere('alternate_phone', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('relation', 'like', '%' . $request->search . '%')
                    ->orWhere('occupation', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $parents = $query->paginate(15)->withQueryString();

        $students = Student::orderBy('name')->get();

        return view('admin.parents.index', compact('parents', 'students'));
    }

    public function create()
    {
        $parent = new StudentParent([
            'relation' => 'Father',
            'status' => 'active',
        ]);

        $students = Student::orderBy('name')->get();

        return view('admin.parents.create', compact('parent', 'students'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        if (!empty($data['student_id'])) {
            $student = Student::find($data['student_id']);

            if ($student) {
                $data['admission_id'] = $student->admission_id;
            }
        }

        StudentParent::create($data);

        return redirect()
            ->route('admin.parents.index')
            ->with('success', 'Parent record created successfully.');
    }

    public function show(StudentParent $parent)
    {
        $parent->load(['student', 'admission']);

        return view('admin.parents.show', compact('parent'));
    }

    public function edit(StudentParent $parent)
    {
        $students = Student::orderBy('name')->get();

        return view('admin.parents.edit', compact('parent', 'students'));
    }

    public function update(Request $request, StudentParent $parent)
    {
        $data = $this->validatedData($request);

        if (!empty($data['student_id'])) {
            $student = Student::find($data['student_id']);

            if ($student) {
                $data['admission_id'] = $student->admission_id;
            }
        }

        $parent->update($data);

        return redirect()
            ->route('admin.parents.index')
            ->with('success', 'Parent record updated successfully.');
    }

    public function destroy(StudentParent $parent)
    {
        $parent->delete();

        return redirect()
            ->route('admin.parents.index')
            ->with('success', 'Parent record deleted successfully.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'student_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'relation' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'alternate_phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }
}