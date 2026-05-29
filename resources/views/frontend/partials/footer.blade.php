@php
    $setting = $siteSetting ?? \App\Models\CoachingSetting::current();

    $rawPhone = preg_replace('/\D/', '', $setting->phone ?: '9999999999');
    $phone = $rawPhone ?: '9999999999';

    $whatsapp = preg_replace('/\D/', '', $setting->whatsapp ?: $phone);

    if ($whatsapp && !str_starts_with($whatsapp, '91')) {
        $whatsapp = '91' . $whatsapp;
    }

    $locations = isset($seoLocations) ? $seoLocations->take(8) : collect();

    $locationUrl = function ($location) {
        return \Illuminate\Support\Facades\Route::has('location.show')
            ? route('location.show', $location->slug)
            : url('/' . $location->slug);
    };

    $footerCourses = [
        ['label' => 'All Courses', 'url' => url('/courses'), 'icon' => 'grid'],
        ['label' => 'NEET', 'url' => url('/courses?category=neet'), 'icon' => 'target'],
        ['label' => 'IIT JEE', 'url' => url('/courses?category=iit-jee'), 'icon' => 'bolt'],
        ['label' => 'School', 'url' => url('/courses?category=school'), 'icon' => 'book'],
        ['label' => 'Govt Exams', 'url' => url('/courses?category=government-exam'), 'icon' => 'award'],
    ];

    $footerLinks = [
        ['label' => 'About', 'url' => url('/about'), 'icon' => 'info'],
        ['label' => 'Admission', 'url' => url('/admission'), 'icon' => 'plus'],
        ['label' => 'Results', 'url' => url('/results'), 'icon' => 'chart'],
        ['label' => 'Reviews', 'url' => url('/testimonials'), 'icon' => 'star'],
        ['label' => 'Gallery', 'url' => url('/gallery'), 'icon' => 'image'],
        ['label' => 'Blogs', 'url' => url('/blogs'), 'icon' => 'edit'],
        ['label' => 'Contact', 'url' => url('/contact'), 'icon' => 'mail'],
    ];

    $footerIcon = function ($name) {
        $icons = [
            'grid' => '<svg viewBox="0 0 24 24"><path d="M4 4h7v7H4V4Zm9 0h7v7h-7V4ZM4 13h7v7H4v-7Zm9 0h7v7h-7v-7Z"/></svg>',
            'target' => '<svg viewBox="0 0 24 24"><path d="M12 3a9 9 0 1 0 9 9h-2a7 7 0 1 1-7-7V3Zm0 5a4 4 0 1 0 4 4h-2a2 2 0 1 1-2-2V8Zm4.6-4.6v3h3L13 13l-1.4-1.4 6.6-6.6h-1.6V3.4Z"/></svg>',
            'bolt' => '<svg viewBox="0 0 24 24"><path d="M13 2 4 14h7l-1 8 10-13h-7l1-7Z"/></svg>',
            'book' => '<svg viewBox="0 0 24 24"><path d="M5 4.5A2.5 2.5 0 0 1 7.5 2H20v17H7.5A2.5 2.5 0 0 0 5 21.5v-17Zm2.5-.5a.5.5 0 0 0-.5.5v13.55c.16-.03.33-.05.5-.05H18V4H7.5Z"/></svg>',
            'award' => '<svg viewBox="0 0 24 24"><path d="M12 2a6 6 0 0 0-3.8 10.65L7 21l5-2.6 5 2.6-1.2-8.35A6 6 0 0 0 12 2Zm0 2a4 4 0 1 1 0 8 4 4 0 0 1 0-8Z"/></svg>',
            'info' => '<svg viewBox="0 0 24 24"><path d="M11 10h2v8h-2v-8Zm0-4h2v2h-2V6Zm1-4a10 10 0 1 0 0 20 10 10 0 0 0 0-20Z"/></svg>',
            'plus' => '<svg viewBox="0 0 24 24"><path d="M11 5h2v6h6v2h-6v6h-2v-6H5v-2h6V5Z"/></svg>',
            'chart' => '<svg viewBox="0 0 24 24"><path d="M4 19h16v2H2V3h2v16Zm3-2V9h3v8H7Zm5 0V5h3v12h-3Zm5 0v-6h3v6h-3Z"/></svg>',
            'star' => '<svg viewBox="0 0 24 24"><path d="m12 2 2.9 6 6.6.9-4.8 4.7 1.2 6.6L12 17.1l-5.9 3.1 1.2-6.6-4.8-4.7 6.6-.9L12 2Z"/></svg>',
            'image' => '<svg viewBox="0 0 24 24"><path d="M4 5h16v14H4V5Zm2 2v8.2l3.2-3.2 3 3 2.8-3.8 3 4V7H6Zm3 3.5A1.5 1.5 0 1 0 9 7a1.5 1.5 0 0 0 0 3.5Z"/></svg>',
            'edit' => '<svg viewBox="0 0 24 24"><path d="M4 17.5V20h2.5L18.1 8.4l-2.5-2.5L4 17.5ZM19.7 6.8a1 1 0 0 0 0-1.4l-1.1-1.1a1 1 0 0 0-1.4 0L16.3 5l2.7 2.7.7-.9Z"/></svg>',
            'mail' => '<svg viewBox="0 0 24 24"><path d="M3 5h18v14H3V5Zm2 3.2V17h14V8.2l-7 4.4-7-4.4ZM18 7H6l6 3.8L18 7Z"/></svg>',
            'map' => '<svg viewBox="0 0 24 24"><path d="M12 2a7 7 0 0 0-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z"/></svg>',
            'phone' => '<svg viewBox="0 0 24 24"><path d="M6.6 10.8a15.7 15.7 0 0 0 6.6 6.6l2.2-2.2c.3-.3.8-.4 1.2-.2 1.3.4 2.6.7 4 .7.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.9 21 3 13.1 3 3.4c0-.6.4-1 1-1h3.3c.6 0 1 .4 1 1 0 1.4.2 2.8.7 4 .1.4 0 .8-.2 1.1l-2.2 2.3Z"/></svg>',
            'whatsapp' => '<svg viewBox="0 0 24 24"><path d="M12 3a9 9 0 0 0-7.7 13.7L3 21l4.4-1.2A9 9 0 1 0 12 3Zm.1 2a7 7 0 1 1-3.6 13l-.4-.2-2.2.6.6-2.1-.2-.4A7 7 0 0 1 12.1 5Zm-3 3.8c-.2 0-.5.1-.7.3-.3.3-.8.8-.8 2s.8 2.4 1 2.6c.1.2 1.7 2.7 4.2 3.6 2 .8 2.4.6 2.8.6.5 0 1.4-.6 1.6-1.2.2-.5.2-1 .1-1.2-.1-.1-.2-.2-.5-.3l-1.6-.8c-.2-.1-.4-.1-.6.2l-.7.9c-.2.2-.3.2-.6.1a5.7 5.7 0 0 1-2-1.2 7.2 7.2 0 0 1-1.3-1.7c-.1-.2 0-.4.1-.5l.4-.4c.1-.1.2-.2.2-.4.1-.1.1-.3 0-.4l-.8-1.8c-.2-.4-.4-.4-.6-.4h-.2Z"/></svg>',
            'clock' => '<svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm1 5v4.6l3.4 2-.9 1.6L11 12.6V7h2Z"/></svg>',
        ];

        return $icons[$name] ?? $icons['grid'];
    };
@endphp

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-intro">
                <a href="{{ url('/') }}" class="brand" style="margin-bottom:16px;">
                    @if($setting->logo)
                        <span class="brand-logo-img">
                            <img src="{{ asset('storage/' . $setting->logo) }}" alt="{{ $setting->institute_name }}">
                        </span>
                    @else
                        <span class="brand-icon">ED</span>
                    @endif

                    <span class="brand-text">
                        <strong style="color:#fff;">{{ $setting->institute_name ?: 'Edu Institute' }}</strong>
                        <span style="color:#bfdbfe;">{{ $setting->tagline ?: 'Coaching | Exams | Results' }}</span>
                    </span>
                </a>

                <p>
                    {{ $setting->footer_description ?: 'Professional coaching for NEET, IIT JEE, board exams and competitive preparation with expert faculty, regular tests and structured learning support.' }}
                </p>

                <div class="footer-actions">
                    <a href="tel:+91{{ $phone }}" class="btn btn-primary">Call Now</a>
                    <a href="https://wa.me/{{ $whatsapp }}" target="_blank" class="btn btn-success">WhatsApp</a>
                </div>
            </div>

            <div>
                <h3>Courses</h3>
                <div class="footer-menu">
                    @foreach($footerCourses as $item)
                        <a href="{{ $item['url'] }}" class="footer-menu-btn">
                            <span class="footer-menu-icon">{!! $footerIcon($item['icon']) !!}</span>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div>
                <h3>Quick Links</h3>
                <div class="footer-menu footer-menu-compact">
                    @foreach($footerLinks as $item)
                        <a href="{{ $item['url'] }}" class="footer-menu-btn">
                            <span class="footer-menu-icon">{!! $footerIcon($item['icon']) !!}</span>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div>
                <h3>Contact</h3>
                <div class="footer-contact-list">
                    <div class="footer-contact-item">
                        <span class="footer-contact-icon">{!! $footerIcon('map') !!}</span>
                        <span>{{ $setting->address ?: 'Greater Noida, Noida Extension' }}</span>
                    </div>
                    <a href="tel:+91{{ $phone }}" class="footer-contact-item">
                        <span class="footer-contact-icon">{!! $footerIcon('phone') !!}</span>
                        <span>+91-{{ $phone }}</span>
                    </a>
                    <a href="https://wa.me/{{ $whatsapp }}" target="_blank" class="footer-contact-item">
                        <span class="footer-contact-icon">{!! $footerIcon('whatsapp') !!}</span>
                        <span>WhatsApp Chat</span>
                    </a>
                    <div class="footer-contact-item">
                        <span class="footer-contact-icon">{!! $footerIcon('mail') !!}</span>
                        <span>{{ $setting->email ?: 'info@coachingcrm.com' }}</span>
                    </div>
                    <div class="footer-contact-item">
                        <span class="footer-contact-icon">{!! $footerIcon('clock') !!}</span>
                        <span>Mon - Sat, 9:00 AM - 7:00 PM</span>
                    </div>
                </div>

                @if($setting->facebook_url || $setting->instagram_url || $setting->youtube_url || $setting->linkedin_url)
                    <div class="footer-social">
                        @if($setting->facebook_url)
                            <a href="{{ $setting->facebook_url }}" target="_blank" aria-label="Facebook">Fb</a>
                        @endif

                        @if($setting->instagram_url)
                            <a href="{{ $setting->instagram_url }}" target="_blank" aria-label="Instagram">In</a>
                        @endif

                        @if($setting->youtube_url)
                            <a href="{{ $setting->youtube_url }}" target="_blank" aria-label="YouTube">Yt</a>
                        @endif

                        @if($setting->linkedin_url)
                            <a href="{{ $setting->linkedin_url }}" target="_blank" aria-label="LinkedIn">Li</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @if($locations->count())
            <div class="footer-locations">
                <h3>Popular Locations</h3>

                <div class="footer-locations-grid">
                    @foreach($locations as $location)
                        <a href="{{ $locationUrl($location) }}">
                            {{ $location->page_title }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="footer-bottom">
            <div>&copy; {{ date('Y') }} {{ $setting->institute_name ?: 'Edu Institute' }}. All Rights Reserved.</div>
            <div>
                Developed by
                <a href="https://urgentitsolution.com" target="_blank" rel="noopener">Urgent IT Solution</a>
            </div>
        </div>
    </div>
</footer>

<div class="floating-actions">
    <a href="https://wa.me/{{ $whatsapp }}" target="_blank" class="float-btn float-whatsapp" aria-label="Chat on WhatsApp">
        <span class="float-icon">
            <svg viewBox="0 0 32 32" aria-hidden="true">
                <path d="M16.04 3.2A12.73 12.73 0 0 0 5.18 22.6L3.6 28.4l5.95-1.56A12.74 12.74 0 1 0 16.04 3.2Zm0 2.31a10.43 10.43 0 0 1 8.88 15.9 10.4 10.4 0 0 1-13.98 3.08l-.43-.26-3.53.93.94-3.43-.28-.45A10.42 10.42 0 0 1 16.04 5.51Zm-4.3 5.54c-.23 0-.6.09-.92.44-.32.35-1.21 1.18-1.21 2.88s1.24 3.34 1.41 3.57c.18.23 2.39 3.82 5.92 5.2 2.93 1.15 3.54.92 4.18.86.64-.06 2.07-.85 2.36-1.67.29-.82.29-1.52.2-1.67-.09-.15-.32-.23-.67-.41-.35-.17-2.07-1.02-2.39-1.14-.32-.12-.56-.17-.79.18-.23.35-.91 1.14-1.11 1.37-.2.23-.41.26-.76.09-.35-.17-1.48-.55-2.83-1.74-1.04-.93-1.75-2.08-1.96-2.43-.2-.35-.02-.54.15-.71.16-.16.35-.41.53-.61.17-.2.23-.35.35-.58.12-.23.06-.44-.03-.61-.09-.17-.79-1.9-1.08-2.6-.28-.68-.57-.58-.79-.59h-.68Z"/>
            </svg>
        </span>
        <span>WhatsApp</span>
    </a>
    <a href="tel:+91{{ $phone }}" class="float-btn float-call">Call Now</a>
</div>

<nav class="mobile-appbar">
    <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">
        <span class="mobile-tab-icon">{!! $footerIcon('grid') !!}</span>
        <span class="mobile-tab-label">Home</span>
    </a>

    <a href="{{ url('/courses') }}" class="{{ request()->is('courses*') ? 'active' : '' }}">
        <span class="mobile-tab-icon">{!! $footerIcon('book') !!}</span>
        <span class="mobile-tab-label">Courses</span>
    </a>

    <a href="tel:+91{{ $phone }}" class="main-action">
        <span class="mobile-tab-icon">{!! $footerIcon('phone') !!}</span>
        <span class="mobile-tab-label">Call</span>
    </a>

    <a href="{{ url('/admission') }}">
        <span class="mobile-tab-icon">{!! $footerIcon('plus') !!}</span>
        <span class="mobile-tab-label">Admission</span>
    </a>

    <a href="https://wa.me/{{ $whatsapp }}" target="_blank">
        <span class="mobile-tab-icon">{!! $footerIcon('whatsapp') !!}</span>
        <span class="mobile-tab-label">Chat</span>
    </a>
</nav>
