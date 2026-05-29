@extends('admin.layouts.app')

@section('title', 'Admission Details')
@section('page_title', 'Admission Details')

@section('content')

<style>
    .detail-grid {
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
        letter-spacing: .4px;
    }

    .info-box strong {
        color: #111827;
        line-height: 1.4;
        word-break: break-word;
    }

    .status-pill {
        display: inline-flex;
        padding: 8px 12px;
        border-radius: 999px;
        font-weight: 900;
        font-size: 13px;
        border: 1px solid;
    }

    .status-new {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    .status-counselling {
        background: #fef3c7;
        color: #92400e;
        border-color: #fde68a;
    }

    .status-document_pending {
        background: #f5f3ff;
        color: #6d28d9;
        border-color: #ddd6fe;
    }

    .status-admitted {
        background: #dcfce7;
        color: #166534;
        border-color: #bbf7d0;
    }

    .status-rejected {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .status-cancelled {
        background: #f1f5f9;
        color: #475569;
        border-color: #cbd5e1;
    }

    .summary-card {
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
    }

    .summary-card .detail-head {
        background: transparent;
        border-bottom: 1px solid rgba(255,255,255,.18);
    }

    .summary-card .detail-head h3 {
        color: #fff;
    }

    .summary-card .info-box {
        background: rgba(255,255,255,.12);
        border-color: rgba(255,255,255,.18);
    }

    .summary-card .info-box small,
    .summary-card .info-box strong {
        color: #fff;
    }

    .action-stack {
        display: grid;
        gap: 10px;
        margin-top: 18px;
    }

    @media(max-width: 900px) {
        .detail-grid,
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="detail-grid">
    <main>
        <div class="detail-card">
            <div class="detail-head">
                <h3>Student Information</h3>
            </div>

            <div class="detail-body">
                <div class="info-grid">
                    <div class="info-box">
                        <small>Student Name</small>
                        <strong>{{ $admission->student_name }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Student Phone</small>
                        <strong>{{ $admission->student_phone ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Student Email</small>
                        <strong>{{ $admission->student_email ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>DOB / Gender</small>
                        <strong>
                            {{ $admission->dob ? $admission->dob->format('d M Y') : '-' }}
                            /
                            {{ $admission->gender ?: '-' }}
                        </strong>
                    </div>

                    <div class="info-box">
                        <small>Course</small>
                        <strong>{{ $admission->course_name ?: optional($admission->course)->title ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Class / Level</small>
                        <strong>{{ $admission->class_level ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Previous School</small>
                        <strong>{{ $admission->previous_school ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Source</small>
                        <strong>{{ $admission->source ?: '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-head">
                <h3>Parent / Guardian Details</h3>
            </div>

            <div class="detail-body">
                <div class="info-grid">
                    <div class="info-box">
                        <small>Parent Name</small>
                        <strong>{{ $admission->parent_name ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Relation</small>
                        <strong>{{ $admission->parent_relation ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Parent Phone</small>
                        <strong>{{ $admission->parent_phone ?: '-' }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Parent Email</small>
                        <strong>{{ $admission->parent_email ?: '-' }}</strong>
                    </div>

                    <div class="info-box" style="grid-column:1/-1;">
                        <small>Address</small>
                        <strong>
                            {{ $admission->address ?: '-' }}

                            @if($admission->city || $admission->state || $admission->pincode)
                                <br>
                                {{ $admission->city }} {{ $admission->state }} {{ $admission->pincode }}
                            @endif
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-head">
                <h3>Notes</h3>
            </div>

            <div class="detail-body">
                <p style="margin:0;color:#475569;line-height:1.8;">
                    {{ $admission->notes ?: 'No notes added.' }}
                </p>
            </div>
        </div>
    </main>

    <aside>
        <div class="detail-card summary-card">
            <div class="detail-head">
                <h3>Admission Summary</h3>
            </div>

            <div class="detail-body">
                <div style="display:grid;gap:13px;">
                    <div class="info-box">
                        <small>Admission No</small>
                        <strong>{{ $admission->admission_no }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Admission Date</small>
                        <strong>
                            {{ $admission->admission_date ? $admission->admission_date->format('d M Y') : '-' }}
                        </strong>
                    </div>

                    <div class="info-box">
                        <small>Status</small>
                        <span class="status-pill status-{{ $admission->status }}">
                            {{ ucwords(str_replace('_', ' ', $admission->status)) }}
                        </span>
                    </div>

                    <div class="info-box">
                        <small>Registration Fee</small>
                        <strong>₹{{ number_format($admission->registration_fee ?? 0, 2) }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Admission Fee</small>
                        <strong>₹{{ number_format($admission->admission_fee ?? 0, 2) }}</strong>
                    </div>

                    <div class="info-box">
                        <small>Total Initial Fee</small>
                        <strong>
                            ₹{{ number_format(($admission->registration_fee ?? 0) + ($admission->admission_fee ?? 0), 2) }}
                        </strong>
                    </div>

                    @if($admission->student)
                        <div class="info-box">
                            <small>Student Code</small>
                            <strong>{{ $admission->student->student_code }}</strong>
                        </div>
                    @endif
                </div>

                <div class="action-stack">
                    <a href="{{ route('admin.admissions.edit', $admission) }}" class="btn btn-light">
                        Edit Admission
                    </a>

                    <a href="{{ route('admin.admissions.index') }}" class="btn btn-light">
                        Back to Admissions
                    </a>

                    @if($admission->student)
                        <a href="{{ route('admin.students.show', $admission->student) }}" class="btn btn-dark">
                            View Student
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </aside>
</div>

@endsection