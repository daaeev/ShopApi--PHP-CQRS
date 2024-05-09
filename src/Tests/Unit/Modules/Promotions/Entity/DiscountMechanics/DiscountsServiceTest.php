<?php

namespace Project\Tests\Unit\Modules\Promotions\Entity\DiscountMechanics;

use PHPUnit\Framework\TestCase;
use Project\Modules\Shopping\Cart\Entity\Cart;
use Project\Modules\Shopping\Entity\OfferBuilder;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Tests\Unit\Modules\Helpers\OffersFactory;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountType;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\MechanicHandlerInterface;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Factory\HandlerFactoryInterface;

class DiscountsServiceTest extends TestCase
{
    use CartFactory, OffersFactory, PromotionFactory;

    private PromotionsRepositoryInterface $promotions;
    private HandlerFactoryInterface $handlerFactory;
    private MechanicHandlerInterface $handler;

    protected function setUp(): void
    {
        $this->promotions = $this->getMockBuilder(PromotionsRepositoryInterface::class)->getMock();
        $this->handlerFactory = $this->getMockBuilder(HandlerFactoryInterface::class)->getMock();
        $this->handler = $this->getMockBuilder(MechanicHandlerInterface::class)->getMock();
    }

    public function testApplyDiscounts()
    {
        $cartItem = $this->generateOffer();
        $cartItems = [$cartItem];

        $builderMock = $this->getMockBuilder(OfferBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builderMock->expects($this->exactly(1))
            ->method('from')
            ->with($cartItem)
            ->willReturnSelf();

        $builderMock->expects($this->exactly(1))
            ->method('withPrice')
            ->with($cartItem->getRegularPrice())
            ->willReturnSelf();

        $builderMock->expects($this->exactly(1))
            ->method('build')
            ->willReturn($cartItem);

        $cart = $this->getMockBuilder(Cart::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cart->expects($this->once())
            ->method('getOffers')
            ->willReturn($cartItems);

        $cart->expects($this->once())
            ->method('setOffers')
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

        $service = new DiscountsService($builderMock, $this->promotions, $this->handlerFactory);
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