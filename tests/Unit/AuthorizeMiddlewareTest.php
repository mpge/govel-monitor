<?php

namespace Mpge\GovelMonitor\Tests\Unit;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mpge\GovelMonitor\Http\Middleware\Authorize;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthorizeMiddlewareTest extends TestCase
{
    private function passThrough(): \Closure
    {
        return fn (Request $request) => new Response('OK');
    }

    #[Test]
    public function handle_allows_request_in_local_environment(): void
    {
        // Default gate is null, so it falls back to environment check
        config()->set('govel-monitor.gate', null);

        // Orchestra Testbench defaults to 'testing' environment, override to 'local'
        $this->app['env'] = 'local';

        $middleware = new Authorize();
        $request = Request::create('/govel-monitor');

        $response = $middleware->handle($request, $this->passThrough());

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('OK', $response->getContent());
    }

    #[Test]
    public function handle_blocks_request_in_production_environment(): void
    {
        config()->set('govel-monitor.gate', null);

        $this->app['env'] = 'production';

        $middleware = new Authorize();
        $request = Request::create('/govel-monitor');

        $this->expectException(HttpException::class);

        $middleware->handle($request, $this->passThrough());
    }

    #[Test]
    public function handle_blocks_request_in_testing_environment(): void
    {
        config()->set('govel-monitor.gate', null);

        // 'testing' is not 'local', so it should be blocked
        $this->app['env'] = 'testing';

        $middleware = new Authorize();
        $request = Request::create('/govel-monitor');

        $this->expectException(HttpException::class);

        $middleware->handle($request, $this->passThrough());
    }

    #[Test]
    public function handle_uses_custom_gate_closure_that_allows(): void
    {
        config()->set('govel-monitor.gate', function (Request $request) {
            return true;
        });

        // Even in production, a gate returning true should allow access
        $this->app['env'] = 'production';

        $middleware = new Authorize();
        $request = Request::create('/govel-monitor');

        $response = $middleware->handle($request, $this->passThrough());

        $this->assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function handle_uses_custom_gate_closure_that_denies(): void
    {
        config()->set('govel-monitor.gate', function (Request $request) {
            return false;
        });

        // Even in local, a gate returning false should block access
        $this->app['env'] = 'local';

        $middleware = new Authorize();
        $request = Request::create('/govel-monitor');

        $this->expectException(HttpException::class);

        $middleware->handle($request, $this->passThrough());
    }

    #[Test]
    public function handle_passes_request_to_custom_gate_closure(): void
    {
        $capturedRequest = null;

        config()->set('govel-monitor.gate', function (Request $request) use (&$capturedRequest) {
            $capturedRequest = $request;
            return true;
        });

        $middleware = new Authorize();
        $request = Request::create('/govel-monitor', 'GET', ['foo' => 'bar']);

        $middleware->handle($request, $this->passThrough());

        $this->assertSame($request, $capturedRequest);
    }
}
