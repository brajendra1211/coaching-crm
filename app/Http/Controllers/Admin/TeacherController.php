<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::query()->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('specialization', 'like', '%' . $request->search . '%');
            });
        }

        $items = $query->paginate(15)->withQueryString();

        return view('admin.academics.index', [
            'title' => 'Teachers',
            'description' => 'Manage teachers, faculty details and status.',
            'items' => $items,
            'routePrefix' => 'teachers',
            'columns' => [
                'name' => 'Name',
                'phone' => 'Phone',
                'email' => 'Email',
                'specialization' => 'Specialization',
                'status' => 'Status',
            ],
        ]);
    }

    public function create()
    {
        return view('admin.academics.form', [
            'title' => 'Add Teacher',
            'routePrefix' => 'teachers',
            'item' => new Teacher(['status' => 'active']),
            'fields' => $this->fields(),
        ]);
    }

    public function store(Request $request)
    {
        Teacher::create($this->validateData($request));

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher added successfully.');
    }

    public function edit(Teacher $teacher)
    {
        return view('admin.academics.form', [
            'title' => 'Edit Teacher',
            'routePrefix' => 'teachers',
            'item' => $teacher,
            'fields' => $this->fields(),
        ]);
    }

    public function update(Request $request, Teacher $teacher)
    {
        $teacher->update($this->validateData($request));

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher updated successfully.');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher deleted successfully.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'experience' => ['nullable', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'joining_date' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }

    private function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Teacher Name', 'type' => 'text', 'required' => true],
            ['name' => 'phone', 'label' => 'Phone', 'type' => 'text'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
            ['name' => 'qualification', 'label' => 'Qualification', 'type' => 'text'],
            ['name' => 'experience', 'label' => 'Experience', 'type' => 'text'],
            ['name' => 'specialization', 'label' => 'Specialization', 'type' => 'text'],
            ['name' => 'joining_date', 'label' => 'Joining Date', 'type' => 'date'],
            ['name' => 'address', 'label' => 'Address', 'type' => 'textarea'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
        ];
    }
}