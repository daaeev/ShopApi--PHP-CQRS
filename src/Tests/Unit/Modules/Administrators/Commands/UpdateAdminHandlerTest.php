<?php

namespace Project\Tests\Unit\Modules\Administrators\Commands;

use Project\Common\Administrators\Role;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\IdentityMap;
use Project\Tests\Unit\Modules\Helpers\AdminFactory;
use Project\Modules\Administrators\Commands\UpdateAdminCommand;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Administrators\Repository\AdminsMemoryRepository;
use Project\Modules\Administrators\Commands\Handlers\UpdateAdminHandler;

class UpdateAdminHandlerTest extends \PHPUnit\Framework\TestCase
{
    use AdminFactory;

    private MessageBusInterface $dispatcher;

    protected function setUp(): void
    {
        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();

        $this->dispatcher->expects($this->exactly(3)) // Login updated, password updated, roles updated
            ->method('dispatch');

        parent::setUp();
    }

    public function testUpdate()
    {
        $initial = $this->generateAdmin();
        $repository = new AdminsMemoryRepository(new Hydrator, new IdentityMap);
        $repository->add($initial);

        $command = new UpdateAdminCommand(
            $initial->getId()->getId(),
            $name = 'Updated admin name',
            $this->correctAdminLogin,
            $this->correctAdminPassword,
            [Role::MANAGER->value],
        );

        $handler = new UpdateAdminHandler($repository);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);

        $updated = $repository->get($initial->getId());
        $this->assertSame($initial, $updated);
        $this->assertSame($updated->getName(), $name);
        $this->assertSame($updated->getLogin(), $this->correctAdminLogin);
        $this->assertSame($updated->getPassword(), $this->correctAdminPassword);
        $this->assertSame($updated->getRoles(), [Role::MANAGER]);
    }
}
