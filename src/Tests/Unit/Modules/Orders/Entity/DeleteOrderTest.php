<?php

namespace Project\Tests\Unit\Modules\Orders\Entity;

use Project\Tests\Unit\Modules\Helpers\OrderFactory;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\OffersFactory;
use Project\Modules\Shopping\Order\Entity\OrderStatus;
use Project\Modules\Shopping\Api\Events\Orders\OrderDeleted;

class DeleteOrderTest extends \PHPUnit\Framework\TestCase
{
    use OrderFactory, OffersFactory, AssertEvents;

    public function testDeleteOrder()
    {
        $order = $this->generateOrder([$this->generateOffer()]);
        $order->updateStatus(OrderStatus::CANCELED);
        $order->flushEvents();

        $order->delete();
        $this->assertEvents($order, [new OrderDeleted($order)]);
    }

    public function testDeleteNotCancelledOrder()
    {
        $order = $this->generateOrder([$this->generateOffer()]);
        $this->expectException(\DomainException::class);
        $order->delete();
    }
}