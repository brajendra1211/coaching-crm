@extends('frontend.layouts.app')

@section('title', 'Blogs | Latest Coaching Updates & Exam Tips')
@section('meta_description', 'Read latest coaching updates, admission news, exam preparation tips, study guidance and student success articles.')
@section('canonical', route('blogs.index'))

@push('styles')
<style>
    .blog-hero {
        background:
            radial-gradient(circle at top left, rgba(37,99,235,.20), transparent 30%),
            linear-gradient(135deg, #eff6ff, #ffffff);
        padding: 62px 0;
    }

    .blog-hero h1 {
        margin: 0 0 12px;
        color: #0f172a;
        font-size: clamp(34px, 5vw, 54px);
        line-height: 1.08;
    }

    .blog-hero p {
        margin: 0;
        max-width: 760px;
        color: #475569;
        font-size: 18px;
        line-height: 1.7;
    }

    .blog-card img {
        width: 100%;
        height: 220px;
        object-fit: cover;
    }

    .blog-meta {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin: 12px 0;
    }

    .blog-meta span {
        background: #eff6ff;
        color: #2563eb;
        padding: 7px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
    }
</style>
@endpush

@section('content')

<section class="blog-hero">
    <div class="container">
        <h1>Latest Blogs & Updates</h1>
        <p>
            Read expert guidance, coaching updates, exam preparation tips and admission information to help students choose the right path.
        </p>
    </div>
</section>

<section class="section section-light">
    <div class="container">
        <div class="grid-3">
            @forelse($blogs as $blog)
                <article class="card blog-card">
                    @if($blog->featured_image)
                        <img src="{{ asset('storage/' . $blog->featured_image) }}" alt="{{ $blog->title }}" loading="lazy">
                    @endif

                    <div class="card-body">
                        <div class="blog-meta">
                            <span>{{ $blog->category ?: 'Blog' }}</span>
                            <span>{{ $blog->published_at ? $blog->published_at->format('d M Y') : $blog->created_at->format('d M Y') }}</span>
                        </div>

                        <h3>{{ $blog->title }}</h3>

                        <p>
                            {{ \Illuminate\Support\Str::limit($blog->excerpt ?: strip_tags($blog->content), 125) }}
                        </p>

                        <a href="{{ route('blogs.show', $blog->slug) }}" class="btn btn-primary">
                            Read More
                        </a>
                    </div>
                </article>
            @empty
                <div class="card" style="grid-column:1/-1;text-align:center;">
                    <div class="card-body">
                        <h3>No Blogs Found</h3>
                        <p>Blogs will appear here once published by admin.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div style="margin-top:30px;">
            {{ $blogs->links() }}
        </div>
    </div>
</section>

@endsection