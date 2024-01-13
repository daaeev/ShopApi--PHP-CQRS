<?php

namespace Project\Tests\Unit\Modules\Promotions\Commands;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Modules\Shopping\Discounts\Promotions\Entity;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Discounts\Promotions\Commands\UpdatePromotionCommand;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsMemoryRepository;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Commands\Handlers\UpdatePromotionHandler;

class UpdatePromotionCommandTest extends \PHPUnit\Framework\TestCase
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
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $promotion->flushEvents();
        $this->promotions->add($promotion);

        $command = new UpdatePromotionCommand(
            id: $promotion->getId()->getId(),
            name: 'Promotion',
            startDate: new \DateTimeImmutable('-1 day'),
            endDate: new \DateTimeImmutable('+1 day'),
        );
        $handler = new UpdatePromotionHandler($this->promotions);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);

        $updatedPromotion = $this->promotions->get($promotion->getId());
        $this->assertSamePromotion($updatedPromotion, $command);
    }

    private function assertSamePromotion(
        Entity\Promotion $promotion,
        UpdatePromotionCommand $command
    ): void {
        $this->assertSame($promotion->getName(), $command->name);
        $this->assertSame($promotion->getDuration()->getStartDate()?->getTimestamp(), $command->startDate?->getTimestamp());
        $this->assertSame($promotion->getDuration()->getEndDate()?->getTimestamp(), $command->endDate?->getTimestamp());
    }
}