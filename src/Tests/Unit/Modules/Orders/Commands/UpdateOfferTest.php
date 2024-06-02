<?php

namespace Project\Tests\Unit\Modules\Orders\Commands;

use Project\Modules\Shopping\Offers\Offer;
use Project\Modules\Shopping\Offers\OfferId;
use Project\Modules\Shopping\Order\Entity\Order;
use Project\Modules\Shopping\Offers\OfferBuilder;
use Project\Modules\Shopping\Order\Entity\OrderId;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Shopping\Order\Commands\UpdateOfferCommand;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Order\Repository\OrdersRepositoryInterface;
use Project\Modules\Shopping\Order\Commands\Handlers\UpdateOfferHandler;

class UpdateOfferTest extends \PHPUnit\Framework\TestCase
{
    private readonly OrdersRepositoryInterface $orders;
    private readonly OfferBuilder $offerBuilder;
    private readonly MessageBusInterface $eventBus;

    private readonly OrderId $orderId;
    private readonly OfferId $offerId;
    private readonly Order $order;
    private readonly Offer $offer;
    private readonly Offer $updatedOffer;
    private readonly Event $event;

    protected function setUp(): void
    {
        $this->orders = $this->getMockBuilder(OrdersRepositoryInterface::class)->getMock();
        $this->offerBuilder = $this->getMockBuilder(OfferBuilder::class)->getMock();
        $this->eventBus = $this->getMockBuilder(MessageBusInterface::class)->getMock();

        $this->orderId = OrderId::random();
        $this->offerId = OfferId::random();
        $this->order = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->offer = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->updatedOffer = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->event = $this->getMockBuilder(Event::class)->getMock();
    }

    public function testUpdateOffer()
    {
        $this->orders->expects($this->once())
            ->method('get')
            ->with($this->orderId)
            ->willReturn($this->order);

        $this->order->expects($this->once())
            ->method('getOffer')
            ->with($this->offerId)
            ->willReturn($this->offer);

        $command = new UpdateOfferCommand(
            id: $this->orderId->getId(),
            offerId: $this->offerId->getId(),
            quantity: rand(1, 10),
        );

        $this->offerBuilder->expects($this->once())
            ->method('from')
            ->with($this->offer)
            ->willReturnSelf();

        $this->offerBuilder->expects($this->once())
            ->method('withQuantity')
            ->with($command->quantity)
            ->willReturnSelf();

        $this->offerBuilder->expects($this->once())
            ->method('build')
            ->willReturn($this->updatedOffer);

        $this->order->expects($this->once())
            ->method('replaceOffer')
            ->with($this->offer, $this->updatedOffer);

        $this->orders->expects($this->once())
            ->method('update')
            ->with($this->order);

        $this->order->expects($this->once())
            ->method('flushEvents')
            ->willReturn([$this->event]);

        $this->eventBus->expects($this->once())
            ->method('dispatch')
            ->with($this->event);

        $handler = new UpdateOfferHandler($this->orders, $this->offerBuilder);
        $handler->setDispatcher($this->eventBus);
        call_user_func($handler, $command);
    }
}