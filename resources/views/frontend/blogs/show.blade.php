@extends('frontend.layouts.app')

@php
    use Illuminate\Support\Str;

    $setting = $siteSetting ?? \App\Models\CoachingSetting::current();
    $instituteName = $setting->institute_name ?? 'Best Coaching Institute';

    $pageTitle = $blog->seo_title ?: $blog->title;

    $metaDescription = $blog->seo_description
        ?: Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($blog->excerpt ?: $blog->content))), 158, '');

    if (!$metaDescription) {
        $metaDescription = 'Read latest blog: ' . $blog->title . ' by ' . $instituteName . '.';
    }

    $canonicalUrl = route('blogs.show', $blog->slug);

    $imageUrl = $blog->featured_image
        ? asset('storage/' . $blog->featured_image)
        : (!empty($setting->logo) ? asset('storage/' . $setting->logo) : null);

    $content = trim((string) $blog->content);
    $hasHtmlContent = $content !== strip_tags($content);
    $readingMinutes = max(1, (int) ceil(str_word_count(strip_tags($content)) / 180));

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $blog->title,
        'description' => $metaDescription,
        'image' => $imageUrl,
        'url' => $canonicalUrl,
        'datePublished' => optional($blog->published_at ?: $blog->created_at)->toIso8601String(),
        'dateModified' => optional($blog->updated_at)->toIso8601String(),
        'author' => [
            '@type' => 'Person',
            'name' => $blog->author_name ?: $instituteName,
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => $instituteName,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => !empty($setting->logo) ? asset('storage/' . $setting->logo) : $imageUrl,
            ],
        ],
    ];
@endphp

@section('title', $pageTitle)
@section('meta_description', $metaDescription)
@section('meta_keywords', $blog->seo_keywords ?: ($blog->title . ', coaching blog, exam tips, admission guide'))
@section('canonical', $canonicalUrl)
@section('og_title', $pageTitle)
@section('og_description', $metaDescription)
@section('robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')

@push('head')
    <meta property="og:locale" content="en_IN">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="{{ $instituteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">

    @if($imageUrl)
        <meta property="og:image" content="{{ $imageUrl }}">
        <meta property="og:image:secure_url" content="{{ $imageUrl }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="{{ $blog->title }}">
        <meta name="twitter:image" content="{{ $imageUrl }}">
        <meta itemprop="image" content="{{ $imageUrl }}">
    @endif

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">

    <script type="application/ld+json">
        {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@push('styles')
<style>
    .blog-detail-hero {
        background:
            radial-gradient(circle at top left, rgba(37,99,235,.26), transparent 30%),
            linear-gradient(135deg, #0f172a, #1d4ed8, #7c3aed);
        color: #fff;
        padding: 72px 0;
    }

    .blog-detail-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 430px;
        gap: 34px;
        align-items: center;
    }

    .blog-detail-hero h1 {
        margin: 0;
        font-size: clamp(34px, 5vw, 56px);
        line-height: 1.08;
        color: #fff;
    }

    .blog-detail-hero p {
        margin: 18px 0 0;
        color: rgba(255,255,255,.9);
        font-size: 18px;
        line-height: 1.7;
    }

    .blog-meta-line {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .blog-meta-line span {
        display: inline-flex;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(255,255,255,.14);
        color: #fff;
        border: 1px solid rgba(255,255,255,.20);
        font-size: 13px;
        font-weight: 900;
    }

    .blog-hero-img {
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 30px 80px rgba(15,23,42,.32);
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.25);
    }

    .blog-hero-img img {
        width: 100%;
        height: 360px;
        object-fit: cover;
        display: block;
    }

    .blog-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 350px;
        gap: 28px;
        align-items: start;
    }

    .blog-content-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 28px;
        padding: 32px;
        box-shadow: var(--shadow-soft);
    }

    .blog-content {
        color: var(--text);
        line-height: 1.9;
        font-size: 17px;
    }

    .blog-content h2,
    .blog-content h3,
    .blog-content h4 {
        color: var(--heading);
        line-height: 1.25;
        margin: 28px 0 12px;
    }

    .blog-content h2 {
        font-size: 30px;
    }

    .blog-content h3 {
        font-size: 24px;
    }

    .blog-content p {
        margin: 0 0 16px;
    }

    .blog-content ul,
    .blog-content ol {
        padding-left: 22px;
        margin: 14px 0 20px;
    }

    .blog-content li {
        margin: 8px 0;
    }

    .blog-content a {
        color: var(--primary);
        font-weight: 900;
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    .blog-sidebar {
        position: sticky;
        top: 105px;
    }

    .side-box {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 22px;
        box-shadow: var(--shadow-soft);
        margin-bottom: 18px;
    }

    .side-box h3 {
        margin: 0 0 14px;
        color: var(--heading);
    }

    .related-link {
        display: block;
        padding: 13px 0;
        border-bottom: 1px solid var(--border);
        color: var(--heading);
        font-weight: 900;
        line-height: 1.45;
    }

    .related-link:last-child {
        border-bottom: 0;
    }

    @media(max-width: 1000px) {
        .blog-detail-grid,
        .blog-layout {
            grid-template-columns: 1fr;
        }

        .blog-sidebar {
            position: static;
        }
    }
</style>
@endpush

@section('content')

<section class="blog-detail-hero">
    <div class="container blog-detail-grid">
        <div>
            <div class="blog-meta-line">
                <span>{{ $blog->category ?: 'Blog' }}</span>
                <span>{{ ($blog->published_at ?: $blog->created_at)->format('d M Y') }}</span>
                <span>{{ $readingMinutes }} min read</span>
            </div>

            <h1>{{ $blog->title }}</h1>

            <p>{{ $blog->excerpt ?: $metaDescription }}</p>
        </div>

        @if($imageUrl)
            <div class="blog-hero-img">
                <img src="{{ $imageUrl }}" alt="{{ $blog->title }}" loading="eager" fetchpriority="high">
            </div>
        @endif
    </div>
</section>

<section class="section section-light">
    <div class="container blog-layout">
        <main>
            <article class="blog-content-card">
                <div class="blog-content">
                    @if($content)
                        @if($hasHtmlContent)
                            {!! $content !!}
                        @else
                            {!! nl2br(e($content)) !!}
                        @endif
                    @else
                        <p>Blog content will be updated soon.</p>
                    @endif
                </div>
            </article>
        </main>

        <aside class="blog-sidebar">
            <div class="side-box">
                <h3>Need Admission Help?</h3>
                <p style="color:#64748b;line-height:1.7;">
                    Talk to our counsellor for course, batch, fee and admission guidance.
                </p>
                <a href="{{ url('/contact') }}" class="btn btn-primary">Book Free Counselling</a>
            </div>

            @if($relatedBlogs->count())
                <div class="side-box">
                    <h3>Related Blogs</h3>

                    @foreach($relatedBlogs as $related)
                        <a href="{{ route('blogs.show', $related->slug) }}" class="related-link">
                            {{ $related->title }}
                        </a>
                    @endforeach
                </div>
            @endif

            @if($courses->count())
                <div class="side-box">
                    <h3>Popular Courses</h3>

                    @foreach($courses->take(4) as $course)
                        <a href="{{ route('courses.show', $course->slug) }}" class="related-link">
                            {{ $course->title }}
                        </a>
                    @endforeach
                </div>
            @endif
        </aside>
    </div>
</section>

@endsection