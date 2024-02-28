<?php

namespace Project\Tests\Unit\Modules\Administrators\Commands;

use Project\Common\Administrators\Role;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\IdentityMap;
use Project\Modules\Administrators\Entity\AdminId;
use Project\Tests\Unit\Modules\Helpers\AdminFactory;
use Project\Modules\Administrators\Commands\CreateAdminCommand;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Administrators\Repository\AdminsMemoryRepository;
use Project\Modules\Administrators\Commands\Handlers\CreateAdminHandler;

class CreateAdminHandlerTest extends \PHPUnit\Framework\TestCase
{
    use AdminFactory;

    private MessageBusInterface $dispatcher;

    protected function setUp(): void
    {
        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();

        $this->dispatcher->expects($this->once()) // Admin created
            ->method('dispatch');

        parent::setUp();
    }

    public function testCreate()
    {
        $repository = new AdminsMemoryRepository(new Hydrator, new IdentityMap);
        $command = new CreateAdminCommand(
            $name = 'admin',
            $login = $this->correctAdminLogin,
            $this->correctAdminPassword,
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
