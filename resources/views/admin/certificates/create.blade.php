@extends('admin.layouts.app')

@section('title', 'Create Certificate')
@section('page_title', 'Create Certificate')

@section('content')

<style>
    .certificate-form-wrap {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 360px;
        gap: 22px;
        align-items: start;
    }

    .form-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 12px 35px rgba(15,23,42,.07);
        margin-bottom: 22px;
        overflow: hidden;
    }

    .form-head {
        padding: 18px 22px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #fff, #f8fafc);
    }

    .form-head h3 {
        margin: 0;
        color: #111827;
        font-size: 20px;
    }

    .form-head p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .form-body {
        padding: 22px;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        font-weight: 900;
        color: #334155;
        margin-bottom: 8px;
    }

    .form-group textarea {
        min-height: 110px;
        resize: vertical;
    }

    .preview-box {
        background:
            radial-gradient(circle at top right, rgba(37,99,235,.12), transparent 30%),
            linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
        border-radius: 22px;
        padding: 22px;
        min-height: 250px;
        text-align: center;
    }

    .preview-box h2 {
        margin: 0;
        color: #fff;
        font-size: 28px;
    }

    .preview-box p {
        color: rgba(255,255,255,.86);
        line-height: 1.7;
    }

    .error-box {
        background:#fef2f2;
        color:#991b1b;
        border:1px solid #fecaca;
        padding:12px 14px;
        border-radius:12px;
        margin-bottom:15px;
    }

    @media(max-width: 1000px) {
        .certificate-form-wrap,
        .grid-2 {
            grid-template-columns: 1fr;
        }
    }
</style>

@if($errors->any())
    <div class="error-box">
        <strong>Please fix errors:</strong>
        <ul style="margin:8px 0 0;padding-left:18px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.certificates.store') }}">
    @csrf

    <div class="certificate-form-wrap">
        <main>
            <div class="form-card">
                <div class="form-head">
                    <h3>Student Details</h3>
                    <p>Select a student or enter recipient details manually.</p>
                </div>

                <div class="form-body">
                    <div class="form-group">
                        <label>Select Student</label>
                        <select name="student_id" id="studentSelect">
                            <option value="">Manual Certificate</option>
                            @foreach($students as $student)
                                <option
                                    value="{{ $student->id }}"
                                    data-name="{{ $student->name }}"
                                    data-code="{{ $student->student_code }}"
                                    data-course="{{ $student->course_name }}"
                                    data-class="{{ $student->class_level }}"
                                    data-phone="{{ $student->phone }}"
                                >
                                    {{ $student->name }} | {{ $student->student_code ?? 'STU-' . $student->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Recipient Name *</label>
                            <input type="text" name="recipient_name" id="recipientName" value="{{ old('recipient_name') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Student Code</label>
                            <input type="text" name="student_code" id="studentCode" value="{{ old('student_code') }}">
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Course Name</label>
                            <input type="text" name="course_name" id="courseName" value="{{ old('course_name') }}">
                        </div>

                        <div class="form-group">
                            <label>Class / Level</label>
                            <input type="text" name="class_level" id="classLevel" value="{{ old('class_level') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Batch Name</label>
                        <input type="text" name="batch_name" value="{{ old('batch_name') }}">
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="form-head">
                    <h3>Certificate Details</h3>
                    <p>Set certificate title, type, date and remarks.</p>
                </div>

                <div class="form-body">
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Certificate Title *</label>
                            <input type="text" name="certificate_title" id="certificateTitle" value="{{ old('certificate_title', 'Certificate of Completion') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Certificate Type *</label>
                            <select name="certificate_type" required>
                                <option value="completion">Completion</option>
                                <option value="participation">Participation</option>
                                <option value="achievement">Achievement</option>
                                <option value="training">Training</option>
                                <option value="appreciation">Appreciation</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Issue Date</label>
                            <input type="date" name="issue_date" value="{{ old('issue_date', now()->format('Y-m-d')) }}">
                        </div>

                        <div class="form-group">
                            <label>Completion Date</label>
                            <input type="date" name="completion_date" value="{{ old('completion_date') }}">
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Grade / Rank</label>
                            <input type="text" name="grade" value="{{ old('grade') }}" placeholder="A+, First Rank, Excellent">
                        </div>

                        <div class="form-group">
                            <label>Duration</label>
                            <input type="text" name="duration" value="{{ old('duration') }}" placeholder="3 Months / 6 Weeks">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" id="descriptionText" placeholder="Certificate description">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea name="remarks">{{ old('remarks') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="form-head">
                    <h3>Template & Signature</h3>
                    <p>Choose certificate design and signatory details.</p>
                </div>

                <div class="form-body">
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Template *</label>
                            <select name="template" required>
                                <option value="premium">Premium</option>
                                <option value="classic">Classic</option>
                                <option value="minimal">Minimal</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Status *</label>
                            <select name="status" required>
                                <option value="active">Active</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Signed By</label>
                            <input type="text" name="signed_by" value="{{ old('signed_by') }}" placeholder="Director / Principal Name">
                        </div>

                        <div class="form-group">
                            <label>Signature Title</label>
                            <input type="text" name="signature_title" value="{{ old('signature_title', 'Director') }}">
                        </div>
                    </div>

                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        <button type="submit" class="btn btn-primary">Create Certificate</button>
                        <a href="{{ route('admin.certificates.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>
            </div>
        </main>

        <aside>
            <div class="form-card">
                <div class="form-head" style="background:linear-gradient(135deg,#2563eb,#7c3aed);">
                    <h3 style="color:#fff;">Live Preview</h3>
                    <p style="color:rgba(255,255,255,.86);">Certificate content preview.</p>
                </div>

                <div class="form-body">
                    <div class="preview-box">
                        <h2 id="previewTitle">Certificate of Completion</h2>
                        <p>This certificate is proudly presented to</p>
                        <h3 id="previewName" style="color:#fff;font-size:24px;margin:10px 0;">Student Name</h3>
                        <p id="previewCourse">for successful completion of the selected course.</p>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const studentSelect = document.getElementById('studentSelect');
        const recipientName = document.getElementById('recipientName');
        const studentCode = document.getElementById('studentCode');
        const courseName = document.getElementById('courseName');
        const classLevel = document.getElementById('classLevel');

        const certificateTitle = document.getElementById('certificateTitle');
        const previewTitle = document.getElementById('previewTitle');
        const previewName = document.getElementById('previewName');
        const previewCourse = document.getElementById('previewCourse');

        function updatePreview() {
            previewTitle.textContent = certificateTitle.value || 'Certificate';
            previewName.textContent = recipientName.value || 'Student Name';
            previewCourse.textContent = courseName.value
                ? 'for successful completion of ' + courseName.value + '.'
                : 'for successful completion of the selected course.';
        }

        studentSelect.addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];

            if (!selected || !selected.value) {
                return;
            }

            recipientName.value = selected.dataset.name || '';
            studentCode.value = selected.dataset.code || '';
            courseName.value = selected.dataset.course || '';
            classLevel.value = selected.dataset.class || '';

            updatePreview();
        });

        [recipientName, courseName, certificateTitle].forEach(function (input) {
            input.addEventListener('input', updatePreview);
        });

        updatePreview();
    });
</script>

@endsection