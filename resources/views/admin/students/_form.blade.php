<style>
    .student-form-wrap {
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

    .image-box {
        border: 1px dashed #93c5fd;
        background: #eff6ff;
        border-radius: 18px;
        padding: 16px;
    }

    .student-photo-preview {
        width: 140px;
        height: 140px;
        border-radius: 26px;
        object-fit: cover;
        object-position: center top;
        display: block;
        background: #fff;
        border: 1px solid #bfdbfe;
        margin-bottom: 14px;
    }

    .help {
        display: block;
        margin-top: 6px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
    }

    .check-card {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        padding: 13px;
        border-radius: 16px;
        background: #fff;
        border: 1px solid #e5e7eb;
        margin-top: 12px;
        cursor: pointer;
    }

    .check-card input {
        width: auto;
        margin-top: 3px;
    }

    @media(max-width: 1050px) {
        .student-form-wrap,
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
    $status = old('status', $student->status ?: 'active');
@endphp

<div class="student-form-wrap">
    <div>
        <div class="form-card">
            <div class="form-card-head">
                <h3>Student Details</h3>
                <p>Basic profile information for student.</p>
            </div>

            <div class="form-card-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Student Code</label>
                        <input type="text" name="student_code" value="{{ old('student_code', $student->student_code) }}">
                    </div>

                    <div class="form-group">
                        <label>Joining Date</label>
                        <input
                            type="date"
                            name="joining_date"
                            value="{{ old('joining_date', $student->joining_date ? $student->joining_date->format('Y-m-d') : now()->format('Y-m-d')) }}"
                        >
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Student Name *</label>
                        <input type="text" name="name" value="{{ old('name', $student->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $student->phone) }}">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email', $student->email) }}">
                    </div>

                    <div class="form-group">
                        <label>DOB</label>
                        <input type="date" name="dob" value="{{ old('dob', $student->dob ? $student->dob->format('Y-m-d') : '') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Gender</label>
                    @php $gender = old('gender', $student->gender); @endphp

                    <select name="gender">
                        <option value="">Select Gender</option>
                        <option value="Male" {{ $gender === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ $gender === 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ $gender === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="form-card-head">
                <h3>Course Details</h3>
                <p>Assign course and class/level.</p>
            </div>

            <div class="form-card-body">
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
                                    {{ old('course_id', $student->course_id) == $course->id ? 'selected' : '' }}
                                >
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Course Name</label>
                        <input type="text" name="course_name" id="courseNameInput" value="{{ old('course_name', $student->course_name) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Class / Level</label>
                    <input type="text" name="class_level" id="classLevelInput" value="{{ old('class_level', $student->class_level) }}">
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="form-card-head">
                <h3>Parent Details</h3>
                <p>Parent/guardian contact details.</p>
            </div>

            <div class="form-card-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Parent Name</label>
                        <input type="text" name="parent_name" value="{{ old('parent_name', $parent->name) }}">
                    </div>

                    <div class="form-group">
                        <label>Relation</label>
                        @php $relation = old('parent_relation', $parent->relation); @endphp

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
                        <input type="text" name="parent_phone" value="{{ old('parent_phone', $parent->phone) }}">
                    </div>

                    <div class="form-group">
                        <label>Alternate Phone</label>
                        <input type="text" name="parent_alternate_phone" value="{{ old('parent_alternate_phone', $parent->alternate_phone) }}">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Parent Email</label>
                        <input type="email" name="parent_email" value="{{ old('parent_email', $parent->email) }}">
                    </div>

                    <div class="form-group">
                        <label>Occupation</label>
                        <input type="text" name="parent_occupation" value="{{ old('parent_occupation', $parent->occupation) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Parent Address</label>
                    <textarea name="parent_address">{{ old('parent_address', $parent->address) }}</textarea>
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
                    <label>Address</label>
                    <textarea name="address">{{ old('address', $student->address) }}</textarea>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" value="{{ old('city', $student->city) }}">
                    </div>

                    <div class="form-group">
                        <label>State</label>
                        <input type="text" name="state" value="{{ old('state', $student->state) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Pincode</label>
                    <input type="text" name="pincode" value="{{ old('pincode', $student->pincode) }}">
                </div>
            </div>
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
            <a href="{{ route('admin.students.index') }}" class="btn btn-light">Cancel</a>
        </div>
    </div>

    <aside>
        <div class="form-card">
            <div class="form-card-head" style="background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;">
                <h3 style="color:#fff;">Photo & Status</h3>
                <p style="color:rgba(255,255,255,.9);">Student photo and account status.</p>
            </div>

            <div class="form-card-body">
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" required>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="passed_out" {{ $status === 'passed_out' ? 'selected' : '' }}>Passed Out</option>
                        <option value="left" {{ $status === 'left' ? 'selected' : '' }}>Left</option>
                    </select>
                </div>

                <div class="image-box">
                    @if($isEdit && $student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}" class="student-photo-preview" alt="{{ $student->name }}">
                    @endif

                    <input type="file" name="photo" id="photoInput" accept="image/*">

                    <span class="help">Optional. JPG/PNG/WebP, max 4MB.</span>

                    <img id="photoPreview" class="student-photo-preview" style="display:none;margin-top:14px;" alt="Preview">

                    @if($isEdit && $student->photo)
                        <label class="check-card">
                            <input type="checkbox" name="remove_photo" value="1">
                            <div>
                                <strong>Remove Current Photo</strong>
                                <br>
                                <small>Current photo remove ho jayegi.</small>
                            </div>
                        </label>
                    @endif
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

        courseSelect?.addEventListener('change', function () {
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

        const photoInput = document.getElementById('photoInput');
        const photoPreview = document.getElementById('photoPreview');

        photoInput?.addEventListener('change', function () {
            const file = this.files && this.files[0];

            if (!file) {
                photoPreview.style.display = 'none';
                photoPreview.removeAttribute('src');
                return;
            }

            const reader = new FileReader();

            reader.onload = function (event) {
                photoPreview.src = event.target.result;
                photoPreview.style.display = 'block';
            };

            reader.readAsDataURL(file);
        });
    });
</script>