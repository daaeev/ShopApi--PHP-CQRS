<?php

namespace Project\Tests\Unit\Modules\Orders\Commands;

use Project\Common\Product\Currency;
use Project\Modules\Shopping\Offers\Offer;
use Project\Modules\Shopping\Order\Entity\Order;
use Project\Modules\Shopping\Order\Entity\OrderId;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Shopping\Adapters\CatalogueService;
use Project\Modules\Shopping\Order\Commands\AddOfferCommand;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Order\Commands\Handlers\AddOfferHandler;
use Project\Modules\Shopping\Order\Repository\OrdersRepositoryInterface;

class AddOfferTest extends \PHPUnit\Framework\TestCase
{
    private readonly OrdersRepositoryInterface $orders;
    private readonly CatalogueService $catalogue;
    private readonly MessageBusInterface $eventBus;

    private readonly OrderId $orderId;
    private readonly Order $order;
    private readonly Offer $offer;
    private readonly Event $event;

    protected function setUp(): void
    {
        $this->orders = $this->getMockBuilder(OrdersRepositoryInterface::class)->getMock();
        $this->catalogue = $this->getMockBuilder(CatalogueService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventBus = $this->getMockBuilder(MessageBusInterface::class)->getMock();

        $this->orderId = OrderId::random();
        $this->order = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->offer = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->event = $this->getMockBuilder(Event::class)->getMock();
    }

    public function testAddOffer()
    {
        $this->orders->expects($this->once())
            ->method('get')
            ->with($this->orderId)
            ->willReturn($this->order);

        $this->order->expects($this->once())
            ->method('getCurrency')
            ->willReturn(Currency::default());

        $command = new AddOfferCommand(
            id: $this->orderId->getId(),
            productId: rand(1, 10),
            quantity: rand(1, 10),
            size: uniqid(),
            color: uniqid(),
        );

        $this->catalogue->expects($this->once())
            ->method('resolveOffer')
            ->with($command->productId, $command->quantity, Currency::default(), $command->size, $command->color)
            ->willReturn($this->offer);

        $this->order->expects($this->once())
            ->method('addOffer')
            ->with($this->offer);

        $this->orders->expects($this->once())
            ->method('update')
            ->with($this->order);

        $this->order->expects($this->once())
            ->method('flushEvents')
            ->willReturn([$this->event]);

        $this->eventBus->expects($this->once())
            ->method('dispatch')
            ->with($this->event);

        $handler = new AddOfferHandler($this->orders, $this->catalogue);
        $handler->setDispatcher($this->eventBus);
        call_user_func($handler, $command);
    }
}