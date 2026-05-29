<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        if (!in_array(Auth::user()->role, ['admin', 'staff'], true) || Auth::user()->status !== 'active') {
            Auth::logout();

            return redirect()
                ->route('admin.login')
                ->with('error', 'You are not allowed to access admin panel.');
        }

        return $next($request);
    }
}
