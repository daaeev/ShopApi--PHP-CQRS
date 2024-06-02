<?php

namespace Project\Tests\Unit\Modules\Orders\Commands;

use Project\Modules\Shopping\Order\Entity\Order;
use Project\Modules\Shopping\Order\Entity\OrderId;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Shopping\Order\Commands\RemovePromoCommand;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Order\Repository\OrdersRepositoryInterface;
use Project\Modules\Shopping\Order\Commands\Handlers\RemovePromoHandler;

class RemovePromoTest extends \PHPUnit\Framework\TestCase
{
    private readonly OrdersRepositoryInterface $orders;
    private readonly MessageBusInterface $eventBus;

    private readonly OrderId $orderId;
    private readonly Order $order;
    private readonly Event $event;

    protected function setUp(): void
    {
        $this->orders = $this->getMockBuilder(OrdersRepositoryInterface::class)->getMock();
        $this->eventBus = $this->getMockBuilder(MessageBusInterface::class)->getMock();

        $this->orderId = OrderId::random();
        $this->order = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->event = $this->getMockBuilder(Event::class)->getMock();
    }

    public function testRemovePromo()
    {
        $this->orders->expects($this->once())
            ->method('get')
            ->with($this->orderId)
            ->willReturn($this->order);

        $this->order->expects($this->once())
            ->method('removePromocode');

        $this->orders->expects($this->once())
            ->method('update')
            ->with($this->order);

        $this->order->expects($this->once())
            ->method('flushEvents')
            ->willReturn([$this->event]);

        $this->eventBus->expects($this->once())
            ->method('dispatch')
            ->with($this->event);

        $command = new RemovePromoCommand($this->orderId->getId());
        $handler = new RemovePromoHandler($this->orders);
        $handler->setDispatcher($this->eventBus);
        call_user_func($handler, $command);
    }
}