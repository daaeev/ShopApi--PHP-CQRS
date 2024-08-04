<?php

namespace Project\Tests\Laravel\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Project\Tests\Laravel\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Project\Common\Services\Environment\Language;
use Project\Infrastructure\Laravel\Middleware\AssignQueryLocale;

class AssignQueryLocaleTest extends TestCase
{
    private readonly AssignQueryLocale $middleware;

    private readonly Request $request;
    private readonly Response $response;
    private readonly \Closure $next;

    protected function setUp(): void
    {
        $this->middleware = new AssignQueryLocale;

        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->next = fn (Request $request) => $this->response;
        parent::setUp();
    }

    public function testSetLocale()
    {
        $this->request->expects($this->once())
            ->method('query')
            ->with('language')
            ->willReturn(Language::EN->value);

        App::shouldReceive('setLocale')
            ->once()
            ->with(Language::EN->value);

        $response = $this->middleware->handle($this->request, $this->next);
        $this->assertSame($this->response, $response);
    }

    public function testSetLocaleIfLanguageParamIsNull()
    {
        $this->request->expects($this->once())
            ->method('query')
            ->with('language')
            ->willReturn(null);

        App::shouldReceive('setLocale')
            ->once()
            ->with(Language::default()->value);

        $response = $this->middleware->handle($this->request, $this->next);
        $this->assertSame($this->response, $response);
    }

    public function testSetLocaleIfLanguageParamHasUndefinedLocale()
    {
        $this->request->expects($this->once())
            ->method('query')
            ->with('language')
            ->willReturn('undefined');

        App::shouldReceive('setLocale')
            ->once()
            ->with(Language::default()->value);

        $response = $this->middleware->handle($this->request, $this->next);
        $this->assertSame($this->response, $response);
    }
}