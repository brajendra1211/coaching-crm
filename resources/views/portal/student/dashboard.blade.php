@extends('portal.layouts.app', ['portalRole' => 'student'])

@section('title', 'Student Dashboard')
@section('page_title', 'Student Dashboard')
@section('page_subtitle', 'Exams, results, fees, attendance and study resources')

@section('content')
@if(!$student)
    <div class="card" style="margin-bottom:18px;">
        <h3>Profile Not Linked</h3>
        <p style="margin:0;color:#64748b;">This login is active, but no student record is linked yet. Ask admin to link this user with a student profile or use the same email/phone in student record.</p>
    </div>
@endif

<div class="grid-4">
    <div class="card stat"><small>Active Batches</small><strong>{{ $batches->count() }}</strong></div>
    <div class="card stat"><small>Upcoming / Recent Exams</small><strong>{{ $exams->count() }}</strong></div>
    <div class="card stat"><small>Total Paid</small><strong>Rs. {{ number_format((float) $totalPaid, 2) }}</strong></div>
    <div class="card stat"><small>Pending Fees</small><strong>Rs. {{ number_format((float) $totalPending, 2) }}</strong></div>
</div>

<div class="page-actions">
    <a href="{{ route('student.exams.index') }}" class="btn btn-primary">Open Exams</a>
    <a href="{{ route('student.materials.index') }}" class="btn btn-light">Study Materials</a>
    <a href="{{ route('student.fees.index') }}" class="btn btn-light">Fee Details</a>
    <a href="{{ route('student.profile.index') }}" class="btn btn-light">My Profile</a>
</div>

<div class="grid-2">
    <section class="card" id="student-exams">
        <h3>My Exams</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Exam</th><th>Subject</th><th>Date</th><th>Access</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($exams as $exam)
                        <tr>
                            <td><strong>{{ $exam->title }}</strong><br><small>{{ $exam->label ?: ucfirst($exam->difficulty ?? 'medium') }}</small></td>
                            <td>{{ $exam->subject->name ?? 'General' }}</td>
                            <td>{{ $exam->exam_date ? $exam->exam_date->format('d M Y') : '-' }}</td>
                            <td><span class="pill">{{ ucfirst($exam->access_type ?? 'free') }}</span></td>
                            <td>{{ ucfirst($exam->status) }}</td>
                            <td><a href="{{ route('student.exams.show', $exam) }}" class="btn btn-primary">Attempt</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center;color:#64748b;">No exams assigned.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="card" id="student-results">
        <h3>Latest Results</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Exam</th><th>Marks</th><th>%</th><th>Grade</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($results as $result)
                        <tr>
                            <td>{{ $result->exam->title ?? '-' }}</td>
                            <td>{{ $result->marks_obtained }} / {{ $result->total_marks }}</td>
                            <td>{{ $result->percentage }}%</td>
                            <td>{{ $result->grade ?: '-' }}</td>
                            <td><span class="pill {{ $result->result_status === 'fail' ? 'red' : 'green' }}">{{ ucfirst($result->result_status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;color:#64748b;">No results published.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<div class="grid-2">
    <section class="card" id="student-fees">
        <h3>Fees</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Batch</th><th>Total</th><th>Paid</th><th>Balance</th><th>Next Due</th></tr></thead>
                <tbody>
                    @forelse($feeAssignments as $fee)
                        <tr>
                            <td>{{ $fee->batch->name ?? '-' }}</td>
                            <td>Rs. {{ number_format((float) $fee->total_amount, 2) }}</td>
                            <td>Rs. {{ number_format((float) $fee->paid_amount, 2) }}</td>
                            <td>Rs. {{ number_format((float) $fee->balance_amount, 2) }}</td>
                            <td>{{ $fee->next_due_date ? $fee->next_due_date->format('d M Y') : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;color:#64748b;">No fee plan assigned.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="card">
        <h3>Attendance</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Date</th><th>Batch</th><th>Status</th><th>Note</th></tr></thead>
                <tbody>
                    @forelse($attendance as $row)
                        <tr>
                            <td>{{ $row->attendance_date ? $row->attendance_date->format('d M Y') : '-' }}</td>
                            <td>{{ $row->batch->name ?? '-' }}</td>
                            <td><span class="pill {{ $row->status === 'absent' ? 'red' : 'green' }}">{{ ucfirst($row->status) }}</span></td>
                            <td>{{ $row->note ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;color:#64748b;">No attendance found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<div class="grid-2">
    <section class="card">
        <h3>Online Classes</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Class</th><th>Date</th><th>Teacher</th><th>Join</th></tr></thead>
                <tbody>
                    @forelse($classes as $class)
                        <tr>
                            <td><strong>{{ $class->title }}</strong><br><small>{{ $class->subject->name ?? 'General' }}</small></td>
                            <td>{{ $class->class_date ? $class->class_date->format('d M Y') : '-' }}</td>
                            <td>{{ $class->teacher->name ?? '-' }}</td>
                            <td>
                                @if($class->meeting_link)
                                    <a class="btn btn-light" href="{{ $class->meeting_link }}" target="_blank">Join</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;color:#64748b;">No online classes available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="card">
        <h3>Recent Attempts</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Exam</th><th>Marks</th><th>%</th><th>Review</th></tr></thead>
                <tbody>
                    @forelse($attempts as $attempt)
                        <tr>
                            <td>{{ $attempt->exam->title ?? '-' }}</td>
                            <td>{{ $attempt->marks_obtained }} / {{ $attempt->total_marks }}</td>
                            <td>{{ $attempt->percentage }}%</td>
                            <td><a href="{{ route('student.exams.result', $attempt) }}" class="btn btn-light">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;color:#64748b;">No attempts yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<section class="card" id="student-materials">
    <h3>Study Materials</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Title</th><th>Type</th><th>Batch</th><th>Subject</th><th>Access</th></tr></thead>
            <tbody>
                @forelse($materials as $material)
                    <tr>
                        <td>{{ $material->title }}</td>
                        <td>{{ $material->type ?: '-' }}</td>
                        <td>{{ $material->batch->name ?? 'All' }}</td>
                        <td>{{ $material->subject->name ?? '-' }}</td>
                        <td>
                            @if($material->file_path)
                                <a class="btn btn-light" href="{{ asset('storage/' . $material->file_path) }}" target="_blank">Open File</a>
                            @elseif($material->external_link)
                                <a class="btn btn-light" href="{{ $material->external_link }}" target="_blank">Open Link</a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;color:#64748b;">No materials available.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
