<?php

namespace Administrators\Commands;

use Project\Common\Administrators\Role;
use Project\Common\Entity\Hydrator\Hydrator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Tests\Unit\Modules\Helpers\AdminFactory;
use Project\Modules\Administrators\Commands\UpdateAdminCommand;
use Project\Modules\Administrators\Repository\MemoryAdminRepository;
use Project\Modules\Administrators\Commands\Handlers\UpdateAdminHandler;

class UpdateAdminHandlerTest extends \PHPUnit\Framework\TestCase
{
    use AdminFactory;

    private EventDispatcherInterface $dispatcher;

    protected function setUp(): void
    {
        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->getMock();
        $this->dispatcher->expects($this->exactly(3)) // Login updated, password updated, roles updated
            ->method('dispatch');
        parent::setUp();
    }

    public function testCreate()
    {
        $initial = $this->generateAdmin();
        $repository = new MemoryAdminRepository(new Hydrator);
        $repository->add($initial);

        $command = new UpdateAdminCommand(
            $initial->getId()->getId(),
            $name = 'Updated admin name',
            $login = 'Updated admin login',
            'Updated admin password',
            [Role::MANAGER->value],
        );
        $handler = new UpdateAdminHandler($repository);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);
        $updated = $repository->get($initial->getId());

        $this->assertNotSame($initial->getName(), $updated->getName());
        $this->assertNotSame($initial->getLogin(), $updated->getLogin());
        $this->assertNotSame($initial->getRoles(), $updated->getRoles());

        $this->assertSame($updated->getName(), $name);
        $this->assertSame($updated->getLogin(), $login);
        $this->assertSame($updated->getRoles(), [Role::MANAGER]);
    }
}