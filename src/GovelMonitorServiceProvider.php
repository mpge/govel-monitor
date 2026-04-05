<?php

namespace Mpge\GovelMonitor;

use Mpge\GovelMonitor\Http\Middleware\Authorize;
use Mpge\GovelMonitor\Recorders\TaskRecorder;
use Mpge\Govel\Services\GoManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class GovelMonitorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/govel-monitor.php', 'govel-monitor');

        $this->app->singleton(TaskRecorder::class);
    }

    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerMigrations();
        $this->registerViews();
        $this->registerRecorder();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/govel-monitor.php' => config_path('govel-monitor.php'),
            ], 'govel-monitor-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/govel-monitor'),
            ], 'govel-monitor-views');
        }
    }

    protected function registerRoutes(): void
    {
        Route::group([
            'prefix' => config('govel-monitor.path', 'govel-monitor'),
            'middleware' => config('govel-monitor.middleware', ['web']),
            'namespace' => 'Mpge\\GovelMonitor\\Http\\Controllers',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'govel-monitor');
    }

    protected function registerRecorder(): void
    {
        if (! config('govel-monitor.enabled', true)) {
            return;
        }

        // Decorate the GoManager to record task executions
        $this->app->extend(GoManager::class, function (GoManager $manager) {
            return new RecordingManager($manager, $this->app->make(TaskRecorder::class));
        });
    }
}
