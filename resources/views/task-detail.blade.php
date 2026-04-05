@extends('govel-monitor::layout')

@section('content')
    <h1 class="page-title">
        Execution #{{ $execution->id }}
        @if(is_null($execution->success))
            <span class="badge pending">Pending</span>
        @elseif($execution->success)
            <span class="badge success">Success</span>
        @else
            <span class="badge failed">Failed</span>
        @endif
    </h1>

    <div class="detail-card">
        <div class="detail-row">
            <div class="detail-label">Task</div>
            <div class="detail-value mono">{{ $execution->task }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Driver</div>
            <div class="detail-value">{{ $execution->driver }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Mode</div>
            <div class="detail-value"><span class="badge {{ $execution->mode }}">{{ $execution->mode }}</span></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Duration</div>
            <div class="detail-value mono">{{ $execution->duration ? round($execution->duration, 2) . 'ms' : '—' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Executed At</div>
            <div class="detail-value">{{ $execution->executed_at->format('Y-m-d H:i:s.u') }} ({{ $execution->executed_at->diffForHumans() }})</div>
        </div>

        @if($execution->error)
            <div class="detail-row">
                <div class="detail-label">Error</div>
                <div class="detail-value" style="color: var(--red)">{{ $execution->error }}</div>
            </div>
        @endif
    </div>

    <h2 style="font-size:18px; font-weight:600; margin: 24px 0 12px">Payload</h2>
    <pre class="mono">{{ json_encode($execution->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

    @if($execution->output)
        <h2 style="font-size:18px; font-weight:600; margin: 24px 0 12px">Output</h2>
        <pre class="mono">{{ json_encode($execution->output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
    @endif

    <div style="margin: 24px 0">
        <a href="{{ url()->previous() }}" class="btn">&larr; Back</a>
    </div>
@endsection
