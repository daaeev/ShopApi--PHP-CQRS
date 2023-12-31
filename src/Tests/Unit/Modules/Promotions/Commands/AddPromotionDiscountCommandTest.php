<?php

namespace Project\Tests\Unit\Modules\Promotions\Commands;

use Project\Common\Entity\Hydrator\Hydrator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Modules\Shopping\Discounts\Promotions\Commands\AddPromotionDiscountCommand;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsMemoryRepository;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Commands\Handlers\AddPromotionDiscountHandler;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics;

class AddPromotionDiscountCommandTest extends \PHPUnit\Framework\TestCase
{
    use PromotionFactory;

    private DiscountMechanics\DiscountMechanicFactoryInterface $mechanicFactory;
    private PromotionsRepositoryInterface $promotions;
    private EventDispatcherInterface $dispatcher;

    protected function setUp(): void
    {
        $this->promotions = new PromotionsMemoryRepository(new Hydrator);
        $this->mechanicFactory = $this->getMockBuilder(DiscountMechanics\DiscountMechanicFactoryInterface::class)
            ->getMock();
        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->getMock();
        $this->dispatcher->expects($this->exactly(1)) // promotion updated
            ->method('dispatch');
        parent::setUp();
    }

    public function testCreate()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $promotion->flushEvents();
        $this->promotions->add($promotion);

        $promotionDiscount = $this->generateDiscount();
        $this->mechanicFactory
            ->expects($this->once())
            ->method('make')
            ->with($promotionDiscount->getType(), $promotionDiscount->getData())
            ->willReturn($promotionDiscount);

        $command = new AddPromotionDiscountCommand(
            id: $promotion->getId()->getId(),
            discountType: $promotionDiscount->getType()->value,
            discountData: $promotionDiscount->getData()
        );
        $handler = new AddPromotionDiscountHandler($this->mechanicFactory, $this->promotions);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);

        $updatedPromotion = $this->promotions->get($promotion->getId());
        $this->assertCount(1, $updatedPromotion->getDiscounts());
        $addedDiscount = $updatedPromotion->getDiscounts()[0];
        $this->assertSame($promotionDiscount->getType(), $addedDiscount->getType());
        $this->assertSame($promotionDiscount->getData(), $addedDiscount->getData());
    }
}