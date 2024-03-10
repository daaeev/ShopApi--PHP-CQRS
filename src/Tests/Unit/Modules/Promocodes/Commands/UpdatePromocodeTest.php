<?php

namespace Project\Tests\Unit\Modules\Promocodes\Commands;

use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;
use Project\Modules\Shopping\Discounts\Promocodes\Entity;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\UpdatePromocodeCommand;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesMemoryRepository;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\Handlers\UpdatePromocodeHandler;

class UpdatePromocodeTest extends \PHPUnit\Framework\TestCase
{
    use PromocodeFactory;

    private PromocodesRepositoryInterface $promocodes;
    private MessageBusInterface $dispatcher;

    protected function setUp(): void
    {
        $this->promocodes = new PromocodesMemoryRepository(new Hydrator, new IdentityMap);
        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();

        $this->dispatcher->expects($this->exactly(1)) // promo updated
            ->method('dispatch');

        parent::setUp();
    }

    public function testCreate()
    {
        $initial = $this->generatePromocode();
        $initial->deactivate();
        $initial->flushEvents();
        $this->promocodes->add($initial);

        $command = new UpdatePromocodeCommand(
            id: $initial->getId()->getId(),
            name: md5(rand()),
            startDate: $initial->getStartDate()->add(\DateInterval::createFromDateString('-1 day')),
            endDate: $initial->getEndDate()?->add(\DateInterval::createFromDateString('+1 day')),
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
            $promocode->getStartDate()->getTimestamp(),
            $command->startDate->getTimestamp()
        );
        $this->assertSame(
            $promocode->getEndDate()?->getTimestamp(),
            $command->endDate?->getTimestamp()
        );

        $this->assertSame($promocode->getCode(), $initial->getCode());
        $this->assertSame($promocode->getDiscountPercent(), $initial->getDiscountPercent());
        $this->assertSame($promocode->getActive(), $initial->getActive());
        $this->assertSame($promocode->isActive(), $initial->isActive());

    }
}