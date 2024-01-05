<?php

namespace Project\Tests\Unit\Modules\Promotions\Commands;

use Project\Common\Entity\Hydrator\Hydrator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Modules\Shopping\Discounts\Promotions\Entity;
use Project\Modules\Shopping\Discounts\Promotions\Commands\CreatePromotionCommand;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsMemoryRepository;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Commands\Handlers\CreatePromotionHandler;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics;

class CreatePromotionCommandTest extends \PHPUnit\Framework\TestCase
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

        $this->dispatcher->expects($this->exactly(1)) // promotion created
            ->method('dispatch');

        parent::setUp();
    }

    public function testCreate()
    {
        $promotionDiscount = $this->generateDiscount();
        $this->mechanicFactory
            ->expects($this->once())
            ->method('make')
            ->with($promotionDiscount->getType(), $promotionDiscount->getData())
            ->willReturn($promotionDiscount);

        $command = new CreatePromotionCommand(
            name: 'Promotion',
            startDate: new \DateTimeImmutable('-1 day'),
            endDate: new \DateTimeImmutable('+1 day'),
            disabled: false,
            discounts: [[
                'type' => $promotionDiscount->getType()->value,
                'data' => $promotionDiscount->getData()
            ]]
        );
        $handler = new CreatePromotionHandler($this->mechanicFactory, $this->promotions);
        $handler->setDispatcher($this->dispatcher);
        $promotionId = call_user_func($handler, $command);

        $promotion = $this->promotions->get(new Entity\PromotionId($promotionId));
        $this->assertSamePromotion($promotion, $command);
    }

    private function assertSamePromotion(
        Entity\Promotion $promotion,
        CreatePromotionCommand $command
    ): void {
        $this->assertSame($promotion->getName(), $command->name);
        $this->assertSame($promotion->getDuration()->getStartDate()?->getTimestamp(), $command->startDate?->getTimestamp());
        $this->assertSame($promotion->getDuration()->getEndDate()?->getTimestamp(), $command->endDate?->getTimestamp());
        $this->assertSame($promotion->disabled(), $command->disabled);

        $this->assertCount(count($command->discounts), $promotion->getDiscounts());
        $addedDiscount = $promotion->getDiscounts()[0];
        $this->assertSame($command->discounts[0]['type'], $addedDiscount->getType()->value);
        $this->assertSame($command->discounts[0]['data'], $addedDiscount->getData());
    }
}