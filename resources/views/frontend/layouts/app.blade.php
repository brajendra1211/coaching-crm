@php
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Str;

    $layoutSetting = $siteSetting ?? \App\Models\CoachingSetting::current();

    $seoMeta = null;

    try {
        if (
            class_exists(\App\Models\SeoMeta::class)
            && Schema::hasTable('seo_metas')
            && method_exists(\App\Models\SeoMeta::class, 'current')
        ) {
            $seoMeta = \App\Models\SeoMeta::current();
        }
    } catch (\Throwable $e) {
        $seoMeta = null;
    }

    $siteName = $layoutSetting->institute_name ?? config('app.name', 'Best Coaching Institute');

    $defaultTitle = $layoutSetting->default_seo_title ?: 'Best Coaching Institute';

    $defaultDescription = $layoutSetting->default_seo_description
        ?: 'Professional coaching institute for NEET, IIT JEE, Board Exams and competitive preparation with expert faculty and structured learning.';

    $defaultKeywords = $layoutSetting->default_seo_keywords
        ?: 'coaching institute, NEET coaching, IIT JEE coaching, board exam coaching';

    $sectionTitle = trim($__env->yieldContent('title'));
    $sectionDescription = trim($__env->yieldContent('meta_description'));
    $sectionKeywords = trim($__env->yieldContent('meta_keywords'));
    $sectionCanonical = trim($__env->yieldContent('canonical'));
    $sectionRobots = trim($__env->yieldContent('robots'));
    $sectionOgTitle = trim($__env->yieldContent('og_title'));
    $sectionOgDescription = trim($__env->yieldContent('og_description'));
    $sectionOgImage = trim($__env->yieldContent('og_image'));

    /*
    |--------------------------------------------------------------------------
    | SEO Priority
    |--------------------------------------------------------------------------
    | 1. SEO Manager current URL record
    | 2. Page wise @section SEO
    | 3. Institute default SEO setting
    */

    $seoTitle = $seoMeta?->meta_title ?: ($sectionTitle ?: $defaultTitle);

    $seoDescription = $seoMeta?->meta_description ?: ($sectionDescription ?: $defaultDescription);
    $seoDescription = Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $seoDescription))), 158, '');

    $seoKeywords = $seoMeta?->meta_keywords ?: ($sectionKeywords ?: $defaultKeywords);

    $seoCanonical = $seoMeta?->canonical_url ?: ($sectionCanonical ?: url()->current());

    $seoRobots = $seoMeta?->robots ?: ($sectionRobots ?: 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1');

    $seoOgTitle = $seoMeta?->og_title ?: ($sectionOgTitle ?: $seoTitle);

    $seoOgDescription = $seoMeta?->og_description ?: ($sectionOgDescription ?: $seoDescription);

    $logoUrl = !empty($layoutSetting->logo)
        ? asset('storage/' . $layoutSetting->logo)
        : null;

    $seoOgImage = $sectionOgImage ?: (!empty($seoMeta?->og_image)
        ? asset('storage/' . $seoMeta->og_image)
        : $logoUrl);

    $seoTwitterTitle = $seoMeta?->twitter_title ?: $seoOgTitle;
    $seoTwitterDescription = $seoMeta?->twitter_description ?: $seoOgDescription;

    $seoTwitterImage = $sectionOgImage ?: (!empty($seoMeta?->twitter_image)
        ? asset('storage/' . $seoMeta->twitter_image)
        : $seoOgImage);

    $themePrimary = $layoutSetting->primary_color ?: '#2563eb';
    $themeSecondary = $layoutSetting->secondary_color ?: '#7c3aed';
    $themeAccent = $layoutSetting->accent_color ?: '#16a34a';

    $fontOptions = [
        'Arial' => 'Arial, sans-serif',
        'Inter' => 'Inter, Arial, sans-serif',
        'Poppins' => 'Poppins, Arial, sans-serif',
        'Roboto' => 'Roboto, Arial, sans-serif',
        'Nunito' => 'Nunito, Arial, sans-serif',
        'Lato' => 'Lato, Arial, sans-serif',
        'Merriweather' => 'Merriweather, Georgia, serif',
    ];

    $themeFont = $fontOptions[$layoutSetting->font_family ?: 'Arial'] ?? $fontOptions['Arial'];
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">

    @if($seoKeywords)
        <meta name="keywords" content="{{ $seoKeywords }}">
    @endif

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="{{ $seoRobots }}">
    <link rel="canonical" href="{{ $seoCanonical }}">

    @if($layoutSetting->favicon)
        <link rel="icon" href="{{ asset('storage/' . $layoutSetting->favicon) }}">
    @endif

    <meta property="og:locale" content="en_IN">
    <meta property="og:title" content="{{ $seoOgTitle }}">
    <meta property="og:description" content="{{ $seoOgDescription }}">
    <meta property="og:url" content="{{ $seoCanonical }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">

    @if($seoOgImage)
        <meta property="og:image" content="{{ $seoOgImage }}">
        <meta property="og:image:secure_url" content="{{ $seoOgImage }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="{{ $seoOgTitle }}">
    @endif

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTwitterTitle }}">
    <meta name="twitter:description" content="{{ $seoTwitterDescription }}">

    @if($seoTwitterImage)
        <meta name="twitter:image" content="{{ $seoTwitterImage }}">
    @endif

    @if($seoMeta && !empty($seoMeta->schema_json))
        <script type="application/ld+json">
            {!! $seoMeta->schema_json !!}
        </script>
    @endif

    @stack('head')

    <style>
        :root {
            --primary: {{ $themePrimary }};
            --primary-dark: {{ $themePrimary }};
            --secondary: {{ $themeSecondary }};
            --dark: #0f172a;
            --heading: #111827;
            --text: #475569;
            --muted: #64748b;
            --light: #f8fafc;
            --soft-blue: #eff6ff;
            --white: #ffffff;
            --border: #e5e7eb;
            --green: {{ $themeAccent }};
            --red: #dc2626;
            --orange: #f59e0b;
            --shadow: 0 16px 42px rgba(15, 23, 42, .09);
            --shadow-soft: 0 10px 28px rgba(15, 23, 42, .06);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: {{ $themeFont }};
            color: var(--text);
            background: #fff;
        }

        body.menu-open {
            overflow: hidden;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        img {
            max-width: 100%;
        }

        .container {
            width: min(1180px, 92%);
            margin: auto;
        }

        .top-strip {
            background: linear-gradient(90deg, #0f172a, var(--primary), var(--secondary));
            color: #fff;
            padding: 8px 0;
            font-size: 13px;
        }

        .top-strip-flex {
            min-height: 34px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: nowrap;
        }

        .top-strip-left,
        .top-strip-right {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
            line-height: 1;
        }

        .top-strip a {
            color: #fff;
            font-weight: 900;
        }

        .top-strip-right a {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 999;
            background: rgba(255, 255, 255, .97);
            border-bottom: 1px solid var(--border);
            box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
        }

        .header-inner {
            min-height: 78px;
            display: grid;
            grid-template-columns: 270px minmax(0, 1fr) 190px;
            align-items: center;
            gap: 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
            min-width: 0;
            flex-shrink: 0;
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            flex: 0 0 48px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            font-weight: 900;
            box-shadow: 0 12px 28px rgba(37, 99, 235, .24);
        }

        .brand-logo-img {
            width: 48px;
            height: 48px;
            flex: 0 0 48px;
            border-radius: 16px;
            background: #fff;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
        }

        .brand-logo-img img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 5px;
        }

        .brand-text {
            min-width: 0;
        }

        .brand-text strong {
            display: block;
            color: var(--heading);
            font-size: 23px;
            line-height: 1.05;
            font-weight: 900;
            white-space: nowrap;
        }

        .brand-text span {
            display: block;
            color: var(--primary);
            font-size: 11px;
            line-height: 1.2;
            font-weight: 900;
            letter-spacing: .8px;
            margin-top: 3px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .desktop-nav {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
            min-width: 0;
            white-space: nowrap;
        }

        .desktop-nav > a,
        .dropdown-btn {
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 0;
            border: 0;
            background: transparent;
            color: #334155;
            font-size: 15px;
            line-height: 1;
            font-weight: 900;
            cursor: pointer;
            font-family: inherit;
            white-space: nowrap;
        }

        .desktop-nav > a:hover,
        .desktop-nav > a.active,
        .dropdown:hover .dropdown-btn {
            color: var(--primary);
        }

        .dropdown {
            position: relative;
        }

        .dropdown-btn small {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            line-height: 1;
            margin-top: 2px;
        }

        .dropdown-panel {
            position: absolute;
            top: calc(100% + 2px);
            left: 0;
            width: 310px;
            max-height: 420px;
            overflow-y: auto;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 10px;
            box-shadow: var(--shadow);
            display: none;
            z-index: 1005;
        }

        .dropdown:hover .dropdown-panel {
            display: block;
        }

        .dropdown-panel a {
            display: block;
            padding: 11px 12px;
            border-radius: 12px;
            color: #334155;
            font-size: 14px;
            font-weight: 800;
            line-height: 1.4;
            white-space: normal;
        }

        .dropdown-panel a:hover {
            background: var(--soft-blue);
            color: var(--primary);
        }

        .dropdown-panel-sm {
            width: 220px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            min-width: 0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 11px 17px;
            border-radius: 999px;
            border: 0;
            cursor: pointer;
            font-size: 15px;
            font-weight: 900;
            font-family: inherit;
            white-space: nowrap;
            transition: .22s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff;
            box-shadow: 0 12px 24px rgba(37, 99, 235, .22);
        }

        .btn-dark {
            background: var(--dark);
            color: #fff;
        }

        .btn-light {
            background: #fff;
            color: var(--primary);
            border: 1px solid #bfdbfe;
        }

        .btn-success {
            background: var(--green);
            color: #fff;
        }

        .btn-danger {
            background: var(--red);
            color: #fff;
        }

        .header-actions .btn {
            height: 46px;
            padding: 0 18px;
        }

        .mobile-toggle {
            display: none;
            width: 46px;
            height: 44px;
            border: 0;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff;
            cursor: pointer;
            flex-direction: column;
            gap: 5px;
            box-shadow: 0 12px 24px rgba(15, 23, 42, .18);
        }

        .mobile-toggle span {
            display: block;
            width: 21px;
            height: 2px;
            border-radius: 999px;
            background: #fff;
            box-shadow: 0 1px 0 rgba(255, 255, 255, .22);
        }

        .mobile-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .58);
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: .25s ease;
        }

        .mobile-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }

        .mobile-drawer {
            position: fixed;
            top: 0;
            right: -100%;
            width: min(88%, 390px);
            height: 100vh;
            background: #fff;
            z-index: 1001;
            padding: 22px;
            padding-bottom: 95px;
            overflow-y: auto;
            box-shadow: -25px 0 60px rgba(15, 23, 42, .25);
            transition: .3s ease;
        }

        .mobile-drawer.show {
            right: 0;
        }

        .drawer-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 16px;
        }

        .drawer-close {
            width: 42px;
            height: 42px;
            border: 0;
            border-radius: 14px;
            background: var(--soft-blue);
            color: var(--primary);
            font-size: 26px;
            font-weight: 900;
            cursor: pointer;
        }

        .drawer-menu {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .drawer-link,
        .drawer-dropdown-btn {
            width: 100%;
            min-height: 49px;
            border: 0;
            border-radius: 15px;
            background: #f8fafc;
            color: #334155;
            padding: 13px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 15px;
            font-weight: 900;
            cursor: pointer;
            font-family: inherit;
        }

        .drawer-link.active {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff;
        }

        .drawer-dropdown-list {
            display: none;
            padding: 8px;
            border: 1px solid var(--border);
            border-radius: 15px;
            background: #fff;
            margin-top: -4px;
            margin-bottom: 5px;
            max-height: 300px;
            overflow-y: auto;
        }

        .drawer-dropdown.open .drawer-dropdown-list {
            display: block;
        }

        .drawer-dropdown-list a {
            display: block;
            padding: 11px 12px;
            border-radius: 12px;
            color: #334155;
            font-weight: 800;
            font-size: 14px;
            line-height: 1.4;
        }

        .drawer-dropdown-list a:hover {
            background: var(--soft-blue);
            color: var(--primary);
        }

        .section {
            padding: 64px 0;
        }

        .section-light {
            background: linear-gradient(180deg, #f8fbff, #ffffff);
        }

        .section-title {
            max-width: 760px;
            margin: 0 auto 34px;
            text-align: center;
        }

        .section-title h2 {
            margin: 0 0 10px;
            color: var(--heading);
            font-size: 36px;
            line-height: 1.18;
        }

        .section-title p {
            margin: 0;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.65;
        }

        .hero {
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, .18), transparent 30%),
                linear-gradient(135deg, #eff6ff, #ffffff);
            padding: 80px 0;
            overflow: hidden;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.12fr .88fr;
            gap: 42px;
            align-items: center;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid #bfdbfe;
            color: var(--primary);
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 16px;
        }

        .hero h1 {
            margin: 0 0 18px;
            color: var(--heading);
            font-size: 52px;
            line-height: 1.06;
            letter-spacing: -1.1px;
        }

        .hero h1 span {
            color: var(--primary);
        }

        .hero p {
            margin: 0;
            color: var(--text);
            font-size: 17px;
            line-height: 1.75;
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 26px;
        }

        .hero-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 30px;
            padding: 26px;
            box-shadow: var(--shadow);
        }

        .hero-card h3 {
            margin: 0 0 8px;
            color: var(--heading);
            font-size: 24px;
        }

        .hero-card p {
            font-size: 15px;
            color: var(--muted);
        }

        .quick-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
            margin-top: 22px;
        }

        .quick-stat {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 16px;
        }

        .quick-stat strong {
            display: block;
            color: var(--primary);
            font-size: 26px;
            font-weight: 900;
            margin-bottom: 4px;
        }

        .quick-stat span {
            color: var(--muted);
            font-size: 13px;
            font-weight: 800;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
            transition: .22s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow);
        }

        .card img {
            width: 100%;
            height: 210px;
            object-fit: cover;
            background: #f1f5f9;
        }

        .card-body {
            padding: 22px;
        }

        .card h3 {
            margin: 0 0 10px;
            color: var(--heading);
            font-size: 21px;
        }

        .card p {
            color: var(--muted);
            line-height: 1.65;
        }

        .page-header {
            background: linear-gradient(135deg, #0f172a, var(--primary), var(--secondary));
            color: #fff;
            padding: 64px 0;
            text-align: center;
        }

        .page-header h1 {
            margin: 0 0 12px;
            font-size: 42px;
            line-height: 1.16;
        }

        .page-header p {
            margin: 0 auto;
            max-width: 760px;
            font-size: 17px;
            opacity: .92;
            line-height: 1.7;
        }

        .form-box {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 24px;
            box-shadow: var(--shadow);
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            color: #334155;
            font-weight: 900;
            margin-bottom: 7px;
            font-size: 14px;
        }

        input,
        select,
        textarea {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 13px;
            padding: 13px 14px;
            font-size: 15px;
            outline: none;
            background: #fff;
            color: var(--heading);
            font-family: inherit;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .10);
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 12px;
            border-radius: 999px;
            background: var(--soft-blue);
            color: var(--primary);
            border: 1px solid #bfdbfe;
            font-size: 12px;
            font-weight: 900;
            margin: 3px;
        }

        .price {
            color: var(--primary);
            font-size: 21px;
            font-weight: 900;
        }

        .old-price {
            color: #94a3b8;
            text-decoration: line-through;
            margin-left: 7px;
            font-size: 14px;
        }

        .content {
            color: var(--text);
            font-size: 16px;
            line-height: 1.8;
        }

        .footer {
            background: linear-gradient(135deg, #0f172a, #111827);
            color: #cbd5e1;
            padding: 40px 0 20px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.55fr .95fr 1.05fr 1.2fr;
            gap: 22px;
            align-items: start;
        }

        .footer h3 {
            color: #fff;
            margin: 0 0 12px;
            font-size: 13px;
            line-height: 1;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #bfdbfe;
        }

        .footer p {
            line-height: 1.65;
            margin: 0 0 8px;
            font-size: 13px;
        }

        .footer a {
            color: #e5e7eb;
            font-weight: 800;
        }

        .footer a:hover {
            color: #fff;
        }

        .footer-intro .brand {
            margin-bottom: 12px !important;
        }

        .footer-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 14px;
        }

        .footer-actions .btn {
            height: 38px;
            padding: 0 13px;
            font-size: 12px;
        }

        .footer-menu {
            display: grid;
            gap: 8px;
        }

        .footer-menu-compact {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .footer-menu-btn,
        .footer-contact-item {
            min-height: 38px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 9px;
            border-radius: 12px;
            background: rgba(255, 255, 255, .055);
            border: 1px solid rgba(255, 255, 255, .10);
            color: #e5e7eb;
            font-size: 12px;
            line-height: 1.25;
            font-weight: 850;
            transition: .2s ease;
        }

        .footer-menu-btn:hover,
        .footer-contact-item:hover {
            transform: translateY(-1px);
            background: rgba(255, 255, 255, .11);
            border-color: rgba(255, 255, 255, .20);
        }

        .footer-menu-icon,
        .footer-contact-icon {
            width: 24px;
            height: 24px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 24px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff;
            font-size: 11px;
            font-weight: 950;
            box-shadow: 0 8px 18px rgba(15, 23, 42, .18);
        }

        .footer-menu-icon svg,
        .footer-contact-icon svg {
            width: 14px;
            height: 14px;
            fill: currentColor;
            display: block;
        }

        .footer-contact-list {
            display: grid;
            gap: 8px;
        }

        .footer-contact-item {
            width: 100%;
            color: #dbeafe;
            overflow-wrap: anywhere;
        }

        .footer-locations {
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid rgba(255,255,255,.10);
        }

        .footer-locations-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .footer-locations-grid a {
            display: block;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.10);
            border-radius: 999px;
            padding: 8px 11px;
            font-size: 12px;
            line-height: 1.35;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,.10);
            margin-top: 22px;
            padding-top: 15px;
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 14px;
            color: #94a3b8;
            font-size: 14px;
        }

        .footer-bottom > div:first-child {
            justify-self: start;
        }

        .footer-bottom > div:last-child {
            justify-self: center;
            grid-column: 2;
            text-align: center;
        }

        .footer-social {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 16px;
        }

        .footer-social a {
            width: 38px;
            height: 38px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.14);
            color: #fff;
            font-weight: 900;
            font-size: 14px;
        }

        .footer-social a:hover {
            background: #fff;
            color: var(--primary);
        }

        .floating-actions {
            position: fixed;
            right: 18px;
            bottom: 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 998;
        }

        .float-btn {
            min-height: 50px;
            padding: 10px 16px 10px 11px;
            border-radius: 999px;
            color: #fff;
            font-weight: 900;
            box-shadow: 0 12px 28px rgba(0,0,0,.22);
            display: inline-flex;
            align-items: center;
            gap: 9px;
            position: relative;
            overflow: visible;
        }

        .float-call {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
        }

        .float-whatsapp {
            background: linear-gradient(135deg, #16a34a, #22c55e);
            box-shadow: 0 16px 34px rgba(22, 163, 74, .36);
            animation: whatsappLift 2.6s ease-in-out infinite;
        }

        .float-whatsapp::before {
            content: "";
            position: absolute;
            inset: -7px;
            border-radius: 999px;
            border: 2px solid rgba(34, 197, 94, .32);
            animation: whatsappPulse 1.8s ease-out infinite;
        }

        .float-icon {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            background: rgba(255,255,255,.18);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 34px;
        }

        .float-icon svg {
            width: 21px;
            height: 21px;
            fill: currentColor;
            display: block;
        }

        @keyframes whatsappPulse {
            0% {
                transform: scale(.92);
                opacity: .9;
            }

            100% {
                transform: scale(1.18);
                opacity: 0;
            }
        }

        @keyframes whatsappLift {
            0%, 100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-3px);
            }
        }

        .mobile-appbar {
            display: none;
        }

        @media(max-width: 1240px) {
            .header-inner {
                grid-template-columns: 250px minmax(0, 1fr) 175px;
                gap: 16px;
            }

            .desktop-nav {
                gap: 18px;
            }

            .header-actions .btn {
                padding: 0 15px;
                font-size: 14px;
            }

            .brand-text strong {
                font-size: 21px;
            }

            .brand-text span {
                font-size: 10px;
            }
        }

        @media(max-width: 1120px) {
            .header-inner {
                grid-template-columns: 235px minmax(0, 1fr) 170px;
            }

            .desktop-nav {
                gap: 14px;
            }

            .desktop-nav > a,
            .dropdown-btn {
                font-size: 14px;
            }

            .header-actions .btn-light {
                display: none;
            }

            .grid-4 {
                grid-template-columns: repeat(2, 1fr);
            }

            .footer-locations-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width: 980px) {
            body {
                padding-bottom: 66px;
            }

            .top-strip {
                display: none;
            }

            .header-inner {
                min-height: 70px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .desktop-nav,
            .header-actions {
                display: none;
            }

            .mobile-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .brand-icon,
            .brand-logo-img {
                width: 42px;
                height: 42px;
                flex-basis: 42px;
                font-size: 22px;
            }

            .brand-text strong {
                font-size: 19px;
            }

            .brand-text span {
                font-size: 10px;
            }

            .hero,
            .section {
                padding: 50px 0;
            }

            .hero-grid,
            .grid-2,
            .grid-3,
            .grid-4,
            .footer-grid,
            .footer-locations-grid {
                grid-template-columns: 1fr !important;
            }

            .hero h1 {
                font-size: 35px;
            }

            .hero p {
                font-size: 16px;
            }

            .section-title h2 {
                font-size: 30px;
            }

            .page-header {
                padding: 50px 0;
            }

            .page-header h1 {
                font-size: 32px;
            }

            .floating-actions {
                display: none;
            }

            .footer {
                padding-bottom: 92px;
            }

            .mobile-appbar {
                position: fixed;
                left: 0;
                right: 0;
                bottom: 0;
                height: 66px;
                background: #fff;
                display: grid;
                grid-template-columns: repeat(5, 1fr);
                z-index: 9999;
                border-top: 1px solid var(--border);
                box-shadow: 0 -12px 30px rgba(15,23,42,.14);
            }

            .mobile-appbar a {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 4px;
                color: #334155;
                font-weight: 900;
                min-width: 0;
            }

            .mobile-tab-icon {
                width: 22px;
                height: 22px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: currentColor;
            }

            .mobile-tab-icon svg {
                width: 18px;
                height: 18px;
                fill: currentColor;
                display: block;
            }

            .mobile-tab-label {
                display: block;
                max-width: 100%;
                font-size: 10px;
                line-height: 1;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .mobile-appbar a.active {
                color: var(--primary);
                background: var(--soft-blue);
            }

            .mobile-appbar .main-action {
                background: linear-gradient(135deg, var(--primary), var(--secondary));
                color: #fff;
                margin: -10px 6px 7px;
                border-radius: 16px;
                box-shadow: 0 12px 26px color-mix(in srgb, var(--primary) 28%, transparent);
            }

            .mobile-appbar .main-action .mobile-tab-icon {
                width: 24px;
                height: 24px;
            }

            .mobile-appbar .main-action .mobile-tab-label {
                font-size: 10px;
            }
        }

        @media(max-width: 540px) {
            .container {
                width: 92%;
            }

            .brand-text strong {
                font-size: 17px;
            }

            .brand-text span {
                display: none;
            }

            .hero h1 {
                font-size: 30px;
            }

            .section-title h2 {
                font-size: 27px;
            }

            .quick-stats {
                grid-template-columns: 1fr;
            }

            .btn {
                width: 100%;
            }

            .drawer-menu .btn {
                width: 100%;
            }

            .footer-bottom {
                grid-template-columns: 1fr;
                text-align: center;
                justify-content: center;
            }

            .footer-bottom > div:first-child,
            .footer-bottom > div:last-child {
                justify-self: center;
                grid-column: auto;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

@include('frontend.partials.header')

@yield('content')

@include('frontend.partials.footer')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const body = document.body;
        const openBtn = document.getElementById('openMobileMenu');
        const closeBtn = document.getElementById('closeMobileMenu');
        const drawer = document.getElementById('mobileDrawer');
        const overlay = document.getElementById('mobileOverlay');

        function openMenu() {
            if (!drawer || !overlay) return;
            drawer.classList.add('show');
            overlay.classList.add('show');
            body.classList.add('menu-open');
        }

        function closeMenu() {
            if (!drawer || !overlay) return;
            drawer.classList.remove('show');
            overlay.classList.remove('show');
            body.classList.remove('menu-open');

            document.querySelectorAll('.drawer-dropdown').forEach(function (item) {
                item.classList.remove('open');
            });
        }

        if (openBtn && closeBtn && drawer && overlay) {
            openBtn.addEventListener('click', openMenu);
            closeBtn.addEventListener('click', closeMenu);
            overlay.addEventListener('click', closeMenu);

            drawer.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', closeMenu);
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeMenu();
                }
            });
        }

        document.querySelectorAll('.drawer-dropdown-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                const parent = button.closest('.drawer-dropdown');

                document.querySelectorAll('.drawer-dropdown').forEach(function (item) {
                    if (item !== parent) {
                        item.classList.remove('open');
                    }
                });

                parent.classList.toggle('open');
            });
        });
    });
</script>

@stack('scripts')

</body>
</html>
