<style>
    .parent-form-wrap {
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

    .profile-preview {
        text-align: center;
        padding: 18px;
        border-radius: 22px;
        background: linear-gradient(135deg, #eff6ff, #fff);
        border: 1px solid #bfdbfe;
    }

    .avatar-preview {
        width: 96px;
        height: 96px;
        border-radius: 28px;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        font-weight: 900;
        margin-bottom: 14px;
    }

    .help-card {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 14px;
        color: #64748b;
        line-height: 1.7;
        font-size: 13px;
    }

    @media(max-width: 1050px) {
        .parent-form-wrap,
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
    $status = old('status', $parent->status ?: 'active');
    $relation = old('relation', $parent->relation ?: 'Father');
@endphp

<div class="parent-form-wrap">
    <div>
        <div class="form-card">
            <div class="form-card-head">
                <h3>Student Mapping</h3>
                <p>Select student jiske parent/guardian ki details add karni hain.</p>
            </div>

            <div class="form-card-body">
                <div class="form-group">
                    <label>Student</label>
                    <select name="student_id" id="studentSelect">
                        <option value="">Select Student</option>

                        @foreach($students as $student)
                            <option
                                value="{{ $student->id }}"
                                data-address="{{ $student->address }}"
                                {{ old('student_id', $parent->student_id) == $student->id ? 'selected' : '' }}
                            >
                                {{ $student->name }} - {{ $student->student_code }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="form-card-head">
                <h3>Parent / Guardian Details</h3>
                <p>Parent name, relation and contact information.</p>
            </div>

            <div class="form-card-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Parent Name *</label>
                        <input
                            type="text"
                            name="name"
                            id="parentNameInput"
                            value="{{ old('name', $parent->name) }}"
                            required
                            placeholder="Parent full name"
                        >
                    </div>

                    <div class="form-group">
                        <label>Relation</label>
                        <select name="relation">
                            <option value="">Select Relation</option>
                            <option value="Father" {{ $relation === 'Father' ? 'selected' : '' }}>Father</option>
                            <option value="Mother" {{ $relation === 'Mother' ? 'selected' : '' }}>Mother</option>
                            <option value="Guardian" {{ $relation === 'Guardian' ? 'selected' : '' }}>Guardian</option>
                            <option value="Brother" {{ $relation === 'Brother' ? 'selected' : '' }}>Brother</option>
                            <option value="Sister" {{ $relation === 'Sister' ? 'selected' : '' }}>Sister</option>
                            <option value="Other" {{ $relation === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Phone</label>
                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone', $parent->phone) }}"
                            placeholder="Mobile number"
                        >
                    </div>

                    <div class="form-group">
                        <label>Alternate Phone</label>
                        <input
                            type="text"
                            name="alternate_phone"
                            value="{{ old('alternate_phone', $parent->alternate_phone) }}"
                            placeholder="Alternate mobile"
                        >
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Email</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $parent->email) }}"
                            placeholder="parent@email.com"
                        >
                    </div>

                    <div class="form-group">
                        <label>Occupation</label>
                        <input
                            type="text"
                            name="occupation"
                            value="{{ old('occupation', $parent->occupation) }}"
                            placeholder="Business / Job / Homemaker"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea
                        name="address"
                        id="addressInput"
                        placeholder="Parent address"
                    >{{ old('address', $parent->address) }}</textarea>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
            <a href="{{ route('admin.parents.index') }}" class="btn btn-light">Cancel</a>
        </div>
    </div>

    <aside>
        <div class="form-card">
            <div class="form-card-head" style="background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;">
                <h3 style="color:#fff;">Status & Preview</h3>
                <p style="color:rgba(255,255,255,.9);">Record status and live preview.</p>
            </div>

            <div class="form-card-body">
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" required>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="profile-preview">
                    <div class="avatar-preview" id="avatarPreview">
                        {{ strtoupper(mb_substr($parent->name ?: 'P', 0, 1)) }}
                    </div>

                    <h3 id="namePreview" style="margin:0 0 6px;color:#111827;">
                        {{ $parent->name ?: 'Parent Name' }}
                    </h3>

                    <p style="margin:0;color:#64748b;">
                        Parent / Guardian Record
                    </p>
                </div>

                <div class="help-card" style="margin-top:16px;">
                    <strong style="color:#111827;">Tip:</strong>
                    <br>
                    Student select karne par address blank ho to student ka address auto fill ho jayega.
                </div>
            </div>
        </div>
    </aside>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const nameInput = document.getElementById('parentNameInput');
        const namePreview = document.getElementById('namePreview');
        const avatarPreview = document.getElementById('avatarPreview');

        function updatePreview() {
            const value = nameInput?.value || 'Parent Name';

            if (namePreview) {
                namePreview.textContent = value;
            }

            if (avatarPreview) {
                avatarPreview.textContent = value.trim().charAt(0).toUpperCase() || 'P';
            }
        }

        nameInput?.addEventListener('input', updatePreview);
        updatePreview();

        const studentSelect = document.getElementById('studentSelect');
        const addressInput = document.getElementById('addressInput');

        studentSelect?.addEventListener('change', function () {
            const selected = studentSelect.options[studentSelect.selectedIndex];

            if (selected && selected.value && addressInput && !addressInput.value) {
                addressInput.value = selected.dataset.address || '';
            }
        });
    });
</script>