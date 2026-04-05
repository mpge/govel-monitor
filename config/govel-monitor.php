<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | The URI prefix for the Govel Monitor dashboard.
    |
    */

    'path' => env('GOVEL_MONITOR_PATH', 'govel-monitor'),

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware applied to the monitor dashboard routes.
    |
    */

    'middleware' => ['web', Mpge\GovelMonitor\Http\Middleware\Authorize::class],

    /*
    |--------------------------------------------------------------------------
    | Recording
    |--------------------------------------------------------------------------
    |
    | Enable or disable task execution recording. When disabled, the
    | dashboard will still show historical data but won't record new entries.
    |
    */

    'enabled' => env('GOVEL_MONITOR_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | The database connection used to store task execution records.
    | Set to null to use the default connection.
    |
    */

    'connection' => env('GOVEL_MONITOR_DB_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | Data Retention
    |--------------------------------------------------------------------------
    |
    | Number of hours to retain task execution records.
    | Set to null to keep records indefinitely.
    |
    */

    'retention' => env('GOVEL_MONITOR_RETENTION', 168), // 7 days

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    |
    | Define who can access the monitor dashboard. By default, it is
    | only accessible in the local environment. Override the gate
    | callback in your AuthServiceProvider for production.
    |
    */

    'gate' => null, // Closure or null (defaults to local-only)

];
