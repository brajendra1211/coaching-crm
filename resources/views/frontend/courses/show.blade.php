@extends('frontend.layouts.app')

@php
    use Illuminate\Support\Str;

    $setting = $siteSetting ?? \App\Models\CoachingSetting::current();

    $instituteName = $setting->institute_name ?? 'Best Coaching Institute';

    $description = $course->short_description ?: $course->description;
    $overview = Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $description))), 430);

    if (!$overview) {
        $overview = 'Get complete course details, batch information, fee structure and admission guidance from our expert counsellor.';
    }

    $pageTitle = $course->seo_title ?: $course->title . ' | ' . $instituteName;
    $metaDescription = $course->seo_description ?: Str::limit($overview, 158, '');
    $metaKeywords = $course->seo_keywords ?: $course->title . ', coaching course, best coaching classes, admission open, ' . $instituteName;

    $canonicalUrl = route('courses.show', $course->slug);

    $phoneDigits = preg_replace('/\D+/', '', (string) ($setting->phone ?: '9999999999'));
    $phoneDigits = ltrim($phoneDigits, '0') ?: '9999999999';

    $whatsappDigits = preg_replace('/\D+/', '', (string) ($setting->whatsapp ?: $phoneDigits));
    $whatsappDigits = ltrim($whatsappDigits, '0') ?: $phoneDigits;

    $telNumber = strlen($phoneDigits) === 10 ? '91' . $phoneDigits : $phoneDigits;
    $whatsappNumber = strlen($whatsappDigits) === 10 ? '91' . $whatsappDigits : $whatsappDigits;

    $displayPhone = strlen($phoneDigits) === 10
        ? '+91 ' . substr($phoneDigits, 0, 5) . ' ' . substr($phoneDigits, 5)
        : '+' . $phoneDigits;

    $subjects = collect(preg_split('/\r\n|\r|\n/', (string) $course->subjects))
        ->map(fn($item) => trim($item))
        ->filter()
        ->take(10);

    $eligibility = collect(preg_split('/\r\n|\r|\n/', (string) $course->eligibility))
        ->map(fn($item) => trim($item))
        ->filter()
        ->take(8);

    $features = collect(preg_split('/\r\n|\r|\n/', (string) $course->features))
        ->map(fn($item) => trim($item))
        ->filter()
        ->take(10);

    $mainFee = $course->offer_fee ?: $course->fee;
    $hasDiscount = !empty($course->offer_fee) && !empty($course->fee) && $course->offer_fee < $course->fee;

    $courseImageUrl = $course->image
        ? asset('storage/' . $course->image)
        : 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80';

    $relatedCourses = $relatedCourses ?? collect();

    $whatsappText = urlencode('Hello, I want details about ' . $course->title . ' - ' . $canonicalUrl);

    $courseSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Course',
        'name' => $course->title,
        'description' => $metaDescription,
        'url' => $canonicalUrl,
        'image' => $courseImageUrl,
        'provider' => [
            '@type' => 'EducationalOrganization',
            'name' => $instituteName,
            'url' => url('/'),
            'telephone' => '+' . $telNumber,
            'email' => $setting->email ?? null,
            'address' => $setting->address ?? null,
        ],
        'offers' => [
            '@type' => 'Offer',
            'price' => $mainFee ?: 0,
            'priceCurrency' => 'INR',
            'availability' => 'https://schema.org/InStock',
            'url' => $canonicalUrl,
        ],
    ];

    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => url('/'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Courses',
                'item' => route('courses.index'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $course->title,
                'item' => $canonicalUrl,
            ],
        ],
    ];
@endphp

@section('title', $pageTitle)
@section('meta_description', $metaDescription)
@section('meta_keywords', $metaKeywords)
@section('canonical', $canonicalUrl)
@section('og_title', $pageTitle)
@section('og_description', $metaDescription)
@section('og_image', $courseImageUrl)
@section('robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')

@push('head')
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $instituteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $courseImageUrl }}">
    <meta property="og:image:secure_url" content="{{ $courseImageUrl }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $course->title }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $courseImageUrl }}">

    <script type="application/ld+json">
        {!! json_encode($courseSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    <script type="application/ld+json">
        {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@push('styles')
<style>
    .course-hero {
        position: relative;
        overflow: hidden;
        color: #fff;
        padding: 78px 0 74px;
        background:
            radial-gradient(circle at 12% 12%, color-mix(in srgb, var(--primary) 34%, transparent), transparent 30%),
            radial-gradient(circle at 86% 18%, color-mix(in srgb, var(--secondary) 34%, transparent), transparent 32%),
            linear-gradient(135deg, #0f172a 0%, var(--primary) 55%, var(--secondary) 100%);
    }

    .course-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(rgba(255,255,255,.06) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.06) 1px, transparent 1px);
        background-size: 42px 42px;
        opacity: .36;
    }

    .course-hero::after {
        content: "";
        position: absolute;
        inset: auto -10% -46% -10%;
        height: 270px;
        background: rgba(255,255,255,.10);
        filter: blur(52px);
        transform: rotate(-3deg);
    }

    .course-hero-grid {
        position: relative;
        z-index: 2;
        display: grid;
        grid-template-columns: minmax(0, 1fr) 430px;
        gap: 38px;
        align-items: center;
    }

    .course-breadcrumb {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 18px;
        font-size: 14px;
        color: rgba(255,255,255,.82);
        font-weight: 800;
    }

    .course-breadcrumb a {
        color: #fff;
    }

    .course-badges {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .course-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 13px;
        border-radius: 999px;
        background: rgba(255,255,255,.13);
        border: 1px solid rgba(255,255,255,.22);
        color: #fff;
        font-size: 13px;
        font-weight: 900;
        backdrop-filter: blur(14px);
    }

    .course-hero h1 {
        margin: 0;
        max-width: 880px;
        color: #fff;
        font-size: clamp(35px, 5vw, 58px);
        line-height: 1.06;
        letter-spacing: -1.35px;
    }

    .course-hero p {
        max-width: 780px;
        color: rgba(255,255,255,.91);
        font-size: 18px;
        line-height: 1.75;
        margin: 18px 0 0;
    }

    .course-hero-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 28px;
    }

    .course-hero-points {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 24px;
    }

    .course-hero-points span {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 12px;
        border-radius: 999px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.18);
        color: rgba(255,255,255,.94);
        font-weight: 900;
        font-size: 13px;
    }

.course-hero-img {
    position: relative;
    overflow: hidden;
    min-height: 430px;
    border-radius: 34px;
    background: linear-gradient(135deg, rgba(255,255,255,.18), rgba(255,255,255,.08));
    border: 1px solid rgba(255,255,255,.26);
    box-shadow: 0 30px 80px rgba(15,23,42,.34);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 14px;
}

.course-hero-img img {
    width: 100%;
    height: auto;
    max-height: 430px;
    object-fit: cover;
    display: block;
    border-radius: 24px;
    background: #fff;
}

    .course-hero-img::after {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(180deg, transparent 42%, rgba(15,23,42,.74)),
            radial-gradient(circle at top right, color-mix(in srgb, var(--primary) 18%, transparent), transparent 34%);
    }

    .course-image-label {
        position: absolute;
        left: 22px;
        right: 22px;
        bottom: 22px;
        z-index: 2;
        padding: 16px 18px;
        border-radius: 22px;
        background: rgba(255,255,255,.95);
        color: #0f172a;
        box-shadow: 0 12px 30px rgba(15,23,42,.18);
    }

    .course-image-label strong {
        display: block;
        font-size: 17px;
        margin-bottom: 4px;
    }

    .course-image-label span {
        display: block;
        color: #64748b;
        font-size: 13px;
        font-weight: 800;
        line-height: 1.45;
    }

    .course-trust-strip {
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
    }

    .course-trust-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-top: -34px;
        position: relative;
        z-index: 5;
    }

    .trust-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        padding: 20px;
        box-shadow: 0 12px 35px rgba(15,23,42,.07);
        display: flex;
        gap: 14px;
        align-items: flex-start;
    }

    .trust-card span {
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        border-radius: 16px;
        background: color-mix(in srgb, var(--primary) 10%, #ffffff);
        color: var(--primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 21px;
    }

    .trust-card strong {
        display: block;
        color: #0f172a;
        font-size: 17px;
        margin-bottom: 5px;
    }

    .trust-card small {
        color: #64748b;
        line-height: 1.45;
        font-weight: 700;
    }

    .course-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 390px;
        gap: 30px;
        align-items: start;
    }

    .info-panel {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 28px;
        padding: 30px;
        box-shadow: 0 12px 35px rgba(15,23,42,.07);
        margin-bottom: 24px;
    }

    .info-panel h2,
    .info-panel h3 {
        margin: 0 0 14px;
        color: #0f172a;
        letter-spacing: -.4px;
    }

    .info-panel h2 {
        font-size: 32px;
    }

    .info-panel h3 {
        font-size: 25px;
    }

    .info-panel p {
        color: #475569;
        line-height: 1.85;
        margin: 0;
        font-size: 16px;
    }

    .course-facts {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-top: 24px;
    }

    .fact-box {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 18px;
    }

    .fact-box small {
        display: block;
        color: #64748b;
        font-weight: 900;
        margin-bottom: 7px;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .fact-box strong {
        color: #0f172a;
        font-size: 16px;
        line-height: 1.35;
    }

    .clean-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        gap: 11px;
    }

    .clean-list li {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        padding: 13px 15px;
        border-radius: 16px;
        background: #f8fafc;
        color: #334155;
        border: 1px solid #e5e7eb;
        font-weight: 800;
        line-height: 1.5;
    }

    .clean-list li::before {
        content: "\2713";
        color: var(--green);
        font-weight: 900;
        flex: 0 0 auto;
    }

    .course-sidebar {
        position: sticky;
        top: 105px;
    }

    .booking-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 18px 48px rgba(15,23,42,.10);
    }

    .booking-head {
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at 90% 10%, rgba(255,255,255,.24), transparent 26%),
            linear-gradient(135deg, var(--primary), var(--secondary));
        color: #fff;
        padding: 24px;
    }

    .booking-head h3 {
        margin: 0 0 8px;
        color: #fff;
        font-size: 24px;
        line-height: 1.15;
    }

    .booking-head p {
        margin: 0;
        opacity: .92;
        line-height: 1.65;
        font-size: 14px;
    }

    .booking-body {
        padding: 22px;
    }

    .fee-card {
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at top right, color-mix(in srgb, var(--primary) 12%, transparent), transparent 28%),
            linear-gradient(180deg, #ffffff, #f8fafc);
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        padding: 18px;
        margin-bottom: 16px;
    }

    .fee-card small {
        display: block;
        color: #64748b;
        font-weight: 900;
        margin-bottom: 7px;
        text-transform: uppercase;
        letter-spacing: .4px;
        font-size: 12px;
    }

    .fee-card strong {
        display: block;
        color: var(--primary);
        font-size: 34px;
        line-height: 1;
        letter-spacing: -1px;
    }

    .old-price {
        display: inline-flex;
        margin-top: 7px;
        color: #94a3b8;
        text-decoration: line-through;
        font-weight: 900;
        font-size: 14px;
    }

    .discount-tag {
        display: inline-flex;
        margin-top: 10px;
        padding: 7px 10px;
        border-radius: 999px;
        background: color-mix(in srgb, var(--green) 14%, #ffffff);
        color: var(--green);
        font-size: 12px;
        font-weight: 900;
    }

    .sidebar-actions {
        display: grid;
        gap: 11px;
        margin-bottom: 18px;
    }

    .cta-btn {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 9px;
        width: 100%;
        border-radius: 16px;
        padding: 14px 16px;
        font-size: 15px;
        font-weight: 900;
        text-decoration: none;
    }

    .sidebar-note {
        display: grid;
        gap: 10px;
        margin: 16px 0 18px;
    }

    .sidebar-note div {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        padding: 12px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        color: #334155;
        font-weight: 800;
        line-height: 1.45;
        font-size: 13px;
    }

    .sidebar-note span {
        color: var(--green);
        font-weight: 900;
    }

    .form-box {
        margin-top: 18px;
        padding: 18px;
        border-radius: 22px;
        background:
            radial-gradient(circle at top right, color-mix(in srgb, var(--primary) 10%, transparent), transparent 26%),
            #f8fafc;
        border: 1px solid #e5e7eb;
    }

    .form-box-head {
        margin-bottom: 14px;
    }

    .form-box-head h4 {
        margin: 0 0 5px;
        color: #0f172a;
        font-size: 20px;
    }

    .form-box-head p {
        margin: 0;
        color: #64748b;
        line-height: 1.5;
        font-size: 13px;
        font-weight: 700;
    }

    .related-card img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        background: #f8fafc;
        padding: 8px;
    }

    .related-card .card-body {
        padding: 20px;
    }

    .related-card h3 {
        font-size: 20px;
        line-height: 1.3;
    }

    @media(max-width: 1050px) {
        .course-hero-grid,
        .course-layout {
            grid-template-columns: 1fr;
        }

        .course-sidebar {
            position: static;
        }

        .course-trust-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media(max-width: 680px) {
        .course-hero {
            padding: 56px 0 62px;
        }

        .course-hero h1 {
            font-size: 34px;
        }

        .course-hero p {
            font-size: 16px;
        }

        .course-hero-img {
            min-height: auto;
            border-radius: 24px;
            padding: 10px;
        }

        .course-hero-img img {
            max-height: 320px;
            min-height: unset;
        }

        .course-trust-grid {
            grid-template-columns: 1fr;
            margin-top: -24px;
        }

        .course-facts {
            grid-template-columns: 1fr;
        }

        .info-panel {
            padding: 22px;
            border-radius: 24px;
        }

        .info-panel h2 {
            font-size: 27px;
        }

        .booking-body {
            padding: 18px;
        }
    }
</style>
@endpush

@section('content')

<section class="course-hero">
    <div class="container course-hero-grid">
        <div>
            <nav class="course-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <span>›</span>
                <a href="{{ route('courses.index') }}">Courses</a>
                <span>›</span>
                <span>{{ $course->title }}</span>
            </nav>

            <div class="course-badges">
                @if($course->course_type)
                    <span class="course-badge">🎓 {{ $course->course_type }}</span>
                @endif

                @if($course->class_level)
                    <span class="course-badge">📚 {{ $course->class_level }}</span>
                @endif

                @if($course->duration)
                    <span class="course-badge">⏱ {{ $course->duration }}</span>
                @endif
            </div>

            <h1>{{ $course->title }}</h1>

            <p>{{ $overview }}</p>

            <div class="course-hero-actions">
                <a href="tel:+{{ $telNumber }}" class="btn btn-light">
                    📞 Call {{ $displayPhone }}
                </a>

                <a href="https://wa.me/{{ $whatsappNumber }}?text={{ $whatsappText }}"
                   target="_blank"
                   rel="noopener"
                   class="btn btn-success">
                    💬 WhatsApp Enquiry
                </a>

                <a href="#course-enquiry-form" class="btn btn-dark">
                    📝 Book Free Counselling
                </a>
            </div>

            <div class="course-hero-points">
                <span>✓ Expert Faculty</span>
                <span>✓ Regular Tests</span>
                <span>✓ Doubt Support</span>
                <span>✓ Admission Guidance</span>
            </div>
        </div>

        <div class="course-hero-img">
            <img src="{{ $courseImageUrl }}" alt="{{ $course->title }}" loading="eager" fetchpriority="high">

            <div class="course-image-label">
                <strong>{{ $instituteName }}</strong>
                <span>Structured learning, regular guidance and result-focused preparation.</span>
            </div>
        </div>
    </div>
</section>

<section class="course-trust-strip">
    <div class="container">
        <div class="course-trust-grid">
            <div class="trust-card">
                <span>👨‍🏫</span>
                <div>
                    <strong>Expert Faculty</strong>
                    <small>Experienced teachers with practical teaching approach.</small>
                </div>
            </div>

            <div class="trust-card">
                <span>📝</span>
                <div>
                    <strong>Regular Practice</strong>
                    <small>Tests, assignments and performance improvement support.</small>
                </div>
            </div>

            <div class="trust-card">
                <span>🎯</span>
                <div>
                    <strong>Focused Batch</strong>
                    <small>Structured course plan for better learning outcomes.</small>
                </div>
            </div>

            <div class="trust-card">
                <span>💬</span>
                <div>
                    <strong>Counselling</strong>
                    <small>Complete admission, fee and batch timing guidance.</small>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section section-light">
    <div class="container course-layout">
        <main>
            <div class="info-panel">
                <h2>Course Overview</h2>
                <p>{{ $overview }}</p>

                <div class="course-facts">
                    <div class="fact-box">
                        <small>Course Type</small>
                        <strong>{{ $course->course_type ?: 'Coaching Course' }}</strong>
                    </div>

                    <div class="fact-box">
                        <small>Class / Level</small>
                        <strong>{{ $course->class_level ?: 'All Levels' }}</strong>
                    </div>

                    <div class="fact-box">
                        <small>Duration</small>
                        <strong>{{ $course->duration ?: 'Flexible' }}</strong>
                    </div>
                </div>
            </div>

            @if($features->count())
                <div class="info-panel">
                    <h3>Course Highlights</h3>

                    <ul class="clean-list">
                        @foreach($features as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($subjects->count())
                <div class="info-panel">
                    <h3>Subjects Covered</h3>

                    <ul class="clean-list">
                        @foreach($subjects as $subject)
                            <li>{{ $subject }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($eligibility->count())
                <div class="info-panel">
                    <h3>Eligibility</h3>

                    <ul class="clean-list">
                        @foreach($eligibility as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($relatedCourses->count())
                <div class="section-title" style="text-align:left;margin:42px 0 22px;">
                    <h2>Related Courses</h2>
                    <p>Explore more courses that may match your preparation goal.</p>
                </div>

                <div class="grid-3">
                    @foreach($relatedCourses as $item)
                        <div class="card related-card">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" loading="lazy">
                            @endif

                            <div class="card-body">
                                <h3>{{ $item->title }}</h3>

                                <p>
                                    {{ Str::limit($item->short_description ?: $item->description, 95) }}
                                </p>

                                <a href="{{ route('courses.show', $item->slug) }}" class="btn btn-primary">
                                    View Course
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </main>

        <aside class="course-sidebar">
            <div class="booking-card">
                <div class="booking-head">
                    <h3>Book Free Counselling</h3>
                    <p>Get batch details, fee structure and admission guidance from our counsellor.</p>
                </div>

                <div class="booking-body">
                    <div class="fee-card">
                        <small>Course Fee</small>

                        <strong>
                            {{ $mainFee ? '₹' . number_format($mainFee, 0) : 'On Request' }}
                        </strong>

                        @if($hasDiscount)
                            <span class="old-price">₹{{ number_format($course->fee, 0) }}</span>
                            <br>
                            <span class="discount-tag">Special offer available</span>
                        @endif
                    </div>

                    <div class="sidebar-actions">
                        <a href="tel:+{{ $telNumber }}" class="btn btn-primary cta-btn">
                            📞 Call Now
                        </a>

                        <a href="https://wa.me/{{ $whatsappNumber }}?text={{ $whatsappText }}"
                           target="_blank"
                           rel="noopener"
                           class="btn btn-success cta-btn">
                            💬 WhatsApp Enquiry
                        </a>

                        <a href="#course-enquiry-form" class="btn btn-dark cta-btn">
                            📝 Send Enquiry
                        </a>

                        <a href="{{ route('courses.index') }}" class="btn btn-light cta-btn">
                            ← Back to Courses
                        </a>
                    </div>

                    <div class="sidebar-note">
                        <div><span>✓</span> Counsellor will contact you shortly</div>
                        <div><span>✓</span> Get batch timing and fee details</div>
                        <div><span>✓</span> No obligation, free guidance</div>
                    </div>

                    <div class="form-box" id="course-enquiry-form">
                        <div class="form-box-head">
                            <h4>Request a Callback</h4>
                            <p>Fill your details and our team will guide you about this course.</p>
                        </div>

                        @include('frontend.partials.lead-form', [
                            'course' => $course,
                            'courses' => collect([$course]),
                            'source' => 'course_detail_' . $course->slug
                        ])
                    </div>
                </div>
            </div>
        </aside>
    </div>
</section>

@endsection
