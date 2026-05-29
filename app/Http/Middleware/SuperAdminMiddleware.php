<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('super-admin.login');
        }

        if (Auth::user()->role !== 'super_admin' || Auth::user()->status !== 'active') {
            Auth::logout();

            return redirect()
                ->route('super-admin.login')
                ->with('error', 'You are not allowed to access super admin panel.');
        }

        return $next($request);
    }
}
