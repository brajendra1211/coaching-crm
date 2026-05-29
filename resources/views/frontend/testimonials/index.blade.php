@extends('frontend.layouts.app')

@php
    $setting = $siteSetting ?? \App\Models\CoachingSetting::current();
    $instituteName = $setting->institute_name ?? 'Best Coaching Institute';

    $pageTitle = 'Testimonials | Student Reviews - ' . $instituteName;
    $metaDescription = 'Read student and parent testimonials, reviews, success stories and feedback about courses, faculty and learning support at ' . $instituteName . '.';
    $canonicalUrl = route('testimonials.index');
@endphp

@section('title', $pageTitle)
@section('meta_description', $metaDescription)
@section('canonical', $canonicalUrl)
@section('robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')

@push('head')
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta name="twitter:card" content="summary_large_image">

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Review',
            'itemReviewed' => [
                '@type' => 'EducationalOrganization',
                'name' => $instituteName,
                'url' => url('/'),
            ],
            'reviewRating' => [
                '@type' => 'Rating',
                'ratingValue' => $averageRating,
                'bestRating' => 5,
            ],
            'author' => [
                '@type' => 'Organization',
                'name' => $instituteName,
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@push('styles')
<style>
    .testimonial-hero {
        position: relative;
        overflow: hidden;
        padding: 78px 0;
        color: #fff;
        background:
            radial-gradient(circle at 15% 10%, rgba(96,165,250,.34), transparent 30%),
            radial-gradient(circle at 85% 20%, rgba(168,85,247,.32), transparent 30%),
            linear-gradient(135deg, #0f172a, #1d4ed8, #7c3aed);
    }

    .testimonial-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(rgba(255,255,255,.06) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.06) 1px, transparent 1px);
        background-size: 42px 42px;
        opacity: .42;
    }

    .testimonial-hero .container {
        position: relative;
        z-index: 2;
    }

    .testimonial-hero h1 {
        margin: 0;
        color: #fff;
        font-size: clamp(36px, 5vw, 62px);
        line-height: 1.06;
        letter-spacing: -1.2px;
    }

    .testimonial-hero p {
        margin: 18px 0 0;
        max-width: 780px;
        color: rgba(255,255,255,.90);
        font-size: 18px;
        line-height: 1.75;
    }

    .review-stats {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 26px;
    }

    .review-stats span {
        display: inline-flex;
        padding: 10px 14px;
        border-radius: 999px;
        background: rgba(255,255,255,.13);
        border: 1px solid rgba(255,255,255,.20);
        font-weight: 900;
        color: #fff;
        font-size: 13px;
    }

    .featured-testimonials {
        margin-top: -36px;
        position: relative;
        z-index: 5;
    }

    .featured-review-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .testimonial-card {
        position: relative;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 28px;
        padding: 24px;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
    }

    .testimonial-card::before {
        content: "“";
        position: absolute;
        right: 22px;
        top: 4px;
        font-size: 94px;
        line-height: 1;
        color: rgba(37, 99, 235, .10);
        font-family: Georgia, serif;
    }

    .testimonial-top {
        display: flex;
        gap: 14px;
        align-items: center;
        margin-bottom: 16px;
        position: relative;
        z-index: 1;
    }

    .testimonial-photo {
        width: 62px;
        height: 62px;
        border-radius: 20px;
        object-fit: cover;
        object-position: center top;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
        flex: 0 0 62px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        border: 1px solid #e5e7eb;
    }

    .testimonial-top strong {
        display: block;
        color: var(--heading);
        font-size: 17px;
        margin-bottom: 3px;
    }

    .testimonial-top small {
        display: block;
        color: var(--muted);
        line-height: 1.4;
        font-weight: 700;
    }

    .stars {
        color: #f59e0b;
        font-weight: 900;
        letter-spacing: 1px;
        margin-bottom: 12px;
        position: relative;
        z-index: 1;
    }

    .testimonial-card p {
        color: #475569;
        line-height: 1.75;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    .filter-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 26px;
        padding: 20px;
        box-shadow: var(--shadow-soft);
        margin-bottom: 28px;
    }

    .filter-form {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 180px auto;
        gap: 12px;
        align-items: center;
    }

    .filter-form input,
    .filter-form select {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 14px;
        padding: 13px 14px;
        outline: none;
    }

    .testimonial-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 22px;
    }

    .cta-review-box {
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.24), transparent 25%),
            linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
        border-radius: 30px;
        padding: 30px;
        margin-top: 34px;
        box-shadow: 0 22px 55px rgba(37, 99, 235, .22);
        display: flex;
        justify-content: space-between;
        gap: 18px;
        align-items: center;
        flex-wrap: wrap;
    }

    .cta-review-box h2 {
        margin: 0 0 8px;
        color: #fff;
    }

    .cta-review-box p {
        margin: 0;
        color: rgba(255,255,255,.90);
        line-height: 1.7;
    }

    @media(max-width: 1100px) {
        .featured-review-grid,
        .testimonial-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media(max-width: 680px) {
        .testimonial-hero {
            padding: 56px 0;
        }

        .featured-review-grid,
        .testimonial-grid,
        .filter-form {
            grid-template-columns: 1fr;
        }

        .featured-testimonials {
            margin-top: -24px;
        }
    }
</style>
@endpush

@section('content')

<section class="testimonial-hero">
    <div class="container">
        <h1>What Our Students Say</h1>
        <p>
            Real feedback from students and parents about our teaching quality, guidance, support and learning experience.
        </p>

        <div class="review-stats">
            <span>⭐ {{ $averageRating }}/5 Average Rating</span>
            <span>💬 {{ $totalTestimonials }}+ Reviews</span>
            <span>🎓 Student Success Stories</span>
            <span>👨‍🏫 Expert Guidance</span>
        </div>
    </div>
</section>

@if($featuredTestimonials->count())
    <section class="featured-testimonials">
        <div class="container">
            <div class="featured-review-grid">
                @foreach($featuredTestimonials as $item)
                    <article class="testimonial-card">
                        <div class="testimonial-top">
                            @if($item->image_url)
                                <img src="{{ $item->image_url }}" class="testimonial-photo" alt="{{ $item->name }}" loading="lazy">
                            @else
                                <div class="testimonial-photo">{{ $item->initials }}</div>
                            @endif

                            <div>
                                <strong>{{ $item->name }}</strong>
                                <small>
                                    {{ $item->designation ?: 'Student' }}
                                    @if($item->course_name)
                                        • {{ $item->course_name }}
                                    @endif
                                </small>
                                @if($item->location)
                                    <small>{{ $item->location }}</small>
                                @endif
                            </div>
                        </div>

                        <div class="stars">
                            {!! str_repeat('★', $item->rating) !!}{!! str_repeat('☆', 5 - $item->rating) !!}
                        </div>

                        <p>{{ \Illuminate\Support\Str::limit($item->review, 210) }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif

<section class="section section-light">
    <div class="container">
        <div class="filter-card">
            <form method="GET" class="filter-form">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search reviews...">

                <select name="rating">
                    <option value="">All Ratings</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                            {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                        </option>
                    @endfor
                </select>

                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>

        <div class="testimonial-grid">
            @forelse($testimonials as $item)
                <article class="testimonial-card">
                    <div class="testimonial-top">
                        @if($item->image_url)
                            <img src="{{ $item->image_url }}" class="testimonial-photo" alt="{{ $item->name }}" loading="lazy">
                        @else
                            <div class="testimonial-photo">{{ $item->initials }}</div>
                        @endif

                        <div>
                            <strong>{{ $item->name }}</strong>
                            <small>
                                {{ $item->designation ?: 'Student' }}
                                @if($item->course_name)
                                    • {{ $item->course_name }}
                                @endif
                            </small>
                            @if($item->location)
                                <small>{{ $item->location }}</small>
                            @endif
                        </div>
                    </div>

                    <div class="stars">
                        {!! str_repeat('★', $item->rating) !!}{!! str_repeat('☆', 5 - $item->rating) !!}
                    </div>

                    <p>{{ $item->review }}</p>
                </article>
            @empty
                <div class="card" style="grid-column:1/-1;text-align:center;">
                    <div class="card-body">
                        <h3>No Testimonials Found</h3>
                        <p>Testimonials will appear here once added by admin.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div style="margin-top:30px;">
            {{ $testimonials->links() }}
        </div>

        <div class="cta-review-box">
            <div>
                <h2>Want to Join Our Successful Students?</h2>
                <p>Book free counselling and get complete course, batch and admission guidance.</p>
            </div>

            <a href="{{ url('/courses') }}" class="btn btn-light">Explore Courses</a>
        </div>
    </div>
</section>

@endsection