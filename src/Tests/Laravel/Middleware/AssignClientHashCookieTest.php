<?php

namespace Project\Tests\Laravel\Middleware;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Project\Common\Services\Cookie\CookieManagerInterface;
use Project\Infrastructure\Laravel\Middleware\AssignClientHashCookie;

class AssignClientHashCookieTest extends \PHPUnit\Framework\TestCase
{
    private readonly CookieManagerInterface $cookie;
    private readonly string $cookieName;
    private readonly int $cookieLifeTimeInMinutes;
    private readonly int $hashLength;
    private readonly AssignClientHashCookie $middleware;

    private readonly Request $request;
    private readonly Response $response;
    private readonly \Closure $next;

    protected function setUp(): void
    {
        $this->cookie = $this->getMockBuilder(CookieManagerInterface::class)->getMock();
        $this->cookieName = uniqid();
        $this->cookieLifeTimeInMinutes = random_int(1, 100);
        $this->hashLength = random_int(10, 20);
        $this->middleware = new AssignClientHashCookie(
            $this->cookie,
            $this->cookieName,
            $this->cookieLifeTimeInMinutes,
            $this->hashLength,
        );

        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->next = fn (Request $request) => $this->response;
    }

    public function testAssignClientHash()
    {
        $this->cookie->expects($this->once())
            ->method('get')
            ->with($this->cookieName)
            ->willReturn(null);

        $this->cookie->expects($this->once())
            ->method('add')
            ->with(
                $this->cookieName,
                $this->callback(fn (string $hash) => $this->hashLength === mb_strlen($hash)),
                $this->cookieLifeTimeInMinutes,
            );

        $response = $this->middleware->handle($this->request, $this->next);
        $this->assertSame($response, $this->response);
    }

    public function testAssignClientHashIfCookieAlreadyExists()
    {
        $this->cookie->expects($this->exactly(2))
            ->method('get')
            ->with($this->cookieName)
            ->willReturn(Str::random($this->hashLength));

        $response = $this->middleware->handle($this->request, $this->next);
        $this->assertSame($response, $this->response);
    }

    public function testAssignClientHashIfCurrentHashNotValid()
    {
        $this->cookie->expects($this->exactly(2))
            ->method('get')
            ->with($this->cookieName)
            ->willReturn(Str::random((int) ($this->hashLength / 2)));

        $this->cookie->expects($this->once())
            ->method('add')
            ->with(
                $this->cookieName,
                $this->callback(fn (string $generatedHash) => mb_strlen($generatedHash) === $this->hashLength),
                $this->cookieLifeTimeInMinutes,
            );

        $response = $this->middleware->handle($this->request, $this->next);
        $this->assertSame($response, $this->response);
    }
}