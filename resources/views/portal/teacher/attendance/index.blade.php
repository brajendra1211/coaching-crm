@extends('portal.layouts.app', ['portalRole' => 'teacher'])
@section('title', 'Attendance')
@section('page_title', 'Attendance')
@section('page_subtitle', 'Attendance records for your batches')
@section('content')
@if(!$teacher)
    <div class="card" style="margin-bottom:18px;">
        <h3>Profile Not Linked</h3>
        <p class="muted" style="margin:0;">Ask admin to link this user with a teacher profile.</p>
    </div>
@endif

@if(session('success'))
    <div class="card" style="margin-bottom:18px;background:#dcfce7;color:#166534;border-color:#bbf7d0;">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="card" style="margin-bottom:18px;background:#fef2f2;color:#991b1b;border-color:#fecaca;">{{ $errors->first() }}</div>
@endif

@php
    $presentCount = $attendanceRecords->where('status', 'present')->count();
    $absentCount = $attendanceRecords->where('status', 'absent')->count();
    $lateCount = $attendanceRecords->where('status', 'late')->count();
    $leaveCount = $attendanceRecords->where('status', 'leave')->count();
@endphp

<div class="grid-4">
    <div class="card stat"><small>Students Loaded</small><strong>{{ $students->count() }}</strong></div>
    <div class="card stat"><small>Present</small><strong>{{ $presentCount }}</strong></div>
    <div class="card stat"><small>Absent</small><strong>{{ $absentCount }}</strong></div>
    <div class="card stat"><small>Late / Leave</small><strong>{{ $lateCount + $leaveCount }}</strong></div>
</div>

<section class="card" style="margin-bottom:18px;">
    <div class="card-header" style="display:flex;justify-content:space-between;gap:14px;align-items:center;flex-wrap:wrap;">
        <div>
            <h3>Mark Attendance</h3>
            <p class="muted" style="margin:6px 0 0;">Select only your assigned batch and mark attendance date-wise.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('teacher.attendance.index') }}" style="display:grid;grid-template-columns:240px 220px auto;gap:12px;margin-bottom:18px;">
        <select name="batch_id" required>
            <option value="">Select Assigned Batch</option>
            @foreach($batches as $batch)
                <option value="{{ $batch->id }}" {{ (string) $selectedBatchId === (string) $batch->id ? 'selected' : '' }}>
                    {{ $batch->name }}{{ $batch->class_level ? ' - ' . $batch->class_level : '' }}
                </option>
            @endforeach
        </select>
        <input type="date" name="attendance_date" value="{{ $selectedDate }}" required>
        <button type="submit" class="btn btn-primary">Load Students</button>
    </form>

    <form method="POST" action="{{ route('teacher.attendance.store') }}">
        @csrf
        <input type="hidden" name="batch_id" value="{{ $selectedBatchId }}">
        <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">

        <div class="table-wrap"><table>
            <thead><tr><th>Student</th><th>Course</th><th>Status</th><th>Note</th></tr></thead>
            <tbody>
            @forelse($students as $student)
                @php
                    $record = $attendanceRecords->get($student->id);
                    $currentStatus = old('attendance.' . $student->id . '.status', $record->status ?? 'present');
                @endphp
                <tr>
                    <td><strong>{{ $student->name }}</strong><br><small class="muted">{{ $student->student_code }}</small></td>
                    <td>{{ $student->course_name ?: optional($student->course)->title ?: '-' }}<br><small class="muted">{{ $student->class_level ?: '-' }}</small></td>
                    <td>
                        <select name="attendance[{{ $student->id }}][status]">
                            @foreach(['present' => 'Present', 'absent' => 'Absent', 'late' => 'Late', 'leave' => 'Leave'] as $value => $label)
                                <option value="{{ $value }}" {{ $currentStatus === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input name="attendance[{{ $student->id }}][note]" value="{{ old('attendance.' . $student->id . '.note', $record->note ?? '') }}" placeholder="Optional note"></td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;color:#64748b;">No active students found in selected batch.</td></tr>
            @endforelse
            </tbody>
        </table></div>

        @if($students->count())
            <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:18px;">
                <button type="submit" class="btn btn-primary">Save Attendance</button>
                <a href="{{ route('teacher.attendance.index') }}" class="btn btn-light">Reset</a>
            </div>
        @endif
    </form>
</section>

<section class="card">
    <h3>Recent Attendance</h3>
    <div class="table-wrap"><table>
        <thead><tr><th>Date</th><th>Student</th><th>Batch</th><th>Status</th><th>Note</th></tr></thead>
        <tbody>
        @forelse($attendance as $row)
            <tr>
                <td>{{ $row->attendance_date ? $row->attendance_date->format('d M Y') : '-' }}</td>
                <td>{{ $row->student->name ?? '-' }}</td>
                <td>{{ $row->batch->name ?? '-' }}</td>
                <td><span class="pill {{ $row->status === 'absent' ? 'red' : 'green' }}">{{ ucfirst($row->status) }}</span></td>
                <td>{{ $row->note ?: '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="5" style="text-align:center;color:#64748b;">No attendance found.</td></tr>
        @endforelse
        </tbody>
    </table></div>
</section>
@endsection
