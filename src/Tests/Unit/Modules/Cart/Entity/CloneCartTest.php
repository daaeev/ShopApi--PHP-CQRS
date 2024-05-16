<?php

namespace Project\Tests\Unit\Modules\Cart\Entity;

use Project\Modules\Shopping\Entity\Promocode;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Tests\Unit\Modules\Helpers\OffersFactory;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;

class CloneCartTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory, OffersFactory, PromocodeFactory;

    public function testClone()
    {
        $cart = $this->generateCart();
		$cart->addOffer($this->generateOffer());
		$cart->usePromocode(Promocode::fromBaseEntity($this->generatePromocode()));

		$cloned = clone $cart;
		$this->assertNotSame($cart->getId(), $cloned->getId());
		$this->assertNotSame($cart->getClient(), $cloned->getClient());
		$this->assertNotSame($cart->getPromocode(), $cloned->getPromocode());
		$this->assertNotSame($cart->getCreatedAt(), $cloned->getCreatedAt());
		$this->assertNotSame($cart->getUpdatedAt(), $cloned->getUpdatedAt());
		$this->assertNotSame($cart->getOffers(), $cloned->getOffers());
    }
}