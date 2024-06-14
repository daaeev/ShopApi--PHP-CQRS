<?php

namespace Project\Tests\Unit\Modules\Orders\Commands;

use Project\Modules\Shopping\Entity\Promocode;
use Project\Modules\Shopping\Order\Entity\Order;
use Project\Modules\Shopping\Order\Entity\OrderId;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Shopping\Order\Commands\AddPromoCommand;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Order\Commands\Handlers\AddPromoHandler;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\PromocodeId;
use Project\Modules\Shopping\Order\Repository\OrdersRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\Promocode as BasePromocode;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesRepositoryInterface;

class AddPromoTest extends \PHPUnit\Framework\TestCase
{
    private readonly OrdersRepositoryInterface $orders;
    private readonly PromocodesRepositoryInterface $promocodes;
    private readonly MessageBusInterface $eventBus;

    private readonly PromocodeId $promoId;
    private readonly OrderId $orderId;
    private readonly Order $order;
    private readonly BasePromocode $promocode;
    private readonly Event $event;

    protected function setUp(): void
    {
        $this->orders = $this->getMockBuilder(OrdersRepositoryInterface::class)->getMock();
        $this->promocodes = $this->getMockBuilder(PromocodesRepositoryInterface::class)->getMock();
        $this->eventBus = $this->getMockBuilder(MessageBusInterface::class)->getMock();

        $this->promoId = PromocodeId::random();
        $this->orderId = OrderId::random();
        $this->order = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->promocode = $this->getMockBuilder(BasePromocode::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->event = $this->getMockBuilder(Event::class)->getMock();
    }

    public function testAddPromo()
    {
        $this->orders->expects($this->once())
            ->method('get')
            ->with($this->orderId)
            ->willReturn($this->order);

        $this->promocodes->expects($this->once())
            ->method('get')
            ->with($this->promoId)
            ->willReturn($this->promocode);

        $this->promocode->expects($this->once())
            ->method('isActive')
            ->willReturn(true);

        $this->promocode->expects($this->once())
            ->method('getId')
            ->willReturn($this->promoId);

        $this->promocode->expects($this->once())
            ->method('getCode')
            ->willReturn('test');

        $this->promocode->expects($this->once())
            ->method('getDiscountPercent')
            ->willReturn(50);

        $this->order->expects($this->once())
            ->method('usePromocode')
            ->with(new Promocode(
                id: $this->promoId,
                code: 'test',
                discountPercent: 50
            ));

        $this->orders->expects($this->once())
            ->method('update')
            ->with($this->order);

        $this->order->expects($this->once())
            ->method('flushEvents')
            ->willReturn([$this->event]);

        $this->eventBus->expects($this->once())
            ->method('dispatch')
            ->with($this->event);

        $command = new AddPromoCommand($this->orderId->getId(), $this->promoId->getId());
        $handler = new AddPromoHandler($this->orders, $this->promocodes);
        $handler->setDispatcher($this->eventBus);
        call_user_func($handler, $command);
    }

    public function testAddDisabledPromo()
    {
        $this->orders->expects($this->once())
            ->method('get')
            ->with($this->orderId)
            ->willReturn($this->order);

        $this->promocodes->expects($this->once())
            ->method('get')
            ->with($this->promoId)
            ->willReturn($this->promocode);

        $this->promocode->expects($this->once())
            ->method('isActive')
            ->willReturn(false);

        $command = new AddPromoCommand($this->orderId->getId(), $this->promoId->getId());
        $handler = new AddPromoHandler($this->orders, $this->promocodes);
        $handler->setDispatcher($this->eventBus);

        $this->expectException(\DomainException::class);
        call_user_func($handler, $command);
    }
}