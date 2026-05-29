@extends('portal.layouts.app', ['portalRole' => 'teacher'])

@section('title', 'Teacher Dashboard')
@section('page_title', 'Teacher Dashboard')
@section('page_subtitle', 'Batches, students, exams, materials and attendance overview')

@section('content')
@if(!$teacher)
    <div class="card" style="margin-bottom:18px;">
        <h3>Profile Not Linked</h3>
        <p style="margin:0;color:#64748b;">This login is active, but no teacher record is linked yet. Ask admin to link this user with a teacher profile or use the same email/phone in teacher record.</p>
    </div>
@endif

<section class="card" style="margin-bottom:18px;">
    <div class="card-header" style="display:flex;justify-content:space-between;gap:14px;align-items:center;flex-wrap:wrap;">
        <div>
            <h3>Teacher Workbench</h3>
            <p style="margin:6px 0 0;color:#64748b;">Create questions, build exams, upload study materials and manage academic work.</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('teacher.questions.import') }}" class="btn btn-primary">Import Questions</a>
            <a href="{{ route('teacher.questions.index') }}" class="btn btn-light">Question Bank</a>
            <a href="{{ route('teacher.exams.index') }}" class="btn btn-light">Exams</a>
            <a href="{{ url('/teacher/materials') }}" class="btn btn-light">Study Materials</a>
        </div>
    </div>
</section>

<div class="page-actions">
    <a href="{{ url('/teacher/batches') }}" class="btn btn-primary">My Batches</a>
    <a href="{{ url('/teacher/exams') }}" class="btn btn-light">Exams</a>
    <a href="{{ url('/teacher/series') }}" class="btn btn-light">Series</a>
    <a href="{{ url('/teacher/questions') }}" class="btn btn-light">Questions</a>
    <a href="{{ url('/teacher/questions/import') }}" class="btn btn-light">Import Excel</a>
    <a href="{{ url('/teacher/materials') }}" class="btn btn-light">Materials</a>
    <a href="{{ url('/teacher/attendance') }}" class="btn btn-light">Attendance</a>
    <a href="{{ url('/teacher/classes') }}" class="btn btn-light">Online Classes</a>
</div>

<div class="grid-4">
    <div class="card stat"><small>My Batches</small><strong>{{ $batches->count() }}</strong></div>
    <div class="card stat"><small>Students</small><strong>{{ $studentsCount }}</strong></div>
    <div class="card stat"><small>Exams</small><strong>{{ $exams->count() }}</strong></div>
    <div class="card stat"><small>Question Bank</small><strong>{{ $questionsCount }}</strong></div>
</div>

<div class="grid-2">
    <section class="card">
        <h3>Students</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Student</th><th>Class</th><th>Phone</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td><strong>{{ $student->name }}</strong><br><small>{{ $student->student_code }}</small></td>
                            <td>{{ $student->class_level ?: '-' }}</td>
                            <td>{{ $student->phone ?: '-' }}</td>
                            <td><span class="pill">{{ ucfirst($student->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;color:#64748b;">No students found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="card">
        <h3>Online Classes</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Class</th><th>Date</th><th>Batch</th><th>Join</th></tr></thead>
                <tbody>
                    @forelse($classes as $class)
                        <tr>
                            <td>{{ $class->title }}</td>
                            <td>{{ $class->class_date ? $class->class_date->format('d M Y') : '-' }}</td>
                            <td>{{ $class->batch->name ?? 'All' }}</td>
                            <td>
                                @if($class->meeting_link)
                                    <a href="{{ $class->meeting_link }}" target="_blank" class="btn btn-light">Open</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;color:#64748b;">No classes found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<div class="grid-2">
    <section class="card" id="teacher-batches">
        <h3>My Batches</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Batch</th><th>Class</th><th>Students</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($batches as $batch)
                        <tr>
                            <td><strong>{{ $batch->name }}</strong><br><small>{{ $batch->code ?: '-' }}</small></td>
                            <td>{{ $batch->class_level ?: '-' }}</td>
                            <td>{{ $batch->students_count }}</td>
                            <td><span class="pill">{{ ucfirst($batch->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;color:#64748b;">No batches assigned.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="card" id="teacher-exams">
        <h3>Exams</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Exam</th><th>Batch</th><th>Marks</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($exams as $exam)
                        <tr>
                            <td><strong>{{ $exam->title }}</strong><br><small>{{ $exam->label ?: ucfirst($exam->difficulty ?? 'medium') }}</small></td>
                            <td>{{ $exam->batch->name ?? 'All' }}</td>
                            <td>{{ $exam->total_marks }}</td>
                            <td><span class="pill">{{ ucfirst($exam->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;color:#64748b;">No exams found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<div class="grid-2">
    <section class="card" id="teacher-materials">
        <h3>Study Materials</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Title</th><th>Batch</th><th>Subject</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($materials as $material)
                        <tr>
                            <td>{{ $material->title }}</td>
                            <td>{{ $material->batch->name ?? 'All' }}</td>
                            <td>{{ $material->subject->name ?? '-' }}</td>
                            <td><span class="pill">{{ ucfirst($material->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;color:#64748b;">No materials found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="card" id="teacher-attendance">
        <h3>Recent Attendance</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Date</th><th>Student</th><th>Batch</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($attendance as $row)
                        <tr>
                            <td>{{ $row->attendance_date ? $row->attendance_date->format('d M Y') : '-' }}</td>
                            <td>{{ $row->student->name ?? '-' }}</td>
                            <td>{{ $row->batch->name ?? '-' }}</td>
                            <td><span class="pill {{ $row->status === 'absent' ? 'red' : 'green' }}">{{ ucfirst($row->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;color:#64748b;">No attendance found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
