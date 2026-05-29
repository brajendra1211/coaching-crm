<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::query()->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('code', 'like', '%' . $request->search . '%')
                ->orWhere('class_level', 'like', '%' . $request->search . '%');
        }

        $items = $query->paginate(15)->withQueryString();

        return view('admin.academics.index', [
            'title' => 'Subjects',
            'description' => 'Manage academic subjects and class levels.',
            'items' => $items,
            'routePrefix' => 'subjects',
            'columns' => [
                'name' => 'Subject',
                'code' => 'Code',
                'class_level' => 'Class / Level',
                'status' => 'Status',
            ],
        ]);
    }

    public function create()
    {
        return view('admin.academics.form', [
            'title' => 'Add Subject',
            'routePrefix' => 'subjects',
            'item' => new Subject(['status' => 'active']),
            'fields' => $this->fields(),
        ]);
    }

    public function store(Request $request)
    {
        Subject::create($this->validateData($request));

        return redirect()->route('admin.subjects.index')->with('success', 'Subject added successfully.');
    }

    public function edit(Subject $subject)
    {
        return view('admin.academics.form', [
            'title' => 'Edit Subject',
            'routePrefix' => 'subjects',
            'item' => $subject,
            'fields' => $this->fields(),
        ]);
    }

    public function update(Request $request, Subject $subject)
    {
        $subject->update($this->validateData($request, $subject->id));

        return redirect()->route('admin.subjects.index')->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('admin.subjects.index')->with('success', 'Subject deleted successfully.');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        $codeRule = 'unique:subjects,code';

        if ($ignoreId) {
            $codeRule .= ',' . $ignoreId;
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:100', $codeRule],
            'class_level' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }

    private function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Subject Name', 'type' => 'text', 'required' => true],
            ['name' => 'code', 'label' => 'Subject Code', 'type' => 'text'],
            ['name' => 'class_level', 'label' => 'Class / Level', 'type' => 'text'],
            ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
        ];
    }
}