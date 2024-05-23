<?php

namespace Project\Tests\Unit\Modules\Promotions\Services;

use PHPUnit\Framework\TestCase;
use Project\Modules\Shopping\Offers\Offer;
use Project\Modules\Shopping\Offers\OfferBuilder;
use Project\Tests\Unit\Modules\Helpers\OffersFactory;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Modules\Shopping\Discounts\Promotions\Entity\Promotion;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\MechanicHandlerInterface;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\AbstractDiscountMechanic;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Factory\HandlerFactoryInterface;

class DiscountsServiceTest extends TestCase
{
    use OffersFactory, PromotionFactory;

    private OfferBuilder $builderMock;
    private PromotionsRepositoryInterface $promotions;
    private HandlerFactoryInterface $handlerFactory;
    private MechanicHandlerInterface $handler;
    private Promotion $promotion;
    private AbstractDiscountMechanic $discount;
    private Offer $offer;

    protected function setUp(): void
    {
        $this->builderMock = $this->getMockBuilder(OfferBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->promotions = $this->getMockBuilder(PromotionsRepositoryInterface::class)->getMock();
        $this->handlerFactory = $this->getMockBuilder(HandlerFactoryInterface::class)->getMock();
        $this->handler = $this->getMockBuilder(MechanicHandlerInterface::class)->getMock();

        $this->offer = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->promotion = $this->getMockBuilder(Promotion::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->discount = $this->getMockBuilder(AbstractDiscountMechanic::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testApplyDiscounts()
    {
        $this->offer->expects($this->once())
            ->method('getRegularPrice')
            ->willReturn($regularPrice = 500);

        $this->builderMock->expects($this->once())
            ->method('from')
            ->with($this->offer)
            ->willReturnSelf();

        $this->builderMock->expects($this->once())
            ->method('withPrice')
            ->with($regularPrice)
            ->willReturnSelf();

        $this->builderMock->expects($this->once())
            ->method('build')
            ->willReturn($this->offer);

        $this->promotions->expects($this->once())
            ->method('getActivePromotions')
            ->willReturn([$this->promotion]);

        $this->promotion->expects($this->once())
            ->method('getDiscounts')
            ->willReturn([$this->discount]);

        $this->handlerFactory->expects($this->once())
            ->method('make')
            ->with($this->discount)
            ->willReturn($this->handler);

        $this->handler->expects($this->once())
            ->method('handle')
            ->with([$this->offer])
            ->willReturn([$this->offer]);

        $service = new DiscountsService($this->builderMock, $this->promotions, $this->handlerFactory);
        $offers = $service->applyDiscounts([$this->offer]);
        $this->assertSame([$this->offer], $offers);
    }
}