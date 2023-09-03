<?php

namespace Project\Tests\Unit\Modules\Promocodes\Commands;

use Project\Common\Utils\DateTimeFormat;
use Project\Common\Entity\Hydrator\Hydrator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;
use Project\Modules\Shopping\Discounts\Promocodes\Entity;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\UpdatePromocodeCommand;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\MemoryPromocodesRepository;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodeRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\Handlers\UpdatePromocodeHandler;

class UpdatePromocodeTest extends \PHPUnit\Framework\TestCase
{
    use PromocodeFactory;

    private PromocodeRepositoryInterface $promocodes;
    private EventDispatcherInterface $dispatcher;

    protected function setUp(): void
    {
        $this->promocodes = new MemoryPromocodesRepository(new Hydrator);
        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->getMock();
        $this->dispatcher->expects($this->exactly(1)) // promo updated
            ->method('dispatch');
        parent::setUp();
    }

    public function testCreate()
    {
        $initial = $this->generatePromocode();
        $this->promocodes->add($initial);

        $command = new UpdatePromocodeCommand(
            $initial->getId()->getId(),
            md5(rand()),
            $initial->getStartDate()->add(
                \DateInterval::createFromDateString('-1 day')
            ),
            $initial->getEndDate()?->add(
                \DateInterval::createFromDateString('+1 day')
            ),
        );
        $handler = new UpdatePromocodeHandler($this->promocodes);
        $handler->setDispatcher($this->dispatcher);

        call_user_func($handler, $command);
        $promocode = $this->promocodes->get($initial->getId());
        $this->assertSamePromocode($promocode, $initial, $command);
    }

    private function assertSamePromocode(
        Entity\Promocode $promocode,
        Entity\Promocode $initial,
        UpdatePromocodeCommand $command
    ): void {
        $this->assertTrue($promocode->getId()->equalsTo(new Entity\PromocodeId($command->id)));
        $this->assertSame($command->name, $promocode->getName());
        $this->assertSame(
            $promocode->getStartDate()->format(DateTimeFormat::FULL_DATE->value),
            $command->startDate->format(DateTimeFormat::FULL_DATE->value)
        );
        $this->assertSame(
            $promocode->getEndDate()?->format(DateTimeFormat::FULL_DATE->value),
            $command->endDate?->format(DateTimeFormat::FULL_DATE->value)
        );

        $this->assertSame($promocode->getCode(), $initial->getCode());
        $this->assertSame($promocode->getDiscountPercent(), $initial->getDiscountPercent());
        $this->assertSame($promocode->getActive(), $initial->getActive());
        $this->assertSame($promocode->isActive(), $initial->isActive());

    }
}