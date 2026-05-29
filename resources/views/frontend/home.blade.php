@extends('frontend.layouts.app')

@php
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Str;

    $setting = $siteSetting ?? \App\Models\CoachingSetting::current();

    $instituteName = $setting->institute_name ?: 'Edu Institute';
    $tagline = $setting->tagline ?: 'Coaching | Exams | Results';

    $phoneDigits = preg_replace('/\D+/', '', (string) ($setting->phone ?: '9999999999'));
    $phoneDigits = ltrim($phoneDigits, '0') ?: '9999999999';

    $whatsappDigits = preg_replace('/\D+/', '', (string) ($setting->whatsapp ?: $phoneDigits));
    $whatsappDigits = ltrim($whatsappDigits, '0') ?: $phoneDigits;

    $telNumber = strlen($phoneDigits) === 10 ? '91' . $phoneDigits : $phoneDigits;
    $whatsappNumber = strlen($whatsappDigits) === 10 ? '91' . $whatsappDigits : $whatsappDigits;

    $logoImage = $setting->logo ? asset('storage/' . $setting->logo) : null;

    $homeTitle = $setting->home_seo_title
        ?? $setting->default_seo_title
        ?? ($instituteName . ' | Best Coaching Institute for NEET, IIT JEE & Board Exams');

    $homeDescription = $setting->home_seo_description
        ?? $setting->default_seo_description
        ?? 'Join result-focused coaching classes for NEET, IIT JEE, Board Exams and competitive preparation with expert faculty, regular tests and personal guidance.';

    $heroTitle = $setting->home_hero_title
        ?? 'Build Strong Concepts for NEET, IIT JEE & Board Exams';

    $heroHighlight = $setting->home_hero_highlight
        ?? 'Result-Focused Coaching';

    $heroSubtitle = $setting->home_hero_subtitle
        ?? 'Join structured coaching with expert faculty, regular tests, doubt sessions, study material and personal academic guidance.';

    $homeHeroImage = !empty($setting->home_hero_image)
        ? asset('storage/' . $setting->home_hero_image)
        : null;

    $heroImage = $homeHeroImage ?: $logoImage;

    $whatsappText = 'Hello, I want admission/course details for ' . $instituteName . '. Please share more information.';

    /*
    |--------------------------------------------------------------------------
    | Dynamic Courses Fallback
    |--------------------------------------------------------------------------
    */
    $featuredCourses = isset($featuredCourses) ? collect($featuredCourses) : collect();

    if ($featuredCourses->isEmpty()) {
        try {
            if (class_exists(\App\Models\Course::class) && Schema::hasTable('courses')) {
                $courseQuery = \App\Models\Course::query();

                if (Schema::hasColumn('courses', 'status')) {
                    $courseQuery->where('status', 'active');
                }

                if (Schema::hasColumn('courses', 'is_active')) {
                    $courseQuery->where('is_active', 1);
                }

                if (Schema::hasColumn('courses', 'is_featured')) {
                    $courseQuery->orderByDesc('is_featured');
                }

                if (Schema::hasColumn('courses', 'sort_order')) {
                    $courseQuery->orderBy('sort_order');
                } elseif (Schema::hasColumn('courses', 'created_at')) {
                    $courseQuery->latest();
                } else {
                    $courseQuery->orderByDesc('id');
                }

                $featuredCourses = $courseQuery->take(6)->get();
            }
        } catch (\Throwable $e) {
            $featuredCourses = collect();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Dynamic Testimonials Fallback
    |--------------------------------------------------------------------------
    */
    $testimonials = isset($testimonials) ? collect($testimonials) : collect();

    if ($testimonials->isEmpty()) {
        try {
            if (class_exists(\App\Models\Testimonial::class) && Schema::hasTable('testimonials')) {
                $testimonialQuery = \App\Models\Testimonial::query();

                if (Schema::hasColumn('testimonials', 'status')) {
                    $testimonialQuery->where('status', 'active');
                }

                if (Schema::hasColumn('testimonials', 'is_active')) {
                    $testimonialQuery->where('is_active', 1);
                }

                if (Schema::hasColumn('testimonials', 'sort_order')) {
                    $testimonialQuery->orderBy('sort_order');
                } elseif (Schema::hasColumn('testimonials', 'created_at')) {
                    $testimonialQuery->latest();
                } else {
                    $testimonialQuery->orderByDesc('id');
                }

                $testimonials = $testimonialQuery->take(3)->get();
            }
        } catch (\Throwable $e) {
            $testimonials = collect();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Dynamic Blogs Fallback
    |--------------------------------------------------------------------------
    */
    $latestBlogs = isset($latestBlogs) ? collect($latestBlogs) : collect();

    if ($latestBlogs->isEmpty()) {
        try {
            if (class_exists(\App\Models\Blog::class) && Schema::hasTable('blogs')) {
                $blogQuery = \App\Models\Blog::query();

                if (Schema::hasColumn('blogs', 'status')) {
                    $blogQuery->where('status', 'active');
                }

                if (Schema::hasColumn('blogs', 'is_active')) {
                    $blogQuery->where('is_active', 1);
                }

                if (Schema::hasColumn('blogs', 'published_at')) {
                    $blogQuery->whereNotNull('published_at')->latest('published_at');
                } elseif (Schema::hasColumn('blogs', 'created_at')) {
                    $blogQuery->latest();
                } else {
                    $blogQuery->orderByDesc('id');
                }

                $latestBlogs = $blogQuery->take(3)->get();
            }
        } catch (\Throwable $e) {
            $latestBlogs = collect();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Gallery Fallback
    |--------------------------------------------------------------------------
    */
    $galleryItems = isset($galleryItems) ? collect($galleryItems) : collect();

    if ($galleryItems->isEmpty()) {
        try {
            if (class_exists(\App\Models\Gallery::class) && Schema::hasTable('galleries')) {
                $galleryQuery = \App\Models\Gallery::query();

                if (Schema::hasColumn('galleries', 'status')) {
                    $galleryQuery->where('status', 'active');
                }

                if (Schema::hasColumn('galleries', 'is_active')) {
                    $galleryQuery->where('is_active', 1);
                }

                if (Schema::hasColumn('galleries', 'created_at')) {
                    $galleryQuery->latest();
                } else {
                    $galleryQuery->orderByDesc('id');
                }

                $galleryItems = $galleryQuery->take(6)->get();
            }
        } catch (\Throwable $e) {
            $galleryItems = collect();
        }
    }

    $locations = isset($seoLocations) ? collect($seoLocations)->take(8) : collect();

    $courseUrl = function ($course) {
        if (!empty($course->slug) && Route::has('courses.show')) {
            return route('courses.show', $course->slug);
        }

        return !empty($course->slug) ? url('/courses/' . $course->slug) : url('/courses');
    };

    $blogUrl = function ($blog) {
        if (!empty($blog->slug) && Route::has('blogs.show')) {
            return route('blogs.show', $blog->slug);
        }

        return !empty($blog->slug) ? url('/blogs/' . $blog->slug) : url('/blogs');
    };

    $locationUrl = function ($location) {
        return Route::has('location.show')
            ? route('location.show', $location->slug)
            : url('/' . $location->slug);
    };

    $stats = [
        [
            'number' => $setting->students_count ?? '500+',
            'label' => 'Students Guided',
            'text' => 'Personal attention and performance tracking'
        ],
        [
            'number' => $setting->courses_count ?? ($featuredCourses->count() ? $featuredCourses->count() . '+' : '20+'),
            'label' => 'Courses & Batches',
            'text' => 'Foundation, board and competitive programs'
        ],
        [
            'number' => $setting->tests_count ?? '50+',
            'label' => 'Mock Tests',
            'text' => 'Regular practice with analysis'
        ],
        [
            'number' => $setting->rating ?? '4.8/5',
            'label' => 'Student Rating',
            'text' => 'Trusted by students and parents'
        ],
    ];

    $features = [
        [
            'icon' => '🎯',
            'title' => 'Goal-Based Learning',
            'text' => 'Course plans are designed according to student goals, exam pattern and academic level.'
        ],
        [
            'icon' => '👨‍🏫',
            'title' => 'Expert Faculty',
            'text' => 'Experienced teachers focus on concept clarity, doubt solving and exam-oriented preparation.'
        ],
        [
            'icon' => '📊',
            'title' => 'Test & Performance Tracking',
            'text' => 'Regular tests, mock exams and progress analysis help students improve continuously.'
        ],
        [
            'icon' => '📚',
            'title' => 'Study Material',
            'text' => 'Structured notes, worksheets, assignments and practice questions for every chapter.'
        ],
    ];

    $process = [
        ['step' => '01', 'title' => 'Submit Enquiry', 'text' => 'Call, WhatsApp or fill the enquiry form to connect with our counsellor.'],
        ['step' => '02', 'title' => 'Free Counselling', 'text' => 'Get proper course, batch and preparation guidance as per your goal.'],
        ['step' => '03', 'title' => 'Join Batch', 'text' => 'Complete admission and join the suitable batch with proper study plan.'],
        ['step' => '04', 'title' => 'Track Growth', 'text' => 'Attend classes, tests and doubt sessions with performance monitoring.'],
    ];
@endphp

@section('title', $homeTitle)
@section('meta_description', $homeDescription)
@section('meta_keywords', $setting->home_seo_keywords ?: ($setting->default_seo_keywords ?: 'coaching institute, NEET coaching, IIT JEE coaching, board exam coaching, competitive exam coaching'))
@section('canonical', url('/'))
@section('og_title', $homeTitle)
@section('og_description', $homeDescription)
@section('og_image', $logoImage)
@section('robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')

@push('styles')
<style>
    .edu-hero {
        position: relative;
        overflow: hidden;
        padding: 54px 0 62px;
        background:
            radial-gradient(circle at 12% 12%, color-mix(in srgb, var(--primary) 18%, transparent), transparent 28%),
            radial-gradient(circle at 88% 18%, color-mix(in srgb, var(--secondary) 16%, transparent), transparent 30%),
            linear-gradient(135deg, #f8fbff 0%, #eef6ff 44%, #ffffff 100%);
    }

    .edu-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(color-mix(in srgb, var(--primary) 6%, transparent) 1px, transparent 1px),
            linear-gradient(90deg, color-mix(in srgb, var(--primary) 6%, transparent) 1px, transparent 1px);
        background-size: 42px 42px;
        pointer-events: none;
    }

    .edu-hero-grid {
        position: relative;
        display: grid;
        grid-template-columns: 1.08fr .92fr;
        gap: 36px;
        align-items: center;
    }

    .edu-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 14px;
        border-radius: 999px;
        background: #fff;
        border: 1px solid color-mix(in srgb, var(--primary) 28%, #ffffff);
        color: var(--primary);
        font-size: 13px;
        font-weight: 900;
        box-shadow: 0 10px 24px color-mix(in srgb, var(--primary) 12%, transparent);
        margin-bottom: 16px;
    }

    .edu-kicker span {
        width: 9px;
        height: 9px;
        border-radius: 999px;
        background: var(--green);
        box-shadow: 0 0 0 6px color-mix(in srgb, var(--green) 16%, transparent);
    }

    .edu-hero h1 {
        margin: 0 0 18px;
        color: var(--heading);
        font-size: clamp(34px, 5vw, 62px);
        line-height: 1.02;
        letter-spacing: -1.5px;
    }

    .edu-hero h1 strong {
        color: var(--primary);
        font-weight: 950;
    }

    .edu-hero p {
        margin: 0;
        max-width: 680px;
        color: #475569;
        font-size: 17px;
        line-height: 1.78;
    }

    .edu-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 28px;
    }

    .edu-trust-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 28px;
    }

    .edu-trust-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 13px;
        border-radius: 999px;
        background: rgba(255,255,255,.86);
        border: 1px solid #dbeafe;
        color: #334155;
        font-size: 13px;
        font-weight: 900;
    }

    .edu-visual {
        position: relative;
    }

    .edu-visual-card {
        position: relative;
        background: rgba(255, 255, 255, .92);
        border: 1px solid rgba(226, 232, 240, .95);
        border-radius: 34px;
        padding: 18px;
        box-shadow: 0 24px 70px rgba(15, 23, 42, .13);
        overflow: hidden;
    }

    .edu-visual-card::before {
        content: "";
        position: absolute;
        width: 180px;
        height: 180px;
        right: -60px;
        top: -60px;
        border-radius: 999px;
        background: linear-gradient(135deg, rgba(37,99,235,.16), rgba(124,58,237,.14));
    }

    .edu-visual-top {
        position: relative;
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 14px;
    }

    .edu-hero-photo {
        position: relative;
        height: 270px;
        border-radius: 28px;
        overflow: hidden;
        margin-bottom: 16px;
        border: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #dbeafe, #f5f3ff);
        box-shadow: 0 16px 38px rgba(15,23,42,.10);
    }

    .edu-hero-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center top;
        display: block;
    }

    .edu-hero-photo::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(15,23,42,0) 52%, rgba(15,23,42,.42));
    }

    .edu-hero-photo-label {
        position: absolute;
        left: 16px;
        bottom: 16px;
        z-index: 2;
        padding: 10px 13px;
        border-radius: 999px;
        background: rgba(255,255,255,.94);
        color: var(--primary);
        font-size: 13px;
        font-weight: 950;
        box-shadow: 0 12px 26px rgba(15,23,42,.14);
    }

    .edu-logo-box {
        width: 66px;
        height: 66px;
        border-radius: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: #fff;
        font-size: 26px;
        font-weight: 900;
        overflow: hidden;
        box-shadow: 0 18px 36px rgba(37,99,235,.20);
    }

    .edu-logo-box img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        background: #fff;
        padding: 8px;
    }

    .edu-visual-top h3 {
        margin: 0 0 5px;
        color: var(--heading);
        font-size: 24px;
        line-height: 1.15;
    }

    .edu-visual-top p {
        margin: 0;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.45;
    }

    .edu-stats-grid {
        position: relative;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }

    .edu-stat {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        padding: 18px;
    }

    .edu-stat strong {
        display: block;
        color: var(--primary);
        font-size: 28px;
        line-height: 1;
        font-weight: 950;
        margin-bottom: 7px;
    }

    .edu-stat span {
        display: block;
        color: #0f172a;
        font-size: 14px;
        font-weight: 900;
        margin-bottom: 5px;
    }

    .edu-stat small {
        display: block;
        color: #64748b;
        font-size: 12px;
        line-height: 1.45;
        font-weight: 700;
    }

    .edu-floating-note {
        position: relative;
        max-width: 100%;
        margin: 16px 18px 0;
        padding: 14px 16px;
        border-radius: 18px;
        background: #0f172a;
        color: #fff;
        box-shadow: 0 18px 36px rgba(15,23,42,.24);
        font-size: 13px;
        font-weight: 850;
        line-height: 1.45;
    }

    .edu-floating-note b {
        color: #86efac;
    }

    .edu-section {
        padding: 72px 0;
    }

    .edu-section-light {
        background: linear-gradient(180deg, #f8fbff, #ffffff);
    }

    .edu-section-head {
        max-width: 820px;
        margin: 0 auto 36px;
        text-align: center;
    }

    .edu-section-head .mini {
        display: inline-flex;
        padding: 8px 13px;
        border-radius: 999px;
        background: color-mix(in srgb, var(--primary) 8%, #ffffff);
        border: 1px solid color-mix(in srgb, var(--primary) 28%, #ffffff);
        color: var(--primary);
        font-size: 12px;
        font-weight: 950;
        margin-bottom: 12px;
    }

    .edu-section-head h2 {
        margin: 0 0 12px;
        color: var(--heading);
        font-size: clamp(28px, 4vw, 42px);
        line-height: 1.15;
        letter-spacing: -.6px;
    }

    .edu-section-head p {
        margin: 0 auto;
        color: var(--muted);
        font-size: 16px;
        line-height: 1.75;
    }

    .edu-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    .edu-grid-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    .edu-course-card {
        position: relative;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 14px 34px rgba(15,23,42,.07);
        transition: .25s ease;
    }

    .edu-course-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 24px 58px rgba(15,23,42,.13);
    }

    .edu-course-image {
        height: 210px;
        background:
            radial-gradient(circle at top left, rgba(37,99,235,.18), transparent 35%),
            linear-gradient(135deg, #dbeafe, #f5f3ff);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .edu-course-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .edu-course-image .fallback {
        width: 76px;
        height: 76px;
        border-radius: 24px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        font-weight: 900;
    }

    .edu-course-body {
        padding: 24px;
    }

    .edu-course-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-bottom: 13px;
    }

    .edu-chip {
        display: inline-flex;
        align-items: center;
        padding: 7px 10px;
        border-radius: 999px;
        background: color-mix(in srgb, var(--primary) 8%, #ffffff);
        color: var(--primary);
        border: 1px solid color-mix(in srgb, var(--primary) 28%, #ffffff);
        font-size: 11px;
        font-weight: 950;
    }

    .edu-course-body h3 {
        margin: 0 0 10px;
        color: var(--heading);
        font-size: 22px;
        line-height: 1.25;
    }

    .edu-course-body p {
        margin: 0;
        color: var(--muted);
        font-size: 15px;
        line-height: 1.65;
    }

    .edu-price-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 16px;
    }

    .edu-price {
        color: var(--green);
        font-size: 22px;
        font-weight: 950;
    }

    .edu-old-price {
        color: #94a3b8;
        text-decoration: line-through;
        font-size: 14px;
        font-weight: 800;
    }

    .edu-card-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 20px;
    }

    .edu-feature-card,
    .edu-process-card,
    .edu-testimonial-card,
    .edu-blog-card,
    .edu-location-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 26px;
        padding: 24px;
        box-shadow: 0 12px 30px rgba(15,23,42,.06);
        transition: .25s ease;
    }

    .edu-feature-card:hover,
    .edu-process-card:hover,
    .edu-testimonial-card:hover,
    .edu-blog-card:hover,
    .edu-location-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 22px 52px rgba(15,23,42,.11);
    }

    .edu-feature-icon {
        width: 54px;
        height: 54px;
        border-radius: 18px;
        background: linear-gradient(135deg, color-mix(in srgb, var(--primary) 9%, #ffffff), color-mix(in srgb, var(--secondary) 8%, #ffffff));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 25px;
        margin-bottom: 16px;
    }

    .edu-review-head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }

    .edu-review-avatar {
        width: 54px;
        height: 54px;
        border-radius: 18px;
        overflow: hidden;
        flex: 0 0 54px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 950;
        font-size: 18px;
    }

    .edu-review-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .edu-feature-card h3,
    .edu-process-card h3,
    .edu-testimonial-card h3,
    .edu-blog-card h3,
    .edu-location-card h3 {
        margin: 0 0 9px;
        color: var(--heading);
        font-size: 20px;
        line-height: 1.3;
    }

    .edu-feature-card p,
    .edu-process-card p,
    .edu-testimonial-card p,
    .edu-blog-card p,
    .edu-location-card p {
        margin: 0;
        color: var(--muted);
        line-height: 1.68;
        font-size: 15px;
    }

    .edu-process-card {
        position: relative;
        overflow: hidden;
    }

    .edu-step {
        display: inline-flex;
        width: 48px;
        height: 48px;
        border-radius: 16px;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: #fff;
        font-size: 16px;
        font-weight: 950;
        margin-bottom: 16px;
        box-shadow: 0 14px 26px rgba(37,99,235,.22);
    }

    .edu-cta-box {
        position: relative;
        overflow: hidden;
        border-radius: 34px;
        padding: 42px;
        color: #fff;
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.18), transparent 28%),
            linear-gradient(135deg, #0f172a, var(--primary), var(--secondary));
        box-shadow: 0 24px 70px rgba(37,99,235,.22);
    }

    .edu-cta-box h2 {
        margin: 0 0 12px;
        font-size: clamp(28px, 4vw, 42px);
        line-height: 1.16;
    }

    .edu-cta-box p {
        margin: 0;
        max-width: 760px;
        color: rgba(255,255,255,.88);
        line-height: 1.75;
        font-size: 16px;
    }

    .edu-cta-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 24px;
    }

    .edu-cta-actions .btn-light {
        border: 0;
    }

    .edu-gallery-grid {
        display: grid;
        grid-template-columns: 1.2fr .8fr .8fr;
        gap: 18px;
    }

    .edu-gallery-item {
        min-height: 210px;
        border-radius: 26px;
        overflow: hidden;
        background: linear-gradient(135deg, #dbeafe, #f5f3ff);
        border: 1px solid #e5e7eb;
    }

    .edu-gallery-item:first-child {
        grid-row: span 2;
    }

    .edu-gallery-item img {
        width: 100%;
        height: 100%;
        min-height: 210px;
        object-fit: cover;
        display: block;
    }

    .edu-gallery-fallback {
        height: 100%;
        min-height: 210px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-weight: 950;
        font-size: 18px;
        text-align: center;
        padding: 20px;
    }

    .edu-empty {
        grid-column: 1 / -1;
        text-align: center;
        padding: 34px;
        background: #fff;
        border: 1px dashed color-mix(in srgb, var(--primary) 32%, #ffffff);
        border-radius: 26px;
        color: var(--muted);
        font-weight: 800;
    }

    @media(max-width: 1040px) {
        .edu-hero-grid,
        .edu-grid-3,
        .edu-grid-4,
        .edu-gallery-grid {
            grid-template-columns: 1fr 1fr;
        }

        .edu-gallery-item:first-child {
            grid-row: auto;
        }

        .edu-floating-note {
            margin: 14px 0 0;
            max-width: 100%;
        }
    }

    @media(max-width: 700px) {
        .edu-hero {
            padding: 38px 0 46px;
        }

        .edu-hero-grid,
        .edu-grid-3,
        .edu-grid-4,
        .edu-gallery-grid,
        .edu-stats-grid {
            grid-template-columns: 1fr;
        }

        .edu-section {
            padding: 52px 0;
        }

        .edu-actions .btn,
        .edu-card-actions .btn,
        .edu-cta-actions .btn {
            width: 100%;
        }

        .edu-cta-box {
            padding: 28px;
            border-radius: 26px;
        }
    }
</style>
@endpush

@section('content')

<section class="edu-hero">
    <div class="container edu-hero-grid">
        <div>
            <div class="edu-kicker">
                <span></span>
                Admissions Open • Free Counselling Available
            </div>

            <h1>
                {{ $heroTitle }}
                <strong>{{ $heroHighlight }}</strong>
            </h1>

            <p>{{ $heroSubtitle }}</p>

            <div class="edu-actions">
                <a href="{{ url('/courses') }}" class="btn btn-primary">Explore Courses</a>
                <a href="{{ url('/contact') }}" class="btn btn-dark">Book Free Counselling</a>
                <a href="https://wa.me/{{ $whatsappNumber }}?text={{ rawurlencode($whatsappText) }}" target="_blank" rel="noopener" class="btn btn-success">WhatsApp Enquiry</a>
            </div>

            <div class="edu-trust-row">
                <span class="edu-trust-pill">✅ Expert Faculty</span>
                <span class="edu-trust-pill">✅ Regular Tests</span>
                <span class="edu-trust-pill">✅ Doubt Sessions</span>
                <span class="edu-trust-pill">✅ Parent Updates</span>
            </div>
        </div>

        <div class="edu-visual">
            <div class="edu-visual-card">
                <div class="edu-visual-top">
                    <div class="edu-logo-box">
                        @if($heroImage)
                            <img src="{{ $heroImage }}" alt="{{ $instituteName }}">
                        @else
                            ED
                        @endif
                    </div>

                    <div>
                        <h3>{{ $instituteName }}</h3>
                        <p>{{ $tagline }}</p>
                    </div>
                </div>

                @if($homeHeroImage)
                    <div class="edu-hero-photo">
                        <img src="{{ $homeHeroImage }}" alt="{{ $instituteName }}" loading="eager" fetchpriority="high">
                        <span class="edu-hero-photo-label">Admissions Open</span>
                    </div>
                @endif

                <div class="edu-stats-grid">
                    @foreach($stats as $stat)
                        <div class="edu-stat">
                            <strong>{{ $stat['number'] }}</strong>
                            <span>{{ $stat['label'] }}</span>
                            <small>{{ $stat['text'] }}</small>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="edu-floating-note">
                <b>Free Counselling:</b> Find the right course, batch and preparation plan for your child.
            </div>
        </div>
    </div>
</section>

<section class="edu-section edu-section-light">
    <div class="container">
        <div class="edu-section-head">
            <span class="mini">Popular Programs</span>
            <h2>Choose the Right Course for Your Goal</h2>
            <p>All active/featured courses from admin panel will appear here automatically.</p>
        </div>

        <div class="edu-grid-3">
            @forelse($featuredCourses as $course)
                <article class="edu-course-card">
                    <div class="edu-course-image">
                        @if(!empty($course->image))
                            <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" loading="lazy">
                        @elseif(!empty($course->thumbnail))
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" loading="lazy">
                        @else
                            <div class="fallback">ED</div>
                        @endif
                    </div>

                    <div class="edu-course-body">
                        <div class="edu-course-meta">
                            @if(!empty($course->course_type))
                                <span class="edu-chip">{{ $course->course_type }}</span>
                            @endif

                            @if(!empty($course->class_level))
                                <span class="edu-chip">{{ $course->class_level }}</span>
                            @endif

                            @if(!empty($course->duration))
                                <span class="edu-chip">{{ $course->duration }}</span>
                            @endif
                        </div>

                        <h3>{{ $course->title }}</h3>

                        <p>
                            {{ Str::limit(strip_tags($course->short_description ?: $course->description ?: 'Structured course with expert guidance, tests and study support.'), 125) }}
                        </p>

                        <div class="edu-price-row">
                            @if(!empty($course->offer_fee))
                                <span class="edu-price">₹{{ number_format((float) $course->offer_fee, 0) }}</span>

                                @if(!empty($course->fee))
                                    <span class="edu-old-price">₹{{ number_format((float) $course->fee, 0) }}</span>
                                @endif
                            @elseif(!empty($course->fee))
                                <span class="edu-price">₹{{ number_format((float) $course->fee, 0) }}</span>
                            @else
                                <span class="edu-price">Fee on Request</span>
                            @endif
                        </div>

                        <div class="edu-card-actions">
                            <a href="{{ $courseUrl($course) }}" class="btn btn-primary">View Course</a>
                            <a href="https://wa.me/{{ $whatsappNumber }}?text={{ rawurlencode('Hi, I want to know about ' . $course->title) }}" target="_blank" rel="noopener" class="btn btn-dark">Enquire</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="edu-empty">
                    No active courses found. Admin panel se courses add karte hi yahan automatic show honge.
                </div>
            @endforelse
        </div>

        <div style="text-align:center;margin-top:34px;">
            <a href="{{ url('/courses') }}" class="btn btn-dark">View All Courses</a>
        </div>
    </div>
</section>

<section class="edu-section">
    <div class="container">
        <div class="edu-section-head">
            <span class="mini">Why Choose Us</span>
            <h2>A Complete Learning System for Better Results</h2>
            <p>Students ko sirf classes nahi, proper academic planning, practice, analysis aur guidance milti hai.</p>
        </div>

        <div class="edu-grid-4">
            @foreach($features as $feature)
                <div class="edu-feature-card">
                    <div class="edu-feature-icon">{{ $feature['icon'] }}</div>
                    <h3>{{ $feature['title'] }}</h3>
                    <p>{{ $feature['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="edu-section edu-section-light">
    <div class="container">
        <div class="edu-section-head">
            <span class="mini">Admission Process</span>
            <h2>Start Learning in 4 Simple Steps</h2>
            <p>Admission process ko simple, fast aur parent-friendly banaya gaya hai.</p>
        </div>

        <div class="edu-grid-4">
            @foreach($process as $item)
                <div class="edu-process-card">
                    <div class="edu-step">{{ $item['step'] }}</div>
                    <h3>{{ $item['title'] }}</h3>
                    <p>{{ $item['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

@if($testimonials->count())
<section class="edu-section">
    <div class="container">
        <div class="edu-section-head">
            <span class="mini">Student Reviews</span>
            <h2>What Students & Parents Say</h2>
            <p>Admin panel me active testimonials add karoge to yahan automatic show honge.</p>
        </div>

        <div class="edu-grid-3">
            @foreach($testimonials as $testimonial)
                <div class="edu-testimonial-card">
                    @php
                        $reviewImage = $testimonial->image ?? $testimonial->photo ?? $testimonial->student_image ?? null;
                        $reviewName = $testimonial->name ?? $testimonial->student_name ?? 'Student';
                    @endphp

                    <div class="edu-review-head">
                        <div class="edu-review-avatar">
                            @if($reviewImage)
                                <img src="{{ asset('storage/' . $reviewImage) }}" alt="{{ $reviewName }}" loading="lazy">
                            @else
                                {{ strtoupper(substr($reviewName, 0, 1)) }}
                            @endif
                        </div>

                        <div>
                            <h3 style="margin:0;">{{ $reviewName }}</h3>
                            <div style="color:#f59e0b;font-size:15px;margin-top:4px;">★★★★★</div>
                        </div>
                    </div>

                    <p>
                        “{{ Str::limit(strip_tags($testimonial->message ?? $testimonial->description ?? $testimonial->review ?? 'Excellent coaching experience.'), 160) }}”
                    </p>

                    @if(!empty($testimonial->designation) || !empty($testimonial->course))
                        <p>{{ $testimonial->designation ?? $testimonial->course }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($galleryItems->count())
<section class="edu-section edu-section-light">
    <div class="container">
        <div class="edu-section-head">
            <span class="mini">Campus Gallery</span>
            <h2>Classroom, Activities & Student Moments</h2>
            <p>Gallery images admin se manage hongi aur homepage par automatically update hongi.</p>
        </div>

        <div class="edu-gallery-grid">
            @foreach($galleryItems as $item)
                <div class="edu-gallery-item">
                    @php
                        $galleryImage = $item->image ?? $item->photo ?? $item->thumbnail ?? null;
                    @endphp

                    @if($galleryImage)
                        <img src="{{ asset('storage/' . $galleryImage) }}" alt="{{ $item->title ?? 'Gallery Image' }}" loading="lazy">
                    @else
                        <div class="edu-gallery-fallback">{{ $item->title ?? 'Gallery' }}</div>
                    @endif
                </div>
            @endforeach
        </div>

        <div style="text-align:center;margin-top:34px;">
            <a href="{{ url('/gallery') }}" class="btn btn-dark">View Gallery</a>
        </div>
    </div>
</section>
@endif

@if($latestBlogs->count())
<section class="edu-section">
    <div class="container">
        <div class="edu-section-head">
            <span class="mini">Latest Updates</span>
            <h2>Exam Tips, News & Study Guidance</h2>
            <p>Latest active blogs homepage par automatic show honge.</p>
        </div>

        <div class="edu-grid-3">
            @foreach($latestBlogs as $blog)
                <article class="edu-blog-card">
                    @if(!empty($blog->thumbnail))
                        <img src="{{ asset('storage/' . $blog->thumbnail) }}" alt="{{ $blog->title }}" loading="lazy" style="width:100%;height:190px;object-fit:cover;border-radius:20px;margin-bottom:18px;">
                    @elseif(!empty($blog->image))
                        <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}" loading="lazy" style="width:100%;height:190px;object-fit:cover;border-radius:20px;margin-bottom:18px;">
                    @endif

                    <h3>{{ $blog->title }}</h3>

                    <p>
                        {{ Str::limit(strip_tags($blog->short_description ?? $blog->description ?? $blog->content ?? ''), 130) }}
                    </p>

                    <div style="margin-top:18px;">
                        <a href="{{ $blogUrl($blog) }}" class="btn btn-primary">Read More</a>
                    </div>
                </article>
            @endforeach
        </div>

        <div style="text-align:center;margin-top:34px;">
            <a href="{{ url('/blogs') }}" class="btn btn-dark">View All Blogs</a>
        </div>
    </div>
</section>
@endif

@if($locations->count())
<section class="edu-section edu-section-light">
    <div class="container">
        <div class="edu-section-head">
            <span class="mini">Locations</span>
            <h2>Find Coaching Classes Near You</h2>
            <p>SEO location pages yahan automatic show honge, jisse local search ranking improve hogi.</p>
        </div>

        <div class="edu-grid-4">
            @foreach($locations as $location)
                <a href="{{ $locationUrl($location) }}" class="edu-location-card">
                    <h3>{{ $location->page_title ?? $location->title ?? $location->name }}</h3>
                    <p>{{ Str::limit(strip_tags($location->meta_description ?? $location->description ?? 'Explore coaching classes and admission details for this location.'), 100) }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="edu-section">
    <div class="container">
        <div class="edu-cta-box">
            <h2>Need Help Choosing the Right Course?</h2>
            <p>
                Call or WhatsApp us for free counselling. Our academic team will guide you about course,
                batch, fees, test system and preparation strategy.
            </p>

            <div class="edu-cta-actions">
                <a href="tel:+{{ $telNumber }}" class="btn btn-light">Call Now: +{{ $telNumber }}</a>
                <a href="https://wa.me/{{ $whatsappNumber }}?text={{ rawurlencode($whatsappText) }}" target="_blank" rel="noopener" class="btn btn-success">WhatsApp Enquiry</a>
                <a href="{{ url('/contact') }}" class="btn btn-dark">Book Free Counselling</a>
            </div>
        </div>
    </div>
</section>

@endsection
