<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserCanModerate
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless($request->user()?->canModerate(), 403);
        return $next($request);
    }
}
