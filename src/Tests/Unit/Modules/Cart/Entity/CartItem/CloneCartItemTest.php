<?php

namespace Project\Tests\Unit\Modules\Cart\Entity\CartItem;

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