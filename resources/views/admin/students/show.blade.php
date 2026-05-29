@extends('admin.layouts.app')

@section('title', 'Student Details')
@section('page_title', 'Student Details')

@section('content')

<style>
    .student-detail-grid {
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

    .student-profile {
        text-align: center;
    }

    .student-profile img,
    .student-avatar {
        width: 130px;
        height: 130px;
        border-radius: 32px;
        object-fit: cover;
        object-position: center top;
        margin: 0 auto 16px;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 52px;
        font-weight: 900;
    }

    .status-pill {
        display: inline-flex;
        padding: 8px 12px;
        border-radius: 999px;
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
        font-weight: 900;
        font-size: 13px;
    }

    @media(max-width: 900px) {
        .student-detail-grid,
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="student-detail-grid">
    <main>
        <div class="detail-card">
            <div class="detail-head">
                <h3>Student Information</h3>
            </div>

            <div class="detail-body">
                <div class="info-grid">
                    <div class="info-box">
                        <small>Student Name</small>
                        <strong>{{ $student->name }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Student Code</small>
                        <strong>{{ $student->student_code }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Phone</small>
                        <strong>{{ $student->phone ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Email</small>
                        <strong>{{ $student->email ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>DOB / Gender</small>
                        <strong>
                            {{ $student->dob ? $student->dob->format('d M Y') : '-' }}
                            /
                            {{ $student->gender ?: '-' }}
                        </strong>
                    </div>

                    <div class="info-box">
                        <small>Joining Date</small>
                        <strong>{{ $student->joining_date ? $student->joining_date->format('d M Y') : '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-head">
                <h3>Course Details</h3>
            </div>

            <div class="detail-body">
                <div class="info-grid">
                    <div class="info-box">
                        <small>Course</small>
                        <strong>{{ $student->course_name ?: optional($student->course)->title ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Class / Level</small>
                        <strong>{{ $student->class_level ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Status</small>
                        <span class="status-pill">{{ ucwords(str_replace('_', ' ', $student->status)) }}</span>
                    </div>

                    <div class="info-box">
                        <small>Admission</small>
                        <strong>{{ optional($student->admission)->admission_no ?: '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-head">
                <h3>Parent Details</h3>
            </div>

            <div class="detail-body">
                <div class="info-grid">
                    <div class="info-box">
                        <small>Parent Name</small>
                        <strong>{{ optional($student->parent)->name ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Relation</small>
                        <strong>{{ optional($student->parent)->relation ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Phone</small>
                        <strong>{{ optional($student->parent)->phone ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Email</small>
                        <strong>{{ optional($student->parent)->email ?: '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-head">
                <h3>Address</h3>
            </div>

            <div class="detail-body">
                <p style="margin:0;color:#475569;line-height:1.8;">
                    {{ $student->address ?: '-' }}

                    @if($student->city || $student->state || $student->pincode)
                        <br>
                        {{ $student->city }} {{ $student->state }} {{ $student->pincode }}
                    @endif
                </p>
            </div>
        </div>
    </main>

    <aside>
        <div class="detail-card">
            <div class="detail-head" style="background:linear-gradient(135deg,#2563eb,#7c3aed);">
                <h3 style="color:#fff;">Profile</h3>
            </div>

            <div class="detail-body">
                <div class="student-profile">
                    @if($student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}">
                    @else
                        <div class="student-avatar">
                            {{ strtoupper(mb_substr($student->name, 0, 1)) }}
                        </div>
                    @endif

                    <h3 style="margin:0 0 6px;color:#111827;">{{ $student->name }}</h3>
                    <p style="margin:0 0 16px;color:#64748b;">{{ $student->student_code }}</p>
                </div>

                <div style="display:grid;gap:10px;">
                    <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-primary">Edit Student</a>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-light">Back to Students</a>

                    @if($student->admission)
                        <a href="{{ route('admin.admissions.show', $student->admission) }}" class="btn btn-dark">
                            View Admission
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </aside>
</div>

@endsection