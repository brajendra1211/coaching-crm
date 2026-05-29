<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\OnlineClass;
use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Http\Request;

class OnlineClassController extends Controller
{
    public function index(Request $request)
    {
        $query = OnlineClass::with(['batch', 'teacher', 'subject'])->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('platform', 'like', '%' . $request->search . '%')
                    ->orWhere('meeting_link', 'like', '%' . $request->search . '%');
            });
        }

        $items = $query->paginate(15)->withQueryString();

        return view('admin.academics.index', [
            'title' => 'Online Classes',
            'description' => 'Manage live class schedule, meeting link and platform.',
            'items' => $items,
            'routePrefix' => 'online-classes',
            'columns' => [
                'title' => 'Title',
                'class_date' => 'Date',
                'platform' => 'Platform',
                'status' => 'Status',
            ],
        ]);
    }

    public function create()
    {
        return view('admin.academics.form', [
            'title' => 'Add Online Class',
            'routePrefix' => 'online-classes',
            'item' => new OnlineClass(['status' => 'scheduled']),
            'fields' => $this->fields(),
        ]);
    }

    public function store(Request $request)
    {
        OnlineClass::create($this->validateData($request));

        return redirect()
            ->route('admin.online-classes.index')
            ->with('success', 'Online class added successfully.');
    }

    public function edit(OnlineClass $onlineClass)
    {
        return view('admin.academics.form', [
            'title' => 'Edit Online Class',
            'routePrefix' => 'online-classes',
            'item' => $onlineClass,
            'fields' => $this->fields(),
        ]);
    }

    public function update(Request $request, OnlineClass $onlineClass)
    {
        $onlineClass->update($this->validateData($request));

        return redirect()
            ->route('admin.online-classes.index')
            ->with('success', 'Online class updated successfully.');
    }

    public function destroy(OnlineClass $onlineClass)
    {
        $onlineClass->delete();

        return redirect()
            ->route('admin.online-classes.index')
            ->with('success', 'Online class deleted successfully.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'batch_id' => ['nullable', 'integer'],
            'teacher_id' => ['nullable', 'integer'],
            'subject_id' => ['nullable', 'integer'],

            'title' => ['required', 'string', 'max:255'],
            'class_date' => ['nullable', 'date'],
            'start_time' => ['nullable'],
            'end_time' => ['nullable'],

            'platform' => ['nullable', 'string', 'max:255'],
            'meeting_link' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],

            'status' => ['required', 'in:scheduled,completed,cancelled'],
        ]);
    }

    private function fields(): array
    {
        $batches = Batch::orderBy('name')->pluck('name', 'id')->toArray();
        $teachers = Teacher::orderBy('name')->pluck('name', 'id')->toArray();
        $subjects = Subject::orderBy('name')->pluck('name', 'id')->toArray();

        return [
            ['name' => 'title', 'label' => 'Class Title', 'type' => 'text', 'required' => true],
            ['name' => 'batch_id', 'label' => 'Batch', 'type' => 'select', 'options' => $batches],
            ['name' => 'teacher_id', 'label' => 'Teacher', 'type' => 'select', 'options' => $teachers],
            ['name' => 'subject_id', 'label' => 'Subject', 'type' => 'select', 'options' => $subjects],
            ['name' => 'class_date', 'label' => 'Class Date', 'type' => 'date'],
            ['name' => 'start_time', 'label' => 'Start Time', 'type' => 'time'],
            ['name' => 'end_time', 'label' => 'End Time', 'type' => 'time'],
            ['name' => 'platform', 'label' => 'Platform', 'type' => 'text'],
            ['name' => 'meeting_link', 'label' => 'Meeting Link', 'type' => 'textarea'],
            ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => [
                'scheduled' => 'Scheduled',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ]],
        ];
    }
}