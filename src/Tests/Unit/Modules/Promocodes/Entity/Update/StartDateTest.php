<?php

namespace Project\Tests\Unit\Modules\Promocodes\Entity\Update;

use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;
use Project\Modules\Shopping\Api\Events\Promocodes\PromocodeUpdated;

class StartDateTest extends \PHPUnit\Framework\TestCase
{
    use PromocodeFactory, AssertEvents;

    public function testUpdate()
    {
        $promocode = $this->generatePromocode();
        $startDate = $promocode->getStartDate()
            ->add(\DateInterval::createFromDateString('1 day'));
        $promocode->setStartDate($startDate);
        $this->assertSame($startDate, $promocode->getStartDate());
        $this->assertEvents($promocode, [new PromocodeUpdated($promocode)]);
    }

    public function testUpdateToSame()
    {
        $promocode = $this->generatePromocode();
        $promocode->setStartDate($promocode->getStartDate());
        $this->assertEvents($promocode, []);
    }

    public function testUpdateToDateThatGreaterThanEndDate()
    {
        $this->expectException(\DomainException::class);
        $promocode = $this->generatePromocode();
        $startDate = $promocode->getEndDate()->add(
            \DateInterval::createFromDateString('1 day')
        );
        $promocode->setStartDate($startDate);
    }
}