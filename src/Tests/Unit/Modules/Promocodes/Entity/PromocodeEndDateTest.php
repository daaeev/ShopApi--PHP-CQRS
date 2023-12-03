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
        $endDate = $promocode->getEndDate()
            ->add(\DateInterval::createFromDateString('1 day'));
        $promocode->setEndDate($endDate);
        $this->assertSame($endDate, $promocode->getEndDate());
        $this->assertNotEmpty($promocode->getUpdatedAt());
        $this->assertEvents($promocode, [new PromocodeUpdated($promocode)]);
    }

    public function testUpdateToSame()
    {
        $promocode = $this->generatePromocode();
        $promocode->setEndDate($promocode->getEndDate());
        $this->assertEmpty($promocode->getUpdatedAt());
        $this->assertEvents($promocode, []);
    }

    public function testUpdateToNull()
    {
        $promocode = $this->generatePromocode();
        $promocode->setEndDate(null);
        $this->assertNull($promocode->getEndDate());
        $this->assertNotEmpty($promocode->getUpdatedAt());
        $this->assertEvents($promocode, [new PromocodeUpdated($promocode)]);
    }

    public function testUpdateToDateThatLessThanStartDate()
    {
        $this->expectException(\DomainException::class);
        $promocode = $this->generatePromocode();
        $endDate = $promocode->getStartDate()->add(
            \DateInterval::createFromDateString('-1 day')
        );
        $promocode->setEndDate($endDate);
    }
}