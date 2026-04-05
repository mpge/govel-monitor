<?php

namespace Mpge\GovelMonitor\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authorize
{
    public function handle(Request $request, Closure $next): Response
    {
        $gate = config('govel-monitor.gate');

        if ($gate instanceof Closure) {
            abort_unless($gate($request), 403);

            return $next($request);
        }

        // Default: only allow in local environment
        abort_unless(app()->environment('local'), 403);

        return $next($request);
    }
}
