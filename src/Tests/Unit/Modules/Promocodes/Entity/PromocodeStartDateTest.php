<?php

namespace Project\Tests\Unit\Modules\Promocodes\Entity;

use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;
use Project\Modules\Shopping\Api\Events\Promocodes\PromocodeUpdated;

class PromocodeStartDateTest extends \PHPUnit\Framework\TestCase
{
    use PromocodeFactory, AssertEvents;

    public function testUpdate()
    {
        $promocode = $this->generatePromocode();
        $promocode->deactivate();
        $promocode->flushEvents();
        $oldUpdatedAt = $promocode->getUpdatedAt();

        $startDate = $promocode->getStartDate()->add(\DateInterval::createFromDateString('1 day'));
        $promocode->setStartDate($startDate);

        $this->assertSame($startDate, $promocode->getStartDate());
        $this->assertNotSame($promocode->getUpdatedAt(), $oldUpdatedAt);
        $this->assertEvents($promocode, [new PromocodeUpdated($promocode)]);
    }

    public function testUpdateToSame()
    {
        $promocode = $this->generatePromocode();
        $promocode->deactivate();
        $promocode->flushEvents();
        $oldUpdatedAt = $promocode->getUpdatedAt();
        $promocode->setStartDate($promocode->getStartDate());
        $this->assertSame($promocode->getUpdatedAt(), $oldUpdatedAt);
        $this->assertEvents($promocode, []);
    }

    public function testUpdateWhenPromocodeActive()
    {
        $promocode = $this->generatePromocode();
        $this->expectException(\DomainException::class);
        $promocode->setStartDate(new \DateTimeImmutable);
    }

    public function testUpdateToDateThatGreaterThanEndDate()
    {
        $promocode = $this->generatePromocode();
        $promocode->deactivate();
        $promocode->flushEvents();
        $startDate = $promocode->getEndDate()->add(
            \DateInterval::createFromDateString('1 day')
        );
        $this->expectException(\DomainException::class);
        $promocode->setStartDate($startDate);
    }
}