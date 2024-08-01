<?php

namespace Project\Tests\Unit\Modules\Orders\Commands;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Shopping\Order\Entity\Order;
use Project\Modules\Shopping\Order\Entity\OrderId;
use Project\Modules\Shopping\Order\Entity\Manager;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Shopping\Order\Entity\ManagerId;
use Project\Common\Services\Environment\Administrator;
use Project\Common\Services\Environment\EnvironmentInterface;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Order\Commands\DetachManagerCommand;
use Project\Modules\Shopping\Order\Repository\OrdersRepositoryInterface;
use Project\Modules\Shopping\Order\Commands\Handlers\DetachManagerHandler;

class DetachManagerTest extends \PHPUnit\Framework\TestCase
{
    private readonly OrdersRepositoryInterface $orders;
    private readonly EnvironmentInterface $environment;
    private readonly MessageBusInterface $eventBus;

    private readonly Administrator $administrator;
    private readonly int $adminId;
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
        $this->orderId = OrderId::random();
        $this->order = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderUpdatedEvent = $this->getMockBuilder(Event::class)->getMock();
    }

    public function testDetach()
    {
        $this->environment->expects($this->once())
            ->method('getAdministrator')
            ->willReturn($this->administrator);

        $this->orders->expects($this->once())
            ->method('get')
            ->with($this->orderId)
            ->willReturn($this->order);

        $this->administrator->expects($this->once())
            ->method('getId')
            ->willReturn($this->adminId);

        $manager = new Manager(ManagerId::make($this->adminId), uniqid());
        $this->order->expects($this->once())
            ->method('getManager')
            ->willReturn($manager);

        $this->order->expects($this->once())->method('detachManager');

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

        $command = new DetachManagerCommand($this->orderId->getId());
        $handler = new DetachManagerHandler($this->orders, $this->environment);
        $handler->setDispatcher($this->eventBus);
        call_user_func($handler, $command);
    }

    public function testDetachIfAdminUnauthenticated()
    {
        $this->environment->expects($this->once())
            ->method('getAdministrator')
            ->willReturn(null);

        $command = new DetachManagerCommand($this->orderId->getId());
        $handler = new DetachManagerHandler($this->orders, $this->environment);
        $handler->setDispatcher($this->eventBus);

        $this->expectException(\DomainException::class);
        call_user_func($handler, $command);
    }

    public function testDetachIfOtherManagerAttached()
    {
        $this->environment->expects($this->once())
            ->method('getAdministrator')
            ->willReturn($this->administrator);

        $this->orders->expects($this->once())
            ->method('get')
            ->with($this->orderId)
            ->willReturn($this->order);

        $this->administrator->expects($this->once())
            ->method('getId')
            ->willReturn($this->adminId);

        $manager = new Manager(ManagerId::make($this->adminId + 1), uniqid());
        $this->order->expects($this->once())
            ->method('getManager')
            ->willReturn($manager);

        $command = new DetachManagerCommand($this->orderId->getId());
        $handler = new DetachManagerHandler($this->orders, $this->environment);
        $handler->setDispatcher($this->eventBus);

        $this->expectException(\DomainException::class);
        call_user_func($handler, $command);
    }
}