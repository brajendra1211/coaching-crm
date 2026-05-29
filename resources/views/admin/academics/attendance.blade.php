@extends('admin.layouts.app')

@section('title', 'Attendance')
@section('page_title', 'Attendance')

@section('content')

<style>
    .attendance-toolbar {
        display: grid;
        grid-template-columns: 220px 220px auto;
        gap: 12px;
        margin-bottom: 18px;
    }

    .attendance-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 18px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 16px;
        box-shadow: 0 10px 28px rgba(15,23,42,.06);
    }

    .stat-card small {
        display: block;
        color: #64748b;
        font-weight: 900;
        margin-bottom: 6px;
    }

    .stat-card strong {
        color: #111827;
        font-size: 26px;
    }

    .attendance-table select,
    .attendance-table input {
        min-width: 130px;
    }

    .status-present { color: #166534; font-weight: 900; }
    .status-absent { color: #991b1b; font-weight: 900; }
    .status-late { color: #92400e; font-weight: 900; }
    .status-leave { color: #1d4ed8; font-weight: 900; }

    @media(max-width: 900px) {
        .attendance-toolbar,
        .attendance-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

@php
    $presentCount = $attendanceRecords->where('status', 'present')->count();
    $absentCount = $attendanceRecords->where('status', 'absent')->count();
    $lateCount = $attendanceRecords->where('status', 'late')->count();
    $leaveCount = $attendanceRecords->where('status', 'leave')->count();
@endphp

@if(session('success'))
    <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">
        {{ session('success') }}
    </div>
@endif

<div class="attendance-stats">
    <div class="stat-card">
        <small>Total Students</small>
        <strong>{{ $students->count() }}</strong>
    </div>

    <div class="stat-card">
        <small>Present</small>
        <strong>{{ $presentCount }}</strong>
    </div>

    <div class="stat-card">
        <small>Absent</small>
        <strong>{{ $absentCount }}</strong>
    </div>

    <div class="stat-card">
        <small>Late / Leave</small>
        <strong>{{ $lateCount + $leaveCount }}</strong>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Mark Attendance</h3>
            <p style="margin:6px 0 0;color:#64748b;">
                Select batch and date, then mark student attendance.
            </p>
        </div>
    </div>

    <form method="GET" class="attendance-toolbar">
        <select name="batch_id">
            <option value="">All Active Students</option>
            @foreach($batches as $batch)
                <option value="{{ $batch->id }}" {{ (string) $selectedBatchId === (string) $batch->id ? 'selected' : '' }}>
                    {{ $batch->name }}
                </option>
            @endforeach
        </select>

        <input type="date" name="attendance_date" value="{{ $selectedDate }}">

        <button type="submit" class="btn btn-primary">Load Students</button>
    </form>

    <form method="POST" action="{{ route('admin.attendance.store') }}">
        @csrf

        <input type="hidden" name="batch_id" value="{{ $selectedBatchId }}">
        <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">

        <div class="table-wrap">
            <table class="table attendance-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Status</th>
                        <th>Note</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($students as $student)
                        @php
                            $record = $attendanceRecords->get($student->id);
                            $currentStatus = old('attendance.' . $student->id . '.status', $record->status ?? 'present');
                        @endphp

                        <tr>
                            <td>
                                <strong>{{ $student->name }}</strong>
                                <br>
                                <small style="color:#64748b;">{{ $student->student_code }}</small>
                            </td>

                            <td>
                                {{ $student->course_name ?: optional($student->course)->title ?: '-' }}
                                <br>
                                <small style="color:#64748b;">{{ $student->class_level ?: '-' }}</small>
                            </td>

                            <td>
                                <select name="attendance[{{ $student->id }}][status]" class="status-select">
                                    <option value="present" {{ $currentStatus === 'present' ? 'selected' : '' }}>Present</option>
                                    <option value="absent" {{ $currentStatus === 'absent' ? 'selected' : '' }}>Absent</option>
                                    <option value="late" {{ $currentStatus === 'late' ? 'selected' : '' }}>Late</option>
                                    <option value="leave" {{ $currentStatus === 'leave' ? 'selected' : '' }}>Leave</option>
                                </select>
                            </td>

                            <td>
                                <input
                                    type="text"
                                    name="attendance[{{ $student->id }}][note]"
                                    value="{{ old('attendance.' . $student->id . '.note', $record->note ?? '') }}"
                                    placeholder="Optional note"
                                >
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center;padding:35px;color:#64748b;">
                                No active students found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->count())
            <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:18px;">
                <button type="submit" class="btn btn-primary">Save Attendance</button>
                <a href="{{ route('admin.attendance.index') }}" class="btn btn-light">Reset</a>
            </div>
        @endif
    </form>
</div>

@endsection