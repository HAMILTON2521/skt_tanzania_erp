<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Authentication is required to access the admin panel.');
        }

        if (! method_exists($user, 'hasRole') || ! $user->hasRole('Admin')) {
            abort(403, 'You do not have permission to access the admin panel.');
        }

        return $next($request);
    }
}
