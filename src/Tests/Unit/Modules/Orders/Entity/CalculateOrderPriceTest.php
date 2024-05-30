<?php

namespace Project\Tests\Unit\Modules\Orders\Entity;

use Project\Modules\Shopping\Entity\Promocode;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\OrderFactory;
use Project\Tests\Unit\Modules\Helpers\OffersFactory;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;

class CalculateOrderPriceTest extends \PHPUnit\Framework\TestCase
{
    use OrderFactory, OffersFactory, PromocodeFactory, AssertEvents;

    public function testCalculateAfterAddOffer()
    {
        $order = $this->generateOrder([$this->generateOffer()]);
        $oldTotalPrice = $order->getTotalPrice();
        $oldRegularPrice = $order->getRegularPrice();

        $order->addOffer($this->generateOffer());
        $totalPrice = $order->getTotalPrice();
        $regularPrice = $order->getRegularPrice();

        $this->assertNotSame($oldTotalPrice, $totalPrice);
        $this->assertNotSame($oldRegularPrice, $regularPrice);

        $this->assertSame($this->calculateTotalPrice($order->getOffers()), $totalPrice);
        $this->assertSame($this->calculateRegularPrice($order->getOffers()), $regularPrice);
    }
    public function testCalculateAfterReplaceOffer()
    {
        $order = $this->generateOrder([$offer = $this->generateOffer()]);
        $oldTotalPrice = $order->getTotalPrice();
        $oldRegularPrice = $order->getRegularPrice();

        $order->replaceOffer($offer, $this->generateOffer());
        $totalPrice = $order->getTotalPrice();
        $regularPrice = $order->getRegularPrice();

        $this->assertNotSame($oldTotalPrice, $totalPrice);
        $this->assertNotSame($oldRegularPrice, $regularPrice);

        $this->assertSame($this->calculateTotalPrice($order->getOffers()), $totalPrice);
        $this->assertSame($this->calculateRegularPrice($order->getOffers()), $regularPrice);
    }

    public function testCalculateAfterRemoveOffer()
    {
        $order = $this->generateOrder([$offer = $this->generateOffer(), $this->generateOffer()]);
        $oldTotalPrice = $order->getTotalPrice();
        $oldRegularPrice = $order->getRegularPrice();

        $order->removeOffer($offer->getId());
        $totalPrice = $order->getTotalPrice();
        $regularPrice = $order->getRegularPrice();

        $this->assertNotSame($oldTotalPrice, $totalPrice);
        $this->assertNotSame($oldRegularPrice, $regularPrice);

        $this->assertSame($this->calculateTotalPrice($order->getOffers()), $totalPrice);
        $this->assertSame($this->calculateRegularPrice($order->getOffers()), $regularPrice);
    }

    public function testCalculateAfterUsePromocode()
    {
        $order = $this->generateOrder([$this->generateOffer()]);
        $oldTotalPrice = $order->getTotalPrice();
        $oldRegularPrice = $order->getRegularPrice();

        $order->usePromocode(Promocode::fromBaseEntity($this->generatePromocode()));
        $totalPrice = $order->getTotalPrice();
        $regularPrice = $order->getRegularPrice();

        $this->assertNotSame($oldTotalPrice, $totalPrice);
        $this->assertSame($oldRegularPrice, $regularPrice);

        $this->assertSame($this->calculateTotalPrice($order->getOffers(), $order->getPromocode()), $totalPrice);
        $this->assertSame($this->calculateRegularPrice($order->getOffers()), $regularPrice);
    }

    public function testCalculateAfterRemovePromocode()
    {
        $order = $this->generateOrder([$this->generateOffer()]);
        $order->usePromocode(Promocode::fromBaseEntity($this->generatePromocode()));
        $oldTotalPrice = $order->getTotalPrice();
        $oldRegularPrice = $order->getRegularPrice();

        $order->removePromocode();
        $totalPrice = $order->getTotalPrice();
        $regularPrice = $order->getRegularPrice();

        $this->assertNotSame($oldTotalPrice, $totalPrice);
        $this->assertSame($oldRegularPrice, $regularPrice);

        $this->assertSame($this->calculateTotalPrice($order->getOffers()), $totalPrice);
        $this->assertSame($this->calculateRegularPrice($order->getOffers()), $regularPrice);
    }
}