<?php

namespace Mpge\GovelMonitor\Recorders;

use Mpge\Govel\DTO\Result;
use Mpge\GovelMonitor\Models\TaskExecution;

class TaskRecorder
{
    public function record(
        string $task,
        string $driver,
        string $mode,
        array $payload,
        Result $result,
    ): TaskExecution {
        return TaskExecution::create([
            'task' => $task,
            'driver' => $driver,
            'mode' => $mode,
            'payload' => $payload,
            'success' => $result->success,
            'output' => $result->output,
            'error' => $result->error,
            'duration' => $result->duration,
            'executed_at' => now(),
        ]);
    }

    public function recordDispatch(
        string $task,
        string $driver,
        string $mode,
        array $payload,
    ): TaskExecution {
        return TaskExecution::create([
            'task' => $task,
            'driver' => $driver,
            'mode' => $mode,
            'payload' => $payload,
            'success' => null,
            'output' => null,
            'error' => null,
            'duration' => null,
            'executed_at' => now(),
        ]);
    }
}
