<?php

namespace Project\Tests\Unit\Modules\Promocodes\Entity;

use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;
use Project\Modules\Shopping\Api\Events\Promocodes\PromocodeUpdated;

class PromocodeEndDateTest extends \PHPUnit\Framework\TestCase
{
    use PromocodeFactory, AssertEvents;

    public function testUpdate()
    {
        $promocode = $this->generatePromocode();
        $promocode->deactivate();
        $promocode->flushEvents();
        $oldUpdatedAt = $promocode->getUpdatedAt();

        $endDate = $promocode->getEndDate()
            ->add(\DateInterval::createFromDateString('1 day'));
        $promocode->setEndDate($endDate);

        $this->assertSame($endDate, $promocode->getEndDate());
        $this->assertNotSame($promocode->getUpdatedAt(), $oldUpdatedAt);
        $this->assertEvents($promocode, [new PromocodeUpdated($promocode)]);
    }

    public function testUpdateToSame()
    {
        $promocode = $this->generatePromocode();
        $promocode->deactivate();
        $promocode->flushEvents();
        $oldUpdatedAt = $promocode->getUpdatedAt();

        $promocode->setEndDate($promocode->getEndDate());
        $this->assertSame($promocode->getUpdatedAt(), $oldUpdatedAt);
        $this->assertEvents($promocode, []);
    }

    public function testUpdateToNull()
    {
        $promocode = $this->generatePromocode();
        $promocode->deactivate();
        $promocode->flushEvents();
        $oldUpdatedAt = $promocode->getUpdatedAt();

        $promocode->setEndDate(null);
        $this->assertNull($promocode->getEndDate());
        $this->assertNotSame($promocode->getUpdatedAt(), $oldUpdatedAt);
        $this->assertEvents($promocode, [new PromocodeUpdated($promocode)]);
    }

    public function testUpdateWhenPromocodeActive()
    {
        $promocode = $this->generatePromocode();
        $this->expectException(\DomainException::class);
        $promocode->setEndDate(new \DateTimeImmutable);
    }

    public function testUpdateToDateThatLessThanStartDate()
    {
        $promocode = $this->generatePromocode();
        $promocode->deactivate();
        $endDate = $promocode->getStartDate()->add(
            \DateInterval::createFromDateString('-1 day')
        );
        $this->expectException(\DomainException::class);
        $promocode->setEndDate($endDate);
    }
}