@php
    $setting = $siteSetting ?? \App\Models\CoachingSetting::current();
    $roleLabel = ucfirst($role);
    $instituteName = $setting?->institute_name ?: 'Edu Institute';
    $portalLogo = $setting?->logo ? asset('storage/' . $setting->logo) : null;
    $primaryColor = $setting?->primary_color ?: '#2563eb';
    $secondaryColor = $setting?->secondary_color ?: '#7c3aed';
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $roleLabel }} Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; display:grid; place-items:center; background:#f5f7fb; font-family:Arial,sans-serif; color:#111827; padding:18px; }
        .login-card { width:100%; max-width:430px; background:#fff; border:1px solid #e5e7eb; border-radius:24px; padding:28px; box-shadow:0 18px 45px rgba(15,23,42,.10); }
        .brand { display:flex; align-items:center; gap:12px; margin-bottom:22px; }
        .brand-icon { width:48px; height:48px; border-radius:17px; background:linear-gradient(135deg,{{ $primaryColor }},{{ $secondaryColor }}); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:900; overflow:hidden; }
        .brand-icon img { width:100%; height:100%; object-fit:contain; background:#fff; padding:4px; }
        h1 { margin:0; font-size:25px; color:#0f172a; }
        p { margin:6px 0 0; color:#64748b; font-size:14px; font-weight:700; }
        label { display:block; margin:14px 0 8px; font-weight:900; color:#334155; }
        input { width:100%; border:1px solid #cbd5e1; border-radius:14px; padding:13px 14px; font-size:15px; outline:none; }
        input:focus { border-color:{{ $primaryColor }}; box-shadow:0 0 0 4px rgba(37,99,235,.10); }
        .btn { width:100%; margin-top:18px; border:0; border-radius:14px; min-height:44px; background:linear-gradient(135deg,{{ $primaryColor }},{{ $secondaryColor }}); color:#fff; font-size:15px; font-weight:900; cursor:pointer; }
        .alert { padding:12px 14px; border-radius:13px; margin-bottom:14px; font-size:14px; font-weight:800; }
        .alert-error { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
        .alert-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
        .links { display:flex; gap:10px; justify-content:center; margin-top:16px; color:{{ $primaryColor }}; font-size:13px; font-weight:900; }
    </style>
</head>
<body>
    <form method="POST" action="{{ route($role . '.login.submit') }}" class="login-card">
        @csrf
        <div class="brand">
            <span class="brand-icon">
                @if($portalLogo)
                    <img src="{{ $portalLogo }}" alt="{{ $instituteName }}">
                @else
                    {{ strtoupper(substr($instituteName, 0, 1)) }}
                @endif
            </span>
            <span>
                <h1>{{ $instituteName }}</h1>
                <p>{{ $roleLabel }} Login</p>
            </span>
        </div>

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus>

        <label>Password</label>
        <input type="password" name="password" required>

        <label style="display:flex;gap:8px;align-items:center;font-size:13px;">
            <input type="checkbox" name="remember" style="width:auto;"> Remember me
        </label>

        <button type="submit" class="btn">Login</button>

        <div class="links">
            <a href="{{ route('student.login') }}">Student Login</a>
            <a href="{{ route('teacher.login') }}">Teacher Login</a>
            <a href="{{ route('staff.login') }}">Staff Login</a>
        </div>
    </form>
</body>
</html>
