@extends('admin.layouts.app')

@section('title', 'Batch Builder')
@section('page_title', 'Batch Builder')

@section('content')

@php
    $capacity = (int) ($batch->capacity ?? 0);
    $assignedCount = count($assignedStudentIds);
    $capacityPercent = $capacity > 0 ? min(100, round(($assignedCount / max($capacity, 1)) * 100)) : 0;
    $isCapacityFull = $capacity > 0 && $assignedCount >= $capacity;

    $courseOptions = $students
        ->map(fn($student) => $student->course_name ?: optional($student->course)->title)
        ->filter()
        ->unique()
        ->sort()
        ->values();

    $classOptions = $students
        ->pluck('class_level')
        ->filter()
        ->unique()
        ->sort()
        ->values();
@endphp

<style>
    .builder-shell {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 390px;
        gap: 22px;
        align-items: start;
    }

    .builder-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 24px;
        box-shadow: 0 14px 38px rgba(15, 23, 42, .08);
        margin-bottom: 22px;
        overflow: hidden;
    }

    .builder-head {
        padding: 20px 24px;
        border-bottom: 1px solid #e5e7eb;
        background:
            radial-gradient(circle at top right, rgba(37, 99, 235, .10), transparent 28%),
            linear-gradient(135deg, #ffffff, #f8fafc);
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: center;
        flex-wrap: wrap;
    }

    .builder-head h3 {
        margin: 0;
        color: #0f172a;
        font-size: 21px;
        letter-spacing: -.3px;
    }

    .builder-head p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
    }

    .builder-body {
        padding: 22px;
    }

    .builder-alert {
        padding: 13px 15px;
        border-radius: 16px;
        margin-bottom: 16px;
        font-weight: 800;
        line-height: 1.5;
    }

    .builder-alert.success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .builder-alert.error {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .builder-alert.warning {
        background: #fff7ed;
        color: #9a3412;
        border: 1px solid #fed7aa;
    }

    .builder-toolbar {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 170px 160px auto;
        gap: 12px;
        margin-bottom: 16px;
    }

    .builder-toolbar input,
    .builder-toolbar select,
    .teacher-row select {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 15px;
        padding: 13px 14px;
        background: #fff;
        color: #111827;
        outline: none;
        font-family: inherit;
        font-size: 14px;
    }

    .builder-toolbar input:focus,
    .builder-toolbar select:focus,
    .teacher-row select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .10);
    }

    .quick-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .student-status-bar {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }

    .mini-stat {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 14px;
    }

    .mini-stat small {
        display: block;
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        margin-bottom: 6px;
        letter-spacing: .35px;
    }

    .mini-stat strong {
        color: #0f172a;
        font-size: 23px;
        line-height: 1;
    }

    .student-list {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        max-height: 650px;
        overflow-y: auto;
        padding-right: 5px;
    }

    .student-card {
        position: relative;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
        border-radius: 18px;
        padding: 14px;
        cursor: pointer;
        transition: .2s ease;
    }

    .student-card:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
        transform: translateY(-1px);
    }

    .student-card input {
        width: 18px;
        height: 18px;
        margin-top: 3px;
        flex: 0 0 auto;
    }

    .student-card .avatar {
        width: 42px;
        height: 42px;
        border-radius: 15px;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        flex: 0 0 42px;
    }

    .student-card strong {
        display: block;
        color: #0f172a;
        line-height: 1.35;
        font-size: 15px;
    }

    .student-card small {
        display: block;
        color: #64748b;
        margin-top: 3px;
        line-height: 1.45;
        font-size: 12px;
        font-weight: 700;
    }

    .student-card .tag-row {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-top: 8px;
    }

    .soft-tag {
        display: inline-flex;
        padding: 5px 8px;
        border-radius: 999px;
        background: #fff;
        border: 1px solid #dbeafe;
        color: #1d4ed8;
        font-size: 11px;
        font-weight: 900;
    }

    .assigned-ribbon {
        position: absolute;
        right: 10px;
        top: 10px;
        padding: 4px 8px;
        border-radius: 999px;
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
        font-size: 10px;
        font-weight: 900;
    }

    .teacher-row {
        display: grid;
        grid-template-columns: 1.2fr 1fr 135px 44px;
        gap: 10px;
        align-items: center;
        margin-bottom: 12px;
        padding: 12px;
        border-radius: 18px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
    }

    .remove-row {
        width: 44px;
        height: 44px;
        border-radius: 15px;
        border: 0;
        background: #fee2e2;
        color: #991b1b;
        cursor: pointer;
        font-weight: 900;
        font-size: 18px;
    }

    .empty-note {
        text-align: center;
        color: #64748b;
        padding: 34px;
        background: #f8fafc;
        border-radius: 20px;
        border: 1px dashed #cbd5e1;
        line-height: 1.7;
    }

    .summary-card {
        position: sticky;
        top: 92px;
    }

    .summary-hero {
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.25), transparent 30%),
            linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
        padding: 24px;
    }

    .summary-hero h3 {
        margin: 0 0 6px;
        color: #fff;
        font-size: 23px;
        line-height: 1.2;
    }

    .summary-hero p {
        margin: 0;
        color: rgba(255,255,255,.88);
        line-height: 1.55;
        font-size: 13px;
    }

    .summary-body {
        padding: 22px;
    }

    .summary-grid {
        display: grid;
        gap: 12px;
    }

    .summary-box {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 17px;
        padding: 14px;
    }

    .summary-box small {
        display: block;
        color: #64748b;
        font-weight: 900;
        margin-bottom: 6px;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: .35px;
    }

    .summary-box strong {
        color: #111827;
        line-height: 1.4;
    }

    .capacity-bar-wrap {
        margin-top: 11px;
    }

    .capacity-bar {
        height: 11px;
        background: #e5e7eb;
        border-radius: 999px;
        overflow: hidden;
    }

    .capacity-fill {
        height: 100%;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        border-radius: 999px;
        transition: .25s ease;
    }

    .capacity-text {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        margin-top: 8px;
    }

    .action-stack {
        display: grid;
        gap: 10px;
        margin-top: 18px;
    }

    .sticky-save {
        position: sticky;
        bottom: 0;
        background: rgba(255,255,255,.92);
        backdrop-filter: blur(14px);
        border-top: 1px solid #e5e7eb;
        margin: 18px -22px -22px;
        padding: 14px 22px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
    }

    .save-note {
        color: #64748b;
        font-size: 13px;
        font-weight: 800;
    }

    @media(max-width: 1150px) {
        .builder-shell {
            grid-template-columns: 1fr;
        }

        .summary-card {
            position: static;
        }
    }

    @media(max-width: 900px) {
        .builder-toolbar,
        .teacher-row,
        .student-status-bar {
            grid-template-columns: 1fr;
        }

        .student-list {
            grid-template-columns: 1fr;
        }

        .remove-row {
            width: 100%;
        }
    }
</style>

@if(session('success'))
    <div class="builder-alert success">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="builder-alert error">
        <strong>Please fix the following errors:</strong>
        <ul style="margin:8px 0 0;padding-left:18px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if($isCapacityFull && $capacity > 0)
    <div class="builder-alert warning">
        Batch capacity is full. You can still remove students, but adding more students will require increasing the capacity first.
    </div>
@endif

<div class="builder-shell">
    <main>
        <div class="builder-card">
            <div class="builder-head">
                <div>
                    <h3>Assign Students</h3>
                    <p>Select students who will study in this batch. Use filters for faster assignment.</p>
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <a href="{{ route('admin.batches.show', $batch) }}" class="btn btn-light">View Batch</a>
                    <a href="{{ route('admin.batches.index') }}" class="btn btn-light">Back to Batches</a>
                </div>
            </div>

            <div class="builder-body">
                <form method="POST" action="{{ route('admin.batches.students.sync', $batch) }}" id="studentAssignForm">
                    @csrf

                    <div class="student-status-bar">
                        <div class="mini-stat">
                            <small>Total Students</small>
                            <strong>{{ $students->count() }}</strong>
                        </div>

                        <div class="mini-stat">
                            <small>Selected</small>
                            <strong id="selectedStudentsCount">{{ $assignedCount }}</strong>
                        </div>

                        <div class="mini-stat">
                            <small>Capacity</small>
                            <strong>{{ $capacity > 0 ? $capacity : '∞' }}</strong>
                        </div>

                        <div class="mini-stat">
                            <small>Visible</small>
                            <strong id="visibleStudentsCount">{{ $students->count() }}</strong>
                        </div>
                    </div>

                    <div class="builder-toolbar">
                        <input
                            type="text"
                            id="studentSearch"
                            placeholder="Search by name, code, phone, course..."
                        >

                        <select id="courseFilter">
                            <option value="">All Courses</option>
                            @foreach($courseOptions as $courseName)
                                <option value="{{ strtolower($courseName) }}">{{ $courseName }}</option>
                            @endforeach
                        </select>

                        <select id="classFilter">
                            <option value="">All Classes</option>
                            @foreach($classOptions as $className)
                                <option value="{{ strtolower($className) }}">{{ $className }}</option>
                            @endforeach
                        </select>

                        <select id="assignmentFilter">
                            <option value="">All Students</option>
                            <option value="assigned">Assigned Only</option>
                            <option value="unassigned">Unassigned Only</option>
                        </select>
                    </div>

                    <div class="quick-actions">
                        <button type="button" class="btn btn-light" id="selectVisibleStudents">Select Visible</button>
                        <button type="button" class="btn btn-light" id="clearVisibleStudents">Clear Visible</button>
                        <button type="button" class="btn btn-light" id="clearAllStudents">Clear All</button>
                    </div>

                    @if($students->count())
                        <div class="student-list" id="studentList">
                            @foreach($students as $student)
                                @php
                                    $courseName = $student->course_name ?: optional($student->course)->title ?: '';
                                    $classLevel = $student->class_level ?: '';
                                    $isAssigned = in_array((string) $student->id, $assignedStudentIds, true);

                                    $searchText = strtolower(
                                        $student->name . ' ' .
                                        $student->student_code . ' ' .
                                        $student->phone . ' ' .
                                        $courseName . ' ' .
                                        $classLevel
                                    );
                                @endphp

                                <label
                                    class="student-card"
                                    data-search="{{ $searchText }}"
                                    data-course="{{ strtolower($courseName) }}"
                                    data-class="{{ strtolower($classLevel) }}"
                                    data-assigned="{{ $isAssigned ? '1' : '0' }}"
                                >
                                    @if($isAssigned)
                                        <span class="assigned-ribbon">Assigned</span>
                                    @endif

                                    <input
                                        type="checkbox"
                                        name="student_ids[]"
                                        value="{{ $student->id }}"
                                        {{ $isAssigned ? 'checked' : '' }}
                                    >

                                    <span class="avatar">
                                        {{ strtoupper(mb_substr($student->name, 0, 1)) }}
                                    </span>

                                    <div>
                                        <strong>{{ $student->name }}</strong>
                                        <small>{{ $student->student_code }} | {{ $student->phone ?: 'No phone' }}</small>
                                        <small>{{ $courseName ?: 'No course assigned' }}</small>

                                        <div class="tag-row">
                                            @if($classLevel)
                                                <span class="soft-tag">{{ $classLevel }}</span>
                                            @endif

                                            @if($student->email)
                                                <span class="soft-tag">Email</span>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="sticky-save">
                            <div class="save-note">
                                Selected: <span id="selectedStudentsText">{{ $assignedCount }}</span>
                                @if($capacity > 0)
                                    / {{ $capacity }} capacity
                                @else
                                    / unlimited capacity
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary">
                                Save Student Assignment
                            </button>
                        </div>
                    @else
                        <div class="empty-note">
                            No active students found. Add or admit students first, then assign them to this batch.
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <div class="builder-card">
            <div class="builder-head">
                <div>
                    <h3>Assign Teachers</h3>
                    <p>Assign primary and assistant teachers with subject responsibility.</p>
                </div>

                <button type="button" class="btn btn-primary" id="addTeacherRow">+ Add Teacher</button>
            </div>

            <div class="builder-body">
                <form method="POST" action="{{ route('admin.batches.teachers.sync', $batch) }}">
                    @csrf

                    <div id="teacherRows">
                        @forelse($teacherRows as $row)
                            <div class="teacher-row">
                                <select name="teacher_ids[]">
                                    <option value="">Select Teacher</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ (string) $row['teacher_id'] === (string) $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <select name="subject_ids[]">
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ (string) ($row['subject_id'] ?? '') === (string) $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <select name="roles[]">
                                    <option value="primary" {{ ($row['role'] ?? '') === 'primary' ? 'selected' : '' }}>Primary</option>
                                    <option value="assistant" {{ ($row['role'] ?? '') === 'assistant' ? 'selected' : '' }}>Assistant</option>
                                </select>

                                <button type="button" class="remove-row">×</button>
                            </div>
                        @empty
                            <div class="teacher-row">
                                <select name="teacher_ids[]">
                                    <option value="">Select Teacher</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                    @endforeach
                                </select>

                                <select name="subject_ids[]">
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>

                                <select name="roles[]">
                                    <option value="primary">Primary</option>
                                    <option value="assistant">Assistant</option>
                                </select>

                                <button type="button" class="remove-row">×</button>
                            </div>
                        @endforelse
                    </div>

                    <div class="sticky-save">
                        <div class="save-note">
                            Teacher rows can include primary and assistant faculty.
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Save Teacher Assignment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <aside>
        <div class="builder-card summary-card">
            <div class="summary-hero">
                <h3>{{ $batch->name }}</h3>
                <p>{{ optional($batch->course)->title ?: 'No course linked' }}</p>
            </div>

            <div class="summary-body">
                <div class="summary-grid">
                    <div class="summary-box">
                        <small>Batch Code</small>
                        <strong>{{ $batch->code ?: '-' }}</strong>
                    </div>

                    <div class="summary-box">
                        <small>Class / Level</small>
                        <strong>{{ $batch->class_level ?: '-' }}</strong>
                    </div>

                    <div class="summary-box">
                        <small>Timing</small>
                        <strong>{{ $batch->start_time ?: '-' }} - {{ $batch->end_time ?: '-' }}</strong>
                    </div>

                    <div class="summary-box">
                        <small>Days</small>
                        <strong>{{ $batch->days ?: '-' }}</strong>
                    </div>

                    <div class="summary-box">
                        <small>Room</small>
                        <strong>{{ $batch->room_no ?: '-' }}</strong>
                    </div>

                    <div class="summary-box">
                        <small>Capacity Usage</small>
                        <strong>
                            <span id="liveCapacityText">{{ $assignedCount }}</span>
                            /
                            {{ $capacity > 0 ? $capacity : 'Unlimited' }}
                        </strong>

                        @if($capacity > 0)
                            <div class="capacity-bar-wrap">
                                <div class="capacity-bar">
                                    <div class="capacity-fill" id="liveCapacityBar" style="width: {{ $capacityPercent }}%;"></div>
                                </div>

                                <div class="capacity-text">
                                    <span id="liveCapacityPercent">{{ $capacityPercent }}% used</span>
                                    <span id="capacityRemainingText">{{ max(0, $capacity - $assignedCount) }} seats left</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="summary-box">
                        <small>Assigned Teachers</small>
                        <strong>{{ $teacherRows->count() }}</strong>
                    </div>
                </div>

                <div class="action-stack">
                    <a href="{{ route('admin.batches.show', $batch) }}" class="btn btn-dark">View Batch Profile</a>
                    <a href="{{ route('admin.attendance.index', ['batch_id' => $batch->id]) }}" class="btn btn-primary">Mark Attendance</a>
                    <a href="{{ route('admin.batches.edit', $batch) }}" class="btn btn-light">Edit Batch</a>
                </div>
            </div>
        </div>
    </aside>
</div>

<div id="teacherRowTemplate" style="display:none;">
    <div class="teacher-row">
        <select name="teacher_ids[]">
            <option value="">Select Teacher</option>
            @foreach($teachers as $teacher)
                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
            @endforeach
        </select>

        <select name="subject_ids[]">
            <option value="">Select Subject</option>
            @foreach($subjects as $subject)
                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
            @endforeach
        </select>

        <select name="roles[]">
            <option value="primary">Primary</option>
            <option value="assistant">Assistant</option>
        </select>

        <button type="button" class="remove-row">×</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const capacity = Number(@json($capacity));
        const studentSearch = document.getElementById('studentSearch');
        const courseFilter = document.getElementById('courseFilter');
        const classFilter = document.getElementById('classFilter');
        const assignmentFilter = document.getElementById('assignmentFilter');
        const studentList = document.getElementById('studentList');
        const selectedStudentsCount = document.getElementById('selectedStudentsCount');
        const selectedStudentsText = document.getElementById('selectedStudentsText');
        const visibleStudentsCount = document.getElementById('visibleStudentsCount');
        const liveCapacityText = document.getElementById('liveCapacityText');
        const liveCapacityBar = document.getElementById('liveCapacityBar');
        const liveCapacityPercent = document.getElementById('liveCapacityPercent');
        const capacityRemainingText = document.getElementById('capacityRemainingText');
        const studentAssignForm = document.getElementById('studentAssignForm');

        function getStudentCards() {
            return Array.from(studentList?.querySelectorAll('.student-card') || []);
        }

        function getStudentCheckboxes() {
            return Array.from(studentList?.querySelectorAll('input[type="checkbox"]') || []);
        }

        function updateCounts() {
            const selected = getStudentCheckboxes().filter((checkbox) => checkbox.checked).length;
            const visible = getStudentCards().filter((card) => card.style.display !== 'none').length;

            if (selectedStudentsCount) selectedStudentsCount.textContent = selected;
            if (selectedStudentsText) selectedStudentsText.textContent = selected;
            if (visibleStudentsCount) visibleStudentsCount.textContent = visible;
            if (liveCapacityText) liveCapacityText.textContent = selected;

            if (capacity > 0) {
                const percent = Math.min(100, Math.round((selected / capacity) * 100));
                const remaining = Math.max(0, capacity - selected);

                if (liveCapacityBar) liveCapacityBar.style.width = percent + '%';
                if (liveCapacityPercent) liveCapacityPercent.textContent = percent + '% used';
                if (capacityRemainingText) capacityRemainingText.textContent = remaining + ' seats left';

                if (selected > capacity) {
                    if (liveCapacityBar) liveCapacityBar.style.background = '#dc2626';
                    if (capacityRemainingText) capacityRemainingText.textContent = 'Over capacity';
                } else {
                    if (liveCapacityBar) liveCapacityBar.style.background = 'linear-gradient(135deg, #2563eb, #7c3aed)';
                }
            }
        }

        function applyFilters() {
            const searchValue = (studentSearch?.value || '').toLowerCase().trim();
            const courseValue = (courseFilter?.value || '').toLowerCase().trim();
            const classValue = (classFilter?.value || '').toLowerCase().trim();
            const assignmentValue = assignmentFilter?.value || '';

            getStudentCards().forEach(function (card) {
                const text = card.dataset.search || '';
                const course = card.dataset.course || '';
                const classLevel = card.dataset.class || '';
                const isAssigned = card.dataset.assigned === '1';
                const checkbox = card.querySelector('input[type="checkbox"]');

                let show = true;

                if (searchValue && !text.includes(searchValue)) show = false;
                if (courseValue && course !== courseValue) show = false;
                if (classValue && classLevel !== classValue) show = false;
                if (assignmentValue === 'assigned' && !checkbox.checked && !isAssigned) show = false;
                if (assignmentValue === 'unassigned' && checkbox.checked) show = false;

                card.style.display = show ? 'flex' : 'none';
            });

            updateCounts();
        }

        [studentSearch, courseFilter, classFilter, assignmentFilter].forEach(function (input) {
            input?.addEventListener('input', applyFilters);
            input?.addEventListener('change', applyFilters);
        });

        document.getElementById('selectVisibleStudents')?.addEventListener('click', function () {
            getStudentCards().forEach(function (card) {
                if (card.style.display !== 'none') {
                    const checkbox = card.querySelector('input[type="checkbox"]');
                    checkbox.checked = true;
                }
            });

            updateCounts();
        });

        document.getElementById('clearVisibleStudents')?.addEventListener('click', function () {
            getStudentCards().forEach(function (card) {
                if (card.style.display !== 'none') {
                    const checkbox = card.querySelector('input[type="checkbox"]');
                    checkbox.checked = false;
                }
            });

            updateCounts();
        });

        document.getElementById('clearAllStudents')?.addEventListener('click', function () {
            getStudentCheckboxes().forEach(function (checkbox) {
                checkbox.checked = false;
            });

            updateCounts();
        });

        getStudentCheckboxes().forEach(function (checkbox) {
            checkbox.addEventListener('change', updateCounts);
        });

        studentAssignForm?.addEventListener('submit', function (event) {
            if (capacity <= 0) return;

            const selected = getStudentCheckboxes().filter((checkbox) => checkbox.checked).length;

            if (selected > capacity) {
                event.preventDefault();
                alert('Batch capacity is ' + capacity + '. You selected ' + selected + ' students. Please reduce selected students or increase batch capacity.');
            }
        });

        const addTeacherRowBtn = document.getElementById('addTeacherRow');
        const teacherRows = document.getElementById('teacherRows');
        const teacherRowTemplate = document.getElementById('teacherRowTemplate');

        addTeacherRowBtn?.addEventListener('click', function () {
            const html = teacherRowTemplate.innerHTML.trim();
            teacherRows.insertAdjacentHTML('beforeend', html);
        });

        document.addEventListener('click', function (event) {
            if (!event.target.classList.contains('remove-row')) return;

            const rows = teacherRows.querySelectorAll('.teacher-row');

            if (rows.length <= 1) {
                rows[0].querySelectorAll('select').forEach(function (select) {
                    select.value = '';
                });

                return;
            }

            event.target.closest('.teacher-row').remove();
        });

        applyFilters();
        updateCounts();
    });
</script>

@endsection