<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth('admin')->user() ?? auth('web')->user();

        if (! $user || ! in_array($user->role, $roles)) {
            abort(403);
        }

        return $next($request);
    }
}
