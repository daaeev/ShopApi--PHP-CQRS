<?php

namespace Project\Tests\Laravel\Middleware;

use Illuminate\Http\Request;
use Project\Common\Administrators\Role;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Project\Modules\Administrators\Entity\Admin;
use Project\Infrastructure\Laravel\Middleware\HasAccess;
use Project\Modules\Administrators\AuthManager\AuthManagerInterface;

class HasAccessTest extends \PHPUnit\Framework\TestCase
{
    private readonly AuthManagerInterface $authManager;
    private readonly HasAccess $middleware;

    private readonly Request $request;
    private readonly Response $response;
    private readonly \Closure $next;

    protected function setUp(): void
    {
        $this->authManager = $this->getMockBuilder(AuthManagerInterface::class)->getMock();
        $this->middleware = new HasAccess($this->authManager);

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
        $logged = $this->getMockBuilder(Admin::class)
            ->disableOriginalConstructor()
            ->getMock();

        $logged->expects($this->once())
            ->method('hasAccess')
            ->with(Role::ADMIN)
            ->willReturn(true);

        $this->authManager->expects($this->once())
            ->method('logged')
            ->willReturn($logged);

        $response = $this->middleware->handle($this->request, $this->next, Role::ADMIN->value);
        $this->assertSame($this->response, $response);
    }

    public function testHasAccessIfUnauthenticated()
    {
        $this->authManager->expects($this->once())
            ->method('logged')
            ->willReturn(null);

        $this->expectException(AuthenticationException::class);
        $this->middleware->handle($this->request, $this->next, Role::ADMIN->value);
    }

    public function testHasAccessIfAdminDoesNotHasAccess()
    {
        $logged = $this->getMockBuilder(Admin::class)
            ->disableOriginalConstructor()
            ->getMock();

        $logged->expects($this->once())
            ->method('hasAccess')
            ->with(Role::ADMIN)
            ->willReturn(false);

        $this->authManager->expects($this->once())
            ->method('logged')
            ->willReturn($logged);

        $this->expectException(AuthenticationException::class);
        $this->middleware->handle($this->request, $this->next, Role::ADMIN->value);
    }
}