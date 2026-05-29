@php
    $adminName = auth()->user()->name ?? 'Super Admin';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Super Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root { --primary:#2563eb; --secondary:#7c3aed; --dark:#0f172a; --muted:#64748b; --border:#e5e7eb; --bg:#f5f7fb; --shadow:0 12px 35px rgba(15,23,42,.08); }
        *{box-sizing:border-box;} body{margin:0;background:var(--bg);font-family:Arial,sans-serif;color:#111827;} a{text-decoration:none;color:inherit;}
        .shell{display:flex;min-height:100vh;} .sidebar{width:286px;position:fixed;inset:0 auto 0 0;background:linear-gradient(180deg,#060d19,#0f172a);color:#fff;padding:18px 14px;overflow:auto;}
        .brand{display:flex;gap:12px;align-items:center;padding:8px 8px 18px;border-bottom:1px solid rgba(255,255,255,.10);margin-bottom:16px;}
        .brand-icon{width:48px;height:48px;border-radius:18px;background:linear-gradient(135deg,var(--primary),var(--secondary));display:flex;align-items:center;justify-content:center;font-weight:900;}
        .brand strong{display:block;font-size:19px}.brand span span{display:block;color:#bfdbfe;font-size:12px;margin-top:4px;font-weight:800}
        .nav-title{padding:13px 11px 8px;color:#94a3b8;font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.8px}
        .nav-link,.logout-btn{width:100%;min-height:44px;border:0;background:transparent;display:flex;align-items:center;justify-content:space-between;color:#cbd5e1;padding:11px 12px;border-radius:14px;font-size:14px;font-weight:850;cursor:pointer;margin-bottom:5px}
        .nav-link:hover,.nav-link.active,.logout-btn:hover{background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff}
        .main{margin-left:286px;width:calc(100% - 286px);padding:22px}.topbar{background:#fff;border:1px solid var(--border);border-radius:24px;padding:16px 18px;box-shadow:var(--shadow);display:flex;justify-content:space-between;gap:14px;align-items:center;margin-bottom:22px}
        .topbar h1{margin:0;font-size:23px}.topbar p{margin:4px 0 0;color:var(--muted);font-size:13px;font-weight:700}
        .chip{display:inline-flex;align-items:center;gap:8px;border:1px solid var(--border);border-radius:999px;padding:8px 12px;background:#fff;font-weight:900;color:#334155}
        .card{background:#fff;border:1px solid var(--border);border-radius:22px;padding:20px;box-shadow:var(--shadow)}.card-header{display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:16px}.card h3{margin:0;font-size:20px}
        .grid-4{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:18px}.grid-2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;margin-bottom:18px}
        .stat small{display:block;color:var(--muted);font-size:13px;font-weight:900}.stat strong{display:block;margin-top:8px;font-size:26px;color:#0f172a}
        .table-wrap{overflow:auto;border:1px solid var(--border);border-radius:16px}table{width:100%;border-collapse:collapse}th,td{padding:13px;border-bottom:1px solid var(--border);font-size:14px;text-align:left;vertical-align:top}th{background:#f8fafc;color:#475569;font-weight:900;white-space:nowrap}
        input,select,textarea{width:100%;border:1px solid #cbd5e1;border-radius:14px;padding:12px 13px;font-size:14px;outline:none;background:#fff;color:#111827}textarea{min-height:110px;resize:vertical}label{display:block;font-weight:900;color:#334155;margin-bottom:8px}.form-group{margin-bottom:16px}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:39px;padding:10px 14px;border-radius:13px;border:1px solid transparent;cursor:pointer;font-size:14px;font-weight:900;white-space:nowrap}.btn-primary{background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff}.btn-light{background:#fff;color:#1d4ed8;border-color:#bfdbfe}.btn-danger{background:#dc2626;color:#fff}.btn-dark{background:#0f172a;color:#fff}
        .pill{display:inline-flex;padding:7px 11px;border-radius:999px;font-size:12px;font-weight:900;background:#eff6ff;color:#1d4ed8}.pill.green{background:#dcfce7;color:#166534}.pill.red{background:#fee2e2;color:#991b1b}.pill.orange{background:#fef3c7;color:#92400e}
        .alert{padding:12px 14px;border-radius:13px;margin-bottom:15px;font-weight:800}.alert-success{background:#dcfce7;color:#166534;border:1px solid #bbf7d0}.alert-error{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}
        @media(max-width:980px){.sidebar{position:static;width:100%;height:auto}.shell{display:block}.main{margin-left:0;width:100%;padding:14px}.grid-4,.grid-2{grid-template-columns:1fr}.topbar{align-items:flex-start;flex-direction:column}}
    </style>
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <a href="{{ route('super-admin.dashboard') }}" class="brand"><span class="brand-icon">SA</span><span><strong>Super Admin</strong><span>SaaS Control Center</span></span></a>
        <div class="nav-title">Management</div>
        <a class="nav-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}" href="{{ route('super-admin.dashboard') }}">Dashboard <span>></span></a>
        <a class="nav-link {{ request()->routeIs('super-admin.tenants.*') ? 'active' : '' }}" href="{{ route('super-admin.tenants.index') }}">Coachings <span>></span></a>
        <a class="nav-link {{ request()->routeIs('super-admin.plans.*') ? 'active' : '' }}" href="{{ route('super-admin.plans.index') }}">Plans <span>></span></a>
        <div class="nav-title">Account</div>
        <form method="POST" action="{{ route('super-admin.logout') }}">@csrf<button class="logout-btn" type="submit">Logout <span>></span></button></form>
    </aside>
    <main class="main">
        <header class="topbar"><div><h1>@yield('page_title', 'Super Admin')</h1><p>@yield('page_subtitle', 'Manage all coaching tenants')</p></div><div class="chip">{{ $adminName }}</div></header>
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif
        @if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
        @yield('content')
    </main>
</div>
</body>
</html>
