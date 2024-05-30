<?php

namespace Project\Tests\Unit\Modules\Orders\Entity;

use Project\Modules\Shopping\Entity\Promocode;
use Project\Tests\Unit\Modules\Helpers\OrderFactory;
use Project\Tests\Unit\Modules\Helpers\OffersFactory;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;

class CloneOrderTest extends \PHPUnit\Framework\TestCase
{
    use OrderFactory, OffersFactory, PromocodeFactory;

    public function testClone()
    {
        $order = $this->generateOrder([$this->generateOffer()]);
        $order->usePromocode(Promocode::fromBaseEntity($this->generatePromocode()));
        $cloned = clone $order;

        $this->assertNotSame($order->getId(), $cloned->getId());
        $this->assertNotSame($order->getClient(), $cloned->getClient());
        $this->assertNotSame($order->getDelivery(), $cloned->getDelivery());
        $this->assertNotSame($order->getOffers(), $cloned->getOffers());
        $this->assertNotSame($order->getPromocode(), $cloned->getPromocode());
        $this->assertNotSame($order->getCreatedAt(), $cloned->getCreatedAt());
        $this->assertNotSame($order->getUpdatedAt(), $cloned->getUpdatedAt());
    }
}