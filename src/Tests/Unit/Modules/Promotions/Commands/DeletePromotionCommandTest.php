<?php

namespace Project\Tests\Unit\Modules\Promotions\Commands;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Discounts\Promotions\Commands\DeletePromotionCommand;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsMemoryRepository;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Commands\Handlers\DeletePromotionHandler;

class DeletePromotionCommandTest extends \PHPUnit\Framework\TestCase
{
    use PromotionFactory;

    private PromotionsRepositoryInterface $promotions;
    private MessageBusInterface $dispatcher;

    protected function setUp(): void
    {
        $this->promotions = new PromotionsMemoryRepository(new Hydrator);
        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();

        $this->dispatcher->expects($this->exactly(1)) // promotion deleted
            ->method('dispatch');

        parent::setUp();
    }

    public function testCreate()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $promotion->flushEvents();
        $this->promotions->add($promotion);

        $command = new DeletePromotionCommand($promotion->getId()->getId());
        $handler = new DeletePromotionHandler($this->promotions);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);

        $this->expectException(NotFoundException::class);
        $this->promotions->get($promotion->getId());
    }
}