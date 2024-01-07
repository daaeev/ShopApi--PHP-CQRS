<?php

namespace Project\Tests\Unit\Modules\Promotions\Commands;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\CQRS\Buses\MessageBusInterface;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
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
        $this->promotions = new PromotionsMemoryRepository(new Hydrator);
        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();

        $this->dispatcher->expects($this->exactly(1)) // promotion updated
            ->method('dispatch');

        parent::setUp();
    }

    public function testCreate()
    {
        $promotionDiscount = $this->generateDiscount();
        $promotion = $this->generatePromotion();
        $promotion->disable();
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

        $updatedPromotion = $this->promotions->get($promotion->getId());
        $this->assertEmpty($updatedPromotion->getDiscounts());
    }
}