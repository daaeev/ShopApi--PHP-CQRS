<?php

namespace Project\Tests\Unit\Modules\Cart\Entity\CartItem;

use Project\Tests\Unit\Modules\Helpers\CartFactory;

class UpdateCartItemTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory;

    public function testUpdatePrice()
    {
        $item = $this->generateCartItem();
        $newPrice = $item->getPrice() + rand(10, 100);
        $item->updatePrice($newPrice);
        $this->assertSame($newPrice, $item->getPrice());
    }

    public function testUpdatePriceToLessThanZero()
    {
        $item = $this->generateCartItem();
        $this->expectException(\InvalidArgumentException::class);
        $item->updatePrice(-1);
    }

    public function testUpdatePriceToGreaterThanRegularPrice()
    {
        $item = $this->generateCartItem();
        $this->expectException(\InvalidArgumentException::class);
        $item->updatePrice($item->getRegularPrice() + rand(10, 100));
    }

    public function testUpdateQuantity()
    {
        $item = $this->generateCartItem();
        $newQuantity = $item->getQuantity() + rand(1, 5);
        $item->updateQuantity($newQuantity);
        $this->assertSame($newQuantity, $item->getQuantity());
    }

    public function testUpdateQuantityToZero()
    {
        $item = $this->generateCartItem();
        $this->expectException(\InvalidArgumentException::class);
        $item->updateQuantity(0);
    }
}