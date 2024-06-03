<?php

namespace Project\Tests\Unit\Modules\Promocodes\Commands;

use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Shopping\Discounts\Promocodes\Entity;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\CreatePromocodeCommand;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesMemoryRepository;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\Handlers\CreatePromocodeHandler;

class CreatePromocodeTest extends \PHPUnit\Framework\TestCase
{
    private PromocodesRepositoryInterface $promocodes;
    private MessageBusInterface $dispatcher;

    protected function setUp(): void
    {
        $this->promocodes = new PromocodesMemoryRepository(new Hydrator, new IdentityMap);
        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();

        $this->dispatcher->expects($this->exactly(1)) // promo created
            ->method('dispatch');
    }

    public function testCreate()
    {
        $command = new CreatePromocodeCommand(
            uniqid(),
            uniqid(),
            rand(1, 100),
            new \DateTimeImmutable('-1 day'),
            new \DateTimeImmutable('+1 day'),
        );
        $handler = new CreatePromocodeHandler($this->promocodes);
        $handler->setDispatcher($this->dispatcher);

        $promocodeId = call_user_func($handler, $command);
        $promocode = $this->promocodes->get(new Entity\PromocodeId($promocodeId));
        $this->assertSamePromocode($promocode, $command);
    }

    private function assertSamePromocode(
        Entity\Promocode $promocode,
        CreatePromocodeCommand $command
    ): void {
        $this->assertSame($command->name, $promocode->getName());
        $this->assertSame($command->code, $promocode->getCode());
        $this->assertSame($command->discountPercent, $promocode->getDiscountPercent());
        $this->assertTrue($promocode->getActive());
        $this->assertTrue($promocode->isActive());
        $this->assertSame(
            $promocode->getStartDate()->getTimestamp(),
            $command->startDate->getTimestamp()
        );
        $this->assertSame(
            $promocode->getEndDate()?->getTimestamp(),
            $command->endDate?->getTimestamp()
        );
    }
}