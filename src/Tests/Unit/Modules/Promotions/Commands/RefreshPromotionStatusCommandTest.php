<?php

namespace Commands;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Modules\Shopping\Discounts\Promotions\Entity;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsMemoryRepository;
use Project\Modules\Shopping\Discounts\Promotions\Commands\RefreshPromotionStatusCommand;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Commands\Handlers\RefreshPromotionStatusHandler;

class RefreshPromotionStatusCommandTest extends \PHPUnit\Framework\TestCase
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

        $command = new RefreshPromotionStatusCommand($promotion->getId()->getId());
        $handler = new RefreshPromotionStatusHandler($this->promotions);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);

        $updatedPromotion = $this->promotions->get($promotion->getId());
        $this->assertSame(Entity\PromotionStatus::DISABLED, $updatedPromotion->getStatus());
    }
}