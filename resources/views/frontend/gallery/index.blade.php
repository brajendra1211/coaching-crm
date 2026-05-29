@extends('frontend.layouts.app')

@php
    $setting = $siteSetting ?? \App\Models\CoachingSetting::current();
    $instituteName = $setting->institute_name ?? 'Best Coaching Institute';

    $pageTitle = 'Gallery | ' . $instituteName;
    $metaDescription = 'Explore our gallery photos, classroom activities, events, results, student achievements and videos at ' . $instituteName . '.';
    $canonicalUrl = route('gallery.index');
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
            '@type' => 'ImageGallery',
            'name' => 'Gallery - ' . $instituteName,
            'description' => $metaDescription,
            'url' => $canonicalUrl,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@push('styles')
<style>
    .gallery-hero {
        position: relative;
        overflow: hidden;
        padding: 78px 0;
        color: #fff;
        background:
            radial-gradient(circle at 15% 10%, rgba(96,165,250,.34), transparent 30%),
            radial-gradient(circle at 85% 20%, rgba(168,85,247,.32), transparent 30%),
            linear-gradient(135deg, #0f172a, #1d4ed8, #7c3aed);
    }

    .gallery-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(rgba(255,255,255,.06) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.06) 1px, transparent 1px);
        background-size: 42px 42px;
        opacity: .42;
    }

    .gallery-hero .container {
        position: relative;
        z-index: 2;
    }

    .gallery-hero h1 {
        margin: 0;
        color: #fff;
        font-size: clamp(36px, 5vw, 62px);
        line-height: 1.06;
        letter-spacing: -1.2px;
    }

    .gallery-hero p {
        margin: 18px 0 0;
        max-width: 780px;
        color: rgba(255,255,255,.90);
        font-size: 18px;
        line-height: 1.75;
    }

    .gallery-stats {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 26px;
    }

    .gallery-stats span {
        display: inline-flex;
        padding: 10px 14px;
        border-radius: 999px;
        background: rgba(255,255,255,.13);
        border: 1px solid rgba(255,255,255,.20);
        font-weight: 900;
        color: #fff;
        font-size: 13px;
    }

    .featured-gallery {
        margin-top: -36px;
        position: relative;
        z-index: 5;
    }

    .featured-grid {
        display: grid;
        grid-template-columns: 1.4fr .8fr .8fr;
        gap: 16px;
    }

    .featured-card {
        position: relative;
        min-height: 230px;
        border-radius: 26px;
        overflow: hidden;
        box-shadow: 0 22px 55px rgba(15,23,42,.18);
        border: 1px solid rgba(255,255,255,.4);
        cursor: pointer;
        background: #e5e7eb;
    }

    .featured-card:first-child {
        min-height: 330px;
        grid-row: span 2;
    }

    .featured-card img {
        width: 100%;
        height: 100%;
        min-height: inherit;
        object-fit: cover;
        display: block;
        transition: .35s ease;
    }

    .featured-card:hover img {
        transform: scale(1.06);
    }

    .featured-card::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, transparent 42%, rgba(15,23,42,.74));
    }

    .featured-content {
        position: absolute;
        left: 18px;
        right: 18px;
        bottom: 18px;
        z-index: 2;
        color: #fff;
    }

    .featured-content strong {
        display: block;
        font-size: 18px;
        margin-bottom: 5px;
    }

    .featured-content small {
        display: inline-flex;
        padding: 7px 10px;
        border-radius: 999px;
        background: rgba(255,255,255,.18);
        font-weight: 900;
    }

    .play-icon {
        position: absolute;
        top: 18px;
        right: 18px;
        z-index: 3;
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: rgba(255,255,255,.92);
        color: #dc2626;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 19px;
        box-shadow: 0 14px 30px rgba(15,23,42,.22);
    }

    .gallery-filter-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 26px;
        padding: 20px;
        box-shadow: var(--shadow-soft);
        margin-bottom: 28px;
    }

    .gallery-filter-form {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 180px 220px auto;
        gap: 12px;
        align-items: center;
    }

    .gallery-filter-form input,
    .gallery-filter-form select {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 14px;
        padding: 13px 14px;
        outline: none;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 22px;
    }

    .gallery-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 26px;
        overflow: hidden;
        box-shadow: var(--shadow-soft);
        cursor: pointer;
        transition: .25s ease;
    }

    .gallery-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow);
    }

    .gallery-card-media {
        position: relative;
        height: 240px;
        overflow: hidden;
        background: #e5e7eb;
    }

    .gallery-card-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: .35s ease;
    }

    .gallery-card:hover img {
        transform: scale(1.06);
    }

    .gallery-type-badge {
        position: absolute;
        left: 14px;
        top: 14px;
        padding: 8px 11px;
        border-radius: 999px;
        background: rgba(255,255,255,.94);
        color: #0f172a;
        font-size: 12px;
        font-weight: 900;
        box-shadow: 0 10px 25px rgba(15,23,42,.14);
    }

    .gallery-card-body {
        padding: 20px;
    }

    .gallery-card-body h3 {
        margin: 0 0 8px;
        color: var(--heading);
        font-size: 20px;
        line-height: 1.3;
    }

    .gallery-card-body p {
        margin: 0;
        color: var(--muted);
        line-height: 1.6;
        font-size: 14px;
    }

    .gallery-category {
        display: inline-flex;
        margin-bottom: 10px;
        padding: 7px 10px;
        border-radius: 999px;
        background: var(--soft-blue);
        color: var(--primary);
        font-size: 12px;
        font-weight: 900;
    }

    .gallery-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(15, 23, 42, .82);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 22px;
    }

    .gallery-modal.active {
        display: flex;
    }

    .gallery-modal-box {
        width: min(980px, 100%);
        background: #fff;
        border-radius: 26px;
        overflow: hidden;
        box-shadow: 0 30px 100px rgba(0,0,0,.35);
        position: relative;
    }

    .gallery-modal-close {
        position: absolute;
        top: 14px;
        right: 14px;
        z-index: 4;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 0;
        background: rgba(255,255,255,.94);
        color: #0f172a;
        font-weight: 900;
        cursor: pointer;
        box-shadow: 0 10px 25px rgba(15,23,42,.18);
    }

    .modal-media img {
        width: 100%;
        max-height: 72vh;
        object-fit: contain;
        background: #0f172a;
        display: block;
    }

    .modal-media iframe {
        width: 100%;
        height: min(60vh, 560px);
        border: 0;
        display: block;
        background: #0f172a;
    }

    .modal-caption {
        padding: 18px 22px;
    }

    .modal-caption h3 {
        margin: 0 0 6px;
        color: var(--heading);
    }

    .modal-caption p {
        margin: 0;
        color: var(--muted);
        line-height: 1.6;
    }

    @media(max-width: 1100px) {
        .featured-grid,
        .gallery-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .featured-card:first-child {
            grid-row: auto;
        }

        .gallery-filter-form {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media(max-width: 680px) {
        .gallery-hero {
            padding: 56px 0;
        }

        .featured-grid,
        .gallery-grid,
        .gallery-filter-form {
            grid-template-columns: 1fr;
        }

        .featured-card,
        .featured-card:first-child,
        .gallery-card-media {
            min-height: 240px;
            height: 240px;
        }

        .modal-media iframe {
            height: 320px;
        }
    }
</style>
@endpush

@section('content')

<section class="gallery-hero">
    <div class="container">
        <h1>Our Gallery</h1>
        <p>
            Explore our institute moments, classroom activities, achievements, events, student success stories and video highlights.
        </p>

        <div class="gallery-stats">
            <span>📸 Photos</span>
            <span>▶ YouTube Videos</span>
            <span>🏆 Achievements</span>
            <span>🎓 Student Activities</span>
        </div>
    </div>
</section>

@if($featuredItems->count())
    <section class="featured-gallery">
        <div class="container">
            <div class="featured-grid">
                @foreach($featuredItems as $item)
                    <div
                        class="featured-card js-gallery-open"
                        data-type="{{ $item->type }}"
                        data-title="{{ $item->title }}"
                        data-description="{{ $item->description }}"
                        data-image="{{ $item->type === 'image' ? $item->image_url : '' }}"
                        data-video="{{ $item->type === 'youtube' ? $item->youtube_embed_url : '' }}"
                    >
                        @if($item->thumbnail_url)
                            <img src="{{ $item->thumbnail_url }}" alt="{{ $item->title }}" loading="lazy">
                        @endif

                        @if($item->type === 'youtube')
                            <div class="play-icon">▶</div>
                        @endif

                        <div class="featured-content">
                            <small>{{ $item->category ?: ($item->type === 'youtube' ? 'Video' : 'Photo') }}</small>
                            <strong>{{ $item->title }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

<section class="section section-light">
    <div class="container">
        <div class="gallery-filter-card">
            <form method="GET" class="gallery-filter-form">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search gallery...">

                <select name="type">
                    <option value="">All Media</option>
                    <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Images</option>
                    <option value="youtube" {{ request('type') === 'youtube' ? 'selected' : '' }}>YouTube Videos</option>
                </select>

                <select name="category">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>

        <div class="gallery-grid">
            @forelse($items as $item)
                <article
                    class="gallery-card js-gallery-open"
                    data-type="{{ $item->type }}"
                    data-title="{{ $item->title }}"
                    data-description="{{ $item->description }}"
                    data-image="{{ $item->type === 'image' ? $item->image_url : '' }}"
                    data-video="{{ $item->type === 'youtube' ? $item->youtube_embed_url : '' }}"
                >
                    <div class="gallery-card-media">
                        @if($item->thumbnail_url)
                            <img src="{{ $item->thumbnail_url }}" alt="{{ $item->title }}" loading="lazy">
                        @endif

                        <span class="gallery-type-badge">
                            {{ $item->type === 'youtube' ? '▶ Video' : '📸 Photo' }}
                        </span>

                        @if($item->type === 'youtube')
                            <div class="play-icon">▶</div>
                        @endif
                    </div>

                    <div class="gallery-card-body">
                        <span class="gallery-category">{{ $item->category ?: 'Gallery' }}</span>
                        <h3>{{ $item->title }}</h3>

                        @if($item->description)
                            <p>{{ \Illuminate\Support\Str::limit($item->description, 95) }}</p>
                        @endif
                    </div>
                </article>
            @empty
                <div class="card" style="grid-column:1/-1;text-align:center;">
                    <div class="card-body">
                        <h3>No Gallery Items Found</h3>
                        <p>Gallery images and videos will appear here once added by admin.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div style="margin-top:30px;">
            {{ $items->links() }}
        </div>
    </div>
</section>

<div class="gallery-modal" id="galleryModal">
    <div class="gallery-modal-box">
        <button type="button" class="gallery-modal-close" id="galleryModalClose">×</button>

        <div class="modal-media" id="modalMedia"></div>

        <div class="modal-caption">
            <h3 id="modalTitle"></h3>
            <p id="modalDescription"></p>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('galleryModal');
    const modalMedia = document.getElementById('modalMedia');
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    const modalClose = document.getElementById('galleryModalClose');

    document.querySelectorAll('.js-gallery-open').forEach(card => {
        card.addEventListener('click', function () {
            const type = this.dataset.type;
            const title = this.dataset.title || '';
            const description = this.dataset.description || '';
            const image = this.dataset.image || '';
            const video = this.dataset.video || '';

            modalTitle.textContent = title;
            modalDescription.textContent = description;

            if (type === 'youtube' && video) {
                modalMedia.innerHTML = `<iframe src="${video}?autoplay=1&rel=0" allow="autoplay; encrypted-media" allowfullscreen></iframe>`;
            } else if (image) {
                modalMedia.innerHTML = `<img src="${image}" alt="${title}">`;
            }

            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });

    function closeGalleryModal() {
        modal.classList.remove('active');
        modalMedia.innerHTML = '';
        document.body.style.overflow = '';
    }

    modalClose.addEventListener('click', closeGalleryModal);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeGalleryModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeGalleryModal();
        }
    });
</script>

@endsection