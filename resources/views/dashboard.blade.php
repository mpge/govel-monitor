@extends('govel-monitor::layout')

@section('content')
    <h1 class="page-title">Dashboard</h1>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total (24h)</div>
            <div class="stat-value">{{ number_format($stats['total_24h']) }}</div>
            <div class="stat-sub">{{ $stats['total_1h'] }} in the last hour</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Successful</div>
            <div class="stat-value green">{{ number_format($stats['success_24h']) }}</div>
            <div class="stat-sub">
                @if($stats['total_24h'] > 0)
                    {{ round(($stats['success_24h'] / $stats['total_24h']) * 100, 1) }}% success rate
                @else
                    —
                @endif
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Failed</div>
            <div class="stat-value red">{{ number_format($stats['failed_24h']) }}</div>
            <div class="stat-sub">{{ $stats['failed_1h'] }} in the last hour</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Avg Duration</div>
            <div class="stat-value blue">{{ $stats['avg_duration_24h'] }}<span style="font-size:14px">ms</span></div>
            <div class="stat-sub">p95: {{ $stats['p95_duration_24h'] }}ms</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pending</div>
            <div class="stat-value yellow">{{ number_format($stats['pending_24h']) }}</div>
            <div class="stat-sub">Async / queued tasks</div>
        </div>
    </div>

    {{-- Tasks by Name --}}
    @if(count($stats['tasks_by_name']) > 0)
        <div class="table-wrap">
            <div class="table-header">
                <span class="table-title">Tasks by Name (24h)</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Total</th>
                        <th>Succeeded</th>
                        <th>Avg Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['tasks_by_name'] as $t)
                        <tr>
                            <td class="mono">{{ $t['task'] }}</td>
                            <td>{{ number_format($t['total']) }}</td>
                            <td>
                                <span class="badge success">{{ number_format($t['succeeded']) }}</span>
                            </td>
                            <td>{{ round($t['avg_duration'] ?? 0, 2) }}ms</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Recent Executions --}}
    <div class="table-wrap">
        <div class="table-header">
            <span class="table-title">Recent Executions</span>
            <a href="{{ route('govel-monitor.tasks') }}" class="btn">View All</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Task</th>
                    <th>Driver</th>
                    <th>Mode</th>
                    <th>Duration</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTasks as $exec)
                    <tr>
                        <td>
                            @if(is_null($exec->success))
                                <span class="badge pending">Pending</span>
                            @elseif($exec->success)
                                <span class="badge success">OK</span>
                            @else
                                <span class="badge failed">Failed</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('govel-monitor.task.show', $exec->id) }}" class="task-link mono">
                                {{ $exec->task }}
                            </a>
                        </td>
                        <td>{{ $exec->driver }}</td>
                        <td><span class="badge {{ $exec->mode }}">{{ $exec->mode }}</span></td>
                        <td class="mono">{{ $exec->duration ? round($exec->duration, 2) . 'ms' : '—' }}</td>
                        <td style="color: var(--text-muted)">{{ $exec->executed_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; color:var(--text-muted); padding:40px">
                            No task executions recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
