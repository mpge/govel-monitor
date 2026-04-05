@extends('govel-monitor::layout')

@section('content')
    <h1 class="page-title">Executions</h1>

    <div style="margin: 20px 0">
        <form method="GET" action="{{ route('govel-monitor.tasks') }}" class="filters">
            <select name="task">
                <option value="">All Tasks</option>
                @foreach(\Mpge\GovelMonitor\Models\TaskExecution::select('task')->distinct()->orderBy('task')->pluck('task') as $t)
                    <option value="{{ $t }}" {{ request('task') == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>

            <select name="status">
                <option value="">All Statuses</option>
                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            </select>

            <select name="driver">
                <option value="">All Drivers</option>
                @foreach(\Mpge\GovelMonitor\Models\TaskExecution::select('driver')->distinct()->orderBy('driver')->pluck('driver') as $d)
                    <option value="{{ $d }}" {{ request('driver') == $d ? 'selected' : '' }}>{{ $d }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn">Filter</button>

            @if(request()->hasAny(['task', 'status', 'driver']))
                <a href="{{ route('govel-monitor.tasks') }}" class="btn">Clear</a>
            @endif
        </form>
    </div>

    <div class="table-wrap">
        <div class="table-header">
            <span class="table-title">
                {{ $executions->total() }} execution{{ $executions->total() !== 1 ? 's' : '' }}
            </span>
            <form method="POST" action="{{ route('govel-monitor.purge') }}" style="display:inline"
                  onsubmit="return confirm('Purge old records?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn danger">Purge Old Records</button>
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Task</th>
                    <th>Driver</th>
                    <th>Mode</th>
                    <th>Duration</th>
                    <th>Error</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($executions as $exec)
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
                        <td style="color: var(--red); max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap">
                            {{ $exec->error ? Str::limit($exec->error, 50) : '—' }}
                        </td>
                        <td style="color: var(--text-muted)">{{ $exec->executed_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; color:var(--text-muted); padding:40px">
                            No executions found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($executions->hasPages())
        <div class="pagination">
            {{ $executions->appends(request()->query())->links('govel-monitor::pagination') }}
        </div>
    @endif
@endsection
