<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PortalMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route($role . '.login');
        }

        if (Auth::user()->role !== $role || Auth::user()->status !== 'active') {
            Auth::logout();

            return redirect()
                ->route($role . '.login')
                ->with('error', 'You are not allowed to access this portal.');
        }

        return $next($request);
    }
}
