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

- **Live Dashboard** — total executions, success rates, avg/p95 durations at a glance
- **Execution History** — searchable, filterable list of every Go task execution
- **Task Breakdown** — per-task metrics (volume, success rate, avg duration)
- **Detail View** — full payload, output, and error for each execution
- **Auto-Recording** — transparently hooks into GoManager, zero code changes
- **Data Retention** — configurable auto-purge of old records
- **Authorization** — local-only by default, customizable gate for production
- **Dark Theme** — clean, modern UI with no frontend build step

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
    'path'       => 'govel-monitor',       // Dashboard URL prefix
    'enabled'    => true,                   // Enable/disable recording
    'connection' => null,                   // Database connection (null = default)
    'retention'  => 168,                    // Hours to keep records (168 = 7 days)
    'middleware' => ['web', Authorize::class],
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

## How It Works

Govel Monitor decorates the `GoManager` singleton with a `RecordingManager` that logs every `run()`, `dispatch()`, and `queue()` call to the database. No changes to your application code are needed — just install the package and task executions are automatically recorded.

```
Govel::run(Task::class, $payload)
  → RecordingManager (records execution)
    → GoManager (executes task)
      → Result (returned to caller)
```

## Dashboard Views

| View | Description |
|---|---|
| **Dashboard** | Stats cards, task breakdown table, recent executions |
| **Executions** | Full paginated list with filters (task, status, driver) |
| **Detail** | Payload, output, error, and timing for a single execution |

## Customizing Views

```bash
php artisan vendor:publish --tag=govel-monitor-views
```

Views are published to `resources/views/vendor/govel-monitor/`.

## API Endpoint

```
GET /govel-monitor/api/stats
```

Returns JSON stats (total, success, failed, pending, avg/p95 duration, tasks by name).

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
