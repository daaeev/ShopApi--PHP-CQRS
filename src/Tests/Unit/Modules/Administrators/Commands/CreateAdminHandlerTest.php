<?php

namespace Administrators\Commands;

use Project\Common\Administrators\Role;
use Project\Common\Entity\Hydrator\Hydrator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Modules\Administrators\Entity\AdminId;
use Project\Modules\Administrators\Commands\CreateAdminCommand;
use Project\Modules\Administrators\Repository\MemoryAdminRepository;
use Project\Modules\Administrators\Commands\Handlers\CreateAdminHandler;

class CreateAdminHandlerTest extends \PHPUnit\Framework\TestCase
{
    private EventDispatcherInterface $dispatcher;

    protected function setUp(): void
    {
        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->getMock();
        $this->dispatcher->expects($this->once()) // Admin created
            ->method('dispatch');
        parent::setUp();
    }

    public function testCreate()
    {
        $repository = new MemoryAdminRepository(new Hydrator);
        $command = new CreateAdminCommand(
            $name = 'Admin name',
            $login = 'Admin login',
            'Admin password',
            [Role::ADMIN->value],
        );
        $handler = new CreateAdminHandler($repository);
        $handler->setDispatcher($this->dispatcher);
        $adminId = call_user_func($handler, $command);
        $admin = $repository->get(new AdminId($adminId));

        $this->assertSame($admin->getName(), $name);
        $this->assertSame($admin->getLogin(), $login);
        $this->assertSame($admin->getRoles(), [Role::ADMIN]);
    }
}