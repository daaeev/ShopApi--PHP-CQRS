<?php

namespace Project\Tests\Unit\Modules\Promocodes\Entity;

use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;
use Project\Modules\Shopping\Api\Events\Promocodes\PromocodeUpdated;

class PromocodeActivityTest extends \PHPUnit\Framework\TestCase
{
    use PromocodeFactory, AssertEvents;

    public function testActivate()
    {
        $promocode = $this->generatePromocode();
        $promocode->deactivate();
        $promocode->flushEvents();
        $promocode->activate();
        $this->assertTrue($promocode->getActive());
        $this->assertTrue($promocode->isActive());
        $this->assertEvents($promocode, [new PromocodeUpdated($promocode)]);
    }

    public function testActivateIfAlreadyActive()
    {
        $this->expectException(\DomainException::class);
        $promocode = $this->generatePromocode();
        $promocode->activate();
        $this->assertEmpty($promocode->getUpdatedAt());
    }

    public function testDeactivate()
    {
        $promocode = $this->generatePromocode();
        $promocode->deactivate();
        $this->assertFalse($promocode->getActive());
        $this->assertFalse($promocode->isActive());
        $this->assertNotEmpty($promocode->getUpdatedAt());
        $this->assertEvents($promocode, [new PromocodeUpdated($promocode)]);
    }

    public function testDeactivateIfAlreadyDeactivated()
    {
        $promocode = $this->generatePromocode();
        $promocode->deactivate();
        $this->expectException(\DomainException::class);
        $promocode->deactivate();
    }

    public function testIsActive()
    {
        $promocode = $this->generatePromocode();
        $this->assertTrue($promocode->isActive());
    }

    public function testIsActiveIfStartDateGreaterThanNow()
    {
        $promocode = $this->generatePromocode();
        $startDate = $promocode->getEndDate()
            ->add(\DateInterval::createFromDateString('-1 second'));
        $promocode->deactivate();
        $promocode->setStartDate($startDate);
        $promocode->activate();
        $this->assertFalse($promocode->isActive());
    }

    public function testIsActiveIfEndDateLessThanNow()
    {
        $promocode = $this->generatePromocode();
        $endDate = $promocode->getStartDate()
            ->add(\DateInterval::createFromDateString('+1 second'));
        $promocode->deactivate();
        $promocode->setEndDate($endDate);
        $promocode->activate();
        $this->assertFalse($promocode->isActive());
    }

    public function testIsActiveWithEmptyEndDate()
    {
        $promocode = $this->generatePromocode();
        $promocode->deactivate();
        $promocode->setEndDate(null);
        $promocode->activate();
        $this->assertTrue($promocode->isActive());
    }
}