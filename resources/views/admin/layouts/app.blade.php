@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Str;

    try {
        $adminSetting = \App\Models\CoachingSetting::current();
    } catch (\Throwable $e) {
        $adminSetting = null;
    }

    $appName = $adminSetting->institute_name ?? 'EduCRM Pro';
    $tagline = $adminSetting->tagline ?? 'Coaching SaaS Admin';

    $logoUrl = !empty($adminSetting?->logo) ? asset('storage/' . $adminSetting->logo) : null;
    $faviconUrl = !empty($adminSetting?->favicon) ? asset('storage/' . $adminSetting->favicon) : null;

    $phone = $adminSetting->phone ?? null;
    $whatsapp = $adminSetting->whatsapp ?? null;
    $email = $adminSetting->email ?? null;
    $address = $adminSetting->address ?? null;

    $adminName = session('admin_name')
        ?? session('admin_email')
        ?? optional(auth()->user())->name
        ?? 'Admin';

    $routeUrl = function ($routeName, $fallbackPath = '/', $params = []) {
        if ($routeName && Route::has($routeName)) {
            return route($routeName, $params);
        }

        return url($fallbackPath);
    };

    $isActive = function ($routePatterns = [], $pathPatterns = []) {
        foreach ((array) $routePatterns as $pattern) {
            if ($pattern && request()->routeIs($pattern)) {
                return true;
            }
        }

        foreach ((array) $pathPatterns as $pattern) {
            $pattern = ltrim((string) $pattern, '/');

            if ($pattern && request()->is($pattern)) {
                return true;
            }
        }

        return false;
    };

    $menuGroups = [
        [
            'title' => 'Overview',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'icon' => '📊',
                    'route' => 'admin.dashboard',
                    'path' => '/admin/dashboard',
                    'active' => ['admin.dashboard'],
                    'path_active' => ['admin/dashboard'],
                    'single' => true,
                ],
            ],
        ],

        [
            'title' => 'Website CMS',
            'group_label' => 'Website CMS',
            'group_icon' => '🌐',
            'items' => [
                ['label' => 'Website Settings', 'route' => 'admin.settings.index', 'path' => '/admin/settings', 'active' => ['admin.settings.*'], 'path_active' => ['admin/settings*']],
                ['label' => 'Courses', 'route' => 'admin.courses.index', 'path' => '/admin/courses', 'active' => ['admin.courses.*'], 'path_active' => ['admin/courses*']],
                ['label' => 'SEO Locations', 'route' => 'admin.locations.index', 'path' => '/admin/locations', 'active' => ['admin.locations.*'], 'path_active' => ['admin/locations*']],
                ['label' => 'Pages', 'route' => 'admin.pages.index', 'path' => '/admin/pages', 'active' => ['admin.pages.*'], 'path_active' => ['admin/pages*']],
                ['label' => 'Blogs', 'route' => 'admin.blogs.index', 'path' => '/admin/blogs', 'active' => ['admin.blogs.*'], 'path_active' => ['admin/blogs*']],
                ['label' => 'Gallery', 'route' => 'admin.gallery.index', 'path' => '/admin/gallery', 'active' => ['admin.gallery.*'], 'path_active' => ['admin/gallery*']],
                ['label' => 'Testimonials', 'route' => 'admin.testimonials.index', 'path' => '/admin/testimonials', 'active' => ['admin.testimonials.*'], 'path_active' => ['admin/testimonials*']],
                ['label' => 'SEO Manager', 'route' => 'admin.seo.index', 'path' => '/admin/seo', 'active' => ['admin.seo.*'], 'path_active' => ['admin/seo*']],
            ],
        ],

        [
            'title' => 'Admissions CRM',
            'group_label' => 'Admissions CRM',
            'group_icon' => '🎯',
            'items' => [
                ['label' => 'Leads & Follow-ups', 'route' => 'admin.leads.index', 'path' => '/admin/leads', 'active' => ['admin.leads.*'], 'path_active' => ['admin/leads*']],
                ['label' => 'Admissions', 'route' => 'admin.admissions.index', 'path' => '/admin/admissions', 'active' => ['admin.admissions.*'], 'path_active' => ['admin/admissions*']],
                ['label' => 'Students', 'route' => 'admin.students.index', 'path' => '/admin/students', 'active' => ['admin.students.*'], 'path_active' => ['admin/students*']],
                ['label' => 'Parents', 'route' => 'admin.parents.index', 'path' => '/admin/parents', 'active' => ['admin.parents.*'], 'path_active' => ['admin/parents*']],
            ],
        ],

        [
            'title' => 'Academics',
            'group_label' => 'Academics',
            'group_icon' => '🏫',
            'items' => [
                ['label' => 'Teachers', 'route' => 'admin.teachers.index', 'path' => '/admin/teachers', 'active' => ['admin.teachers.*'], 'path_active' => ['admin/teachers*']],
                ['label' => 'Subjects', 'route' => 'admin.subjects.index', 'path' => '/admin/subjects', 'active' => ['admin.subjects.*'], 'path_active' => ['admin/subjects*']],
                ['label' => 'Batches', 'route' => 'admin.batches.index', 'path' => '/admin/batches', 'active' => ['admin.batches.*'], 'path_active' => ['admin/batches*']],
                ['label' => 'Attendance', 'route' => 'admin.attendance.index', 'path' => '/admin/attendance', 'active' => ['admin.attendance.*'], 'path_active' => ['admin/attendance*']],
                ['label' => 'Online Classes', 'route' => 'admin.online-classes.index', 'path' => '/admin/online-classes', 'active' => ['admin.online-classes.*'], 'path_active' => ['admin/online-classes*']],
                ['label' => 'Study Materials', 'route' => 'admin.study-materials.index', 'path' => '/admin/study-materials', 'active' => ['admin.study-materials.*'], 'path_active' => ['admin/study-materials*']],
            ],
        ],

        [
            'title' => 'Exams & Results',
            'group_label' => 'Exams & Results',
            'group_icon' => '📝',
            'items' => [
                ['label' => 'Question Bank', 'route' => 'admin.questions.index', 'path' => '/admin/questions', 'active' => ['admin.questions.*'], 'path_active' => ['admin/questions*']],
                ['label' => 'Online Exams', 'route' => 'admin.exams.index', 'path' => '/admin/exams', 'active' => ['admin.exams.*'], 'path_active' => ['admin/exams*']],
                ['label' => 'Exam Series', 'route' => 'admin.exam-series.index', 'path' => '/admin/exam-series', 'active' => ['admin.exam-series.*'], 'path_active' => ['admin/exam-series*']],
                ['label' => 'Results', 'route' => 'admin.results.index', 'path' => '/admin/results', 'active' => ['admin.results.*'], 'path_active' => ['admin/results*']],
                ['label' => 'Marksheets', 'route' => 'admin.marksheets.index', 'path' => '/admin/marksheets', 'active' => ['admin.marksheets.*'], 'path_active' => ['admin/marksheets*']],
            ],
        ],

        [
            'title' => 'Fees & Finance',
            'group_label' => 'Fees & Finance',
            'group_icon' => '💰',
            'items' => [
                ['label' => 'Batch Fee Plans', 'route' => 'admin.batch-fee-plans.index', 'path' => '/admin/batch-fee-plans', 'active' => ['admin.batch-fee-plans.*'], 'path_active' => ['admin/batch-fee-plans*']],
                ['label' => 'Fee Collections', 'route' => 'admin.fee-collections.index', 'path' => '/admin/fee-collections', 'active' => ['admin.fee-collections.*'], 'path_active' => ['admin/fee-collections*']],
                ['label' => 'Fee Plans', 'route' => 'admin.fees.index', 'path' => '/admin/fees', 'active' => ['admin.fees.*'], 'path_active' => ['admin/fees*']],
                ['label' => 'Invoices', 'route' => 'admin.invoices.index', 'path' => '/admin/invoices', 'active' => ['admin.invoices.*'], 'path_active' => ['admin/invoices*']],
                ['label' => 'Payments', 'route' => 'admin.payments.index', 'path' => '/admin/payments', 'active' => ['admin.payments.*'], 'path_active' => ['admin/payments*']],
                ['label' => 'Reports', 'route' => 'admin.fee-reports.index', 'path' => '/admin/fee-reports', 'active' => ['admin.fee-reports.*'], 'path_active' => ['admin/fee-reports*']],
                ['label' => 'Invoice Settings', 'route' => 'admin.invoice-settings.edit', 'path' => '/admin/invoice-settings', 'active' => ['admin.invoice-settings.*'], 'path_active' => ['admin/invoice-settings*']],
            ],
        ],

        [
            'title' => 'Documents',
            'group_label' => 'Documents',
            'group_icon' => '🏆',
            'items' => [
                ['label' => 'ID Cards', 'route' => 'admin.id-cards.index', 'path' => '/admin/id-cards', 'active' => ['admin.id-cards.*'], 'path_active' => ['admin/id-cards*']],
                ['label' => 'Certificates', 'route' => 'admin.certificates.index', 'path' => '/admin/certificates', 'active' => ['admin.certificates.*'], 'path_active' => ['admin/certificates*']],
            ],
        ],

        [
            'title' => 'System',
            'group_label' => 'Settings',
            'group_icon' => '⚙️',
            'items' => [
                ['label' => 'Institute Settings', 'route' => 'admin.settings.index', 'path' => '/admin/settings', 'active' => ['admin.settings.*'], 'path_active' => ['admin/settings*']],
                ['label' => 'Custom Domains', 'route' => 'admin.domains.index', 'path' => '/admin/domains', 'active' => ['admin.domains.*'], 'path_active' => ['admin/domains*']],
                ['label' => 'Users', 'route' => 'admin.users.index', 'path' => '/admin/users', 'active' => ['admin.users.*'], 'path_active' => ['admin/users*']],
                ['label' => 'Roles', 'route' => 'admin.roles.index', 'path' => '/admin/roles', 'active' => ['admin.roles.*'], 'path_active' => ['admin/roles*']],
            ],
        ],
    ];
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Panel') | {{ $appName }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @if($faviconUrl)
        <link rel="icon" href="{{ $faviconUrl }}">
    @endif

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #7c3aed;
            --accent: #06b6d4;
            --dark: #0f172a;
            --sidebar: #08111f;
            --sidebar-2: #0f172a;
            --text: #111827;
            --muted: #64748b;
            --border: #e5e7eb;
            --bg: #f5f7fb;
            --white: #ffffff;
            --green: #16a34a;
            --red: #dc2626;
            --orange: #f59e0b;
            --shadow: 0 12px 35px rgba(15, 23, 42, .08);
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            background:
                radial-gradient(circle at top right, rgba(37,99,235,.08), transparent 30%),
                var(--bg);
            font-family: Arial, sans-serif;
            color: var(--text);
        }

        body.sidebar-open { overflow: hidden; }

        a { text-decoration: none; color: inherit; }

        button, input, textarea, select { font-family: inherit; }

        .admin-shell {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 286px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            background:
                radial-gradient(circle at top left, rgba(37,99,235,.28), transparent 32%),
                linear-gradient(180deg, var(--sidebar), var(--sidebar-2));
            color: #fff;
            padding: 18px 14px;
            overflow-y: auto;
            transition: .28s ease;
            box-shadow: 14px 0 35px rgba(15,23,42,.18);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 8px 18px;
            border-bottom: 1px solid rgba(255,255,255,.10);
            margin-bottom: 16px;
        }

        .brand-icon,
        .brand-logo {
            width: 48px;
            height: 48px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 14px 30px rgba(37,99,235,.35);
        }

        .brand-icon {
            background: linear-gradient(135deg, #2563eb, #7c3aed, #06b6d4);
            font-size: 22px;
            font-weight: 900;
            color: #fff;
        }

        .brand-logo {
            background: #fff;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,.22);
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 6px;
        }

        .brand-text {
            min-width: 0;
        }

        .brand-text strong {
            display: block;
            font-size: 19px;
            line-height: 1.1;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .brand-text span {
            display: block;
            color: #bfdbfe;
            font-size: 12px;
            margin-top: 4px;
            font-weight: 800;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-badge {
            margin: 0 8px 16px;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.10);
            border-radius: 18px;
            padding: 13px;
        }

        .sidebar-badge strong {
            display: block;
            color: #fff;
            font-size: 13px;
            margin-bottom: 4px;
        }

        .sidebar-badge span,
        .sidebar-badge a {
            display: block;
            color: #cbd5e1;
            font-size: 12px;
            line-height: 1.55;
            word-break: break-word;
        }

        .sidebar-badge a:hover {
            color: #fff;
        }

        .menu-title {
            padding: 13px 11px 8px;
            color: #94a3b8;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .8px;
        }

        .nav-item,
        .nav-group-btn {
            width: 100%;
            min-height: 44px;
            border: 0;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            color: #cbd5e1;
            padding: 11px 12px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 850;
            cursor: pointer;
            transition: .2s ease;
            margin-bottom: 5px;
        }

        .nav-item-left,
        .nav-group-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .nav-icon {
            width: 25px;
            height: 25px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,.08);
            flex-shrink: 0;
            font-size: 14px;
        }

        .nav-item:hover,
        .nav-group-btn:hover,
        .nav-item.active,
        .nav-group.active > .nav-group-btn {
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            color: #fff;
            box-shadow: 0 10px 24px rgba(37,99,235,.22);
        }

        .nav-arrow {
            transition: .2s ease;
            color: #94a3b8;
            font-weight: 900;
        }

        .nav-group.open .nav-arrow,
        .nav-group.active .nav-arrow {
            transform: rotate(90deg);
            color: #fff;
        }

        .nav-submenu {
            display: none;
            margin: 4px 0 10px 34px;
            padding-left: 12px;
            border-left: 1px solid rgba(255,255,255,.10);
        }

        .nav-group.open .nav-submenu,
        .nav-group.active .nav-submenu {
            display: block;
        }

        .nav-submenu a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #b6c2d4;
            padding: 9px 10px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 4px;
            transition: .2s ease;
        }

        .nav-submenu a:hover,
        .nav-submenu a.active {
            color: #fff;
            background: rgba(255,255,255,.09);
        }

        .sidebar-footer {
            margin: 18px 8px 6px;
            padding: 14px;
            border-radius: 18px;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.10);
        }

        .sidebar-footer p {
            margin: 0 0 12px;
            color: #cbd5e1;
            font-size: 12px;
            line-height: 1.5;
        }

        .main {
            margin-left: 286px;
            width: calc(100% - 286px);
            min-height: 100vh;
            padding: 22px;
        }

        .topbar {
            background: rgba(255,255,255,.94);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(229,231,235,.85);
            border-radius: 24px;
            padding: 16px 18px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 22px;
            position: sticky;
            top: 14px;
            z-index: 40;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 13px;
            min-width: 0;
        }

        .mobile-toggle {
            display: none;
            width: 44px;
            height: 44px;
            border: 0;
            border-radius: 15px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            color: #fff;
            font-size: 22px;
            font-weight: 900;
            cursor: pointer;
            box-shadow: 0 10px 22px rgba(37,99,235,.22);
            flex-shrink: 0;
        }

        .page-heading h2 {
            margin: 0;
            color: #0f172a;
            font-size: 23px;
            letter-spacing: -.3px;
        }

        .page-heading p {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .top-chip {
            min-height: 38px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 12px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid #e5e7eb;
            color: #334155;
            font-size: 13px;
            font-weight: 900;
            box-shadow: 0 8px 20px rgba(15,23,42,.04);
            white-space: nowrap;
        }

        .admin-profile {
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 5px 12px 5px 5px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 8px 20px rgba(15,23,42,.04);
            white-space: nowrap;
        }

        .admin-avatar {
            width: 31px;
            height: 31px;
            border-radius: 999px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
        }

        .admin-profile strong {
            display: block;
            color: #111827;
            font-size: 13px;
            line-height: 1.1;
        }

        .admin-profile small {
            display: block;
            color: #64748b;
            font-size: 11px;
            margin-top: 2px;
        }

        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            padding: 22px;
            box-shadow: var(--shadow);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .card-header h3 {
            margin: 0;
            color: #111827;
            font-size: 21px;
        }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        .table th,
        .table td {
            padding: 14px 13px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: top;
        }

        .table th {
            background: #f8fafc;
            color: #475569;
            font-size: 13px;
            font-weight: 900;
            white-space: nowrap;
        }

        .table td {
            color: #334155;
            font-size: 14px;
        }

        input, select, textarea {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            padding: 12px 13px;
            font-size: 14px;
            outline: none;
            background: #fff;
            color: #111827;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37,99,235,.10);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-height: 39px;
            padding: 10px 14px;
            border-radius: 13px;
            border: 1px solid transparent;
            cursor: pointer;
            font-size: 14px;
            font-weight: 900;
            transition: .2s ease;
            white-space: nowrap;
        }

        .btn:hover { transform: translateY(-1px); }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            color: #fff;
            box-shadow: 0 12px 24px rgba(37,99,235,.18);
        }

        .btn-dark { background: #0f172a; color: #fff; }

        .btn-light {
            background: #fff;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }

        .btn-danger { background: #dc2626; color: #fff; }
        .btn-success { background: #16a34a; color: #fff; }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15,23,42,.55);
            z-index: 950;
        }

        @media(max-width: 980px) {
            .sidebar {
                transform: translateX(-105%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-overlay.show {
                display: block;
            }

            .main {
                margin-left: 0;
                width: 100%;
                padding: 14px;
            }

            .mobile-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .topbar {
                top: 10px;
                border-radius: 20px;
            }

            .top-chip {
                display: none;
            }
        }

        @media(max-width: 620px) {
            .page-heading h2 { font-size: 19px; }
            .page-heading p { display: none; }

            .admin-profile strong,
            .admin-profile small {
                display: none;
            }

            .admin-profile {
                padding-right: 5px;
            }

            .card {
                padding: 16px;
                border-radius: 19px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="admin-shell">
    <aside class="sidebar" id="adminSidebar">
        <a href="{{ $routeUrl('admin.dashboard', '/admin/dashboard') }}" class="brand">
            @if($logoUrl)
                <span class="brand-logo">
                    <img src="{{ $logoUrl }}" alt="{{ $appName }}">
                </span>
            @else
                <span class="brand-icon">
                    {{ strtoupper(mb_substr($appName, 0, 1)) }}
                </span>
            @endif

            <span class="brand-text">
                <strong>{{ $appName }}</strong>
                <span>{{ $tagline }}</span>
            </span>
        </a>

        <!-- <div class="sidebar-badge">
            <strong>Institute Control Center</strong>

            @if($phone)
                <a href="tel:{{ $phone }}">Phone: {{ $phone }}</a>
            @endif

            @if($whatsapp)
                <a href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsapp) }}" target="_blank">WhatsApp: {{ $whatsapp }}</a>
            @endif

            @if($email)
                <a href="mailto:{{ $email }}">Email: {{ $email }}</a>
            @endif

            @if($address)
                <span>{{ Str::limit($address, 85) }}</span>
            @else
                <span>Website, admissions, academics, fees, exams and certificates.</span>
            @endif
        </div> -->

        @foreach($menuGroups as $group)
            @php
                $groupItems = $group['items'] ?? [];
                $hasOnlySingle = count($groupItems) === 1 && !empty($groupItems[0]['single']);
                $groupActive = false;

                foreach ($groupItems as $groupItem) {
                    if ($isActive($groupItem['active'] ?? [], $groupItem['path_active'] ?? [])) {
                        $groupActive = true;
                        break;
                    }
                }
            @endphp

            <div class="menu-title">{{ $group['title'] }}</div>

            @if($hasOnlySingle)
                @php
                    $item = $groupItems[0];
                    $itemActive = $isActive($item['active'] ?? [], $item['path_active'] ?? []);
                @endphp

                <a href="{{ $routeUrl($item['route'] ?? null, $item['path'] ?? '/admin/dashboard') }}"
                   class="nav-item {{ $itemActive ? 'active' : '' }}">
                    <span class="nav-item-left">
                        <span class="nav-icon">{{ $item['icon'] ?? '•' }}</span>
                        {{ $item['label'] }}
                    </span>
                    <span>›</span>
                </a>
            @else
                <div class="nav-group {{ $groupActive ? 'active open' : '' }}">
                    <button type="button" class="nav-group-btn">
                        <span class="nav-group-left">
                            <span class="nav-icon">{{ $group['group_icon'] ?? '📁' }}</span>
                            {{ $group['group_label'] ?? $group['title'] }}
                        </span>
                        <span class="nav-arrow">›</span>
                    </button>

                    <div class="nav-submenu">
                        @foreach($groupItems as $item)
                            @php
                                $itemActive = $isActive($item['active'] ?? [], $item['path_active'] ?? []);
                            @endphp

                            <a href="{{ $routeUrl($item['route'] ?? null, $item['path'] ?? '/admin/dashboard') }}"
                               class="{{ $itemActive ? 'active' : '' }}">
                                {{ $item['label'] }} <span>›</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

        <div class="menu-title">Account</div>

        <form method="POST" action="{{ $routeUrl('admin.logout', '/admin/logout') }}">
            @csrf
            <button type="submit" class="nav-item">
                <span class="nav-item-left">
                    <span class="nav-icon">🚪</span>
                    Logout
                </span>
                <span>›</span>
            </button>
        </form>

        <div class="sidebar-footer">
            <p>Frontend website and SEO pages can be previewed live from here.</p>
            <a href="{{ url('/') }}" target="_blank" class="btn btn-primary" style="width:100%;">🌐 View Website</a>
        </div>
    </aside>

    <main class="main">
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="mobile-toggle" id="openSidebar">☰</button>

                <div class="page-heading">
                    <h2>@yield('page_title', 'Dashboard')</h2>
                    <p>{{ $appName }} / Admin Management Console</p>
                </div>
            </div>

            <div class="topbar-right">
                @if($phone)
                    <a href="tel:{{ $phone }}" class="top-chip">📞 {{ $phone }}</a>
                @endif

                @if($email)
                    <a href="mailto:{{ $email }}" class="top-chip">✉️ {{ Str::limit($email, 24) }}</a>
                @endif

                <div class="admin-profile">
                    <span class="admin-avatar">{{ strtoupper(mb_substr($adminName, 0, 1)) }}</span>
                    <span>
                        <strong>{{ Str::limit($adminName, 18) }}</strong>
                        <small>Administrator</small>
                    </span>
                </div>
            </div>
        </header>

        @yield('content')
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const openBtn = document.getElementById('openSidebar');

        function openSidebar() {
            sidebar?.classList.add('show');
            overlay?.classList.add('show');
            document.body.classList.add('sidebar-open');
        }

        function closeSidebar() {
            sidebar?.classList.remove('show');
            overlay?.classList.remove('show');
            document.body.classList.remove('sidebar-open');
        }

        openBtn?.addEventListener('click', openSidebar);
        overlay?.addEventListener('click', closeSidebar);

        document.querySelectorAll('.nav-group-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                const group = button.closest('.nav-group');

                document.querySelectorAll('.nav-group').forEach(function (item) {
                    if (item !== group && !item.classList.contains('active')) {
                        item.classList.remove('open');
                    }
                });

                group?.classList.toggle('open');
            });
        });

        document.querySelectorAll('.sidebar a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 980) {
                    closeSidebar();
                }
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        });
    });
</script>

@stack('scripts')

</body>
</html>
