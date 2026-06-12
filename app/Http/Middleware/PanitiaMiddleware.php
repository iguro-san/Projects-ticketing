<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PanitiaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isPanitia()) {
            abort(403, 'Unauthorized access. Panitia only.');
        }
        return $next($request);
    }
}