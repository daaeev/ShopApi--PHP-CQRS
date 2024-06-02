<?php

namespace Project\Tests\Unit\Modules\Orders\Commands;

use Project\Modules\Shopping\Offers\OfferId;
use Project\Modules\Shopping\Order\Entity\Order;
use Project\Modules\Shopping\Order\Entity\OrderId;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Shopping\Order\Commands\RemoveOfferCommand;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Order\Repository\OrdersRepositoryInterface;
use Project\Modules\Shopping\Order\Commands\Handlers\RemoveOfferHandler;

class RemoveOfferTest extends \PHPUnit\Framework\TestCase
{
    private readonly OrdersRepositoryInterface $orders;
    private readonly MessageBusInterface $eventBus;

    private readonly OrderId $orderId;
    private readonly OfferId $offerId;
    private readonly Order $order;
    private readonly Event $event;

    protected function setUp(): void
    {
        $this->orders = $this->getMockBuilder(OrdersRepositoryInterface::class)->getMock();
        $this->eventBus = $this->getMockBuilder(MessageBusInterface::class)->getMock();

        $this->orderId = OrderId::random();
        $this->offerId = OfferId::random();
        $this->order = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->event = $this->getMockBuilder(Event::class)->getMock();
    }

    public function testRemoveOffer()
    {
        $this->orders->expects($this->once())
            ->method('get')
            ->with($this->orderId)
            ->willReturn($this->order);

        $this->order->expects($this->once())
            ->method('removeOffer')
            ->with($this->offerId);

        $this->orders->expects($this->once())
            ->method('update')
            ->with($this->order);

        $this->order->expects($this->once())
            ->method('flushEvents')
            ->willReturn([$this->event]);

        $this->eventBus->expects($this->once())
            ->method('dispatch')
            ->with($this->event);

        $command = new RemoveOfferCommand($this->orderId->getId(), $this->offerId->getId());
        $handler = new RemoveOfferHandler($this->orders);
        $handler->setDispatcher($this->eventBus);
        call_user_func($handler, $command);
    }
}