<?php

namespace Project\Tests\Unit\Modules\Administrators\Commands;

use Project\Modules\Administrators\Entity\Admin;
use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Modules\Administrators\Commands\AuthorizeCommand;
use Project\Modules\Administrators\AuthManager\AuthManagerInterface;
use Project\Modules\Administrators\Commands\Handlers\AuthorizeHandler;

class AuthorizeHandlerTest extends \PHPUnit\Framework\TestCase
{
    private AuthManagerInterface $auth;
    private EventDispatcherInterface $dispatcher;

    protected function setUp(): void
    {
        $this->auth = $this->getMockBuilder(AuthManagerInterface::class)
            ->getMock();
        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->getMock();
        $this->dispatcher->expects($this->never())
            ->method('dispatch');
        parent::setUp();
    }

    public function testAuthorize()
    {
        $login = 'login';
        $password = 'password';

        $this->auth->expects($this->once())
            ->method('logged')
            ->willReturn(null);

        $this->auth->expects($this->once())
            ->method('login')
            ->with($login, $password);

        $command = new AuthorizeCommand($login, $password);
        $handler = new AuthorizeHandler($this->auth);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);
    }

    public function testAuthorizeIfAuthorized()
    {
        $loggedAdmin = $this->getMockBuilder(Admin::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->auth->expects($this->once())
            ->method('logged')
            ->willReturn($loggedAdmin);

        $command = new AuthorizeCommand('login', 'password');
        $handler = new AuthorizeHandler($this->auth);
        $this->expectException(\DomainException::class);
        call_user_func($handler, $command);
    }
}