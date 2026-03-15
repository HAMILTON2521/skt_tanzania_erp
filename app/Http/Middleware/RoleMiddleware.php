<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Authentication is required to access this area.');
        }

        if ($roles === []) {
            return $next($request);
        }

        if (! method_exists($user, 'hasAnyRole') || ! $user->hasAnyRole($roles)) {
            abort(403, 'You do not have the required role for this area.');
        }

        return $next($request);
    }
}
