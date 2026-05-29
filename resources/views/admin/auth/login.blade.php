<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | Coaching CRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, .18), transparent 35%),
                linear-gradient(135deg, #eff6ff, #ffffff);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            background: #fff;
            border-radius: 24px;
            padding: 34px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, .12);
            border: 1px solid #e5e7eb;
        }

        .logo {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 18px;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 28px;
            color: #111827;
        }

        p {
            margin: 0 0 24px;
            color: #6b7280;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: 800;
            color: #374151;
        }

        input {
            width: 100%;
            padding: 13px 14px;
            border-radius: 14px;
            border: 1px solid #d1d5db;
            font-size: 15px;
            outline: none;
        }

        input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .10);
        }

        .check-row {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-bottom: 18px;
            color: #4b5563;
            font-weight: 700;
        }

        .check-row input {
            width: auto;
        }

        .btn {
            width: 100%;
            border: 0;
            border-radius: 999px;
            padding: 13px 18px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            color: #fff;
            font-weight: 900;
            cursor: pointer;
            font-size: 15px;
        }

        .alert {
            padding: 12px 14px;
            border-radius: 14px;
            margin-bottom: 16px;
            font-weight: 800;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .help {
            margin-top: 18px;
            background: #f9fafb;
            border-radius: 16px;
            padding: 14px;
            font-size: 13px;
            color: #4b5563;
            line-height: 1.6;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="logo">🎓</div>

    <h1>Admin Login</h1>
    <p>
        @if(!empty($tenant))
            Login to manage {{ $tenant->name }} coaching CRM.
        @else
            Login to manage coaching website, leads, students, courses and CRM.
        @endif
    </p>

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit', request()->filled('tenant') ? ['tenant' => request('tenant')] : []) }}">
        @csrf

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@coachingcrm.com" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Admin@123" required>
        </div>

        <label class="check-row">
            <input type="checkbox" name="remember">
            Remember me
        </label>

        <button type="submit" class="btn">Login</button>
    </form>

    <div class="help">
        @if(!empty($tenant))
            <strong>{{ $tenant->name }} Login:</strong><br>
            Use the email and password shared by super admin.
        @else
            <strong>Default Login:</strong><br>
            Email: admin@coachingcrm.com<br>
            Password: Admin@123
        @endif
    </div>
</div>

</body>
</html>
