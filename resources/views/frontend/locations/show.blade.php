@extends('frontend.layouts.app')

@section('title', $page->seo_title ?: $page->page_title)
@section('meta_description', $page->seo_description ?: \Illuminate\Support\Str::limit(strip_tags($page->short_description ?: $page->content), 155))
@section('meta_keywords', $page->seo_keywords ?: $page->focus_keyword . ', ' . $page->location_name)

@section('content')

<section class="page-header">
    <div class="container">
        <h1>{{ $page->page_title }}</h1>
        <p>{{ $page->short_description ?: 'Explore professional coaching classes near ' . $page->location_name . '.' }}</p>
    </div>
</section>

<section class="section section-light">
    <div class="container" style="display:grid;grid-template-columns:minmax(0,1fr) 380px;gap:28px;align-items:start;">
        <div>
            <div class="card">
                <div class="card-body">
                    <span class="badge">{{ $page->location_name }}</span>

                    @if($page->focus_keyword)
                        <span class="badge">{{ $page->focus_keyword }}</span>
                    @endif

                    <h2>{{ $page->page_title }}</h2>

                    <div class="content">
                        {!! nl2br(e($page->content ?: $page->short_description)) !!}
                    </div>
                </div>
            </div>

            <div class="section-title" style="text-align:left;margin:36px 0 22px;">
                <h2>Popular Courses Near {{ $page->location_name }}</h2>
                <p>Choose from our available coaching courses and book free counselling.</p>
            </div>

            <div class="grid-3">
                @forelse($courses as $course)
                    <div class="card">
                        @if($course->image)
                            <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}">
                        @endif

                        <div class="card-body">
                            <h3>{{ $course->title }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit($course->short_description ?: $course->description, 90) }}</p>
                            <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-primary">View Course</a>
                        </div>
                    </div>
                @empty
                    <div class="card" style="grid-column:1/-1;">
                        <div class="card-body">
                            <p>No active courses found.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <aside style="position:sticky;top:105px;">
            <div class="form-box">
                <h2 style="margin-top:0;">{{ $page->cta_title ?: 'Book Free Counselling' }}</h2>

                <p>
                    {{ $page->cta_description ?: 'Submit your enquiry and our counsellor will contact you shortly.' }}
                </p>

                @include('frontend.partials.lead-form', [
                    'courses' => $courses,
                    'source' => 'location_page_' . $page->slug
                ])
            </div>
        </aside>
    </div>
</section>

@endsection