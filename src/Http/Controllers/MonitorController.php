<?php

namespace Mpge\GovelMonitor\Http\Controllers;

use Mpge\GovelMonitor\Models\TaskExecution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class MonitorController extends Controller
{
    /**
     * Serve the SPA shell.
     */
    public function index()
    {
        return response()
            ->view('govel-monitor::app', [
                'monitorPath' => config('govel-monitor.path', 'govel-monitor'),
            ])
            ->header('X-Frame-Options', 'DENY')
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Referrer-Policy', 'no-referrer');
    }

    /**
     * GET /api/stats — dashboard stats.
     */
    public function stats(): JsonResponse
    {
        $last24h = TaskExecution::recent(24);
        $last1h = TaskExecution::recent(1);

        return response()->json([
            'total_24h' => (clone $last24h)->count(),
            'success_24h' => (clone $last24h)->successful()->count(),
            'failed_24h' => (clone $last24h)->failed()->count(),
            'pending_24h' => (clone $last24h)->pending()->count(),
            'avg_duration_24h' => round((clone $last24h)->successful()->avg('duration') ?? 0, 2),
            'p95_duration_24h' => $this->percentile((clone $last24h)->successful(), 95),
            'total_1h' => (clone $last1h)->count(),
            'success_1h' => (clone $last1h)->successful()->count(),
            'failed_1h' => (clone $last1h)->failed()->count(),
            'tasks_by_name' => TaskExecution::recent(24)
                ->selectRaw('task, count(*) as total, sum(case when success = 1 then 1 else 0 end) as succeeded, avg(duration) as avg_duration')
                ->groupBy('task')
                ->orderByDesc('total')
                ->limit(20)
                ->get(),
        ]);
    }

    /**
     * GET /api/executions — paginated execution list.
     */
    public function executions(Request $request): JsonResponse
    {
        $query = TaskExecution::latest('executed_at');

        if ($request->filled('task')) {
            $query->forTask($request->input('task'));
        }

        if ($request->filled('status')) {
            match ($request->input('status')) {
                'success' => $query->successful(),
                'failed' => $query->failed(),
                'pending' => $query->pending(),
                default => null,
            };
        }

        if ($request->filled('driver')) {
            $query->forDriver($request->input('driver'));
        }

        $perPage = max(1, min(100, (int) $request->input('per_page', 50)));

        return response()->json($query->paginate($perPage));
    }

    /**
     * GET /api/executions/{id} — single execution detail.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json(TaskExecution::findOrFail($id));
    }

    /**
     * GET /api/filters — available filter options.
     */
    public function filters(): JsonResponse
    {
        return response()->json([
            'tasks' => TaskExecution::select('task')->distinct()->orderBy('task')->pluck('task'),
            'drivers' => TaskExecution::select('driver')->distinct()->orderBy('driver')->pluck('driver'),
        ]);
    }

    /**
     * DELETE /api/purge — remove old records.
     */
    public function purge(): JsonResponse
    {
        $hours = config('govel-monitor.retention', 168);
        $deleted = TaskExecution::where('executed_at', '<', now()->subHours($hours))->delete();

        Log::info('Govel Monitor purge executed.', [
            'deleted' => $deleted,
            'ip' => request()->ip(),
        ]);

        return response()->json(['deleted' => $deleted]);
    }

    protected function percentile($query, int $percentile): float
    {
        $count = $query->whereNotNull('duration')->count();
        if ($count === 0) return 0;
        $offset = (int) ceil(($percentile / 100) * $count) - 1;
        $value = (clone $query)->whereNotNull('duration')
            ->orderBy('duration')->offset(max(0, $offset))->limit(1)->value('duration');
        return round($value ?? 0, 2);
    }
}
