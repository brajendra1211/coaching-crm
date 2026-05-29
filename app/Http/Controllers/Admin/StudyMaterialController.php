<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\StudyMaterial;
use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudyMaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = StudyMaterial::with(['batch', 'teacher', 'subject'])->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('type', 'like', '%' . $request->search . '%')
                    ->orWhere('external_link', 'like', '%' . $request->search . '%');
            });
        }

        $items = $query->paginate(15)->withQueryString();

        return view('admin.academics.index', [
            'title' => 'Study Materials',
            'description' => 'Manage notes, PDFs, videos, links and study resources.',
            'items' => $items,
            'routePrefix' => 'study-materials',
            'columns' => [
                'title' => 'Title',
                'type' => 'Type',
                'external_link' => 'External Link',
                'status' => 'Status',
            ],
        ]);
    }

    public function create()
    {
        return view('admin.academics.form', [
            'title' => 'Add Study Material',
            'routePrefix' => 'study-materials',
            'item' => new StudyMaterial(['status' => 'active']),
            'fields' => $this->fields(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('file_path')) {
            $data['file_path'] = $request->file('file_path')->store('study-materials', 'public');
        }

        StudyMaterial::create($data);

        return redirect()
            ->route('admin.study-materials.index')
            ->with('success', 'Study material added successfully.');
    }

    public function edit(StudyMaterial $studyMaterial)
    {
        return view('admin.academics.form', [
            'title' => 'Edit Study Material',
            'routePrefix' => 'study-materials',
            'item' => $studyMaterial,
            'fields' => $this->fields(),
        ]);
    }

    public function update(Request $request, StudyMaterial $studyMaterial)
    {
        $data = $this->validateData($request);

        $data['file_path'] = $studyMaterial->file_path;

        if ($request->hasFile('file_path')) {
            if ($studyMaterial->file_path && Storage::disk('public')->exists($studyMaterial->file_path)) {
                Storage::disk('public')->delete($studyMaterial->file_path);
            }

            $data['file_path'] = $request->file('file_path')->store('study-materials', 'public');
        }

        $studyMaterial->update($data);

        return redirect()
            ->route('admin.study-materials.index')
            ->with('success', 'Study material updated successfully.');
    }

    public function destroy(StudyMaterial $studyMaterial)
    {
        if ($studyMaterial->file_path && Storage::disk('public')->exists($studyMaterial->file_path)) {
            Storage::disk('public')->delete($studyMaterial->file_path);
        }

        $studyMaterial->delete();

        return redirect()
            ->route('admin.study-materials.index')
            ->with('success', 'Study material deleted successfully.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'batch_id' => ['nullable', 'integer'],
            'teacher_id' => ['nullable', 'integer'],
            'subject_id' => ['nullable', 'integer'],
            'class_level' => ['nullable', 'string', 'max:255'],

            'title' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],

            'file_path' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,webp,zip', 'max:10240'],
            'external_link' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],

            'status' => ['required', 'in:active,inactive'],
        ]);
    }

    private function fields(): array
    {
        $batches = Batch::orderBy('name')->pluck('name', 'id')->toArray();
        $teachers = Teacher::orderBy('name')->pluck('name', 'id')->toArray();
        $subjects = Subject::orderBy('name')->pluck('name', 'id')->toArray();

        return [
            ['name' => 'title', 'label' => 'Material Title', 'type' => 'text', 'required' => true],
            ['name' => 'type', 'label' => 'Type', 'type' => 'select', 'options' => [
                'PDF' => 'PDF',
                'Notes' => 'Notes',
                'Video' => 'Video',
                'Assignment' => 'Assignment',
                'Question Paper' => 'Question Paper',
                'Other' => 'Other',
            ]],
            ['name' => 'batch_id', 'label' => 'Batch', 'type' => 'select', 'options' => $batches],
            ['name' => 'teacher_id', 'label' => 'Teacher', 'type' => 'select', 'options' => $teachers],
            ['name' => 'subject_id', 'label' => 'Subject', 'type' => 'select', 'options' => $subjects],
            ['name' => 'class_level', 'label' => 'Class / Level', 'type' => 'text'],
            ['name' => 'file_path', 'label' => 'Upload File', 'type' => 'file'],
            ['name' => 'external_link', 'label' => 'External Link / YouTube Link', 'type' => 'textarea'],
            ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => [
                'active' => 'Active',
                'inactive' => 'Inactive',
            ]],
        ];
    }
}
