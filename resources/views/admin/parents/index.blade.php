@extends('admin.layouts.app')

@section('title', 'Parents')
@section('page_title', 'Parents / Guardians')

@section('content')

<style>
    .parent-toolbar {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 220px 160px auto;
        gap: 12px;
        margin-bottom: 18px;
    }

    .parent-avatar {
        width: 54px;
        height: 54px;
        border-radius: 18px;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 20px;
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

    .action-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    @media(max-width: 1000px) {
        .parent-toolbar {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Parents / Guardians</h3>
            <p style="margin:6px 0 0;color:#64748b;">
                Manage parent contacts, relation, occupation and student mapping.
            </p>
        </div>

        <a href="{{ route('admin.parents.create') }}" class="btn btn-primary">+ Add Parent</a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" class="parent-toolbar">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search parent name, phone, email, relation"
        >

        <select name="student_id">
            <option value="">All Students</option>
            @foreach($students as $student)
                <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                    {{ $student->name }} - {{ $student->student_code }}
                </option>
            @endforeach
        </select>

        <select name="status">
            <option value="">All Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>

        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Parent</th>
                    <th>Student</th>
                    <th>Contact</th>
                    <th>Relation</th>
                    <th>Status</th>
                    <th width="260">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($parents as $parent)
                    <tr>
                        <td>
                            <div style="display:flex;gap:12px;align-items:center;">
                                <div class="parent-avatar">
                                    {{ strtoupper(mb_substr($parent->name, 0, 1)) }}
                                </div>

                                <div>
                                    <strong>{{ $parent->name }}</strong>
                                    <br>
                                    <small style="color:#64748b;">{{ $parent->occupation ?: '-' }}</small>
                                </div>
                            </div>
                        </td>

                        <td>
                            {{ optional($parent->student)->name ?: '-' }}
                            <br>
                            <small style="color:#64748b;">
                                {{ optional($parent->student)->student_code ?: '-' }}
                            </small>
                        </td>

                        <td>
                            {{ $parent->phone ?: '-' }}
                            <br>
                            <small style="color:#64748b;">{{ $parent->email ?: '-' }}</small>
                            @if($parent->alternate_phone)
                                <br>
                                <small style="color:#64748b;">Alt: {{ $parent->alternate_phone }}</small>
                            @endif
                        </td>

                        <td>{{ $parent->relation ?: '-' }}</td>

                        <td>
                            <span class="status-pill status-{{ $parent->status }}">
                                {{ ucfirst($parent->status) }}
                            </span>
                        </td>

                        <td>
                            <div class="action-row">
                                <a href="{{ route('admin.parents.show', $parent) }}" class="btn btn-light">View</a>
                                <a href="{{ route('admin.parents.edit', $parent) }}" class="btn btn-primary">Edit</a>

                                <form method="POST" action="{{ route('admin.parents.destroy', $parent) }}" onsubmit="return confirm('Delete this parent record?')">
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
                            No parent records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:22px;">
        {{ $parents->links() }}
    </div>
</div>

@endsection