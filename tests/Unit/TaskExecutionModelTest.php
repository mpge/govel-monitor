<?php

namespace Mpge\GovelMonitor\Tests\Unit;

use Mpge\GovelMonitor\Models\TaskExecution;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

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

    #[Test]
    public function get_connection_name_returns_config_value_when_set(): void
    {
        config()->set('govel-monitor.connection', 'mysql_monitor');

        $model = new TaskExecution();
        $this->assertSame('mysql_monitor', $model->getConnectionName());
    }

    #[Test]
    public function get_connection_name_returns_default_when_config_is_null(): void
    {
        config()->set('govel-monitor.connection', null);

        $model = new TaskExecution();

        // When config is null, it falls back to parent::getConnectionName()
        // which returns null (the model's default, meaning "use app default")
        $this->assertNull($model->getConnectionName());
    }
}
