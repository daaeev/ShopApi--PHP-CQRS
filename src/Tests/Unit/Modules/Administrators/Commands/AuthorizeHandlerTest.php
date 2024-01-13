<?php

namespace Project\Tests\Unit\Modules\Administrators\Commands;

use Project\Modules\Administrators\Entity\Admin;
use Project\Tests\Unit\Modules\Helpers\AdminFactory;
use Project\Modules\Administrators\Commands\AuthorizeCommand;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Administrators\AuthManager\AuthManagerInterface;
use Project\Modules\Administrators\Commands\Handlers\AuthorizeHandler;

class AuthorizeHandlerTest extends \PHPUnit\Framework\TestCase
{
    use AdminFactory;

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

    public function testAuthorize()
    {
        $this->auth->expects($this->once())
            ->method('logged')
            ->willReturn(null);

        $this->auth->expects($this->once())
            ->method('login')
            ->with(
                $this->correctAdminLogin,
                $this->correctAdminPassword
            );

        $command = new AuthorizeCommand(
            $this->correctAdminLogin,
            $this->correctAdminPassword
        );
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

        $command = new AuthorizeCommand(
            $this->correctAdminLogin,
            $this->correctAdminPassword
        );
        $handler = new AuthorizeHandler($this->auth);
        $this->expectException(\DomainException::class);
        call_user_func($handler, $command);
    }
}