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
            'payload' => $this->redact($payload),
            'success' => $result->success,
            'output' => is_array($result->output) ? $this->redact($result->output) : $result->output,
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
            'payload' => $this->redact($payload),
            'success' => null,
            'output' => null,
            'error' => null,
            'duration' => null,
            'executed_at' => now(),
        ]);
    }

    private function redact(array $data): array
    {
        $keys = config('govel-monitor.redact_keys', [
            'password', 'token', 'secret', 'api_key', 'authorization',
        ]);

        foreach ($data as $key => $value) {
            if (is_string($key) && in_array(strtolower($key), array_map('strtolower', $keys), true)) {
                $data[$key] = '********';
            } elseif (is_array($value)) {
                $data[$key] = $this->redact($value);
            }
        }

        return $data;
    }
}
