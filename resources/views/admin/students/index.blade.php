@extends('admin.layouts.app')

@section('title', 'Students')
@section('page_title', 'Students')

@section('content')

<style>
    .student-toolbar {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 210px 160px auto;
        gap: 12px;
        margin-bottom: 18px;
    }

    .student-avatar {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        object-fit: cover;
        object-position: center top;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        border: 1px solid #e5e7eb;
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

    .status-active {
        background: #dcfce7;
        color: #166534;
        border-color: #bbf7d0;
    }

    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .status-passed_out {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    .status-left {
        background: #f1f5f9;
        color: #475569;
        border-color: #cbd5e1;
    }

    .action-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    @media(max-width: 1000px) {
        .student-toolbar {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Students</h3>
            <p style="margin:6px 0 0;color:#64748b;">
                Manage student profiles, course details and parent information.
            </p>
        </div>

        <a href="{{ route('admin.students.create') }}" class="btn btn-primary">+ Add Student</a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" class="student-toolbar">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search student code, name, phone, course"
        >

        <select name="course_id">
            <option value="">All Courses</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                    {{ $course->title }}
                </option>
            @endforeach
        </select>

        <select name="status">
            <option value="">All Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="passed_out" {{ request('status') === 'passed_out' ? 'selected' : '' }}>Passed Out</option>
            <option value="left" {{ request('status') === 'left' ? 'selected' : '' }}>Left</option>
        </select>

        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Parent</th>
                    <th>Joining</th>
                    <th>Status</th>
                    <th width="260">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td>
                            <div style="display:flex;gap:12px;align-items:center;">
                                @if($student->photo)
                                    <img src="{{ asset('storage/' . $student->photo) }}" class="student-avatar" alt="{{ $student->name }}">
                                @else
                                    <div class="student-avatar">
                                        {{ strtoupper(mb_substr($student->name, 0, 1)) }}
                                    </div>
                                @endif

                                <div>
                                    <strong>{{ $student->name }}</strong>
                                    <br>
                                    <small style="color:#64748b;">{{ $student->student_code }}</small>
                                    <br>
                                    <small style="color:#64748b;">{{ $student->phone ?: '-' }}</small>
                                </div>
                            </div>
                        </td>

                        <td>
                            {{ $student->course_name ?: optional($student->course)->title ?: '-' }}
                            <br>
                            <small style="color:#64748b;">{{ $student->class_level ?: '-' }}</small>
                        </td>

                        <td>
                            {{ optional($student->parent)->name ?: '-' }}
                            <br>
                            <small style="color:#64748b;">
                                {{ optional($student->parent)->phone ?: '-' }}
                            </small>
                        </td>

                        <td>
                            {{ $student->joining_date ? $student->joining_date->format('d M Y') : '-' }}
                        </td>

                        <td>
                            <span class="status-pill status-{{ $student->status }}">
                                {{ ucwords(str_replace('_', ' ', $student->status)) }}
                            </span>
                        </td>

                        <td>
                            <div class="action-row">
                                <a href="{{ route('admin.students.show', $student) }}" class="btn btn-light">View</a>
                                <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-primary">Edit</a>

                                <form method="POST" action="{{ route('admin.students.destroy', $student) }}" onsubmit="return confirm('Delete this student?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:35px;color:#64748b;">
                            No students found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:22px;">
        {{ $students->links() }}
    </div>
</div>

@endsection