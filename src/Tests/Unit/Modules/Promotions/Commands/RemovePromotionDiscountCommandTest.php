<?php

namespace Project\Tests\Unit\Modules\Promotions\Commands;

use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsMemoryRepository;
use Project\Modules\Shopping\Discounts\Promotions\Commands\RemovePromotionDiscountCommand;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Commands\Handlers\RemovePromotionDiscountHandler;

class RemovePromotionDiscountCommandTest extends \PHPUnit\Framework\TestCase
{
    use PromotionFactory;

    private PromotionsRepositoryInterface $promotions;
    private MessageBusInterface $dispatcher;

    protected function setUp(): void
    {
        $this->promotions = new PromotionsMemoryRepository(new Hydrator, new IdentityMap);
        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();

        $this->dispatcher->expects($this->exactly(1)) // promotion updated
            ->method('dispatch');
    }

    public function testCreate()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
		$promotionDiscount = $this->generateDiscount();
        $promotion->addDiscount($promotionDiscount);
        $promotion->flushEvents();
        $this->promotions->add($promotion);

        $command = new RemovePromotionDiscountCommand(
            promotionId: $promotion->getId()->getId(),
            discountId: $promotionDiscount->getId()->getId()
        );

        $handler = new RemovePromotionDiscountHandler($this->promotions);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);

        $this->assertEmpty($promotion->getDiscounts());
    }
}