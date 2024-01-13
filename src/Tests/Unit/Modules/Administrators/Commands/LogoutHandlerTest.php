<?php

namespace Project\Tests\Unit\Modules\Administrators\Commands;

use Project\Modules\Administrators\Entity\Admin;
use Project\Modules\Administrators\Commands\LogoutCommand;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Administrators\Commands\Handlers\LogoutHandler;
use Project\Modules\Administrators\AuthManager\AuthManagerInterface;

class LogoutHandlerTest extends \PHPUnit\Framework\TestCase
{
    private AuthManagerInterface $auth;
    private MessageBusInterface $dispatcher;

    protected function setUp(): void
    {
        $this->auth = $this->getMockBuilder(AuthManagerInterface::class)
            ->getMock();

        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)
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