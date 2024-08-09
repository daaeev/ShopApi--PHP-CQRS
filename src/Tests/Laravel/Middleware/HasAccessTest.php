<?php

namespace Project\Tests\Laravel\Middleware;

use Illuminate\Http\Request;
use Project\Common\Administrators\Role;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Project\Common\Services\Environment\Administrator;
use Project\Infrastructure\Laravel\Middleware\HasAccess;
use Project\Common\Services\Environment\EnvironmentInterface;

class HasAccessTest extends \PHPUnit\Framework\TestCase
{
    private readonly EnvironmentInterface $environment;
    private readonly HasAccess $middleware;

    private readonly Administrator $administrator;
    private readonly Request $request;
    private readonly Response $response;
    private readonly \Closure $next;

    protected function setUp(): void
    {
        $this->environment = $this->getMockBuilder(EnvironmentInterface::class)->getMock();
        $this->middleware = new HasAccess($this->environment);

        $this->administrator = $this->getMockBuilder(Administrator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->next = fn (Request $request) => $this->response;
    }

    public function testHasAccess()
    {
        $this->environment->expects($this->once())
            ->method('getAdministrator')
            ->willReturn($this->administrator);

        $this->administrator->expects($this->once())
            ->method('hasAccess')
            ->with(Role::ADMIN)
            ->willReturn(true);

        $response = $this->middleware->handle($this->request, $this->next, Role::ADMIN->value);
        $this->assertSame($this->response, $response);
    }

    public function testHasAccessIfUnauthenticated()
    {
        $this->environment->expects($this->once())
            ->method('getAdministrator')
            ->willReturn(null);

        $this->expectException(AuthenticationException::class);
        $this->middleware->handle($this->request, $this->next, Role::ADMIN->value);
    }

    public function testHasAccessIfAdminDoesNotHasAccess()
    {
        $this->environment->expects($this->once())
            ->method('getAdministrator')
            ->willReturn($this->administrator);

        $this->administrator->expects($this->once())
            ->method('hasAccess')
            ->with(Role::ADMIN)
            ->willReturn(false);

        $this->expectException(AuthenticationException::class);
        $this->middleware->handle($this->request, $this->next, Role::ADMIN->value);
    }
}