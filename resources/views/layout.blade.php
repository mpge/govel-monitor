<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Govel Monitor</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #0f1117;
            --surface: #1a1d27;
            --surface-hover: #22252f;
            --border: #2a2d3a;
            --text: #e4e6eb;
            --text-muted: #8b8fa3;
            --green: #22c55e;
            --green-bg: rgba(34, 197, 94, 0.1);
            --red: #ef4444;
            --red-bg: rgba(239, 68, 68, 0.1);
            --yellow: #f59e0b;
            --yellow-bg: rgba(245, 158, 11, 0.1);
            --blue: #3b82f6;
            --blue-bg: rgba(59, 130, 246, 0.1);
            --accent: #6d9e37;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }

        .container { max-width: 1280px; margin: 0 auto; padding: 0 24px; }

        /* Header */
        .header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 16px 0;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text);
        }

        .header-brand svg { width: 28px; height: 28px; fill: var(--accent); }
        .header-brand span { font-size: 18px; font-weight: 700; letter-spacing: -0.02em; }

        .nav { display: flex; gap: 4px; }
        .nav a {
            color: var(--text-muted);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.15s;
        }
        .nav a:hover { color: var(--text); background: var(--surface-hover); }
        .nav a.active { color: var(--text); background: var(--surface-hover); }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin: 24px 0;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
        }

        .stat-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .stat-value.green { color: var(--green); }
        .stat-value.red { color: var(--red); }
        .stat-value.yellow { color: var(--yellow); }
        .stat-value.blue { color: var(--blue); }

        .stat-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Table */
        .table-wrap {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            margin: 24px 0;
        }

        .table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
        }

        .table-title {
            font-size: 16px;
            font-weight: 600;
        }

        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left;
            padding: 12px 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
        }
        td {
            padding: 14px 20px;
            font-size: 14px;
            border-bottom: 1px solid var(--border);
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: var(--surface-hover); }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge.success { background: var(--green-bg); color: var(--green); }
        .badge.failed { background: var(--red-bg); color: var(--red); }
        .badge.pending { background: var(--yellow-bg); color: var(--yellow); }
        .badge.sync { background: var(--blue-bg); color: var(--blue); }
        .badge.async { background: var(--yellow-bg); color: var(--yellow); }
        .badge.queued { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }

        /* Code / Mono */
        .mono { font-family: 'SF Mono', 'Fira Code', monospace; font-size: 13px; }

        /* Detail Card */
        .detail-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
        }

        .detail-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }
        .detail-row:last-child { border-bottom: none; }

        .detail-label {
            width: 160px;
            flex-shrink: 0;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .detail-value { font-size: 14px; flex: 1; }

        pre {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 16px;
            overflow-x: auto;
            font-size: 13px;
            line-height: 1.5;
        }

        /* Filters */
        .filters {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filters select, .filters input {
            background: var(--bg);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 14px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text);
            cursor: pointer;
            transition: all 0.15s;
        }
        .btn:hover { background: var(--surface-hover); }
        .btn.danger { border-color: var(--red); color: var(--red); }
        .btn.danger:hover { background: var(--red-bg); }

        /* Links */
        a.task-link {
            color: var(--blue);
            text-decoration: none;
        }
        a.task-link:hover { text-decoration: underline; }

        /* Pagination */
        .pagination {
            display: flex;
            gap: 8px;
            justify-content: center;
            padding: 20px;
        }
        .pagination a, .pagination span {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 14px;
            text-decoration: none;
            border: 1px solid var(--border);
            color: var(--text-muted);
        }
        .pagination a:hover { background: var(--surface-hover); color: var(--text); }
        .pagination .active { background: var(--accent); color: #fff; border-color: var(--accent); }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .table-wrap { overflow-x: auto; }
            table { min-width: 700px; }
        }

        /* Pulse dot */
        .pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
            animation: pulse 2s infinite;
        }
        .pulse.green { background: var(--green); }
        .pulse.red { background: var(--red); }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            margin: 24px 0 8px;
            letter-spacing: -0.02em;
        }

        .flash {
            background: var(--green-bg);
            color: var(--green);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 16px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container header-inner">
            <a href="{{ route('govel-monitor.dashboard') }}" class="header-brand">
                <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                <span>Govel Monitor</span>
            </a>
            <nav class="nav">
                <a href="{{ route('govel-monitor.dashboard') }}"
                   class="{{ request()->routeIs('govel-monitor.dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('govel-monitor.tasks') }}"
                   class="{{ request()->routeIs('govel-monitor.tasks') ? 'active' : '' }}">Executions</a>
            </nav>
        </div>
    </header>

    <main class="container">
        @if(session('message'))
            <div class="flash">{{ session('message') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
