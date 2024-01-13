<?php

namespace Project\Tests\Unit\Modules\Administrators\Commands;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Tests\Unit\Modules\Helpers\AdminFactory;
use Project\Modules\Administrators\Commands\DeleteAdminCommand;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Administrators\Repository\AdminsMemoryRepository;
use Project\Modules\Administrators\Commands\Handlers\DeleteAdminHandler;

class DeleteAdminHandlerTest extends \PHPUnit\Framework\TestCase
{
    use AdminFactory;

    private MessageBusInterface $dispatcher;

    protected function setUp(): void
    {
        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();

        $this->dispatcher->expects($this->once()) // Admin deleted
            ->method('dispatch');

        parent::setUp();
    }

    public function testCreate()
    {
        $initial = $this->generateAdmin();
        $repository = new AdminsMemoryRepository(new Hydrator);
        $repository->add($initial);

        $command = new DeleteAdminCommand($initial->getId()->getId());
        $handler = new DeleteAdminHandler($repository);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);

        $this->expectException(NotFoundException::class);
        $repository->get($initial->getId());
    }
}