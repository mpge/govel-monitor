<?php

namespace Mpge\GovelMonitor\Tests\Unit;

use Mpge\GovelMonitor\Http\Middleware\Authorize;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AuthorizeMiddlewareTest extends TestCase
{
    #[Test]
    public function it_can_be_instantiated(): void
    {
        $middleware = new Authorize();
        $this->assertInstanceOf(Authorize::class, $middleware);
    }
}
