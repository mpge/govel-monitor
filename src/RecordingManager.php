<?php

namespace Mpge\GovelMonitor;

use Mpge\Govel\Contracts\Driver;
use Mpge\Govel\Contracts\Task;
use Mpge\Govel\DTO\Result;
use Mpge\Govel\Queue\PendingGovelDispatch;
use Mpge\Govel\Services\GoManager;
use Mpge\GovelMonitor\Recorders\TaskRecorder;

/**
 * Decorates GoManager to record all task executions.
 */
class RecordingManager extends GoManager
{
    public function __construct(
        protected GoManager $inner,
        protected TaskRecorder $recorder,
    ) {}

    public function run(string|Task $task, array $payload = []): Result
    {
        $taskName = $this->resolveTaskName($task);
        $driver = $this->inner->getDefaultDriver();

        $result = $this->inner->run($task, $payload);

        $this->recorder->record($taskName, $driver, 'sync', $payload, $result);

        return $result;
    }

    public function dispatch(string|Task $task, array $payload = []): void
    {
        $taskName = $this->resolveTaskName($task);
        $driver = $this->inner->getDefaultDriver();

        $this->inner->dispatch($task, $payload);

        $this->recorder->recordDispatch($taskName, $driver, 'async', $payload);
    }

    public function queue(string|Task $task, array $payload = []): PendingGovelDispatch
    {
        $taskName = $this->resolveTaskName($task);

        $this->recorder->recordDispatch($taskName, 'queue', 'queued', $payload);

        return $this->inner->queue($task, $payload);
    }

    public function driver(?string $name = null): Driver
    {
        return $this->inner->driver($name);
    }

    public function getDefaultDriver(): string
    {
        return $this->inner->getDefaultDriver();
    }

    public function extend(string $name, Driver $driver): static
    {
        $this->inner->extend($name, $driver);

        return $this;
    }

    public function resolve(string|Task $task): Task
    {
        return $this->inner->resolve($task);
    }

    protected function resolveTaskName(string|Task $task): string
    {
        if ($task instanceof Task) {
            return $task->name();
        }

        return $task;
    }
}
