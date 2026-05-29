<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && in_array(Auth::user()->role, ['admin', 'staff'], true)) {
            return redirect()->route('admin.dashboard');
        }

        if (Auth::check() && Auth::user()->role === 'teacher') {
            return redirect()->route('teacher.dashboard');
        }

        if (Auth::check() && Auth::user()->role === 'student') {
            return redirect()->route('student.dashboard');
        }

        return view('admin.auth.login', [
            'tenant' => app(TenantManager::class)->current(),
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            if (!in_array(Auth::user()->role, ['admin', 'staff'], true) || Auth::user()->status !== 'active') {
                Auth::logout();

                return back()->with('error', 'Only active admin or staff can login.');
            }

            return redirect()->route('admin.dashboard');
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Invalid email or password.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $tenantSlug = app(TenantManager::class)->current()?->slug;

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $redirect = $tenantSlug
            ? route('admin.login', ['tenant' => $tenantSlug])
            : route('admin.login');

        return redirect($redirect)->with('success', 'Logged out successfully.');
    }
}
