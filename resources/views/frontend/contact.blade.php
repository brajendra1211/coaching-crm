@extends('frontend.layouts.app')

@php
    $setting = $siteSetting ?? \App\Models\CoachingSetting::current();
    $instituteName = $setting->institute_name ?: 'Edu Institute';
    $pageTitle = $page?->seo_title ?: ($page?->hero_title ?: ('Contact ' . $instituteName));
    $pageDescription = $page?->seo_description ?: ('Contact ' . $instituteName . ' for course counselling, admission enquiry, batch details, fees and student support.');

    $phoneDigits = preg_replace('/\D+/', '', (string) ($setting->phone ?: '9999999999'));
    $phoneDigits = ltrim($phoneDigits, '0') ?: '9999999999';

    $whatsappDigits = preg_replace('/\D+/', '', (string) ($setting->whatsapp ?: $phoneDigits));
    $whatsappDigits = ltrim($whatsappDigits, '0') ?: $phoneDigits;

    $telNumber = strlen($phoneDigits) === 10 ? '91' . $phoneDigits : $phoneDigits;
    $whatsappNumber = strlen($whatsappDigits) === 10 ? '91' . $whatsappDigits : $whatsappDigits;
@endphp

@section('title', $pageTitle)
@section('meta_description', $pageDescription)
@section('meta_keywords', $page?->seo_keywords ?: 'contact coaching institute, admission enquiry, course counselling, coaching phone number')

@push('styles')
<style>
    .contact-hero {
        background:
            radial-gradient(circle at 12% 18%, rgba(255,255,255,.18), transparent 28%),
            linear-gradient(135deg, #0f172a, var(--primary), var(--secondary));
        color: #fff;
        padding: 70px 0 92px;
    }

    .contact-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 390px;
        gap: 34px;
        align-items: center;
    }

    .contact-hero h1 {
        margin: 0;
        color: #fff;
        font-size: clamp(36px, 5vw, 58px);
        line-height: 1.08;
    }

    .contact-hero p {
        margin: 18px 0 0;
        max-width: 760px;
        color: rgba(255,255,255,.9);
        font-size: 18px;
        line-height: 1.75;
    }

    .contact-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 26px;
    }

    .contact-mini-card {
        background: rgba(255,255,255,.96);
        color: var(--heading);
        border-radius: 28px;
        padding: 24px;
        box-shadow: 0 28px 70px rgba(15,23,42,.28);
    }

    .contact-mini-card h2 {
        margin: 0 0 14px;
        color: var(--heading);
        font-size: 24px;
    }

    .contact-methods {
        display: grid;
        gap: 12px;
    }

    .contact-methods a,
    .contact-methods div {
        display: block;
        padding: 14px;
        border-radius: 18px;
        background: #f8fafc;
        border: 1px solid var(--border);
    }

    .contact-methods strong {
        display: block;
        color: var(--heading);
        margin-bottom: 4px;
    }

    .contact-methods span {
        color: var(--muted);
        line-height: 1.5;
        font-weight: 800;
    }

    .contact-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 390px;
        gap: 28px;
        align-items: start;
        margin-top: -44px;
        position: relative;
        z-index: 3;
    }

    .contact-panel,
    .contact-side-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 28px;
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .contact-panel-head {
        padding: 24px 26px;
        border-bottom: 1px solid var(--border);
        background: linear-gradient(180deg, #fff, #f8fafc);
    }

    .contact-panel-head h2 {
        margin: 0;
        color: var(--heading);
        font-size: 28px;
    }

    .contact-panel-head p {
        margin: 8px 0 0;
        color: var(--muted);
        line-height: 1.6;
    }

    .contact-form {
        padding: 26px;
    }

    .form-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .alert-box {
        margin-bottom: 18px;
        padding: 14px 16px;
        border-radius: 16px;
        font-weight: 900;
        line-height: 1.5;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .contact-side-card {
        padding: 24px;
        margin-bottom: 18px;
    }

    .contact-side-card h3 {
        margin: 0 0 12px;
        color: var(--heading);
    }

    .contact-side-card p,
    .contact-side-card li {
        color: var(--muted);
        line-height: 1.65;
    }

    .contact-side-card ul {
        margin: 0;
        padding-left: 20px;
    }

    .map-box {
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid var(--border);
        background: #f8fafc;
        min-height: 280px;
    }

    .map-box iframe {
        width: 100%;
        height: 320px;
        border: 0;
        display: block;
    }

    .map-placeholder {
        padding: 34px;
        min-height: 280px;
        display: grid;
        place-items: center;
        text-align: center;
        color: var(--muted);
        font-weight: 800;
        line-height: 1.7;
    }

    .contact-content {
        margin-top: 22px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 24px;
        box-shadow: var(--shadow-soft);
        color: var(--text);
        line-height: 1.8;
    }

    .contact-content h2,
    .contact-content h3 {
        color: var(--heading);
        margin: 22px 0 10px;
    }

    .contact-content p {
        margin: 0 0 14px;
    }

    @media(max-width: 1020px) {
        .contact-hero-grid,
        .contact-grid {
            grid-template-columns: 1fr;
        }
    }

    @media(max-width: 640px) {
        .contact-hero {
            padding: 48px 0 68px;
        }

        .contact-grid {
            margin-top: -28px;
        }

        .contact-form,
        .contact-panel-head,
        .contact-side-card {
            padding: 20px;
        }

        .form-grid-2 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

<section class="contact-hero">
    <div class="container contact-hero-grid">
        <div>
            <span class="badge" style="background:rgba(255,255,255,.14);color:#fff;border-color:rgba(255,255,255,.25);">Contact & Counselling</span>
            <h1>{{ $page?->hero_title ?: ('Talk to ' . $instituteName . ' for admission, courses and student support') }}</h1>
            <p>
                {{ $page?->hero_subtitle ?: 'Submit your enquiry and our team will contact you with course, batch, fee, exam and admission guidance.' }}
            </p>

            <div class="contact-actions">
                <a href="#contactForm" class="btn btn-light">Send Enquiry</a>
                <a href="tel:+{{ $telNumber }}" class="btn btn-dark">Call Now</a>
                <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('Hello, I need course/admission details.') }}" target="_blank" class="btn btn-success">WhatsApp</a>
            </div>
        </div>

        <aside class="contact-mini-card">
            <h2>Quick Contact</h2>

            <div class="contact-methods">
                <a href="tel:+{{ $telNumber }}">
                    <strong>Phone</strong>
                    <span>+{{ $telNumber }}</span>
                </a>

                <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank">
                    <strong>WhatsApp</strong>
                    <span>Chat with counselling team</span>
                </a>

                <div>
                    <strong>Email</strong>
                    <span>{{ $setting->enquiry_email ?: $setting->email ?: 'Not configured' }}</span>
                </div>
            </div>
        </aside>
    </div>
</section>

<section class="section section-light">
    <div class="container contact-grid">
        <main class="contact-panel" id="contactForm">
            <div class="contact-panel-head">
                <h2>Send Enquiry</h2>
                <p>Form submit hote hi enquiry CRM dashboard me save hogi aur configured enquiry email par notification jayega.</p>
            </div>

            <form method="POST" action="{{ route('contact.store') }}" class="contact-form">
                @csrf

                @if(session('success'))
                    <div class="alert-box alert-success">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert-box alert-error">Please check the form and submit again.</div>
                @endif

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Enter student name">
                        @error('name') <small style="color:#dc2626;">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="Enter mobile number">
                        @error('phone') <small style="color:#dc2626;">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="student@example.com">
                    </div>

                    <div class="form-group">
                        <label>Class / Level</label>
                        <input type="text" name="class_level" value="{{ old('class_level') }}" placeholder="Class 10, 11, 12, Dropper">
                    </div>
                </div>

                @if($courses->count())
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
                @endif

                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" placeholder="Write your question, preferred batch timing, course or admission requirement">{{ old('message') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;">Submit Enquiry</button>
            </form>

            @if($page?->content)
                <div class="contact-content">
                    {!! $page->content !!}
                </div>
            @endif
        </main>

        <aside>
            <div class="contact-side-card">
                <h3>What Happens Next?</h3>
                <ul>
                    <li>Enquiry saves in CRM Leads.</li>
                    <li>Admin sees it on dashboard and lead list.</li>
                    <li>Email notification goes to enquiry email.</li>
                    <li>Team can update follow-up and status.</li>
                </ul>
            </div>

            <div class="contact-side-card">
                <h3>Institute Address</h3>
                <p>{{ $setting->address ?: 'Address not configured yet. Add address from Website Settings.' }}</p>
                <p><strong>Phone:</strong> +{{ $telNumber }}</p>
                <p><strong>Email:</strong> {{ $setting->enquiry_email ?: $setting->email ?: 'Not configured' }}</p>
            </div>
        </aside>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Find Us</h2>
            <p>Admin Website Settings me Google Map URL add karne par yahan map show hoga.</p>
        </div>

        <div class="map-box">
            @if($setting->google_map_url)
                @if(str_contains($setting->google_map_url, '<iframe'))
                    {!! $setting->google_map_url !!}
                @else
                    <iframe src="{{ $setting->google_map_url }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                @endif
            @else
                <div class="map-placeholder">
                    Google Map is not configured yet. Add map embed/share URL from Admin CRM > Website Settings.
                </div>
            @endif
        </div>
    </div>
</section>

@endsection
