<?php

namespace Mpge\GovelMonitor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TaskExecution extends Model
{
    public $timestamps = true;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'output' => 'array',
            'success' => 'boolean',
            'duration' => 'float',
            'executed_at' => 'datetime',
        ];
    }

    public function getTable(): string
    {
        return 'govel_task_executions';
    }

    public function getConnectionName(): ?string
    {
        return config('govel-monitor.connection') ?? parent::getConnectionName();
    }

    // Scopes

    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->where('success', true);
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('success', false);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('success');
    }

    public function scopeForTask(Builder $query, string $task): Builder
    {
        return $query->where('task', $task);
    }

    public function scopeForDriver(Builder $query, string $driver): Builder
    {
        return $query->where('driver', $driver);
    }

    public function scopeRecent(Builder $query, int $hours = 24): Builder
    {
        return $query->where('executed_at', '>=', now()->subHours($hours));
    }
}
