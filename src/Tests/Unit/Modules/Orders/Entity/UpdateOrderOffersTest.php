<?php

namespace Project\Tests\Unit\Modules\Orders\Entity;

use Project\Tests\Unit\Modules\Helpers\OrderFactory;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\OffersFactory;
use Project\Modules\Shopping\Order\Entity\OrderStatus;
use Project\Modules\Shopping\Api\Events\Orders\OrderUpdated;

class UpdateOrderOffersTest extends \PHPUnit\Framework\TestCase
{
    use OrderFactory, OffersFactory, AssertEvents;

    public function testAddOffer()
    {
        $order = $this->generateOrder([$this->generateOffer()]);
        $oldUpdatedAt = $order->getUpdatedAt();
        $order->addOffer($offer = $this->generateOffer());

        $this->assertCount(2, $order->getOffers());
        $this->assertSame($offer, $order->getOffer($offer->getId()));
        $this->assertSame($offer, $order->getOffer($offer->getUuid()));
        $this->assertNotSame($oldUpdatedAt, $order->getUpdatedAt());
        $this->assertEvents($order, [new OrderUpdated($order)]);
    }

    public function testAddOfferToCompletedOrder()
    {
        $order = $this->generateOrder([$this->generateOffer()]);
        $order->updateStatus(OrderStatus::COMPLETED);
        $this->expectException(\DomainException::class);
        $order->addOffer($this->generateOffer());
    }

    public function testReplaceOffer()
    {
        $order = $this->generateOrder([$offer = $this->generateOffer()]);
        $offerForReplace = $this->generateOffer();
        $oldUpdatedAt = $order->getUpdatedAt();
        $order->replaceOffer($offer, $offerForReplace);

        $this->assertCount(1, $order->getOffers());
        $this->assertSame($offerForReplace, $order->getOffer($offerForReplace->getId()));
        $this->assertSame($offerForReplace, $order->getOffer($offerForReplace->getUuid()));
        $this->assertNotSame($oldUpdatedAt, $order->getUpdatedAt());
        $this->assertEvents($order, [new OrderUpdated($order)]);

        $this->expectException(\DomainException::class);
        $order->getOffer($offer->getUuid());
    }

    public function testReplaceOfferIfOrderCompleted()
    {
        $order = $this->generateOrder([$offer = $this->generateOffer()]);
        $order->updateStatus(OrderStatus::COMPLETED);
        $this->expectException(\DomainException::class);
        $order->replaceOffer($offer, $this->generateOffer());
    }

    public function testRemoveOfferById()
    {
        $order = $this->generateOrder([$offer = $this->generateOffer(), $this->generateOffer()]);
        $oldUpdatedAt = $order->getUpdatedAt();
        $order->removeOffer($offer->getId());

        $this->assertCount(1, $order->getOffers());
        $this->assertNotSame($oldUpdatedAt, $order->getUpdatedAt());
        $this->assertEvents($order, [new OrderUpdated($order)]);

        $this->expectException(\DomainException::class);
        $order->getOffer($offer->getId());
    }

    public function testRemoveOfferByUuid()
    {
        $order = $this->generateOrder([$offer = $this->generateOffer(), $this->generateOffer()]);
        $oldUpdatedAt = $order->getUpdatedAt();
        $order->removeOffer($offer->getUuid());

        $this->assertCount(1, $order->getOffers());
        $this->assertNotSame($oldUpdatedAt, $order->getUpdatedAt());
        $this->assertEvents($order, [new OrderUpdated($order)]);

        $this->expectException(\DomainException::class);
        $order->getOffer($offer->getUuid());
    }

    public function testRemoveOfferIfOrderCompleted()
    {
        $order = $this->generateOrder([$offer = $this->generateOffer(), $this->generateOffer()]);
        $order->updateStatus(OrderStatus::COMPLETED);
        $this->expectException(\DomainException::class);
        $order->removeOffer($offer->getId());
    }

    public function testRemoveAllOrderOffers()
    {
        $order = $this->generateOrder([$offer = $this->generateOffer()]);
        $this->expectException(\InvalidArgumentException::class);
        $order->removeOffer($offer->getId());
    }
}