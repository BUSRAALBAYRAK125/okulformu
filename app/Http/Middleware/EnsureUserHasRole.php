<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (empty($roles)) {
            abort(403);
        }

        if (!$user->hasAnyRole($roles)) {
            abort(403);
        }

        return $next($request);
    }
}