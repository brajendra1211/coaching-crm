@extends('frontend.layouts.app')

@php
    $setting = $siteSetting ?? \App\Models\CoachingSetting::current();
    $instituteName = $setting->institute_name ?: 'Edu Institute';

    $phoneDigits = preg_replace('/\D+/', '', (string) ($setting->phone ?: '9999999999'));
    $phoneDigits = ltrim($phoneDigits, '0') ?: '9999999999';

    $whatsappDigits = preg_replace('/\D+/', '', (string) ($setting->whatsapp ?: $phoneDigits));
    $whatsappDigits = ltrim($whatsappDigits, '0') ?: $phoneDigits;

    $telNumber = strlen($phoneDigits) === 10 ? '91' . $phoneDigits : $phoneDigits;
    $whatsappNumber = strlen($whatsappDigits) === 10 ? '91' . $whatsappDigits : $whatsappDigits;
@endphp

@section('title', 'Online Admission - ' . $instituteName)
@section('meta_description', 'Apply online for admission, choose course, submit student and parent details, and get counselling support from ' . $instituteName . '.')
@section('meta_keywords', 'online admission, coaching admission, course admission, student registration')

@push('styles')
<style>
    .admission-hero {
        background:
            radial-gradient(circle at 12% 12%, rgba(255,255,255,.18), transparent 26%),
            linear-gradient(135deg, #0f172a, var(--primary), var(--secondary));
        color: #fff;
        padding: 72px 0 88px;
        overflow: hidden;
    }

    .admission-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 390px;
        gap: 34px;
        align-items: center;
    }

    .admission-hero h1 {
        margin: 0;
        color: #fff;
        font-size: clamp(36px, 5vw, 58px);
        line-height: 1.08;
    }

    .admission-hero p {
        margin: 18px 0 0;
        max-width: 760px;
        color: rgba(255,255,255,.9);
        font-size: 18px;
        line-height: 1.75;
    }

    .admission-hero-actions,
    .admission-trust {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 26px;
    }

    .admission-trust span {
        padding: 9px 13px;
        border: 1px solid rgba(255,255,255,.22);
        border-radius: 999px;
        background: rgba(255,255,255,.12);
        font-weight: 900;
        font-size: 13px;
    }

    .admission-hero-card {
        background: rgba(255,255,255,.96);
        color: var(--heading);
        border-radius: 28px;
        padding: 24px;
        box-shadow: 0 28px 70px rgba(15,23,42,.28);
    }

    .admission-hero-card h2 {
        margin: 0 0 14px;
        color: var(--heading);
        font-size: 24px;
    }

    .admission-mini-list {
        display: grid;
        gap: 12px;
    }

    .admission-mini-list div {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 13px;
        border-radius: 18px;
        background: #f8fafc;
        border: 1px solid var(--border);
    }

    .admission-mini-list strong {
        display: block;
        color: var(--heading);
    }

    .admission-mini-list small {
        display: block;
        color: var(--muted);
        line-height: 1.45;
        margin-top: 3px;
    }

    .admission-page-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 390px;
        gap: 28px;
        align-items: start;
        margin-top: -44px;
        position: relative;
        z-index: 3;
    }

    .admission-panel {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 28px;
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .admission-panel-head {
        padding: 24px 26px;
        border-bottom: 1px solid var(--border);
        background: linear-gradient(180deg, #fff, #f8fafc);
    }

    .admission-panel-head h2 {
        margin: 0;
        color: var(--heading);
        font-size: 28px;
    }

    .admission-panel-head p {
        margin: 8px 0 0;
        color: var(--muted);
        line-height: 1.6;
    }

    .admission-form {
        padding: 26px;
    }

    .form-grid-2,
    .form-grid-3 {
        display: grid;
        gap: 16px;
    }

    .form-grid-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .form-grid-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .form-section-title {
        margin: 26px 0 14px;
        color: var(--heading);
        font-size: 18px;
        font-weight: 900;
    }

    .alert-success {
        margin-bottom: 18px;
        padding: 14px 16px;
        border-radius: 16px;
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
        font-weight: 900;
        line-height: 1.5;
    }

    .admission-sidebar {
        display: grid;
        gap: 18px;
    }

    .side-box {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 22px;
        box-shadow: var(--shadow-soft);
    }

    .side-box h3 {
        margin: 0 0 12px;
        color: var(--heading);
    }

    .side-box p,
    .side-box li {
        color: var(--muted);
        line-height: 1.65;
    }

    .side-box ul,
    .side-box ol {
        margin: 0;
        padding-left: 20px;
    }

    .course-preview-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .course-tile {
        padding: 18px;
        border-radius: 20px;
        border: 1px solid var(--border);
        background: #fff;
        box-shadow: var(--shadow-soft);
    }

    .course-tile strong {
        display: block;
        color: var(--heading);
        font-size: 18px;
        margin-bottom: 8px;
    }

    .course-tile p {
        margin: 0;
        color: var(--muted);
        line-height: 1.55;
    }

    @media(max-width: 1020px) {
        .admission-hero-grid,
        .admission-page-grid {
            grid-template-columns: 1fr;
        }

        .course-preview-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media(max-width: 640px) {
        .admission-hero {
            padding: 48px 0 68px;
        }

        .admission-page-grid {
            margin-top: -28px;
        }

        .admission-form,
        .admission-panel-head,
        .side-box {
            padding: 20px;
        }

        .form-grid-2,
        .form-grid-3,
        .course-preview-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

<section class="admission-hero">
    <div class="container admission-hero-grid">
        <div>
            <span class="badge" style="background:rgba(255,255,255,.14);color:#fff;border-color:rgba(255,255,255,.25);">Online Admission Open</span>
            <h1>Apply online and start your coaching journey with {{ $instituteName }}</h1>
            <p>
                Fill the admission form, select your course, share student and parent details, and our counselling team will guide you for batch, fee and documents.
            </p>

            <div class="admission-hero-actions">
                <a href="#admissionForm" class="btn btn-light">Apply Now</a>
                <a href="tel:+{{ $telNumber }}" class="btn btn-dark">Call Counsellor</a>
                <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('Hello, I want admission details.') }}" target="_blank" class="btn btn-success">WhatsApp</a>
            </div>

            <div class="admission-trust">
                <span>Fast application</span>
                <span>Course counselling</span>
                <span>Batch guidance</span>
                <span>Parent support</span>
            </div>
        </div>

        <aside class="admission-hero-card">
            <h2>Admission Flow</h2>
            <div class="admission-mini-list">
                <div>
                    <strong>1</strong>
                    <span>
                        <strong>Submit Form</strong>
                        <small>Student, parent and course details.</small>
                    </span>
                </div>
                <div>
                    <strong>2</strong>
                    <span>
                        <strong>Counselling Call</strong>
                        <small>Discuss course, batch timing and fees.</small>
                    </span>
                </div>
                <div>
                    <strong>3</strong>
                    <span>
                        <strong>Admission Confirmation</strong>
                        <small>Admin verifies details and creates student login.</small>
                    </span>
                </div>
            </div>
        </aside>
    </div>
</section>

<section class="section section-light">
    <div class="container admission-page-grid">
        <main class="admission-panel" id="admissionForm">
            <div class="admission-panel-head">
                <h2>Admission Application Form</h2>
                <p>Details submit hone ke baad ye application admin admission panel me dikh jayegi.</p>
            </div>

            <form method="POST" action="{{ route('admission.store') }}" class="admission-form">
                @csrf

                @if(session('success'))
                    <div class="alert-success">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert-success" style="background:#fee2e2;color:#991b1b;border-color:#fecaca;">
                        Please check the highlighted fields and submit again.
                    </div>
                @endif

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Student Name *</label>
                        <input type="text" name="student_name" value="{{ old('student_name') }}" required placeholder="Enter student full name">
                        @error('student_name') <small style="color:#dc2626;">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group">
                        <label>Mobile Number *</label>
                        <input type="text" name="student_phone" value="{{ old('student_phone') }}" required placeholder="Enter student mobile number">
                        @error('student_phone') <small style="color:#dc2626;">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="form-grid-3">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="student_email" value="{{ old('student_email') }}" placeholder="student@example.com">
                    </div>

                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" value="{{ old('dob') }}">
                    </div>

                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender">
                            <option value="">Select Gender</option>
                            @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('gender') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-section-title">Course Details</div>

                <div class="form-grid-3">
                    <div class="form-group">
                        <label>Interested Course</label>
                        <select name="course_id">
                            <option value="">Select Course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Course Name</label>
                        <input type="text" name="course_name" value="{{ old('course_name') }}" placeholder="If course is not listed">
                    </div>

                    <div class="form-group">
                        <label>Class / Level</label>
                        <input type="text" name="class_level" value="{{ old('class_level') }}" placeholder="Class 10, 11, 12, Dropper">
                    </div>
                </div>

                <div class="form-group">
                    <label>Previous School / Institute</label>
                    <input type="text" name="previous_school" value="{{ old('previous_school') }}" placeholder="Previous school or institute">
                </div>

                <div class="form-section-title">Parent Details</div>

                <div class="form-grid-3">
                    <div class="form-group">
                        <label>Parent Name</label>
                        <input type="text" name="parent_name" value="{{ old('parent_name') }}" placeholder="Parent / guardian name">
                    </div>

                    <div class="form-group">
                        <label>Relation</label>
                        <select name="parent_relation">
                            <option value="">Select Relation</option>
                            @foreach(['father' => 'Father', 'mother' => 'Mother', 'guardian' => 'Guardian'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('parent_relation') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Parent Phone</label>
                        <input type="text" name="parent_phone" value="{{ old('parent_phone') }}" placeholder="Parent mobile number">
                    </div>
                </div>

                <div class="form-section-title">Address</div>

                <div class="form-group">
                    <label>Full Address</label>
                    <textarea name="address" placeholder="House no, street, area">{{ old('address') }}</textarea>
                </div>

                <div class="form-grid-3">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" value="{{ old('city') }}">
                    </div>

                    <div class="form-group">
                        <label>State</label>
                        <input type="text" name="state" value="{{ old('state') }}">
                    </div>

                    <div class="form-group">
                        <label>Pincode</label>
                        <input type="text" name="pincode" value="{{ old('pincode') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Message / Requirement</label>
                    <textarea name="notes" placeholder="Batch timing, subject, counselling requirement, or any note">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;">Submit Admission Application</button>
            </form>
        </main>

        <aside class="admission-sidebar">
            <div class="side-box">
                <h3>Documents Required</h3>
                <ul>
                    <li>Student photo</li>
                    <li>Aadhaar or ID proof</li>
                    <li>Previous class marksheet</li>
                    <li>Parent contact details</li>
                    <li>Fee confirmation after counselling</li>
                </ul>
            </div>

            <div class="side-box">
                <h3>Need Help?</h3>
                <p>Admission counsellor se course, batch timing, fees aur student login ke baare me baat kar sakte hain.</p>
                <div style="display:grid;gap:10px;">
                    <a href="tel:+{{ $telNumber }}" class="btn btn-dark">Call Now</a>
                    <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('Hello, I need help with online admission.') }}" target="_blank" class="btn btn-success">WhatsApp Now</a>
                </div>
            </div>

            <div class="side-box">
                <h3>After Admission</h3>
                <ol>
                    <li>Admin verifies application.</li>
                    <li>Student record and login can be created.</li>
                    <li>Batch, fees and course access can be assigned.</li>
                    <li>Student can access dashboard, exams and materials.</li>
                </ol>
            </div>
        </aside>
    </div>
</section>

@if($courses->count())
    <section class="section">
        <div class="container">
            <div class="section-title">
                <h2>Choose Your Course</h2>
                <p>Select the course while applying, or submit custom requirement if your course is not listed.</p>
            </div>

            <div class="course-preview-grid">
                @foreach($courses->take(6) as $course)
                    <article class="course-tile">
                        <strong>{{ $course->title }}</strong>
                        <p>{{ \Illuminate\Support\Str::limit($course->short_description ?: $course->description ?: 'Get course details, batches and counselling support.', 105) }}</p>
                        @if($course->fee || $course->offer_fee)
                            <p style="margin-top:12px;font-weight:900;color:var(--primary);">
                                Fee: Rs {{ number_format((float) ($course->offer_fee ?: $course->fee), 2) }}
                            </p>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif

@endsection
