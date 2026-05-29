@php
    use Illuminate\Support\Facades\Schema;

    $setting = $siteSetting ?? \App\Models\CoachingSetting::current();

    $rawPhone = preg_replace('/\D/', '', $setting->phone ?: '9999999999');
    $phone = $rawPhone ?: '9999999999';

    $whatsapp = preg_replace('/\D/', '', $setting->whatsapp ?: $phone);

    if ($whatsapp && !str_starts_with($whatsapp, '91')) {
        $whatsapp = '91' . $whatsapp;
    }

    $locations = isset($seoLocations) ? $seoLocations->take(8) : collect();
    $navCourses = collect();

    try {
        if (class_exists(\App\Models\Course::class) && Schema::hasTable('courses')) {
            $navCourses = \App\Models\Course::where('status', 'active')
                ->orderBy('title')
                ->take(10)
                ->get();
        }
    } catch (\Throwable $e) {
        $navCourses = collect();
    }

    $locationUrl = function ($location) {
        return \Illuminate\Support\Facades\Route::has('location.show')
            ? route('location.show', $location->slug)
            : url('/' . $location->slug);
    };
@endphp

<div class="top-strip">
    <div class="container top-strip-flex">
        <div class="top-strip-left">
            Admissions Open for NEET, IIT JEE, Board Exams & Competitive Courses
        </div>

        <div class="top-strip-right">
            <a href="tel:+91{{ $phone }}">Call: +91-{{ $phone }}</a>
            <span>|</span>
            <a href="https://wa.me/{{ $whatsapp }}" target="_blank">WhatsApp Enquiry</a>
        </div>
    </div>
</div>

<header class="site-header">
    <div class="container header-inner">
        <a href="{{ url('/') }}" class="brand">
            @if($setting->logo)
                <span class="brand-logo-img">
                    <img src="{{ asset('storage/' . $setting->logo) }}" alt="{{ $setting->institute_name }}">
                </span>
            @else
                <span class="brand-icon">ED</span>
            @endif

            <span class="brand-text">
                <strong>{{ $setting->institute_name ?: 'Edu Institute' }}</strong>
                <span>{{ $setting->tagline ?: 'Coaching | Exams | Results' }}</span>
            </span>
        </a>

        <nav class="desktop-nav">
            <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a>

            <div class="dropdown">
                <button type="button" class="dropdown-btn">
                    <span>Courses</span>
                    <small>&#9662;</small>
                </button>

                <div class="dropdown-panel">
                    <a href="{{ url('/courses') }}">All Courses</a>
                    @foreach($navCourses as $course)
                        <a href="{{ route('courses.show', $course->slug) }}">
                            {{ $course->title }}
                        </a>
                    @endforeach
                </div>
            </div>

            <a href="{{ url('/admission') }}" class="{{ request()->is('admission') ? 'active' : '' }}">Admission</a>

            <div class="dropdown">
                <button type="button" class="dropdown-btn">
                    <span>Login</span>
                    <small>&#9662;</small>
                </button>

                <div class="dropdown-panel dropdown-panel-sm">
                    <a href="{{ route('student.login') }}">Student Login</a>
                    <a href="{{ route('teacher.login') }}">Teacher Login</a>
                    <a href="{{ route('staff.login') }}">Staff Login</a>
                    <a href="{{ route('admin.login') }}">Admin Login</a>
                </div>
            </div>

            <div class="dropdown">
                <button type="button" class="dropdown-btn">
                    <span>More</span>
                    <small>&#9662;</small>
                </button>

                <div class="dropdown-panel">
                    <a href="{{ url('/about') }}">About Institute</a>
                    <a href="{{ url('/results') }}">Results</a>
                    <a href="{{ url('/testimonials') }}">Student Reviews</a>
                    <a href="{{ url('/gallery') }}">Gallery</a>
                    <a href="{{ url('/blogs') }}">Blogs</a>
                    <a href="{{ url('/contact') }}">Contact</a>
                    @foreach($locations as $location)
                        <a href="{{ $locationUrl($location) }}">
                            {{ $location->page_title }}
                        </a>
                    @endforeach
                </div>
            </div>
        </nav>

        <div class="header-actions">
            <a href="{{ url('/contact') }}" class="btn btn-primary">Free Counselling</a>
        </div>

        <button type="button" class="mobile-toggle" id="openMobileMenu" aria-label="Open Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<div class="mobile-overlay" id="mobileOverlay"></div>

<aside class="mobile-drawer" id="mobileDrawer">
    <div class="drawer-head">
        <a href="{{ url('/') }}" class="brand">
            @if($setting->logo)
                <span class="brand-logo-img">
                    <img src="{{ asset('storage/' . $setting->logo) }}" alt="{{ $setting->institute_name }}">
                </span>
            @else
                <span class="brand-icon">ED</span>
            @endif

            <span class="brand-text">
                <strong>{{ $setting->institute_name ?: 'Edu Institute' }}</strong>
                <span>{{ $setting->tagline ?: 'Coaching | Exams' }}</span>
            </span>
        </a>

        <button type="button" class="drawer-close" id="closeMobileMenu">&times;</button>
    </div>

    <div class="drawer-menu">
        <a href="{{ url('/') }}" class="drawer-link {{ request()->is('/') ? 'active' : '' }}">
            Home <span>&rsaquo;</span>
        </a>

        <div class="drawer-dropdown">
            <button type="button" class="drawer-dropdown-btn">Courses <span>&#9662;</span></button>

            <div class="drawer-dropdown-list">
                <a href="{{ url('/courses') }}">All Courses</a>
                @foreach($navCourses as $course)
                    <a href="{{ route('courses.show', $course->slug) }}">
                        {{ $course->title }}
                    </a>
                @endforeach
            </div>
        </div>

        <a href="{{ url('/admission') }}" class="drawer-link">
            Admission <span>&rsaquo;</span>
        </a>

        <div class="drawer-dropdown">
            <button type="button" class="drawer-dropdown-btn">Login <span>&#9662;</span></button>

            <div class="drawer-dropdown-list">
                <a href="{{ route('student.login') }}">Student Login</a>
                <a href="{{ route('teacher.login') }}">Teacher Login</a>
                <a href="{{ route('staff.login') }}">Staff Login</a>
                <a href="{{ route('admin.login') }}">Admin Login</a>
            </div>
        </div>

        <div class="drawer-dropdown">
            <button type="button" class="drawer-dropdown-btn">More <span>&#9662;</span></button>

            <div class="drawer-dropdown-list">
                <a href="{{ url('/about') }}">About Institute</a>
                <a href="{{ url('/results') }}">Results</a>
                <a href="{{ url('/testimonials') }}">Student Reviews</a>
                <a href="{{ url('/gallery') }}">Gallery</a>
                <a href="{{ url('/blogs') }}">Blogs</a>
                <a href="{{ url('/contact') }}">Contact</a>
                @foreach($locations as $location)
                    <a href="{{ $locationUrl($location) }}">
                        {{ $location->page_title }}
                    </a>
                @endforeach
            </div>
        </div>

        <a href="tel:+91{{ $phone }}" class="btn btn-dark">Call Now</a>
        <a href="https://wa.me/{{ $whatsapp }}" target="_blank" class="btn btn-success">WhatsApp Enquiry</a>
    </div>
</aside>
