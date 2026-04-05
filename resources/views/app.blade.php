<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0b0d11">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Govel Monitor</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #0b0d11;
            --surface: #151821;
            --surface-2: #1c1f2b;
            --surface-hover: #1f2333;
            --border: #262a3a;
            --text: #e8eaf0;
            --text-muted: #7c819a;
            --green: #34d399;
            --green-bg: rgba(52, 211, 153, 0.08);
            --green-border: rgba(52, 211, 153, 0.2);
            --red: #f87171;
            --red-bg: rgba(248, 113, 113, 0.08);
            --red-border: rgba(248, 113, 113, 0.2);
            --yellow: #fbbf24;
            --yellow-bg: rgba(251, 191, 36, 0.08);
            --yellow-border: rgba(251, 191, 36, 0.2);
            --blue: #60a5fa;
            --blue-bg: rgba(96, 165, 250, 0.08);
            --blue-border: rgba(96, 165, 250, 0.2);
            --purple: #a78bfa;
            --purple-bg: rgba(167, 139, 250, 0.08);
            --accent: #6d9e37;
            --accent-bg: rgba(109, 158, 55, 0.1);
            --radius: 10px;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            background: var(--bg); color: var(--text); line-height: 1.6;
            min-height: 100vh; -webkit-font-smoothing: antialiased;
        }

        #app { min-height: 100vh; display: flex; flex-direction: column; }

        .container { max-width: 1320px; margin: 0 auto; padding: 0 28px; width: 100%; }

        /* Header */
        header { background: rgba(21,24,33,0.85); backdrop-filter: blur(12px); border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 50; }
        .header-inner { display: flex; align-items: center; justify-content: space-between; height: 56px; }
        .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--text); }
        .brand-icon { width: 22px; height: 22px; background: var(--accent); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 12px; color: #fff; }
        .brand span { font-size: 15px; font-weight: 700; }
        nav { display: flex; gap: 2px; }
        nav a { color: var(--text-muted); text-decoration: none; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 500; transition: all 0.15s; }
        nav a:hover { color: var(--text); background: var(--surface-hover); }
        nav a.router-link-active { color: var(--text); background: var(--surface-hover); }

        /* Page */
        main { flex: 1; padding-bottom: 48px; }
        .page-title { font-size: 22px; font-weight: 700; margin: 28px 0 4px; letter-spacing: -0.02em; display: flex; align-items: center; gap: 12px; }
        .page-sub { font-size: 14px; color: var(--text-muted); margin-bottom: 20px; }

        /* Stats */
        .stats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; margin: 20px 0 28px; }
        .stat-card { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 20px; transition: border-color 0.2s; }
        .stat-card:hover { border-color: #363b50; }
        .stat-card.highlight { border-color: var(--accent); background: var(--accent-bg); }
        .stat-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); margin-bottom: 10px; }
        .stat-value { font-size: 26px; font-weight: 800; letter-spacing: -0.03em; line-height: 1; }
        .stat-value .unit { font-size: 13px; font-weight: 600; opacity: 0.6; margin-left: 2px; }
        .green { color: var(--green); }
        .red { color: var(--red); }
        .yellow { color: var(--yellow); }
        .blue { color: var(--blue); }
        .stat-sub { font-size: 12px; color: var(--text-muted); margin-top: 8px; }

        /* Table */
        .table-wrap { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; margin: 24px 0; }
        .table-header { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-bottom: 1px solid var(--border); }
        .table-title { font-size: 14px; font-weight: 600; color: var(--text-muted); }
        table { width: 100%; border-collapse: collapse; }
        thead { background: var(--surface-2); }
        th { text-align: left; padding: 10px 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); border-bottom: 1px solid var(--border); white-space: nowrap; }
        td { padding: 12px 20px; font-size: 13px; border-bottom: 1px solid rgba(38,42,58,0.5); transition: background 0.1s; }
        tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: var(--surface-hover); }
        tbody tr { cursor: pointer; }

        /* Badge */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; letter-spacing: 0.02em; text-transform: uppercase; }
        .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
        .badge-success { background: var(--green-bg); color: var(--green); border: 1px solid var(--green-border); }
        .badge-success::before { background: var(--green); }
        .badge-failed { background: var(--red-bg); color: var(--red); border: 1px solid var(--red-border); }
        .badge-failed::before { background: var(--red); }
        .badge-pending { background: var(--yellow-bg); color: var(--yellow); border: 1px solid var(--yellow-border); }
        .badge-pending::before { background: var(--yellow); animation: blink 1.5s infinite; }
        .badge-sync { background: var(--blue-bg); color: var(--blue); border: 1px solid var(--blue-border); }
        .badge-sync::before { background: var(--blue); }
        .badge-async { background: var(--yellow-bg); color: var(--yellow); border: 1px solid var(--yellow-border); }
        .badge-async::before { background: var(--yellow); }
        .badge-queued { background: var(--purple-bg); color: var(--purple); border: 1px solid rgba(167,139,250,0.2); }
        .badge-queued::before { background: var(--purple); }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

        /* Mono */
        .mono { font-family: 'JetBrains Mono', 'SF Mono', 'Fira Code', monospace; font-size: 12px; }

        /* Detail */
        .detail-card { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 4px 24px; margin: 20px 0; }
        .detail-row { display: flex; align-items: baseline; padding: 14px 0; border-bottom: 1px solid rgba(38,42,58,0.5); }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { width: 140px; flex-shrink: 0; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: var(--text-muted); }
        .detail-value { font-size: 14px; flex: 1; word-break: break-word; }
        .section-label { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); margin: 28px 0 10px; }
        pre { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 20px; overflow-x: auto; font-size: 12px; line-height: 1.6; color: var(--text-muted); }

        /* Filters */
        .filters { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin: 20px 0; }
        select {
            -webkit-appearance: none; -moz-appearance: none; appearance: none;
            background: var(--surface) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%237c819a' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E") no-repeat right 12px center;
            border: 1px solid var(--border); color: var(--text); padding: 8px 32px 8px 12px; border-radius: 8px; font-size: 13px; cursor: pointer; transition: border-color 0.15s;
        }
        select:hover { border-color: #404560; }
        select:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-bg); }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none; border: 1px solid var(--border); background: var(--surface); color: var(--text); cursor: pointer; transition: all 0.15s; white-space: nowrap; }
        .btn:hover { background: var(--surface-hover); border-color: #404560; }
        .btn:active { transform: scale(0.98); }
        .btn-danger { border-color: var(--red-border); color: var(--red); }
        .btn-danger:hover { background: var(--red-bg); }
        .btn-primary { border-color: var(--accent); color: var(--accent); }
        .btn-primary:hover { background: var(--accent-bg); }

        /* Links */
        .task-link { color: var(--blue); text-decoration: none; cursor: pointer; }
        .task-link:hover { color: #93c5fd; text-decoration: underline; }

        /* Pagination */
        .pagination { display: flex; gap: 6px; justify-content: center; padding: 20px; flex-wrap: wrap; }
        .pagination button { padding: 6px 12px; border-radius: 6px; font-size: 13px; border: 1px solid var(--border); color: var(--text-muted); background: transparent; cursor: pointer; transition: all 0.15s; }
        .pagination button:hover:not(:disabled) { background: var(--surface-hover); color: var(--text); }
        .pagination button.active { background: var(--accent); color: #fff; border-color: var(--accent); }
        .pagination button:disabled { opacity: 0.3; cursor: not-allowed; }

        /* Truncate */
        .truncate { max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        /* Empty */
        .empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
        .empty-state p { font-size: 14px; margin-top: 8px; }

        /* Loading */
        .loading { text-align: center; padding: 40px; color: var(--text-muted); font-size: 14px; }
        .spinner { display: inline-block; width: 20px; height: 20px; border: 2px solid var(--border); border-top-color: var(--accent); border-radius: 50%; animation: spin 0.6s linear infinite; margin-right: 8px; vertical-align: middle; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Flash */
        .flash { background: var(--green-bg); color: var(--green); border: 1px solid var(--green-border); padding: 12px 20px; border-radius: var(--radius); margin: 16px 0; font-size: 13px; font-weight: 500; display: flex; justify-content: space-between; align-items: center; }
        .flash button { background: none; border: none; color: var(--green); cursor: pointer; font-size: 16px; }

        /* Footer */
        footer { text-align: center; padding: 24px 0 32px; font-size: 12px; color: var(--text-muted); }
        footer a { color: var(--accent); text-decoration: none; }
        footer a:hover { text-decoration: underline; }

        /* Auto-refresh indicator */
        .refresh-indicator { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-muted); }
        .refresh-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--green); animation: blink 2s infinite; }

        /* Responsive */
        @media (max-width: 1024px) { .stats-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .table-wrap { overflow-x: auto; }
            table { min-width: 700px; }
            .detail-row { flex-direction: column; gap: 4px; }
            .detail-label { width: auto; }
        }
        @media (max-width: 480px) { .stats-grid { grid-template-columns: 1fr; } .container { padding: 0 16px; } }
    </style>
</head>
<body>
<div id="app"></div>

<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script src="https://unpkg.com/vue-router@4/dist/vue-router.global.prod.js"></script>
<script>
const BASE = '/{{ $monitorPath }}';
const API = BASE + '/api';

// ── Helpers ──
function timeAgo(dateStr) {
    const seconds = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);
    if (seconds < 60) return seconds + 's ago';
    if (seconds < 3600) return Math.floor(seconds / 60) + 'm ago';
    if (seconds < 86400) return Math.floor(seconds / 3600) + 'h ago';
    return Math.floor(seconds / 86400) + 'd ago';
}

function statusBadge(success) {
    if (success === null || success === undefined) return 'badge badge-pending';
    return success ? 'badge badge-success' : 'badge badge-failed';
}

function statusLabel(success) {
    if (success === null || success === undefined) return 'Pending';
    return success ? 'OK' : 'Failed';
}

function modeBadge(mode) {
    return 'badge badge-' + mode;
}

function fmt(n) {
    return n != null ? Number(n).toLocaleString() : '—';
}

function fmtDuration(d) {
    return d != null ? Math.round(d * 100) / 100 + 'ms' : '—';
}

function jsonPretty(obj) {
    return JSON.stringify(obj, null, 2);
}

async function api(path, opts = {}) {
    const res = await fetch(API + path, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' },
        ...opts,
    });
    return res.json();
}

// ── Dashboard Page ──
const DashboardPage = {
    template: `
    <div>
        <div style="display:flex; justify-content:space-between; align-items:center">
            <div>
                <h1 class="page-title">Dashboard</h1>
                <p class="page-sub">Task execution overview — last 24 hours</p>
            </div>
            <div class="refresh-indicator">
                <span class="refresh-dot"></span> Auto-refreshing
            </div>
        </div>

        <div v-if="stats" class="stats-grid">
            <div class="stat-card highlight">
                <div class="stat-label">Total Executions</div>
                <div class="stat-value">{{ fmt(stats.total_24h) }}</div>
                <div class="stat-sub">{{ stats.total_1h }} in the last hour</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Successful</div>
                <div class="stat-value green">{{ fmt(stats.success_24h) }}</div>
                <div class="stat-sub">{{ stats.total_24h > 0 ? Math.round(stats.success_24h / stats.total_24h * 1000) / 10 + '% success rate' : 'No data yet' }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Failed</div>
                <div class="stat-value red">{{ fmt(stats.failed_24h) }}</div>
                <div class="stat-sub">{{ stats.failed_1h }} in the last hour</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Avg Duration</div>
                <div class="stat-value blue">{{ stats.avg_duration_24h }}<span class="unit">ms</span></div>
                <div class="stat-sub">p95: {{ stats.p95_duration_24h }}ms</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Pending</div>
                <div class="stat-value yellow">{{ fmt(stats.pending_24h) }}</div>
                <div class="stat-sub">Async & queued</div>
            </div>
        </div>
        <div v-else class="loading"><span class="spinner"></span> Loading stats...</div>

        <div v-if="stats && stats.tasks_by_name.length" class="table-wrap">
            <div class="table-header"><span class="table-title">Tasks by Name (24h)</span></div>
            <table>
                <thead><tr><th>Task</th><th>Total</th><th>Succeeded</th><th>Avg Duration</th></tr></thead>
                <tbody>
                    <tr v-for="t in stats.tasks_by_name" :key="t.task">
                        <td class="mono">{{ t.task }}</td>
                        <td>{{ fmt(t.total) }}</td>
                        <td><span class="badge badge-success">{{ fmt(t.succeeded) }}</span></td>
                        <td class="mono">{{ fmtDuration(t.avg_duration) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="recent" class="table-wrap">
            <div class="table-header">
                <span class="table-title">Recent Executions</span>
                <router-link :to="{name:'executions'}" class="btn btn-primary">View All &rarr;</router-link>
            </div>
            <table>
                <thead><tr><th>Status</th><th>Task</th><th>Driver</th><th>Mode</th><th>Duration</th><th>Time</th></tr></thead>
                <tbody>
                    <tr v-for="e in recent" :key="e.id" @click="$router.push({name:'detail', params:{id:e.id}})">
                        <td><span :class="statusBadge(e.success)">{{ statusLabel(e.success) }}</span></td>
                        <td class="mono task-link">{{ e.task }}</td>
                        <td class="mono" style="color:var(--text-muted)">{{ e.driver }}</td>
                        <td><span :class="modeBadge(e.mode)">{{ e.mode }}</span></td>
                        <td class="mono">{{ fmtDuration(e.duration) }}</td>
                        <td style="color:var(--text-muted)">{{ timeAgo(e.executed_at) }}</td>
                    </tr>
                    <tr v-if="!recent.length">
                        <td colspan="6"><div class="empty-state"><p>No task executions recorded yet.<br>Run a Govel task and it will appear here.</p></div></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>`,
    data() { return { stats: null, recent: null, interval: null }; },
    methods: {
        fmt, fmtDuration, timeAgo, statusBadge, statusLabel, modeBadge,
        async load() {
            const [stats, executions] = await Promise.all([
                api('/stats'),
                api('/executions?per_page=25'),
            ]);
            this.stats = stats;
            this.recent = executions.data;
        }
    },
    mounted() { this.load(); this.interval = setInterval(() => this.load(), 10000); },
    unmounted() { clearInterval(this.interval); },
};

// ── Executions Page ──
const ExecutionsPage = {
    template: `
    <div>
        <h1 class="page-title">Executions</h1>
        <p class="page-sub">Browse and filter all task executions.</p>

        <div class="filters">
            <select v-model="filterTask" @change="loadPage(1)">
                <option value="">All Tasks</option>
                <option v-for="t in filterOptions.tasks" :value="t">{{ t }}</option>
            </select>
            <select v-model="filterStatus" @change="loadPage(1)">
                <option value="">All Statuses</option>
                <option value="success">Success</option>
                <option value="failed">Failed</option>
                <option value="pending">Pending</option>
            </select>
            <select v-model="filterDriver" @change="loadPage(1)">
                <option value="">All Drivers</option>
                <option v-for="d in filterOptions.drivers" :value="d">{{ d }}</option>
            </select>
            <button class="btn" @click="clearFilters" v-if="hasFilters">Clear</button>
            <div style="flex:1"></div>
            <button class="btn btn-danger" @click="purge">Purge Old Records</button>
        </div>

        <div v-if="flash" class="flash">
            {{ flash }}
            <button @click="flash=null">&times;</button>
        </div>

        <div v-if="data" class="table-wrap">
            <div class="table-header">
                <span class="table-title">{{ fmt(data.total) }} execution{{ data.total !== 1 ? 's' : '' }}</span>
            </div>
            <table>
                <thead><tr><th>Status</th><th>Task</th><th>Driver</th><th>Mode</th><th>Duration</th><th>Error</th><th>Time</th></tr></thead>
                <tbody>
                    <tr v-for="e in data.data" :key="e.id" @click="$router.push({name:'detail', params:{id:e.id}})">
                        <td><span :class="statusBadge(e.success)">{{ statusLabel(e.success) }}</span></td>
                        <td class="mono task-link">{{ e.task }}</td>
                        <td class="mono" style="color:var(--text-muted)">{{ e.driver }}</td>
                        <td><span :class="modeBadge(e.mode)">{{ e.mode }}</span></td>
                        <td class="mono">{{ fmtDuration(e.duration) }}</td>
                        <td class="truncate" style="color:var(--red)">{{ e.error ? e.error.substring(0, 60) : '—' }}</td>
                        <td style="color:var(--text-muted)">{{ timeAgo(e.executed_at) }}</td>
                    </tr>
                    <tr v-if="!data.data.length">
                        <td colspan="7"><div class="empty-state"><p>No executions match your filters.</p></div></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else class="loading"><span class="spinner"></span> Loading executions...</div>

        <div v-if="data && data.last_page > 1" class="pagination">
            <button :disabled="data.current_page <= 1" @click="loadPage(data.current_page - 1)">&laquo;</button>
            <template v-for="p in pageRange" :key="p">
                <button v-if="p === '...'" disabled>...</button>
                <button v-else :class="{active: p === data.current_page}" @click="loadPage(p)">{{ p }}</button>
            </template>
            <button :disabled="data.current_page >= data.last_page" @click="loadPage(data.current_page + 1)">&raquo;</button>
        </div>
    </div>`,
    data() { return { data: null, filterOptions: { tasks: [], drivers: [] }, filterTask: '', filterStatus: '', filterDriver: '', flash: null }; },
    computed: {
        hasFilters() { return this.filterTask || this.filterStatus || this.filterDriver; },
        pageRange() {
            if (!this.data) return [];
            const c = this.data.current_page, l = this.data.last_page, r = [];
            if (l <= 7) { for (let i = 1; i <= l; i++) r.push(i); return r; }
            r.push(1);
            if (c > 3) r.push('...');
            for (let i = Math.max(2, c - 1); i <= Math.min(l - 1, c + 1); i++) r.push(i);
            if (c < l - 2) r.push('...');
            r.push(l);
            return r;
        }
    },
    methods: {
        fmt, fmtDuration, timeAgo, statusBadge, statusLabel, modeBadge,
        async loadPage(page) {
            let q = `?page=${page}&per_page=50`;
            if (this.filterTask) q += `&task=${encodeURIComponent(this.filterTask)}`;
            if (this.filterStatus) q += `&status=${this.filterStatus}`;
            if (this.filterDriver) q += `&driver=${encodeURIComponent(this.filterDriver)}`;
            this.data = await api('/executions' + q);
        },
        clearFilters() { this.filterTask = ''; this.filterStatus = ''; this.filterDriver = ''; this.loadPage(1); },
        async purge() {
            if (!confirm('Purge old records?')) return;
            const res = await api('/purge', { method: 'DELETE' });
            this.flash = `Purged ${res.deleted} records.`;
            this.loadPage(1);
        }
    },
    async mounted() {
        this.filterOptions = await api('/filters');
        this.loadPage(1);
    },
};

// ── Detail Page ──
const DetailPage = {
    template: `
    <div v-if="exec">
        <h1 class="page-title">
            Execution #{{ exec.id }}
            <span :class="statusBadge(exec.success)">{{ statusLabel(exec.success) }}</span>
        </h1>

        <div class="detail-card">
            <div class="detail-row"><div class="detail-label">Task</div><div class="detail-value mono">{{ exec.task }}</div></div>
            <div class="detail-row"><div class="detail-label">Driver</div><div class="detail-value">{{ exec.driver }}</div></div>
            <div class="detail-row"><div class="detail-label">Mode</div><div class="detail-value"><span :class="modeBadge(exec.mode)">{{ exec.mode }}</span></div></div>
            <div class="detail-row"><div class="detail-label">Duration</div><div class="detail-value mono">{{ fmtDuration(exec.duration) }}</div></div>
            <div class="detail-row"><div class="detail-label">Executed At</div><div class="detail-value">{{ exec.executed_at }} ({{ timeAgo(exec.executed_at) }})</div></div>
            <div v-if="exec.error" class="detail-row"><div class="detail-label">Error</div><div class="detail-value red">{{ exec.error }}</div></div>
        </div>

        <div class="section-label">Payload</div>
        <pre class="mono">{{ jsonPretty(exec.payload) }}</pre>

        <div v-if="exec.output">
            <div class="section-label">Output</div>
            <pre class="mono">{{ jsonPretty(exec.output) }}</pre>
        </div>

        <div style="margin: 28px 0">
            <button class="btn" @click="$router.back()">&larr; Back</button>
        </div>
    </div>
    <div v-else class="loading"><span class="spinner"></span> Loading execution...</div>`,
    data() { return { exec: null }; },
    methods: { statusBadge, statusLabel, modeBadge, fmtDuration, timeAgo, jsonPretty },
    async mounted() { this.exec = await api('/executions/' + this.$route.params.id); },
    watch: { '$route.params.id'() { this.exec = null; api('/executions/' + this.$route.params.id).then(d => this.exec = d); } },
};

// ── Router & App ──
const router = VueRouter.createRouter({
    history: VueRouter.createWebHistory(BASE),
    routes: [
        { path: '/', name: 'dashboard', component: DashboardPage },
        { path: '/executions', name: 'executions', component: ExecutionsPage },
        { path: '/executions/:id', name: 'detail', component: DetailPage },
    ],
});

const app = Vue.createApp({
    template: `
    <header>
        <div class="container header-inner">
            <router-link to="/" class="brand">
                <div class="brand-icon">G</div>
                <span>Govel Monitor</span>
            </router-link>
            <nav>
                <router-link to="/" exact-active-class="router-link-active">Dashboard</router-link>
                <router-link to="/executions" active-class="router-link-active">Executions</router-link>
            </nav>
        </div>
    </header>
    <main class="container">
        <router-view />
    </main>
    <footer>
        <a href="https://github.com/mpge/govel-monitor">Govel Monitor</a> &middot; powered by <a href="https://github.com/mpge/govel">Govel</a>
    </footer>`
});

app.use(router);
app.mount('#app');
</script>
</body>
</html>
