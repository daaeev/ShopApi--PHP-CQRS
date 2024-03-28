<?php

namespace Project\Tests\Unit\Modules\Promotions\Entity\DiscountMechanics;

use PHPUnit\Framework\TestCase;
use Project\Modules\Shopping\Cart\Entity\Cart;
use Project\Modules\Shopping\Cart\Entity\CartItem;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountType;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\MechanicHandlerInterface;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\MechanicHandlerFactoryInterface;

class DiscountsServiceTest extends TestCase
{
    use CartFactory, PromotionFactory;

    private PromotionsRepositoryInterface $promotions;
    private MechanicHandlerFactoryInterface $handlerFactory;
    private MechanicHandlerInterface $handler;

    protected function setUp(): void
    {
        $this->promotions = $this->getMockBuilder(PromotionsRepositoryInterface::class)->getMock();
        $this->handlerFactory = $this->getMockBuilder(MechanicHandlerFactoryInterface::class)->getMock();
        $this->handler = $this->getMockBuilder(MechanicHandlerInterface::class)->getMock();
        parent::setUp();
    }

    public function testApplyDiscounts()
    {
        $cartItemMock = $this->getMockBuilder(CartItem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cartItemMock->expects($this->once())
            ->method('getRegularPrice')
            ->willReturn((float) 100);

        $cartItemMock->expects($this->once())
            ->method('updatePrice')
            ->with(100);

        $cartItems = [$cartItemMock];
        $cart = $this->getMockBuilder(Cart::class)->disableOriginalConstructor()->getMock();

        $cart->expects($this->exactly(2))
            ->method('getItems')
            ->willReturn($cartItems);

        $cart->expects($this->once())
            ->method('setItems')
            ->with($cartItems);

        $this->promotions->expects($this->once())
            ->method('getActivePromotions')
            ->willReturn($this->generateActivePromotions());

        $this->handlerFactory->expects($this->exactly(1))
            ->method('make')
            ->willReturn($this->handler);

        $this->handler->expects($this->exactly(1))
            ->method('handle')
            ->with($cartItems)
            ->willReturn($cartItems);

        $service = new DiscountsService($this->promotions, $this->handlerFactory);
        $service->applyDiscounts($cart);
    }

    private function generateActivePromotions()
    {
        $activePromotions = [];
        $activePromotions[] = $this->generatePercentagePromotion();
        return $activePromotions;
    }

    private function generatePercentagePromotion()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $promotion->addDiscount($this->generateDiscount(DiscountType::PERCENTAGE));
        $promotion->enable();
        return $promotion;
    }
}