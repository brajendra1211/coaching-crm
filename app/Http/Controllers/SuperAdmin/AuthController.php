<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role === 'super_admin') {
            return redirect()->route('super-admin.dashboard');
        }

        return view('super-admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->role !== 'super_admin' || Auth::user()->status !== 'active') {
                Auth::logout();

                return back()->with('error', 'Only active super admin can login.');
            }

            return redirect()->route('super-admin.dashboard');
        }

        return back()->withInput($request->only('email'))->with('error', 'Invalid email or password.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('super-admin.login')->with('success', 'Logged out successfully.');
    }
}
