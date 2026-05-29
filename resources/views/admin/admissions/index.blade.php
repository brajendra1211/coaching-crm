@extends('admin.layouts.app')

@section('title', 'Admissions')
@section('page_title', 'Admissions CRM')

@section('content')

<style>
    .crm-toolbar {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 190px auto;
        gap: 12px;
        margin-bottom: 18px;
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

    .status-new { background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe; }
    .status-counselling { background:#fef3c7;color:#92400e;border-color:#fde68a; }
    .status-document_pending { background:#f5f3ff;color:#6d28d9;border-color:#ddd6fe; }
    .status-admitted { background:#dcfce7;color:#166534;border-color:#bbf7d0; }
    .status-rejected { background:#fee2e2;color:#991b1b;border-color:#fecaca; }
    .status-cancelled { background:#f1f5f9;color:#475569;border-color:#cbd5e1; }

    .action-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .mini-muted {
        color: #64748b;
        font-size: 12px;
    }

    @media(max-width: 850px) {
        .crm-toolbar {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Admissions</h3>
            <p style="margin:6px 0 0;color:#64748b;">
                Manage counselling, admission, student and parent records.
            </p>
        </div>

        <a href="{{ route('admin.admissions.create') }}" class="btn btn-primary">+ New Admission</a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" class="crm-toolbar">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search admission no, student, phone, parent"
        >

        <select name="status">
            <option value="">All Status</option>
            <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
            <option value="counselling" {{ request('status') === 'counselling' ? 'selected' : '' }}>Counselling</option>
            <option value="document_pending" {{ request('status') === 'document_pending' ? 'selected' : '' }}>Document Pending</option>
            <option value="admitted" {{ request('status') === 'admitted' ? 'selected' : '' }}>Admitted</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>

        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Admission</th>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Parent</th>
                    <th>Fees</th>
                    <th>Status</th>
                    <th width="270">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($admissions as $admission)
                    <tr>
                        <td>
                            <strong>{{ $admission->admission_no }}</strong>
                            <br>
                            <span class="mini-muted">
                                {{ $admission->admission_date ? $admission->admission_date->format('d M Y') : '-' }}
                            </span>
                        </td>

                        <td>
                            <strong>{{ $admission->student_name }}</strong>
                            <br>
                            <span class="mini-muted">
                                {{ $admission->student_phone ?: '-' }}
                            </span>
                            @if($admission->student)
                                <br>
                                <span class="mini-muted">
                                    Student Code: {{ $admission->student->student_code }}
                                </span>
                            @endif
                        </td>

                        <td>
                            {{ $admission->course_name ?: optional($admission->course)->title ?: '-' }}
                            <br>
                            <span class="mini-muted">{{ $admission->class_level ?: '-' }}</span>
                        </td>

                        <td>
                            {{ $admission->parent_name ?: '-' }}
                            <br>
                            <span class="mini-muted">{{ $admission->parent_phone ?: '-' }}</span>
                        </td>

                        <td>
                            <strong>₹{{ number_format(($admission->registration_fee ?? 0) + ($admission->admission_fee ?? 0), 2) }}</strong>
                            <br>
                            <span class="mini-muted">
                                Reg: ₹{{ number_format($admission->registration_fee ?? 0, 2) }}
                            </span>
                        </td>

                        <td>
                            <span class="status-pill status-{{ $admission->status }}">
                                {{ ucwords(str_replace('_', ' ', $admission->status)) }}
                            </span>
                        </td>

                        <td>
                            <div class="action-row">
                                <a href="{{ route('admin.admissions.show', $admission) }}" class="btn btn-light">View</a>
                                <a href="{{ route('admin.admissions.edit', $admission) }}" class="btn btn-primary">Edit</a>

                                <form method="POST" action="{{ route('admin.admissions.destroy', $admission) }}" onsubmit="return confirm('Delete this admission?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:35px;color:#64748b;">
                            No admissions found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:22px;">
        {{ $admissions->links() }}
    </div>
</div>

@endsection