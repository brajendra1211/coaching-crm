<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin(string $role)
    {
        if (Auth::check() && Auth::user()->role === $role) {
            return redirect()->route($role . '.dashboard');
        }

        return view('portal.auth.login', [
            'role' => $role,
            'title' => ucfirst($role) . ' Login',
        ]);
    }

    public function login(Request $request, string $role)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->role !== $role || Auth::user()->status !== 'active') {
                Auth::logout();

                return back()->with('error', 'Only active ' . $role . ' users can login.');
            }

            return redirect()->route($role . '.dashboard');
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Invalid email or password.');
    }

    public function logout(Request $request, string $role)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($role . '.login')->with('success', 'Logged out successfully.');
    }
}
