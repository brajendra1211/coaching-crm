@extends('admin.layouts.app')

@section('title', 'Parent Details')
@section('page_title', 'Parent Details')

@section('content')

<style>
    .parent-detail-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 360px;
        gap: 22px;
        align-items: start;
    }

    .detail-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, .07);
        margin-bottom: 22px;
        overflow: hidden;
    }

    .detail-head {
        padding: 18px 22px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #fff, #f8fafc);
    }

    .detail-head h3 {
        margin: 0;
        color: #111827;
        font-size: 20px;
    }

    .detail-body {
        padding: 22px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .info-box {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 14px;
    }

    .info-box small {
        display: block;
        color: #64748b;
        font-weight: 900;
        margin-bottom: 6px;
        font-size: 12px;
        text-transform: uppercase;
    }

    .info-box strong {
        color: #111827;
        word-break: break-word;
    }

    .profile-card {
        text-align: center;
    }

    .parent-avatar-large {
        width: 120px;
        height: 120px;
        border-radius: 32px;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 52px;
        font-weight: 900;
        margin-bottom: 16px;
    }

    .status-pill {
        display: inline-flex;
        padding: 8px 12px;
        border-radius: 999px;
        font-weight: 900;
        font-size: 13px;
        border: 1px solid;
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

    @media(max-width: 900px) {
        .parent-detail-grid,
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="parent-detail-grid">
    <main>
        <div class="detail-card">
            <div class="detail-head">
                <h3>Parent Information</h3>
            </div>

            <div class="detail-body">
                <div class="info-grid">
                    <div class="info-box">
                        <small>Parent Name</small>
                        <strong>{{ $parent->name }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Relation</small>
                        <strong>{{ $parent->relation ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Phone</small>
                        <strong>{{ $parent->phone ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Alternate Phone</small>
                        <strong>{{ $parent->alternate_phone ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Email</small>
                        <strong>{{ $parent->email ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Occupation</small>
                        <strong>{{ $parent->occupation ?: '-' }}</strong>
                    </div>

                    <div class="info-box" style="grid-column:1/-1;">
                        <small>Address</small>
                        <strong>{{ $parent->address ?: '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-head">
                <h3>Mapped Student</h3>
            </div>

            <div class="detail-body">
                <div class="info-grid">
                    <div class="info-box">
                        <small>Student</small>
                        <strong>{{ optional($parent->student)->name ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Student Code</small>
                        <strong>{{ optional($parent->student)->student_code ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Course</small>
                        <strong>
                            {{ optional($parent->student)->course_name ?: optional(optional($parent->student)->course)->title ?: '-' }}
                        </strong>
                    </div>

                    <div class="info-box">
                        <small>Student Phone</small>
                        <strong>{{ optional($parent->student)->phone ?: '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <aside>
        <div class="detail-card">
            <div class="detail-head" style="background:linear-gradient(135deg,#2563eb,#7c3aed);">
                <h3 style="color:#fff;">Profile</h3>
            </div>

            <div class="detail-body">
                <div class="profile-card">
                    <div class="parent-avatar-large">
                        {{ strtoupper(mb_substr($parent->name, 0, 1)) }}
                    </div>

                    <h3 style="margin:0 0 6px;color:#111827;">{{ $parent->name }}</h3>

                    <p style="margin:0 0 12px;color:#64748b;">
                        {{ $parent->relation ?: 'Parent / Guardian' }}
                    </p>

                    <span class="status-pill status-{{ $parent->status }}">
                        {{ ucfirst($parent->status) }}
                    </span>
                </div>

                <div style="display:grid;gap:10px;margin-top:18px;">
                    <a href="{{ route('admin.parents.edit', $parent) }}" class="btn btn-primary">Edit Parent</a>
                    <a href="{{ route('admin.parents.index') }}" class="btn btn-light">Back to Parents</a>

                    @if($parent->student)
                        <a href="{{ route('admin.students.show', $parent->student) }}" class="btn btn-dark">
                            View Student
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </aside>
</div>

@endsection