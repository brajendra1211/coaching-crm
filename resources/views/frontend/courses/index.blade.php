@extends('frontend.layouts.app')

@php
    $setting = $siteSetting ?? \App\Models\CoachingSetting::current();
    $instituteName = $setting->institute_name ?: 'Edu Institute';
    $pageTitle = $page?->seo_title ?: ('Courses | ' . $instituteName);
    $pageDescription = $page?->seo_description ?: 'Explore active coaching courses created by admin for NEET, IIT JEE, Board Exams, SSC and competitive exam preparation.';
    $canonicalUrl = route('courses.index');
    $logoImage = $setting->logo ? asset('storage/' . $setting->logo) : null;
    $phoneDigits = preg_replace('/\D+/', '', (string) ($setting->whatsapp ?: $setting->phone ?: '9999999999'));
    $whatsappNumber = strlen($phoneDigits) === 10 ? '91' . $phoneDigits : $phoneDigits;
@endphp

@section('title', $pageTitle)
@section('meta_description', $pageDescription)
@section('meta_keywords', $page?->seo_keywords ?: 'coaching courses, NEET coaching, IIT JEE coaching, board exam coaching')
@section('canonical', $canonicalUrl)
@section('og_title', $pageTitle)
@section('og_description', $pageDescription)
@section('og_image', $logoImage)
@section('robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')

@section('content')

<section class="page-header">
    <div class="container">
        <h1>{{ $page?->hero_title ?: 'Our Courses' }}</h1>
        <p>{{ $page?->hero_subtitle ?: 'Choose the right coaching course for your academic and competitive exam preparation.' }}</p>
    </div>
</section>

<section class="section section-light">
    <div class="container">
        <div class="grid-3">
            @forelse($courses as $course)
                <div class="card">
                    @if($course->image)
                        <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" loading="lazy">
                    @endif

                    <div class="card-body">
                        @if($course->course_type)
                            <span class="badge">{{ $course->course_type }}</span>
                        @endif

                        <h3>{{ $course->title }}</h3>

                        <p>
                            {{ \Illuminate\Support\Str::limit(strip_tags($course->short_description ?: $course->description), 120) }}
                        </p>

                        <div>
                            @if($course->class_level)
                                <span class="badge">{{ $course->class_level }}</span>
                            @endif

                            @if($course->duration)
                                <span class="badge">{{ $course->duration }}</span>
                            @endif
                        </div>

                        <div style="margin-top:14px;">
                            @if($course->offer_fee)
                                <p class="price">
                                    Rs {{ number_format($course->offer_fee, 0) }}

                                    @if($course->fee)
                                        <span class="old-price">Rs {{ number_format($course->fee, 0) }}</span>
                                    @endif
                                </p>
                            @elseif($course->fee)
                                <p class="price">Rs {{ number_format($course->fee, 0) }}</p>
                            @else
                                <p class="price">Fee on Request</p>
                            @endif
                        </div>

                        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:18px;">
                            <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-primary">View Course</a>

                            <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('Hi, I want to know about ' . $course->title) }}"
                               target="_blank"
                               class="btn btn-dark">
                                Enquire
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card" style="grid-column:1/-1;">
                    <div class="card-body" style="text-align:center;">
                        <h3>No Courses Found</h3>
                        <p>Please add active courses from admin panel.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div style="margin-top:30px;">
            {{ $courses->links() }}
        </div>

        @if($page?->content)
            <div class="content" style="margin-top:34px;">
                {!! $page->content !!}
            </div>
        @endif
    </div>
</section>

@endsection
