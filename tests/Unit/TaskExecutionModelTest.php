<?php

namespace Mpge\GovelMonitor\Tests\Unit;

use Mpge\GovelMonitor\Models\TaskExecution;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TaskExecutionModelTest extends TestCase
{
    #[Test]
    public function it_uses_the_correct_table_name(): void
    {
        $model = new TaskExecution();
        $this->assertSame('govel_task_executions', $model->getTable());
    }

    #[Test]
    public function it_has_correct_casts(): void
    {
        $model = new TaskExecution();
        $casts = $model->getCasts();

        $this->assertSame('array', $casts['payload']);
        $this->assertSame('array', $casts['output']);
        $this->assertSame('boolean', $casts['success']);
        $this->assertSame('float', $casts['duration']);
        $this->assertSame('datetime', $casts['executed_at']);
    }

    #[Test]
    public function it_uses_timestamps(): void
    {
        $model = new TaskExecution();
        $this->assertTrue($model->usesTimestamps());
    }
}
