<?php

namespace Project\Tests\Unit\Modules\Promocodes\Commands;

use Project\Common\Utils\DateTimeFormat;
use Project\Common\Entity\Hydrator\Hydrator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Entity;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\CreatePromocodeCommand;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesMemoryRepository;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\Handlers\CreatePromocodeHandler;

class CreatePromocodeTest extends \PHPUnit\Framework\TestCase
{
    private PromocodesRepositoryInterface $promocodes;
    private EventDispatcherInterface $dispatcher;

    protected function setUp(): void
    {
        $this->promocodes = new PromocodesMemoryRepository(new Hydrator);
        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->getMock();
        $this->dispatcher->expects($this->exactly(1)) // promo created
            ->method('dispatch');
        parent::setUp();
    }

    public function testCreate()
    {
        $command = new CreatePromocodeCommand(
            md5(rand()),
            md5(rand()),
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
            $promocode->getStartDate()->format(DateTimeFormat::FULL_DATE->value),
            $command->startDate->format(DateTimeFormat::FULL_DATE->value)
        );
        $this->assertSame(
            $promocode->getEndDate()?->format(DateTimeFormat::FULL_DATE->value),
            $command->endDate?->format(DateTimeFormat::FULL_DATE->value)
        );
    }
}