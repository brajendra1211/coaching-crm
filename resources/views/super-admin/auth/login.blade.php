<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Super Admin Login</title><meta name="viewport" content="width=device-width, initial-scale=1">
    <style>*{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;background:#f5f7fb;font-family:Arial,sans-serif;padding:18px}.card{width:100%;max-width:430px;background:#fff;border:1px solid #e5e7eb;border-radius:24px;padding:28px;box-shadow:0 18px 45px rgba(15,23,42,.10)}h1{margin:0 0 6px;color:#0f172a}p{margin:0 0 18px;color:#64748b;font-weight:700}label{display:block;margin:14px 0 8px;font-weight:900;color:#334155}input{width:100%;border:1px solid #cbd5e1;border-radius:14px;padding:13px;font-size:15px}.btn{width:100%;margin-top:18px;border:0;border-radius:14px;min-height:44px;background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;font-weight:900;font-size:15px}.alert{padding:12px 14px;border-radius:13px;margin-bottom:14px;font-weight:800}.alert-error{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}.alert-success{background:#dcfce7;color:#166534;border:1px solid #bbf7d0}</style>
</head>
<body>
<form method="POST" action="{{ route('super-admin.login.submit') }}" class="card">
    @csrf
    <h1>Super Admin</h1><p>SaaS control center login</p>
    @if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
    <label>Email</label><input type="email" name="email" value="{{ old('email') }}" required autofocus>
    <label>Password</label><input type="password" name="password" required>
    <label style="display:flex;gap:8px;align-items:center;font-size:13px;"><input type="checkbox" name="remember" style="width:auto;"> Remember me</label>
    <button class="btn" type="submit">Login</button>
</form>
</body>
</html>
