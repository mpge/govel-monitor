<?php

namespace Mpge\GovelMonitor\Http\Controllers;

use Mpge\GovelMonitor\Models\TaskExecution;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MonitorController extends Controller
{
    public function dashboard()
    {
        $stats = $this->getStats();
        $recentTasks = TaskExecution::latest('executed_at')->limit(25)->get();
        $taskNames = TaskExecution::select('task')
            ->distinct()
            ->orderBy('task')
            ->pluck('task');

        return view('govel-monitor::dashboard', compact('stats', 'recentTasks', 'taskNames'));
    }

    public function tasks(Request $request)
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

        $executions = $query->paginate(50);

        return view('govel-monitor::tasks', compact('executions'));
    }

    public function show(int $id)
    {
        $execution = TaskExecution::findOrFail($id);

        return view('govel-monitor::task-detail', compact('execution'));
    }

    public function stats()
    {
        return response()->json($this->getStats());
    }

    public function purge()
    {
        $hours = config('govel-monitor.retention', 168);

        $deleted = TaskExecution::where('executed_at', '<', now()->subHours($hours))->delete();

        return back()->with('message', "Purged {$deleted} records.");
    }

    protected function getStats(): array
    {
        $last24h = TaskExecution::recent(24);
        $last1h = TaskExecution::recent(1);

        return [
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
                ->get()
                ->toArray(),
        ];
    }

    protected function percentile($query, int $percentile): float
    {
        $values = $query->whereNotNull('duration')
            ->orderBy('duration')
            ->pluck('duration');

        if ($values->isEmpty()) {
            return 0;
        }

        $index = (int) ceil(($percentile / 100) * $values->count()) - 1;

        return round($values->get(max(0, $index)) ?? 0, 2);
    }
}
