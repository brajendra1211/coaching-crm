@extends('portal.layouts.app', ['portalRole' => 'teacher'])
@section('title', 'My Students')
@section('page_title', 'Students')
@section('page_subtitle', 'Students from your assigned batches')
@section('content')
<section class="card">
    <h3>Student List</h3>
    <div class="table-wrap"><table>
        <thead><tr><th>Student</th><th>Course</th><th>Class</th><th>Phone</th><th>Email</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($students as $student)
            <tr>
                <td><strong>{{ $student->name }}</strong><br><small class="muted">{{ $student->student_code }}</small></td>
                <td>{{ $student->course->title ?? $student->course_name ?? '-' }}</td>
                <td>{{ $student->class_level ?: '-' }}</td>
                <td>{{ $student->phone ?: '-' }}</td>
                <td>{{ $student->email ?: '-' }}</td>
                <td><span class="pill">{{ ucfirst($student->status) }}</span></td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;color:#64748b;">No students found.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    {{ $students->links() }}
</section>
@endsection
