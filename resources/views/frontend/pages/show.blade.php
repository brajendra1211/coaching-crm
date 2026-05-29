@extends('frontend.layouts.app')

@php
    use Illuminate\Support\Str;

    $setting = $siteSetting ?? \App\Models\CoachingSetting::current();

    $instituteName = $setting->institute_name ?? 'Best Coaching Institute';
    $themePrimary = $setting->primary_color ?: '#2563eb';

    $pageTitle = $page->seo_title ?: ($page->hero_title ?: $page->title);

    $rawDescription = $page->seo_description
        ?: ($page->hero_subtitle ?: strip_tags($page->content ?: ''));

    $metaDescription = Str::limit(
        trim(preg_replace('/\s+/', ' ', strip_tags((string) $rawDescription))),
        158,
        ''
    );

    if (!$metaDescription) {
        $metaDescription = 'Get complete details about ' . $page->title . ', courses, admission, batches and counselling support at ' . $instituteName . '.';
    }

    $metaKeywords = $page->seo_keywords
        ?: ($page->title . ', ' . $instituteName . ', coaching institute, admission, courses, best coaching classes');

    $canonicalUrl = url('/' . $page->slug);

    $phoneDigits = preg_replace('/\D+/', '', (string) ($setting->phone ?: '9999999999'));
    $phoneDigits = ltrim($phoneDigits, '0') ?: '9999999999';

    $whatsappDigits = preg_replace('/\D+/', '', (string) ($setting->whatsapp ?: $phoneDigits));
    $whatsappDigits = ltrim($whatsappDigits, '0') ?: $phoneDigits;

    $telNumber = strlen($phoneDigits) === 10 ? '91' . $phoneDigits : $phoneDigits;
    $whatsappNumber = strlen($whatsappDigits) === 10 ? '91' . $whatsappDigits : $whatsappDigits;

    $displayPhone = strlen($phoneDigits) === 10
        ? '+91 ' . substr($phoneDigits, 0, 5) . ' ' . substr($phoneDigits, 5)
        : '+' . $phoneDigits;

    $content = trim((string) $page->content);
    $hasHtmlContent = $content !== strip_tags($content);

    $contentText = trim(strip_tags($content));
    $readingMinutes = max(1, (int) ceil(str_word_count($contentText) / 180));

    /*
    |--------------------------------------------------------------------------
    | Hero Image + Social Share Image
    |--------------------------------------------------------------------------
    | Hero image admin se upload hogi to hero section aur WhatsApp/Facebook
    | preview dono me wahi image use hogi. Agar hero image nahi hai to logo
    | fallback image ke roop me use hoga.
    */

    $heroImageUrl = !empty($page->hero_image)
        ? asset('storage/' . $page->hero_image)
        : null;

    $logoImageUrl = !empty($setting->logo)
        ? asset('storage/' . $setting->logo)
        : null;

    $shareImageUrl = $heroImageUrl ?: $logoImageUrl;

    $pageTypeText = ucfirst(str_replace('_', ' ', $page->page_type ?: 'coaching page'));

    $breadcrumbs = [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => url('/'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => $page->title,
            'item' => $canonicalUrl,
        ],
    ];

    $webPageSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => $pageTitle,
        'headline' => $page->hero_title ?: $page->title,
        'description' => $metaDescription,
        'url' => $canonicalUrl,
        'image' => $shareImageUrl,
        'dateModified' => optional($page->updated_at)->toIso8601String(),
        'datePublished' => optional($page->created_at)->toIso8601String(),
        'inLanguage' => 'en-IN',
        'isPartOf' => [
            '@type' => 'WebSite',
            'name' => $instituteName,
            'url' => url('/'),
        ],
        'publisher' => [
            '@type' => 'EducationalOrganization',
            'name' => $instituteName,
            'url' => url('/'),
            'telephone' => '+' . $telNumber,
            'email' => $setting->email ?? null,
            'address' => $setting->address ?? null,
            'logo' => $logoImageUrl,
        ],
    ];

    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbs,
    ];

    $organizationSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'EducationalOrganization',
        'name' => $instituteName,
        'url' => url('/'),
        'logo' => $logoImageUrl,
        'image' => $shareImageUrl,
        'telephone' => '+' . $telNumber,
        'email' => $setting->email ?? null,
        'address' => $setting->address ?? null,
    ];

    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => [
            [
                '@type' => 'Question',
                'name' => 'How can I get admission details?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'You can submit the enquiry form or contact our counselling team by phone or WhatsApp to get admission details.',
                ],
            ],
            [
                '@type' => 'Question',
                'name' => 'Can I speak with a counsellor before joining?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'Yes, you can book free counselling and discuss course details, batches, fees and preparation strategy.',
                ],
            ],
            [
                '@type' => 'Question',
                'name' => 'Do you provide regular tests and performance support?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'Yes, students get structured learning support, regular practice and performance improvement guidance.',
                ],
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
@section('robots', $page->status === 'active' ? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' : 'noindex, nofollow')

@push('head')
    {{-- Open Graph / Facebook / WhatsApp --}}
    <meta property="og:locale" content="en_IN">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="{{ $instituteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">

    @if($shareImageUrl)
        <meta property="og:image" content="{{ $shareImageUrl }}">
        <meta property="og:image:secure_url" content="{{ $shareImageUrl }}">
        <meta property="og:image:type" content="image/jpeg">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="{{ $page->hero_title ?: $page->title }}">
        <meta itemprop="image" content="{{ $shareImageUrl }}">
    @endif

    {{-- Twitter / X --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">

    @if($shareImageUrl)
        <meta name="twitter:image" content="{{ $shareImageUrl }}">
        <meta name="twitter:image:alt" content="{{ $page->hero_title ?: $page->title }}">
    @endif

    {{-- Extra SEO --}}
    <meta name="author" content="{{ $instituteName }}">
    <meta name="article:publisher" content="{{ $instituteName }}">
    <meta name="theme-color" content="{{ $themePrimary }}">

    <script type="application/ld+json">
        {!! json_encode($webPageSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    <script type="application/ld+json">
        {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    <script type="application/ld+json">
        {!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    <script type="application/ld+json">
        {!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@push('styles')
<style>
    .advanced-page-hero {
        position: relative;
        overflow: hidden;
        color: #fff;
        padding: 82px 0 78px;
        background:
            radial-gradient(circle at 10% 10%, color-mix(in srgb, var(--primary) 35%, transparent), transparent 28%),
            radial-gradient(circle at 90% 18%, color-mix(in srgb, var(--secondary) 35%, transparent), transparent 30%),
            linear-gradient(135deg, #0f172a 0%, var(--primary) 54%, var(--secondary) 100%);
    }

    .advanced-page-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
        background-size: 42px 42px;
        opacity: .45;
    }

    .advanced-page-hero::after {
        content: "";
        position: absolute;
        inset: auto -10% -45% -10%;
        height: 260px;
        background: rgba(255, 255, 255, .10);
        filter: blur(50px);
        transform: rotate(-3deg);
    }

    .advanced-hero-grid {
        position: relative;
        z-index: 2;
        display: grid;
        grid-template-columns: minmax(0, 1fr) 440px;
        gap: 38px;
        align-items: center;
    }

    .advanced-hero-grid.no-hero-image {
        grid-template-columns: minmax(0, 1fr);
    }

    .breadcrumb {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 18px;
        font-size: 14px;
        color: rgba(255, 255, 255, .82);
        font-weight: 800;
    }

    .breadcrumb a {
        color: #fff;
    }

    .hero-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 14px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, .25);
        background: rgba(255, 255, 255, .12);
        backdrop-filter: blur(14px);
        font-size: 13px;
        font-weight: 900;
        margin-bottom: 18px;
    }

    .advanced-page-hero h1 {
        margin: 0;
        max-width: 900px;
        font-size: clamp(34px, 5vw, 58px);
        line-height: 1.06;
        letter-spacing: -1.4px;
        color: #fff;
    }

    .advanced-page-hero p {
        max-width: 780px;
        margin: 18px 0 0;
        color: rgba(255, 255, 255, .91);
        font-size: 18px;
        line-height: 1.75;
    }

    .hero-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 28px;
    }

    .hero-trust-points {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 24px;
    }

    .hero-trust-points span {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .18);
        color: rgba(255, 255, 255, .92);
        font-weight: 900;
        font-size: 13px;
    }

    .hero-image-card {
        position: relative;
        border-radius: 34px;
        overflow: hidden;
        min-height: 450px;
        box-shadow: 0 30px 80px rgba(15, 23, 42, .34);
        border: 1px solid rgba(255, 255, 255, .25);
        background: rgba(255, 255, 255, .12);
        isolation: isolate;
    }

    .hero-image-card img {
        width: 100%;
        height: 100%;
        min-height: 450px;
        object-fit: cover;
        display: block;
        transform: scale(1.01);
    }

    .hero-image-card::before {
        content: "";
        position: absolute;
        inset: 14px;
        border: 1px solid rgba(255, 255, 255, .25);
        border-radius: 26px;
        z-index: 2;
        pointer-events: none;
    }

    .hero-image-card::after {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(180deg, transparent 42%, rgba(15, 23, 42, .72)),
            radial-gradient(circle at top right, color-mix(in srgb, var(--primary) 18%, transparent), transparent 34%);
        z-index: 1;
    }

    .hero-image-badge {
        position: absolute;
        left: 24px;
        right: 24px;
        bottom: 24px;
        z-index: 3;
        padding: 16px 18px;
        border-radius: 22px;
        background: rgba(255, 255, 255, .94);
        color: #0f172a;
        box-shadow: 0 12px 30px rgba(15, 23, 42, .18);
    }

    .hero-image-badge strong {
        display: block;
        font-size: 17px;
        margin-bottom: 5px;
        color: #0f172a;
    }

    .hero-image-badge span {
        display: block;
        color: #64748b;
        font-size: 13px;
        font-weight: 800;
        line-height: 1.45;
    }

    .hero-floating-tag {
        position: absolute;
        top: 24px;
        right: 24px;
        z-index: 3;
        padding: 10px 13px;
        border-radius: 999px;
        background: rgba(255,255,255,.94);
        color: var(--primary);
        font-size: 13px;
        font-weight: 900;
        box-shadow: 0 12px 26px rgba(15, 23, 42, .16);
    }

    .trust-strip {
        background: #fff;
        border-bottom: 1px solid var(--border);
    }

    .trust-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-top: -34px;
        position: relative;
        z-index: 5;
    }

    .trust-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 22px;
        padding: 20px;
        box-shadow: var(--shadow-soft);
        display: flex;
        gap: 14px;
        align-items: flex-start;
    }

    .trust-card span {
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        border-radius: 16px;
        background: var(--soft-blue);
        color: var(--primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 21px;
    }

    .trust-card strong {
        display: block;
        color: var(--heading);
        font-size: 17px;
        margin-bottom: 5px;
    }

    .trust-card small {
        color: var(--muted);
        line-height: 1.45;
        font-weight: 700;
    }

    .dynamic-page-wrap {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 390px;
        gap: 30px;
        align-items: start;
    }

    .dynamic-page-wrap.no-form {
        grid-template-columns: 1fr;
    }

    .main-content-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 28px;
        overflow: hidden;
        box-shadow: var(--shadow-soft);
    }

    .content-head {
        padding: 28px 30px;
        border-bottom: 1px solid var(--border);
        background:
            radial-gradient(circle at top right, color-mix(in srgb, var(--primary) 10%, transparent), transparent 28%),
            linear-gradient(180deg, #ffffff, #f8fafc);
    }

    .content-head-row {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .content-head h2 {
        margin: 12px 0 0;
        color: var(--heading);
        font-size: clamp(25px, 3vw, 36px);
        line-height: 1.18;
        letter-spacing: -.6px;
    }

    .badge-soft {
        display: inline-flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 999px;
        background: var(--soft-blue);
        color: var(--primary);
        font-size: 13px;
        font-weight: 900;
    }

    .reading-info {
        display: inline-flex;
        gap: 8px;
        align-items: center;
        padding: 8px 12px;
        border-radius: 999px;
        background: #fff;
        border: 1px solid var(--border);
        color: var(--muted);
        font-size: 13px;
        font-weight: 900;
    }

    .page-content-body {
        padding: 30px;
    }

    .seo-content {
        color: var(--text);
        line-height: 1.9;
        font-size: 17px;
    }

    .seo-content h1,
    .seo-content h2,
    .seo-content h3,
    .seo-content h4 {
        color: var(--heading);
        line-height: 1.22;
        margin: 28px 0 12px;
        letter-spacing: -.3px;
    }

    .seo-content h2 {
        font-size: 30px;
    }

    .seo-content h3 {
        font-size: 24px;
    }

    .seo-content p {
        margin: 0 0 16px;
    }

    .seo-content ul,
    .seo-content ol {
        margin: 14px 0 20px;
        padding-left: 22px;
    }

    .seo-content li {
        margin: 8px 0;
    }

    .seo-content a {
        color: var(--primary);
        font-weight: 900;
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    .seo-content blockquote {
        margin: 22px 0;
        padding: 18px 20px;
        border-left: 5px solid var(--primary);
        background: color-mix(in srgb, var(--primary) 9%, #ffffff);
        border-radius: 18px;
        color: var(--primary);
        font-weight: 800;
    }

    .seo-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 22px 0;
        overflow: hidden;
        border-radius: 16px;
    }

    .seo-content th,
    .seo-content td {
        border: 1px solid var(--border);
        padding: 12px;
        text-align: left;
    }

    .seo-content th {
        background: #f8fafc;
        color: var(--heading);
    }

    .empty-content-box {
        padding: 24px;
        border: 1px dashed color-mix(in srgb, var(--primary) 35%, #ffffff);
        border-radius: 22px;
        background: color-mix(in srgb, var(--primary) 9%, #ffffff);
        color: var(--primary);
        font-weight: 800;
        line-height: 1.7;
    }

    .cta-card-pro {
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at 90% 10%, rgba(255,255,255,.24), transparent 25%),
            linear-gradient(135deg, var(--primary), var(--secondary));
        color: #fff;
        border-radius: 28px;
        padding: 30px;
        margin-top: 28px;
        box-shadow: 0 22px 55px color-mix(in srgb, var(--primary) 22%, transparent);
    }

    .cta-card-pro h2 {
        margin: 0 0 10px;
        color: #fff;
        font-size: 30px;
        line-height: 1.18;
    }

    .cta-card-pro p {
        margin: 0;
        color: rgba(255,255,255,.90);
        line-height: 1.75;
        max-width: 780px;
    }

    .cta-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 22px;
    }

    .related-section {
        margin-top: 42px;
    }

    .related-title {
        display: flex;
        justify-content: space-between;
        align-items: end;
        gap: 16px;
        margin-bottom: 20px;
    }

    .related-title h2 {
        margin: 0;
        color: var(--heading);
        font-size: 32px;
    }

    .related-title p {
        margin: 8px 0 0;
        color: var(--muted);
        line-height: 1.6;
    }

    .popular-course-card img {
        height: 176px;
        object-fit: cover;
    }

    .popular-course-card .card-body {
        padding: 20px;
    }

    .popular-course-card h3 {
        font-size: 20px;
    }

    .page-sidebar {
        position: sticky;
        top: 105px;
    }

    .sidebar-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 26px;
        padding: 24px;
        box-shadow: var(--shadow);
        margin-bottom: 18px;
    }

    .sidebar-card h2,
    .sidebar-card h3 {
        margin: 0 0 10px;
        color: var(--heading);
    }

    .sidebar-card p {
        margin: 0 0 16px;
        color: var(--muted);
        line-height: 1.65;
    }

    .sidebar-benefits {
        display: grid;
        gap: 10px;
        margin-top: 14px;
    }

    .sidebar-benefits div {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        padding: 12px;
        border-radius: 16px;
        background: #f8fafc;
        color: #334155;
        font-weight: 800;
        line-height: 1.45;
    }

    .sidebar-benefits span {
        color: var(--green);
        font-weight: 900;
    }

    .quick-contact {
        display: grid;
        gap: 10px;
    }

    .quick-contact a {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        padding: 13px 14px;
        border: 1px solid var(--border);
        border-radius: 16px;
        color: var(--heading);
        font-weight: 900;
        background: #f8fafc;
    }

    .quick-contact a:hover {
        background: var(--soft-blue);
        color: var(--primary);
    }

    .faq-grid {
        display: grid;
        gap: 14px;
        margin-top: 28px;
    }

    .faq-item {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 20px;
        box-shadow: var(--shadow-soft);
    }

    .faq-item h3 {
        margin: 0 0 8px;
        color: var(--heading);
        font-size: 19px;
    }

    .faq-item p {
        margin: 0;
        color: var(--muted);
        line-height: 1.7;
    }

    @media(max-width: 1080px) {
        .advanced-hero-grid,
        .advanced-hero-grid.no-hero-image,
        .dynamic-page-wrap,
        .dynamic-page-wrap.no-form {
            grid-template-columns: 1fr;
        }

        .page-sidebar {
            position: static;
        }

        .trust-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media(max-width: 640px) {
        .advanced-page-hero {
            padding: 54px 0 58px;
        }

        .advanced-page-hero h1 {
            font-size: 34px;
        }

        .advanced-page-hero p {
            font-size: 16px;
        }

        .hero-image-card {
            min-height: 290px;
            border-radius: 24px;
        }

        .hero-image-card img {
            min-height: 290px;
        }

        .hero-image-badge {
            left: 16px;
            right: 16px;
            bottom: 16px;
        }

        .hero-floating-tag {
            top: 16px;
            right: 16px;
        }

        .trust-grid {
            grid-template-columns: 1fr;
            margin-top: -22px;
        }

        .content-head,
        .page-content-body,
        .cta-card-pro,
        .sidebar-card {
            padding: 22px;
        }

        .seo-content {
            font-size: 16px;
        }

        .seo-content h2 {
            font-size: 25px;
        }

        .related-title {
            display: block;
        }
    }
</style>
@endpush

@section('content')

<section class="advanced-page-hero">
    <div class="container advanced-hero-grid {{ $heroImageUrl ? '' : 'no-hero-image' }}">
        <div>
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <span>›</span>
                <span>{{ $page->title }}</span>
            </nav>

            <div class="hero-label">
                <span>🎓</span>
                <span>{{ $pageTypeText }}</span>
            </div>

            <h1>{{ $page->hero_title ?: $page->title }}</h1>

            <p>
                {{ $page->hero_subtitle ?: $metaDescription }}
            </p>

            <div class="hero-actions">
                <a href="tel:+{{ $telNumber }}" class="btn btn-light">Call {{ $displayPhone }}</a>

                <a
                    href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('Hello, I want details about ' . $page->title . ' - ' . $canonicalUrl) }}"
                    target="_blank"
                    rel="noopener"
                    class="btn btn-success"
                >
                    WhatsApp Now
                </a>

                <a href="{{ url('/courses') }}" class="btn btn-dark">Explore Courses</a>
            </div>

            <div class="hero-trust-points">
                <span>✓ Expert Faculty</span>
                <span>✓ Regular Tests</span>
                <span>✓ Student Support</span>
                <span>✓ Counselling Available</span>
            </div>
        </div>

        @if($heroImageUrl)
            <aside class="hero-image-card">
                <span class="hero-floating-tag">Admission Open</span>

                <img
                    src="{{ $heroImageUrl }}"
                    alt="{{ $page->hero_title ?: $page->title }}"
                    loading="eager"
                    fetchpriority="high"
                >

                <div class="hero-image-badge">
                    <strong>{{ $instituteName }}</strong>
                    <span>Admission guidance, courses, batches and student support.</span>
                </div>
            </aside>
        @endif
    </div>
</section>

<section class="trust-strip">
    <div class="container">
        <div class="trust-grid">
            <div class="trust-card">
                <span>👨‍🏫</span>
                <div>
                    <strong>Expert Faculty</strong>
                    <small>Experienced teachers with structured teaching methodology.</small>
                </div>
            </div>

            <div class="trust-card">
                <span>📚</span>
                <div>
                    <strong>Updated Study Plan</strong>
                    <small>Course structure designed for result-oriented preparation.</small>
                </div>
            </div>

            <div class="trust-card">
                <span>📝</span>
                <div>
                    <strong>Regular Tests</strong>
                    <small>Practice, performance tracking and improvement support.</small>
                </div>
            </div>

            <div class="trust-card">
                <span>🎯</span>
                <div>
                    <strong>Personal Guidance</strong>
                    <small>Counselling support for students and parents.</small>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section section-light">
    <div class="container dynamic-page-wrap {{ $page->show_enquiry_form ? '' : 'no-form' }}">
        <main>
            <article class="main-content-card">
                <header class="content-head">
                    <div class="content-head-row">
                        <div>
                            <span class="badge-soft">{{ $pageTypeText }}</span>
                            <h2>{{ $page->title }}</h2>
                        </div>

                        <div class="reading-info">
                            <span>⏱</span>
                            <span>{{ $readingMinutes }} min read</span>
                        </div>
                    </div>
                </header>

                <div class="page-content-body">
                    @if($content)
                        <div class="seo-content">
                            @if($hasHtmlContent)
                                {!! $content !!}
                            @else
                                {!! nl2br(e($content)) !!}
                            @endif
                        </div>
                    @else
                        <div class="empty-content-box">
                            Content abhi add nahi hua hai. Admin panel se is page ka SEO content, headings, paragraphs, points aur CTA update kar sakte hain.
                        </div>
                    @endif
                </div>
            </article>

            <section class="cta-card-pro">
                <h2>{{ $page->cta_title ?: 'Book Free Counselling Session' }}</h2>

                <p>
                    {{ $page->cta_description ?: 'Our academic counsellor will help you choose the right course, batch and preparation plan according to your class, exam target and learning level.' }}
                </p>

                <div class="cta-actions">
                    <a href="tel:+{{ $telNumber }}" class="btn btn-light">Call Now</a>

                    <a
                        href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('Hello, I want counselling for ' . $page->title . ' - ' . $canonicalUrl) }}"
                        target="_blank"
                        rel="noopener"
                        class="btn btn-success"
                    >
                        Chat on WhatsApp
                    </a>

                    <a href="{{ url('/courses') }}" class="btn btn-dark">View Courses</a>
                </div>
            </section>

            @if(isset($courses) && $courses->count())
                <section class="related-section">
                    <div class="related-title">
                        <div>
                            <h2>Popular Courses</h2>
                            <p>Explore our best coaching programs and choose the right course for your preparation.</p>
                        </div>

                        <a href="{{ route('courses.index') }}" class="btn btn-primary">View All Courses</a>
                    </div>

                    <div class="grid-3">
                        @foreach($courses->take(3) as $course)
                            <div class="card popular-course-card">
                                @if($course->image)
                                    <img
                                        src="{{ asset('storage/' . $course->image) }}"
                                        alt="{{ $course->title }}"
                                        loading="lazy"
                                    >
                                @endif

                                <div class="card-body">
                                    <span class="badge-soft">Course</span>

                                    <h3>{{ $course->title }}</h3>

                                    <p>
                                        {{ Str::limit($course->short_description ?: $course->description, 95) }}
                                    </p>

                                    @if(!empty($course->price))
                                        <p class="price">
                                            ₹{{ number_format($course->price) }}

                                            @if(!empty($course->old_price))
                                                <span class="old-price">₹{{ number_format($course->old_price) }}</span>
                                            @endif
                                        </p>
                                    @endif

                                    <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-primary">
                                        View Course
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="related-section">
                <div class="related-title">
                    <div>
                        <h2>Frequently Asked Questions</h2>
                        <p>Common questions students and parents ask before admission.</p>
                    </div>
                </div>

                <div class="faq-grid">
                    <div class="faq-item">
                        <h3>How can I get admission details?</h3>
                        <p>You can call us, send a WhatsApp message or submit the enquiry form. Our counsellor will contact you with complete admission details.</p>
                    </div>

                    <div class="faq-item">
                        <h3>Can I discuss course and batch details before joining?</h3>
                        <p>Yes, our team will guide you about suitable course, batch timing, fee structure and preparation strategy.</p>
                    </div>

                    <div class="faq-item">
                        <h3>Do you provide regular tests and performance support?</h3>
                        <p>Yes, students get structured learning support, regular practice and performance improvement guidance.</p>
                    </div>
                </div>
            </section>
        </main>

        @if($page->show_enquiry_form)
            <aside class="page-sidebar">
                <div class="sidebar-card">
                    <h2>Send Enquiry</h2>
                    <p>Submit your details and our admission counsellor will contact you shortly.</p>

                    @include('frontend.partials.lead-form', [
                        'courses' => $courses,
                        'source' => 'website_page_' . $page->slug
                    ])
                </div>

                <div class="sidebar-card">
                    <h3>Why Choose Us?</h3>

                    <div class="sidebar-benefits">
                        <div><span>✓</span> Experienced and supportive faculty</div>
                        <div><span>✓</span> Result-focused study plan</div>
                        <div><span>✓</span> Regular tests and feedback</div>
                        <div><span>✓</span> Parent and student counselling</div>
                    </div>
                </div>

                <div class="sidebar-card">
                    <h3>Quick Contact</h3>
                    <p>Need instant help? Contact our admission team.</p>

                    <div class="quick-contact">
                        <a href="tel:+{{ $telNumber }}">
                            <span>Call Now</span>
                            <strong>📞</strong>
                        </a>

                        <a
                            href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('Hello, I need admission help. ' . $canonicalUrl) }}"
                            target="_blank"
                            rel="noopener"
                        >
                            <span>WhatsApp</span>
                            <strong>💬</strong>
                        </a>

                        @if(!empty($setting->email))
                            <a href="mailto:{{ $setting->email }}">
                                <span>Email Us</span>
                                <strong>✉️</strong>
                            </a>
                        @endif
                    </div>
                </div>
            </aside>
        @endif
    </div>
</section>

@endsection
