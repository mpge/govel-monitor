<?php

namespace Mpge\GovelMonitor\Tests\Unit;

use Mpge\Govel\Contracts\Driver;
use Mpge\Govel\Contracts\Task;
use Mpge\Govel\DTO\Result;
use Mpge\Govel\Services\GoManager;
use Mpge\GovelMonitor\Recorders\TaskRecorder;
use Mpge\GovelMonitor\RecordingManager;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RecordingManagerTest extends TestCase
{
    private function makeTask(string $name = 'test-task'): Task
    {
        return new class($name) implements Task {
            public function __construct(private string $n) {}
            public function name(): string { return $this->n; }
        };
    }

    #[Test]
    public function it_delegates_run_and_records(): void
    {
        $expected = new Result(true, ['done' => true], null, 5.0);
        $task = $this->makeTask();

        $inner = $this->createMock(GoManager::class);
        $inner->expects($this->once())
            ->method('run')
            ->with($task, ['key' => 'val'])
            ->willReturn($expected);
        $inner->method('getDefaultDriver')->willReturn('process');

        $recorder = $this->createMock(TaskRecorder::class);
        $recorder->expects($this->once())
            ->method('record')
            ->with('test-task', 'process', 'sync', ['key' => 'val'], $expected);

        $manager = new RecordingManager($inner, $recorder);
        $result = $manager->run($task, ['key' => 'val']);

        $this->assertSame($expected, $result);
    }

    #[Test]
    public function it_delegates_dispatch_and_records(): void
    {
        $task = $this->makeTask('dispatch-task');

        $inner = $this->createMock(GoManager::class);
        $inner->expects($this->once())
            ->method('dispatch')
            ->with($task, ['path' => '/tmp']);
        $inner->method('getDefaultDriver')->willReturn('grpc');

        $recorder = $this->createMock(TaskRecorder::class);
        $recorder->expects($this->once())
            ->method('recordDispatch')
            ->with('dispatch-task', 'grpc', 'async', ['path' => '/tmp']);

        $manager = new RecordingManager($inner, $recorder);
        $manager->dispatch($task, ['path' => '/tmp']);
    }

    #[Test]
    public function it_delegates_driver_calls(): void
    {
        $driver = $this->createMock(Driver::class);

        $inner = $this->createMock(GoManager::class);
        $inner->expects($this->once())
            ->method('driver')
            ->with('grpc')
            ->willReturn($driver);

        $recorder = $this->createMock(TaskRecorder::class);

        $manager = new RecordingManager($inner, $recorder);
        $this->assertSame($driver, $manager->driver('grpc'));
    }

    #[Test]
    public function it_delegates_extend(): void
    {
        $driver = $this->createMock(Driver::class);

        $inner = $this->createMock(GoManager::class);
        $inner->expects($this->once())
            ->method('extend')
            ->with('custom', $driver);

        $recorder = $this->createMock(TaskRecorder::class);

        $manager = new RecordingManager($inner, $recorder);
        $result = $manager->extend('custom', $driver);

        $this->assertSame($manager, $result);
    }

    #[Test]
    public function it_resolves_task_name_from_string(): void
    {
        $expected = new Result(true, [], null, 1.0);

        $inner = $this->createMock(GoManager::class);
        $inner->expects($this->once())
            ->method('run')
            ->willReturn($expected);
        $inner->method('getDefaultDriver')->willReturn('process');

        $recorder = $this->createMock(TaskRecorder::class);
        $recorder->expects($this->once())
            ->method('record')
            ->with('App\\Tasks\\MyTask', 'process', 'sync', [], $expected);

        $manager = new RecordingManager($inner, $recorder);
        $manager->run('App\\Tasks\\MyTask');
    }
}
