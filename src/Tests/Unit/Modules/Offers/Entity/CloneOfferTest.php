<?php

namespace Project\Tests\Unit\Modules\Offers\Entity;

use Project\Tests\Unit\Modules\Helpers\OffersFactory;

class CloneOfferTest extends \PHPUnit\Framework\TestCase
{
    use OffersFactory;

    public function testClone()
    {
        $cartItem = $this->generateOffer();
        $cloned = clone $cartItem;
        $this->assertNotSame($cartItem->getId(), $cloned->getId());
        $this->assertNotSame($cartItem->getUuid(), $cloned->getUuid());
    }
}