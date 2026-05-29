@extends('admin.layouts.app')

@section('title', 'ID Cards')
@section('page_title', 'ID Cards')

@section('content')

<style>
    .id-toolbar {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 170px auto;
        gap: 12px;
        margin-bottom: 18px;
    }

    .id-card-list {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 12px 35px rgba(15,23,42,.07);
        padding: 22px;
    }

    .student-mini {
        display: flex;
        align-items: center;
        gap: 11px;
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
        flex-shrink: 0;
    }

    .student-mini strong {
        display: block;
        color: #111827;
    }

    .student-mini small {
        color: #64748b;
        font-weight: 700;
    }

    .action-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
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

    @media(max-width: 850px) {
        .id-toolbar {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="id-card-list">
    <div class="card-header">
        <div>
            <h3>Student ID Cards</h3>
            <p style="margin:6px 0 0;color:#64748b;">
                Generate, preview and download student identity cards.
            </p>
        </div>
    </div>

    <form method="GET" class="id-toolbar">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search by student name, code, phone, email..."
        >

        <select name="status">
            <option value="">All Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>

        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Course / Class</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th width="260">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td>
                            <div class="student-mini">
                                <span class="student-avatar">
                                    {{ strtoupper(mb_substr($student->name ?? 'S', 0, 1)) }}
                                </span>

                                <div>
                                    <strong>{{ $student->name ?? '-' }}</strong>
                                    <small>{{ $student->student_code ?? 'STU-' . $student->id }}</small>
                                </div>
                            </div>
                        </td>

                        <td>
                            {{ $student->course_name ?? '-' }}
                            <br>
                            <small style="color:#64748b;">{{ $student->class_level ?? '-' }}</small>
                        </td>

                        <td>{{ $student->phone ?? '-' }}</td>

                        <td>
                            <span class="status-pill status-{{ $student->status ?? 'active' }}">
                                {{ ucfirst($student->status ?? 'active') }}
                            </span>
                        </td>

                        <td>
                            <div class="action-row">
                                <a href="{{ route('admin.id-cards.show', $student) }}" class="btn btn-light">
                                    Preview
                                </a>

                                <a href="{{ route('admin.id-cards.pdf', ['student' => $student->id, 'template' => 'premium']) }}" class="btn btn-primary">
                                    Download PDF
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:35px;color:#64748b;">
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