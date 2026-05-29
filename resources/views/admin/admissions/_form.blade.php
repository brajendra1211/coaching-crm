<style>
    .admission-form-wrap {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 360px;
        gap: 22px;
        align-items: start;
    }

    .form-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, .07);
        margin-bottom: 22px;
        overflow: hidden;
    }

    .form-card-head {
        padding: 18px 22px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #fff, #f8fafc);
    }

    .form-card-head h3 {
        margin: 0;
        color: #111827;
        font-size: 19px;
    }

    .form-card-head p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
    }

    .form-card-body {
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

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 14px;
        padding: 13px 14px;
        font-size: 15px;
        outline: none;
        background: #fff;
        color: #111827;
        font-family: inherit;
    }

    .form-group textarea {
        min-height: 115px;
        resize: vertical;
        line-height: 1.7;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .10);
    }

    .error-box {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: 14px;
        border-radius: 14px;
        margin-bottom: 18px;
    }

    .help {
        display: block;
        margin-top: 6px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
    }

    .status-help-card {
        padding: 14px;
        border-radius: 18px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        color: #64748b;
        font-size: 13px;
        line-height: 1.7;
    }

    .status-help-card strong {
        color: #111827;
    }

    .fee-summary {
        background: linear-gradient(135deg, #eff6ff, #fff);
        border: 1px solid #bfdbfe;
        border-radius: 20px;
        padding: 16px;
        margin-top: 12px;
    }

    .fee-summary small {
        display: block;
        color: #64748b;
        font-weight: 900;
        margin-bottom: 6px;
    }

    .fee-summary strong {
        color: #2563eb;
        font-size: 30px;
        line-height: 1;
    }

    @media(max-width: 1050px) {
        .admission-form-wrap,
        .grid-2 {
            grid-template-columns: 1fr;
        }
    }
</style>

@if($errors->any())
    <div class="error-box">
        <strong>Please fix errors:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $status = old('status', $admission->status ?: 'new');
@endphp

<div class="admission-form-wrap">
    <div>
        <div class="form-card">
            <div class="form-card-head">
                <h3>Admission Details</h3>
                <p>Admission number, date, course and admission source.</p>
            </div>

            <div class="form-card-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Admission No</label>
                        <input
                            type="text"
                            name="admission_no"
                            value="{{ old('admission_no', $admission->admission_no) }}"
                            placeholder="Auto generated"
                        >
                    </div>

                    <div class="form-group">
                        <label>Admission Date</label>
                        <input
                            type="date"
                            name="admission_date"
                            value="{{ old('admission_date', $admission->admission_date ? $admission->admission_date->format('Y-m-d') : now()->format('Y-m-d')) }}"
                        >
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Course</label>
                        <select name="course_id" id="courseSelect">
                            <option value="">Select Course</option>
                            @foreach($courses as $course)
                                <option
                                    value="{{ $course->id }}"
                                    data-title="{{ $course->title }}"
                                    data-class="{{ $course->class_level ?? '' }}"
                                    {{ old('course_id', $admission->course_id) == $course->id ? 'selected' : '' }}
                                >
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Course Name</label>
                        <input
                            type="text"
                            name="course_name"
                            id="courseNameInput"
                            value="{{ old('course_name', $admission->course_name) }}"
                            placeholder="Auto fill or manual"
                        >
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Class / Level</label>
                        <input
                            type="text"
                            name="class_level"
                            id="classLevelInput"
                            value="{{ old('class_level', $admission->class_level) }}"
                            placeholder="Class 10 / NEET / JEE"
                        >
                    </div>

                    <div class="form-group">
                        <label>Source</label>
                        <select name="source">
                            @php $source = old('source', $admission->source); @endphp
                            <option value="">Select Source</option>
                            <option value="Walk-in" {{ $source === 'Walk-in' ? 'selected' : '' }}>Walk-in</option>
                            <option value="Website" {{ $source === 'Website' ? 'selected' : '' }}>Website</option>
                            <option value="Google" {{ $source === 'Google' ? 'selected' : '' }}>Google</option>
                            <option value="Facebook" {{ $source === 'Facebook' ? 'selected' : '' }}>Facebook</option>
                            <option value="Instagram" {{ $source === 'Instagram' ? 'selected' : '' }}>Instagram</option>
                            <option value="Referral" {{ $source === 'Referral' ? 'selected' : '' }}>Referral</option>
                            <option value="Other" {{ $source === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="form-card-head">
                <h3>Student Details</h3>
                <p>Basic student information for admission and student record.</p>
            </div>

            <div class="form-card-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Student Name *</label>
                        <input
                            type="text"
                            name="student_name"
                            value="{{ old('student_name', $admission->student_name) }}"
                            required
                            placeholder="Student full name"
                        >
                    </div>

                    <div class="form-group">
                        <label>Student Phone</label>
                        <input
                            type="text"
                            name="student_phone"
                            value="{{ old('student_phone', $admission->student_phone) }}"
                            placeholder="Mobile number"
                        >
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Student Email</label>
                        <input
                            type="email"
                            name="student_email"
                            value="{{ old('student_email', $admission->student_email) }}"
                            placeholder="student@email.com"
                        >
                    </div>

                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input
                            type="date"
                            name="dob"
                            value="{{ old('dob', $admission->dob ? $admission->dob->format('Y-m-d') : '') }}"
                        >
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Gender</label>
                        @php $gender = old('gender', $admission->gender); @endphp
                        <select name="gender">
                            <option value="">Select Gender</option>
                            <option value="Male" {{ $gender === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ $gender === 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ $gender === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Previous School</label>
                        <input
                            type="text"
                            name="previous_school"
                            value="{{ old('previous_school', $admission->previous_school) }}"
                            placeholder="Previous school/college"
                        >
                    </div>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="form-card-head">
                <h3>Parent / Guardian Details</h3>
                <p>Parent contact information for communication.</p>
            </div>

            <div class="form-card-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Parent Name</label>
                        <input
                            type="text"
                            name="parent_name"
                            value="{{ old('parent_name', $admission->parent_name) }}"
                            placeholder="Parent/guardian name"
                        >
                    </div>

                    <div class="form-group">
                        <label>Relation</label>
                        @php $relation = old('parent_relation', $admission->parent_relation); @endphp
                        <select name="parent_relation">
                            <option value="">Select Relation</option>
                            <option value="Father" {{ $relation === 'Father' ? 'selected' : '' }}>Father</option>
                            <option value="Mother" {{ $relation === 'Mother' ? 'selected' : '' }}>Mother</option>
                            <option value="Guardian" {{ $relation === 'Guardian' ? 'selected' : '' }}>Guardian</option>
                            <option value="Other" {{ $relation === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Parent Phone</label>
                        <input
                            type="text"
                            name="parent_phone"
                            value="{{ old('parent_phone', $admission->parent_phone) }}"
                            placeholder="Parent mobile number"
                        >
                    </div>

                    <div class="form-group">
                        <label>Parent Email</label>
                        <input
                            type="email"
                            name="parent_email"
                            value="{{ old('parent_email', $admission->parent_email) }}"
                            placeholder="parent@email.com"
                        >
                    </div>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="form-card-head">
                <h3>Address</h3>
                <p>Student address and location details.</p>
            </div>

            <div class="form-card-body">
                <div class="form-group">
                    <label>Full Address</label>
                    <textarea name="address" placeholder="House no, street, area">{{ old('address', $admission->address) }}</textarea>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" value="{{ old('city', $admission->city) }}">
                    </div>

                    <div class="form-group">
                        <label>State</label>
                        <input type="text" name="state" value="{{ old('state', $admission->state) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Pincode</label>
                    <input type="text" name="pincode" value="{{ old('pincode', $admission->pincode) }}">
                </div>
            </div>
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
            <a href="{{ route('admin.admissions.index') }}" class="btn btn-light">Cancel</a>
        </div>
    </div>

    <aside>
        <div class="form-card">
            <div class="form-card-head" style="background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;">
                <h3 style="color:#fff;">Status & Fee</h3>
                <p style="color:rgba(255,255,255,.9);">Admission status and fee details.</p>
            </div>

            <div class="form-card-body">
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" required>
                        <option value="new" {{ $status === 'new' ? 'selected' : '' }}>New</option>
                        <option value="counselling" {{ $status === 'counselling' ? 'selected' : '' }}>Counselling</option>
                        <option value="document_pending" {{ $status === 'document_pending' ? 'selected' : '' }}>Document Pending</option>
                        <option value="admitted" {{ $status === 'admitted' ? 'selected' : '' }}>Admitted</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>

                    <span class="help">
                        Status “Admitted” karne par student aur parent record auto create hoga.
                    </span>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Registration Fee</label>
                        <input
                            type="number"
                            name="registration_fee"
                            id="registrationFeeInput"
                            value="{{ old('registration_fee', $admission->registration_fee ?? 0) }}"
                            step="0.01"
                            min="0"
                        >
                    </div>

                    <div class="form-group">
                        <label>Admission Fee</label>
                        <input
                            type="number"
                            name="admission_fee"
                            id="admissionFeeInput"
                            value="{{ old('admission_fee', $admission->admission_fee ?? 0) }}"
                            step="0.01"
                            min="0"
                        >
                    </div>
                </div>

                <div class="fee-summary">
                    <small>Total Initial Fee</small>
                    <strong id="feeTotalPreview">₹0.00</strong>
                </div>

                <div class="form-group" style="margin-top:18px;">
                    <label>Notes</label>
                    <textarea name="notes" placeholder="Counselling notes, documents, remarks...">{{ old('notes', $admission->notes) }}</textarea>
                </div>

                <div class="status-help-card">
                    <strong>CRM Flow:</strong>
                    <br>
                    New → Counselling → Document Pending → Admitted
                    <br>
                    Rejected/Cancelled cases ko alag status me track karein.
                </div>
            </div>
        </div>
    </aside>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const courseSelect = document.getElementById('courseSelect');
        const courseNameInput = document.getElementById('courseNameInput');
        const classLevelInput = document.getElementById('classLevelInput');

        if (courseSelect) {
            courseSelect.addEventListener('change', function () {
                const selected = courseSelect.options[courseSelect.selectedIndex];

                if (selected && selected.value) {
                    if (!courseNameInput.value) {
                        courseNameInput.value = selected.dataset.title || '';
                    }

                    if (!classLevelInput.value) {
                        classLevelInput.value = selected.dataset.class || '';
                    }
                }
            });
        }

        const regInput = document.getElementById('registrationFeeInput');
        const admInput = document.getElementById('admissionFeeInput');
        const preview = document.getElementById('feeTotalPreview');

        function updateFeeTotal() {
            const reg = parseFloat(regInput?.value || 0);
            const adm = parseFloat(admInput?.value || 0);
            const total = reg + adm;

            if (preview) {
                preview.textContent = '₹' + total.toFixed(2);
            }
        }

        regInput?.addEventListener('input', updateFeeTotal);
        admInput?.addEventListener('input', updateFeeTotal);

        updateFeeTotal();
    });
</script>