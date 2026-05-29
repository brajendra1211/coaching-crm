@extends('admin.layouts.app')

@section('title', 'Batch Profile')
@section('page_title', 'Batch Profile')

@section('content')

<style>
    .batch-profile-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 380px;
        gap: 22px;
        align-items: start;
    }

    .profile-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, .07);
        margin-bottom: 22px;
        overflow: hidden;
    }

    .profile-head {
        padding: 18px 22px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #fff, #f8fafc);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }

    .profile-head h3 {
        margin: 0;
        color: #111827;
        font-size: 20px;
    }

    .profile-head p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .profile-body {
        padding: 22px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .info-box {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 14px;
    }

    .info-box small {
        display: block;
        color: #64748b;
        font-weight: 900;
        margin-bottom: 6px;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .info-box strong {
        color: #111827;
        line-height: 1.4;
        word-break: break-word;
    }

    .status-pill {
        display: inline-flex;
        padding: 7px 11px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        border: 1px solid;
        white-space: nowrap;
    }

    .status-active,
    .status-present,
    .status-scheduled {
        background: #dcfce7;
        color: #166534;
        border-color: #bbf7d0;
    }

    .status-inactive,
    .status-absent,
    .status-cancelled {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .status-completed,
    .status-late,
    .status-leave {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    .capacity-wrap {
        margin-top: 14px;
    }

    .capacity-bar {
        height: 12px;
        background: #e5e7eb;
        border-radius: 999px;
        overflow: hidden;
    }

    .capacity-fill {
        height: 100%;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        border-radius: 999px;
    }

    .mini-table {
        width: 100%;
        border-collapse: collapse;
    }

    .mini-table th,
    .mini-table td {
        text-align: left;
        padding: 13px 10px;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: top;
    }

    .mini-table th {
        color: #475569;
        font-size: 13px;
        background: #f8fafc;
    }

    .mini-table td {
        color: #334155;
        font-size: 14px;
    }

    .student-avatar {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
    }

    .summary-stack {
        display: grid;
        gap: 12px;
    }

    .action-stack {
        display: grid;
        gap: 10px;
        margin-top: 18px;
    }

    @media(max-width: 1100px) {
        .batch-profile-grid,
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="batch-profile-grid">
    <main>
        <div class="profile-card">
            <div class="profile-head">
                <div>
                    <h3>{{ $batch->name }}</h3>
                    <p>Batch Code: {{ $batch->code ?: '-' }}</p>
                </div>

                <span class="status-pill status-{{ $batch->status }}">
                    {{ ucwords(str_replace('_', ' ', $batch->status)) }}
                </span>
            </div>

            <div class="profile-body">
                <div class="info-grid">
                    <div class="info-box">
                        <small>Course</small>
                        <strong>{{ optional($batch->course)->title ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Class / Level</small>
                        <strong>{{ $batch->class_level ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Room No</small>
                        <strong>{{ $batch->room_no ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Date</small>
                        <strong>
                            {{ $batch->start_date ? $batch->start_date->format('d M Y') : '-' }}
                            -
                            {{ $batch->end_date ? $batch->end_date->format('d M Y') : '-' }}
                        </strong>
                    </div>

                    <div class="info-box">
                        <small>Timing</small>
                        <strong>{{ $batch->start_time ?: '-' }} - {{ $batch->end_time ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Days</small>
                        <strong>{{ $batch->days ?: '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-card">
            <div class="profile-head">
                <div>
                    <h3>Assigned Students</h3>
                    <p>{{ $assignedStudentsCount }} students assigned in this batch.</p>
                </div>

                <a href="{{ route('admin.batches.builder', $batch) }}" class="btn btn-primary">
                    Manage Students
                </a>
            </div>

            <div class="profile-body">
                <div style="overflow:auto;">
                    <table class="mini-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Phone</th>
                                <th>Assigned</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($batch->students as $student)
                                <tr>
                                    <td>
                                        <div style="display:flex;gap:10px;align-items:center;">
                                            <span class="student-avatar">
                                                {{ strtoupper(mb_substr($student->name, 0, 1)) }}
                                            </span>

                                            <div>
                                                <strong>{{ $student->name }}</strong>
                                                <br>
                                                <small style="color:#64748b;">{{ $student->student_code }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        {{ $student->course_name ?: optional($student->course)->title ?: '-' }}
                                        <br>
                                        <small style="color:#64748b;">{{ $student->class_level ?: '-' }}</small>
                                    </td>

                                    <td>{{ $student->phone ?: '-' }}</td>

                                    <td>
                                        {{ $student->pivot->assigned_at ? \Illuminate\Support\Carbon::parse($student->pivot->assigned_at)->format('d M Y') : '-' }}
                                    </td>

                                    <td>
                                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-light">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center;padding:32px;color:#64748b;">
                                        No students assigned yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="profile-card">
            <div class="profile-head">
                <div>
                    <h3>Assigned Teachers</h3>
                    <p>Teachers and subjects mapped with this batch.</p>
                </div>

                <a href="{{ route('admin.batches.builder', $batch) }}" class="btn btn-primary">
                    Manage Teachers
                </a>
            </div>

            <div class="profile-body">
                <div style="overflow:auto;">
                    <table class="mini-table">
                        <thead>
                            <tr>
                                <th>Teacher</th>
                                <th>Subject</th>
                                <th>Role</th>
                                <th>Assigned</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($batch->teachers as $teacher)
                                <tr>
                                    <td>
                                        <strong>{{ $teacher->name }}</strong>
                                        <br>
                                        <small style="color:#64748b;">{{ $teacher->phone ?: '-' }}</small>
                                    </td>

                                    <td>
                                        {{ $subjectsMap[$teacher->pivot->subject_id] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ ucwords($teacher->pivot->role ?: 'assistant') }}
                                    </td>

                                    <td>
                                        {{ $teacher->pivot->assigned_at ? \Illuminate\Support\Carbon::parse($teacher->pivot->assigned_at)->format('d M Y') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align:center;padding:32px;color:#64748b;">
                                        No teachers assigned yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="profile-card">
            <div class="profile-head">
                <div>
                    <h3>Online Classes</h3>
                    <p>Recent online classes for this batch.</p>
                </div>

                <a href="{{ route('admin.online-classes.create') }}" class="btn btn-primary">
                    Add Class
                </a>
            </div>

            <div class="profile-body">
                <div style="overflow:auto;">
                    <table class="mini-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Teacher</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($onlineClasses as $class)
                                <tr>
                                    <td>
                                        <strong>{{ $class->title }}</strong>
                                        <br>
                                        @if($class->meeting_link)
                                            <a href="{{ $class->meeting_link }}" target="_blank" style="color:#2563eb;font-weight:900;">
                                                Open Link
                                            </a>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $class->class_date ? $class->class_date->format('d M Y') : '-' }}
                                        <br>
                                        <small style="color:#64748b;">{{ $class->start_time }} - {{ $class->end_time }}</small>
                                    </td>

                                    <td>{{ optional($class->teacher)->name ?: '-' }}</td>

                                    <td>
                                        <span class="status-pill status-{{ $class->status }}">
                                            {{ ucfirst($class->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align:center;padding:32px;color:#64748b;">
                                        No online classes found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="profile-card">
            <div class="profile-head">
                <div>
                    <h3>Study Materials</h3>
                    <p>Recent notes, PDFs, videos and links for this batch.</p>
                </div>

                <a href="{{ route('admin.study-materials.create') }}" class="btn btn-primary">
                    Add Material
                </a>
            </div>

            <div class="profile-body">
                <div style="overflow:auto;">
                    <table class="mini-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Subject</th>
                                <th>Open</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($studyMaterials as $material)
                                <tr>
                                    <td>
                                        <strong>{{ $material->title }}</strong>
                                        <br>
                                        <small style="color:#64748b;">{{ \Illuminate\Support\Str::limit($material->description, 70) }}</small>
                                    </td>

                                    <td>{{ $material->type ?: '-' }}</td>

                                    <td>{{ optional($material->subject)->name ?: '-' }}</td>

                                    <td>
                                        @if($material->file_path)
                                            <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="btn btn-light">
                                                File
                                            </a>
                                        @elseif($material->external_link)
                                            <a href="{{ $material->external_link }}" target="_blank" class="btn btn-light">
                                                Link
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align:center;padding:32px;color:#64748b;">
                                        No study materials found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <aside>
        <div class="profile-card">
            <div class="profile-head" style="background:linear-gradient(135deg,#2563eb,#7c3aed);">
                <div>
                    <h3 style="color:#fff;">Batch Summary</h3>
                    <p style="color:rgba(255,255,255,.9);">Quick batch overview.</p>
                </div>
            </div>

            <div class="profile-body">
                <div class="summary-stack">
                    <div class="info-box">
                        <small>Capacity</small>
                        <strong>
                            {{ $assignedStudentsCount }}
                            /
                            {{ $capacity > 0 ? $capacity : 'Unlimited' }}
                        </strong>

                        @if($capacity > 0)
                            <div class="capacity-wrap">
                                <div class="capacity-bar">
                                    <div class="capacity-fill" style="width: {{ $capacityPercent }}%;"></div>
                                </div>
                                <small style="margin-top:8px;">{{ $capacityPercent }}% filled</small>
                            </div>
                        @endif
                    </div>

                    <div class="info-box">
                        <small>Students</small>
                        <strong>{{ $assignedStudentsCount }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Teachers</small>
                        <strong>{{ $batch->teachers->count() }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Last Attendance</small>
                        <strong>
                            {{ $lastAttendanceDate ? \Illuminate\Support\Carbon::parse($lastAttendanceDate)->format('d M Y') : '-' }}
                        </strong>
                    </div>
                </div>

                <div class="action-stack">
                    <a href="{{ route('admin.batches.builder', $batch) }}" class="btn btn-primary">
                        Open Builder
                    </a>

                    <a href="{{ route('admin.attendance.index', ['batch_id' => $batch->id]) }}" class="btn btn-dark">
                        Mark Attendance
                    </a>

                    <a href="{{ route('admin.batches.edit', $batch) }}" class="btn btn-light">
                        Edit Batch
                    </a>

                    <a href="{{ route('admin.batches.index') }}" class="btn btn-light">
                        Back to Batches
                    </a>
                </div>
            </div>
        </div>

        <div class="profile-card">
            <div class="profile-head">
                <h3>Attendance Summary</h3>
            </div>

            <div class="profile-body">
                <div class="summary-stack">
                    <div class="info-box">
                        <small>Present</small>
                        <strong>{{ $attendanceSummary['present'] ?? 0 }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Absent</small>
                        <strong>{{ $attendanceSummary['absent'] ?? 0 }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Late</small>
                        <strong>{{ $attendanceSummary['late'] ?? 0 }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Leave</small>
                        <strong>{{ $attendanceSummary['leave'] ?? 0 }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</div>

@endsection