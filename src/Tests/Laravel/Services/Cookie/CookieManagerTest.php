<?php

namespace Project\Tests\Laravel\Services\Cookie;

use Project\Tests\Laravel\TestCase;
use Illuminate\Support\Facades\Cookie;
use Project\Common\Services\Cookie\CookieManagerInterface;
use Project\Infrastructure\Laravel\Services\CookieManager;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;

class CookieManagerTest extends TestCase
{
    private readonly SymfonyCookie $queuedCookie;
    private readonly \Mockery\MockInterface $cookieMock;
    private readonly CookieManagerInterface $cookieManager;

    protected function setUp(): void
    {
        $this->queuedCookie = $this->getMockBuilder(SymfonyCookie::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Swap the actual Cookie facade with the mock.
        // Cookie::get() method mock always return null in default mock
        parent::setUp();
        $this->cookieMock = \Mockery::mock('alias:' . Cookie::class);
        $this->app->instance(Cookie::class, $this->cookieMock);

        $this->cookieManager = new CookieManager;
    }

    public function testAdd()
    {
        $key = uniqid();
        $value = uniqid();
        $lifeTimeInMinutes = random_int(1, 100);
        $this->cookieMock->shouldReceive('queue')->once()->with($key, $value, $lifeTimeInMinutes);
        $this->cookieManager->add($key, $value, $lifeTimeInMinutes);
    }

    public function testGet()
    {
        $key = uniqid();
        $value = uniqid();
        $this->cookieMock->shouldReceive('queued')
            ->with($key)
            ->andReturnNull();

        $this->cookieMock->shouldReceive('get')
            ->once()
            ->with($key)
            ->andReturn($value);

        $this->assertSame($this->cookieManager->get($key), $value);
    }

    public function testGetIfCookieQueued()
    {
        $key = uniqid();
        $value = uniqid();
        $this->cookieMock->shouldReceive('queued')
            ->with($key)
            ->andReturn($this->queuedCookie);

        $this->queuedCookie->expects($this->once())
            ->method('getValue')
            ->willReturn($value);

        $this->assertSame($this->cookieManager->get($key), $value);
    }

    public function testGetIfCookieDoesNotExists()
    {
        $key = uniqid();
        $this->cookieMock->shouldReceive('queued')
            ->once()
            ->with($key)
            ->andReturnNull();

        $this->cookieMock->shouldReceive('get')
            ->once()
            ->with($key)
            ->andReturnNull();

        $this->assertNull($this->cookieManager->get($key));
    }
}