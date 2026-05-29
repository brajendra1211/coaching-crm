@php
    $setting = $siteSetting ?? \App\Models\CoachingSetting::current();
    $portalRole = $portalRole ?? auth()->user()->role ?? 'student';
    $portalTitle = ucfirst($portalRole) . ' Portal';
    $userName = auth()->user()->name ?? ucfirst($portalRole);
    $instituteName = $setting?->institute_name ?: 'Edu Institute';
    $portalLogo = $setting?->logo ? asset('storage/' . $setting->logo) : null;
    $primaryColor = $setting?->primary_color ?: '#2563eb';
    $secondaryColor = $setting?->secondary_color ?: '#7c3aed';
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', $portalTitle)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --primary:{{ $primaryColor }};
            --secondary:{{ $secondaryColor }};
            --dark:#0f172a;
            --muted:#64748b;
            --border:#e5e7eb;
            --bg:#f5f7fb;
            --white:#fff;
            --green:#16a34a;
            --red:#dc2626;
            --shadow:0 12px 35px rgba(15,23,42,.08);
        }
        * { box-sizing:border-box; }
        body { margin:0; background:var(--bg); color:#111827; font-family:Arial,sans-serif; }
        a { color:inherit; text-decoration:none; }
        .portal-shell { min-height:100vh; display:flex; }
        .portal-sidebar { width:270px; position:fixed; inset:0 auto 0 0; background:linear-gradient(180deg,#08111f,#0f172a); color:#fff; padding:20px 14px; overflow:auto; }
        .brand { display:flex; gap:12px; align-items:center; padding:8px 8px 20px; border-bottom:1px solid rgba(255,255,255,.10); margin-bottom:16px; }
        .brand-icon { width:46px; height:46px; border-radius:16px; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,var(--primary),var(--secondary)); font-weight:900; }
        .brand-icon img { width:100%; height:100%; object-fit:contain; background:#fff; border-radius:14px; padding:4px; }
        .brand strong { display:block; font-size:18px; }
        .brand span { display:block; margin-top:4px; color:#bfdbfe; font-size:12px; font-weight:800; }
        .nav-title { padding:12px 10px 8px; color:#94a3b8; text-transform:uppercase; font-size:11px; font-weight:900; letter-spacing:.7px; }
        .nav-link, .logout-btn { width:100%; display:flex; justify-content:space-between; align-items:center; border:0; background:transparent; color:#cbd5e1; padding:12px; border-radius:14px; font-size:14px; font-weight:900; cursor:pointer; margin-bottom:6px; }
        .nav-link.active, .nav-link:hover, .logout-btn:hover { background:linear-gradient(135deg,var(--primary),var(--secondary)); color:#fff; }
        .portal-main { margin-left:270px; width:calc(100% - 270px); padding:22px; }
        .topbar { background:rgba(255,255,255,.94); border:1px solid var(--border); border-radius:22px; padding:16px 18px; box-shadow:var(--shadow); display:flex; justify-content:space-between; gap:14px; align-items:center; margin-bottom:22px; }
        .topbar h1 { margin:0; font-size:23px; color:#0f172a; }
        .topbar p { margin:4px 0 0; color:var(--muted); font-size:13px; font-weight:700; }
        .profile-chip { display:inline-flex; align-items:center; gap:9px; background:#fff; border:1px solid var(--border); border-radius:999px; padding:6px 12px 6px 6px; font-weight:900; }
        .avatar { width:32px; height:32px; border-radius:999px; background:linear-gradient(135deg,var(--primary),var(--secondary)); color:#fff; display:inline-flex; align-items:center; justify-content:center; }
        .card { background:#fff; border:1px solid var(--border); border-radius:22px; padding:20px; box-shadow:var(--shadow); }
        .grid-4 { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; margin-bottom:18px; }
        .grid-2 { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; margin-bottom:18px; }
        .stat small { display:block; color:var(--muted); font-size:13px; font-weight:900; }
        .stat strong { display:block; margin-top:8px; font-size:25px; color:#0f172a; }
        .card h3 { margin:0 0 14px; font-size:19px; color:#111827; }
        .table-wrap { overflow:auto; border:1px solid var(--border); border-radius:16px; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:13px; border-bottom:1px solid var(--border); text-align:left; font-size:14px; vertical-align:top; }
        th { background:#f8fafc; color:#475569; font-weight:900; white-space:nowrap; }
        .pill { display:inline-flex; padding:6px 10px; border-radius:999px; background:#eff6ff; color:#1d4ed8; font-size:12px; font-weight:900; }
        .pill.green { background:#dcfce7; color:#166534; }
        .pill.red { background:#fee2e2; color:#991b1b; }
        .pill.orange { background:#ffedd5; color:#9a3412; }
        .muted { color:var(--muted); }
        .stack { display:grid; gap:14px; }
        .info-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; }
        .info-item { border:1px solid var(--border); border-radius:16px; padding:13px; background:#f8fafc; }
        .info-item small { display:block; color:var(--muted); font-size:12px; font-weight:900; margin-bottom:5px; }
        .info-item strong { color:#0f172a; }
        .page-actions { display:flex; gap:10px; flex-wrap:wrap; align-items:center; justify-content:flex-end; margin-bottom:14px; }
        .btn { display:inline-flex; align-items:center; justify-content:center; padding:10px 13px; min-height:38px; border-radius:13px; border:1px solid transparent; font-size:14px; font-weight:900; cursor:pointer; }
        .btn-primary { background:linear-gradient(135deg,var(--primary),var(--secondary)); color:#fff; }
        .btn-light { background:#fff; color:#1d4ed8; border-color:#bfdbfe; }
        .pagination { display:flex; gap:8px; flex-wrap:wrap; padding:0; margin:16px 0 0; list-style:none; }
        .page-link { display:inline-flex; min-width:36px; height:36px; align-items:center; justify-content:center; border:1px solid var(--border); border-radius:12px; background:#fff; color:#1d4ed8; font-weight:900; padding:0 10px; }
        .page-item.active .page-link { background:linear-gradient(135deg,var(--primary),var(--secondary)); color:#fff; border-color:transparent; }
        .page-item.disabled .page-link { color:#94a3b8; background:#f8fafc; }
        @media(max-width:980px){ .portal-sidebar{position:static;width:100%;height:auto;} .portal-shell{display:block;} .portal-main{margin-left:0;width:100%;padding:14px;} .grid-4,.grid-2,.info-grid{grid-template-columns:1fr;} .topbar{align-items:flex-start;flex-direction:column;} .page-actions{justify-content:flex-start;} }
    </style>
</head>
<body>
<div class="portal-shell">
    <aside class="portal-sidebar">
        <a href="{{ route($portalRole . '.dashboard') }}" class="brand">
            <span class="brand-icon">
                @if($portalLogo)
                    <img src="{{ $portalLogo }}" alt="{{ $instituteName }}">
                @else
                    {{ strtoupper(substr($instituteName, 0, 1)) }}
                @endif
            </span>
            <span>
                <strong>{{ $instituteName }}</strong>
                <span>{{ $portalTitle }} | {{ $userName }}</span>
            </span>
        </a>

        <div class="nav-title">Workspace</div>
        <a href="{{ route($portalRole . '.dashboard') }}" class="nav-link {{ request()->routeIs($portalRole . '.dashboard') ? 'active' : '' }}">Dashboard <span>></span></a>
        @if($portalRole === 'student')
            <a href="{{ route('student.exams.index') }}" class="nav-link {{ request()->routeIs('student.exams.*') ? 'active' : '' }}">Exams <span>></span></a>
            <a href="{{ route('student.results.index') }}" class="nav-link {{ request()->routeIs('student.results.*') ? 'active' : '' }}">Results <span>></span></a>
            <a href="{{ route('student.fees.index') }}" class="nav-link {{ request()->routeIs('student.fees.*') ? 'active' : '' }}">Fees <span>></span></a>
            <a href="{{ route('student.attendance.index') }}" class="nav-link {{ request()->routeIs('student.attendance.*') ? 'active' : '' }}">Attendance <span>></span></a>
            <a href="{{ route('student.materials.index') }}" class="nav-link {{ request()->routeIs('student.materials.*') ? 'active' : '' }}">Materials <span>></span></a>
            <a href="{{ route('student.classes.index') }}" class="nav-link {{ request()->routeIs('student.classes.*') ? 'active' : '' }}">Online Classes <span>></span></a>
            <a href="{{ route('student.certificates.index') }}" class="nav-link {{ request()->routeIs('student.certificates.*') ? 'active' : '' }}">Certificates <span>></span></a>
            <a href="{{ route('student.profile.index') }}" class="nav-link {{ request()->routeIs('student.profile.*') ? 'active' : '' }}">Profile <span>></span></a>
        @elseif($portalRole === 'teacher')
            <a href="{{ url('/teacher/batches') }}" class="nav-link {{ request()->routeIs('teacher.batches.*') ? 'active' : '' }}">Batches <span>></span></a>
            <a href="{{ url('/teacher/students') }}" class="nav-link {{ request()->routeIs('teacher.students.*') ? 'active' : '' }}">Students <span>></span></a>
            <a href="{{ url('/teacher/exams') }}" class="nav-link {{ request()->routeIs('teacher.exams.*') ? 'active' : '' }}">Exams <span>></span></a>
            <a href="{{ url('/teacher/series') }}" class="nav-link {{ request()->routeIs('teacher.series.*') ? 'active' : '' }}">Exam Series <span>></span></a>
            <a href="{{ url('/teacher/questions') }}" class="nav-link {{ request()->routeIs('teacher.questions.*') ? 'active' : '' }}">Question Bank <span>></span></a>
            <a href="{{ url('/teacher/questions/import') }}" class="nav-link {{ request()->routeIs('teacher.questions.import') ? 'active' : '' }}">Import Questions <span>></span></a>
            <a href="{{ url('/teacher/materials') }}" class="nav-link {{ request()->routeIs('teacher.materials.*') ? 'active' : '' }}">Materials <span>></span></a>
            <a href="{{ url('/teacher/attendance') }}" class="nav-link {{ request()->routeIs('teacher.attendance.*') ? 'active' : '' }}">Attendance <span>></span></a>
            <a href="{{ url('/teacher/classes') }}" class="nav-link {{ request()->routeIs('teacher.classes.*') ? 'active' : '' }}">Online Classes <span>></span></a>
            <a href="{{ url('/teacher/profile') }}" class="nav-link {{ request()->routeIs('teacher.profile.*') ? 'active' : '' }}">Profile <span>></span></a>
        @else
            <a href="{{ route('staff.leads.index') }}" class="nav-link {{ request()->routeIs('staff.leads.*') ? 'active' : '' }}">Leads <span>></span></a>
            <a href="{{ route('staff.admissions.index') }}" class="nav-link {{ request()->routeIs('staff.admissions.*') ? 'active' : '' }}">Admissions <span>></span></a>
            <a href="{{ route('staff.students.index') }}" class="nav-link {{ request()->routeIs('staff.students.*') ? 'active' : '' }}">Students <span>></span></a>
            <a href="{{ route('staff.fees.index') }}" class="nav-link {{ request()->routeIs('staff.fees.*') ? 'active' : '' }}">Fees <span>></span></a>
            <a href="{{ route('staff.exams.index') }}" class="nav-link {{ request()->routeIs('staff.exams.*') ? 'active' : '' }}">Exams <span>></span></a>
            <a href="{{ route('staff.materials.index') }}" class="nav-link {{ request()->routeIs('staff.materials.*') ? 'active' : '' }}">Materials <span>></span></a>
            <a href="{{ route('staff.certificates.index') }}" class="nav-link {{ request()->routeIs('staff.certificates.*') ? 'active' : '' }}">Certificates <span>></span></a>
        @endif

        <div class="nav-title">Account</div>
        <form method="POST" action="{{ route($portalRole . '.logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Logout <span>></span></button>
        </form>
    </aside>

    <main class="portal-main">
        <header class="topbar">
            <div>
                <h1>@yield('page_title', $portalTitle)</h1>
                <p>{{ $instituteName }} | @yield('page_subtitle', 'Your learning workspace')</p>
            </div>
            <div class="profile-chip">
                <span class="avatar">{{ strtoupper(substr($userName, 0, 1)) }}</span>
                {{ $userName }}
            </div>
        </header>

        @yield('content')
    </main>
</div>
</body>
</html>
