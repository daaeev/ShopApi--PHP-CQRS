<?php

namespace Project\Tests\Unit\Modules\Administrators\Commands;

use Project\Modules\Administrators\Entity\Admin;
use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Modules\Administrators\Commands\LogoutCommand;
use Project\Modules\Administrators\Commands\Handlers\LogoutHandler;
use Project\Modules\Administrators\AuthManager\AuthManagerInterface;

class LogoutHandlerTest extends \PHPUnit\Framework\TestCase
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

    public function testLogout()
    {
        $loggedAdmin = $this->getMockBuilder(Admin::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->auth->expects($this->once())
            ->method('logged')
            ->willReturn($loggedAdmin);

        $this->auth->expects($this->once())
            ->method('logout');

        $command = new LogoutCommand();
        $handler = new LogoutHandler($this->auth);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);
    }

    public function testLogoutIfDoesNotAuthorized()
    {
        $this->auth->expects($this->once())
            ->method('logged')
            ->willReturn(null);

        $command = new LogoutCommand();
        $handler = new LogoutHandler($this->auth);
        $this->expectException(\DomainException::class);
        call_user_func($handler, $command);
    }
}