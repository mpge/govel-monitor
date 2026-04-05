<?php

namespace Mpge\GovelMonitor\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class Authorize
{
    public function handle(Request $request, Closure $next): Response
    {
        $gate = config('govel-monitor.gate');

        if ($gate !== null && is_callable($gate)) {
            if (! $gate($request)) {
                Log::warning('Govel Monitor: unauthorized access attempt.', [
                    'ip' => $request->ip(),
                    'path' => $request->path(),
                ]);
                abort(403);
            }

            return $next($request);
        }

        // Default: only allow in local environment
        if (! app()->environment('local')) {
            Log::warning('Govel Monitor: unauthorized access attempt.', [
                'ip' => $request->ip(),
                'path' => $request->path(),
            ]);
            abort(403);
        }

        return $next($request);
    }
}
