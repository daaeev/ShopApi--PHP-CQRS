<?php

namespace Project\Tests\Unit\Modules\Orders\Commands;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Shopping\Order\Entity\Order;
use Project\Common\Administrators\Administrator;
use Project\Modules\Shopping\Order\Entity\OrderId;
use Project\Modules\Shopping\Order\Entity\Manager;
use Project\Common\Environment\EnvironmentInterface;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Order\Commands\AttachManagerCommand;
use Project\Modules\Shopping\Order\Repository\OrdersRepositoryInterface;
use Project\Modules\Shopping\Order\Commands\Handlers\AttachManagerHandler;

class AttachManagerTest extends \PHPUnit\Framework\TestCase
{
    private readonly OrdersRepositoryInterface $orders;
    private readonly EnvironmentInterface $environment;
    private readonly MessageBusInterface $eventBus;

    private readonly Administrator $administrator;
    private readonly int $adminId;
    private readonly string $adminName;
    private readonly OrderId $orderId;
    private readonly Order $order;
    private readonly Event $orderUpdatedEvent;

    protected function setUp(): void
    {
        $this->hydrator = new Hydrator;

        $this->orders = $this->getMockBuilder(OrdersRepositoryInterface::class)->getMock();
        $this->environment = $this->getMockBuilder(EnvironmentInterface::class)->getMock();
        $this->eventBus = $this->getMockBuilder(MessageBusInterface::class)->getMock();

        $this->administrator = $this->getMockBuilder(Administrator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->adminId = random_int(1, 9999);
        $this->adminName = uniqid();
        $this->orderId = OrderId::random();
        $this->order = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderUpdatedEvent = $this->getMockBuilder(Event::class)->getMock();
    }

    public function testAttach()
    {
        $this->environment->expects($this->once())
            ->method('getAdministrator')
            ->willReturn($this->administrator);

        $this->orders->expects($this->once())
            ->method('get')
            ->with($this->orderId)
            ->willReturn($this->order);

        $this->mockAdministratorMethods();

        $this->order->expects($this->once())
            ->method('attachManager')
            ->with($this->callback(function (Manager $manager) {
                $this->assertSame($manager->getId()->getId(), $this->adminId);
                $this->assertSame($manager->getName(), $this->adminName);
                return true;
            }));

        $this->orders->expects($this->once())
            ->method('update')
            ->with($this->order);

        $this->order->expects($this->once())
            ->method('flushEvents')
            ->willReturn([$this->orderUpdatedEvent]);

        $this->eventBus->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (Event $event) {
                $this->assertSame($event, $this->orderUpdatedEvent);
                return true;
            }));

        $command = new AttachManagerCommand($this->orderId->getId());
        $handler = new AttachManagerHandler($this->orders, $this->environment);
        $handler->setDispatcher($this->eventBus);
        call_user_func($handler, $command);
    }

    private function mockAdministratorMethods()
    {
        $this->administrator->expects($this->once())
            ->method('getId')
            ->willReturn($this->adminId);

        $this->administrator->expects($this->once())
            ->method('getName')
            ->willReturn($this->adminName);
    }

    public function testAttachIfAdminUnauthenticated()
    {
        $this->environment->expects($this->once())
            ->method('getAdministrator')
            ->willReturn(null);

        $command = new AttachManagerCommand($this->orderId->getId());
        $handler = new AttachManagerHandler($this->orders, $this->environment);
        $handler->setDispatcher($this->eventBus);

        $this->expectException(\DomainException::class);
        call_user_func($handler, $command);
    }
}