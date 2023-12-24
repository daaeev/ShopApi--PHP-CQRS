<?php

namespace Project\Tests\Unit\Modules\Promocodes\Entity;

use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;
use Project\Modules\Shopping\Api\Events\Promocodes\PromocodeUpdated;

class PromocodeNameTest extends \PHPUnit\Framework\TestCase
{
    use PromocodeFactory, AssertEvents;

    public function testUpdate()
    {
        $promocode = $this->generatePromocode();
        $promocode->deactivate();
        $promocode->flushEvents();
        $oldUpdatedAt = $promocode->getUpdatedAt();
        $promocode->setName('Test update');
        $this->assertSame('Test update', $promocode->getName());
        $this->assertNotSame($promocode->getUpdatedAt(), $oldUpdatedAt);
        $this->assertEvents($promocode, [new PromocodeUpdated($promocode)]);
    }

    public function testUpdateToSame()
    {
        $promocode = $this->generatePromocode();
        $promocode->deactivate();
        $promocode->flushEvents();
        $oldUpdatedAt = $promocode->getUpdatedAt();
        $promocode->setName($promocode->getName());
        $this->assertSame($promocode->getUpdatedAt(), $oldUpdatedAt);
        $this->assertEvents($promocode, []);
    }

    public function testUpdateWhenPromocodeActive()
    {
        $promocode = $this->generatePromocode();
        $this->expectException(\DomainException::class);
        $promocode->setName('New name');
    }

    public function testUpdateToEmpty()
    {
        $promocode = $this->generatePromocode();
        $promocode->deactivate();
        $this->expectException(\InvalidArgumentException::class);
        $promocode->setName('');
    }
}