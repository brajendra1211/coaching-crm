@extends('portal.layouts.app', ['portalRole' => 'student'])

@section('title', 'My Profile')
@section('page_title', 'My Profile')
@section('page_subtitle', 'Student details, batches and contact information')

@section('content')
@if(!$student)
    <div class="card"><h3>Profile Not Linked</h3><p class="muted" style="margin:0;">Ask admin to link this login with a student profile.</p></div>
@else
    <div class="grid-2">
        <section class="card">
            <h3>Student Information</h3>
            <div class="info-grid">
                <div class="info-item"><small>Name</small><strong>{{ $student->name }}</strong></div>
                <div class="info-item"><small>Student Code</small><strong>{{ $student->student_code }}</strong></div>
                <div class="info-item"><small>Phone</small><strong>{{ $student->phone ?: '-' }}</strong></div>
                <div class="info-item"><small>Email</small><strong>{{ $student->email ?: '-' }}</strong></div>
                <div class="info-item"><small>Class</small><strong>{{ $student->class_level ?: '-' }}</strong></div>
                <div class="info-item"><small>Course</small><strong>{{ $student->course_name ?: ($student->course->title ?? '-') }}</strong></div>
                <div class="info-item"><small>Joining Date</small><strong>{{ $student->joining_date ? $student->joining_date->format('d M Y') : '-' }}</strong></div>
                <div class="info-item"><small>Status</small><strong>{{ ucfirst($student->status) }}</strong></div>
            </div>
        </section>

        <section class="card">
            <h3>Personal Details</h3>
            <div class="info-grid">
                <div class="info-item"><small>Date of Birth</small><strong>{{ $student->dob ? $student->dob->format('d M Y') : '-' }}</strong></div>
                <div class="info-item"><small>Gender</small><strong>{{ $student->gender ?: '-' }}</strong></div>
                <div class="info-item"><small>City</small><strong>{{ $student->city ?: '-' }}</strong></div>
                <div class="info-item"><small>State</small><strong>{{ $student->state ?: '-' }}</strong></div>
                <div class="info-item" style="grid-column:1/-1;"><small>Address</small><strong>{{ $student->address ?: '-' }}</strong></div>
            </div>
        </section>
    </div>

    <section class="card">
        <h3>My Batches</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Batch</th><th>Course</th><th>Subject</th><th>Teacher</th><th>Time</th><th>Room</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($batches as $batch)
                        <tr>
                            <td><strong>{{ $batch->name }}</strong><br><small class="muted">{{ $batch->code ?: '-' }}</small></td>
                            <td>{{ $batch->course->title ?? '-' }}</td>
                            <td>{{ $batch->subject->name ?? '-' }}</td>
                            <td>{{ $batch->teacher->name ?? '-' }}</td>
                            <td>{{ $batch->start_time ?: '-' }} - {{ $batch->end_time ?: '-' }}</td>
                            <td>{{ $batch->room_no ?: '-' }}</td>
                            <td><span class="pill green">{{ ucfirst($batch->pivot->status ?? $batch->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align:center;color:#64748b;">No batch assigned.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endif
@endsection
