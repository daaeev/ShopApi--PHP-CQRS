<?php

namespace Modules\Offers;

use Project\Tests\Unit\Modules\Helpers\CartFactory;

class CloneCartItemTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory;

    public function testClone()
    {
        $cartItem = $this->generateCartItem();
        $cloned = clone $cartItem;
        $this->assertNotSame($cartItem->getId(), $cloned->getId());
    }
}