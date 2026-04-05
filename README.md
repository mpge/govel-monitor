<p align="center">
    <img src="art/logo.png" width="500" alt="Govel Monitor — real-time task monitoring dashboard">
</p>

<p align="center">
    <a href="https://packagist.org/packages/mpge/govel-monitor"><img src="https://img.shields.io/packagist/v/mpge/govel-monitor.svg?style=flat-square" alt="Latest Version on Packagist"></a>
    <a href="https://packagist.org/packages/mpge/govel-monitor"><img src="https://img.shields.io/packagist/dt/mpge/govel-monitor.svg?style=flat-square" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/mpge/govel-monitor"><img src="https://img.shields.io/packagist/php-v/mpge/govel-monitor.svg?style=flat-square" alt="PHP Version"></a>
    <a href="https://github.com/mpge/govel-monitor/actions"><img src="https://img.shields.io/github/actions/workflow/status/mpge/govel-monitor/tests.yml?branch=main&style=flat-square&label=tests" alt="Tests"></a>
    <a href="https://github.com/mpge/govel-monitor/blob/main/LICENSE"><img src="https://img.shields.io/packagist/l/mpge/govel-monitor.svg?style=flat-square" alt="License"></a>
</p>

<p align="center">
    Real-time task monitoring dashboard for <a href="https://github.com/mpge/govel">Govel</a>.
</p>

---

## Features

- **Live Dashboard** — total executions, success rates, avg/p95 durations with auto-refresh
- **Execution History** — searchable, filterable, paginated list of every Go task execution
- **Task Breakdown** — per-task metrics (volume, success rate, avg duration)
- **Detail View** — full payload, output, and error for each execution
- **Auto-Recording** — transparently hooks into GoManager, zero code changes
- **Payload Redaction** — automatically redacts sensitive keys (passwords, tokens, secrets)
- **Data Retention** — configurable auto-purge of old records
- **Authorization** — local-only by default, customizable gate for production
- **Security Hardened** — OWASP-audited with security headers, audit logging, input validation
- **Vue.js SPA** — modern dark-themed UI, no frontend build step (CDN-loaded)

## Requirements

- PHP 8.3+
- Laravel 11+
- [mpge/govel](https://github.com/mpge/govel) ^0.1

## Installation

```bash
composer require mpge/govel-monitor
```

Run migrations:

```bash
php artisan migrate
```

Visit `/govel-monitor` in your browser.

## Configuration

Publish the config:

```bash
php artisan vendor:publish --tag=govel-monitor-config
```

```php
// config/govel-monitor.php
return [
    'path'        => 'govel-monitor',       // Dashboard URL prefix
    'enabled'     => true,                  // Enable/disable recording
    'connection'  => null,                  // Database connection (null = default)
    'retention'   => 168,                   // Hours to keep records (168 = 7 days)
    'middleware'  => ['web', Authorize::class],
    'redact_keys' => [                      // Keys redacted from stored payloads
        'password', 'token', 'secret', 'api_key', 'authorization',
    ],
];
```

## Authorization

By default, the dashboard is only accessible in the `local` environment. For production, define a gate:

```php
// config/govel-monitor.php
'gate' => function ($request) {
    return in_array($request->user()?->email, [
        'admin@example.com',
    ]);
},
```

The gate accepts any `callable` — closures, invokable classes, or `[Class::class, 'method']` arrays.

Unauthorized access attempts are logged with the request IP and path.

## How It Works

Govel Monitor decorates the `GoManager` singleton with a `RecordingManager` that logs every `run()`, `dispatch()`, and `queue()` call to the database. No changes to your application code are needed — just install the package and task executions are automatically recorded.

```
Govel::run(Task::class, $payload)
  → RecordingManager (records execution)
    → GoManager (executes task)
      → Result (returned to caller)
```

## Payload Redaction

Sensitive data is automatically redacted before being stored in the database. Any key matching `redact_keys` in the config will have its value replaced with `********`:

```php
// Payload sent to task:
['path' => '/tmp/img.jpg', 'api_key' => 'sk-live-xxx']

// What gets stored:
['path' => '/tmp/img.jpg', 'api_key' => '********']
```

Redaction applies recursively to nested arrays in both payloads and outputs. Customize the redacted keys in your config:

```php
'redact_keys' => ['password', 'token', 'secret', 'api_key', 'authorization', 'ssn'],
```

## Dashboard

The dashboard is a Vue.js SPA with three views:

| View | Route | Description |
|---|---|---|
| **Dashboard** | `/` | Stats cards, task breakdown table, recent executions (auto-refreshes every 10s) |
| **Executions** | `/executions` | Paginated list with filters for task, status, and driver |
| **Detail** | `/executions/:id` | Full payload, output, error, and timing for a single execution |

## API Endpoints

All data is served via JSON APIs under your configured path prefix:

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/stats` | Dashboard statistics (24h totals, success rate, avg/p95 duration) |
| `GET` | `/api/executions` | Paginated execution list with `?task=`, `?status=`, `?driver=`, `?per_page=` filters |
| `GET` | `/api/executions/{id}` | Single execution detail |
| `GET` | `/api/filters` | Available filter options (task names, driver names) |
| `DELETE` | `/api/purge` | Purge records older than the configured retention period |

## Security

Govel Monitor has been audited against the OWASP Top 10:

- **Mass assignment protection** — explicit `$fillable` whitelist on the model
- **Input validation** — `per_page` clamped to 1-100, route IDs constrained to integers
- **Security headers** — `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `Referrer-Policy: no-referrer`
- **Payload redaction** — sensitive keys automatically replaced before database storage
- **Audit logging** — auth failures and purge operations logged with IP addresses
- **Authorization** — environment-gated access with customizable callable gate

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
