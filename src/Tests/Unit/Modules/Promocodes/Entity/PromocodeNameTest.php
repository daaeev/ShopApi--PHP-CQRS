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
        $promocode->setName('Test update');
        $this->assertSame('Test update', $promocode->getName());
        $this->assertNotEmpty($promocode->getUpdatedAt());
        $this->assertEvents($promocode, [new PromocodeUpdated($promocode)]);
    }

    public function testUpdateToSame()
    {
        $promocode = $this->generatePromocode();
        $promocode->setName($promocode->getName());
        $this->assertEmpty($promocode->getUpdatedAt());
        $this->assertEvents($promocode, []);
    }

    public function testUpdateToEmpty()
    {
        $this->expectException(\InvalidArgumentException::class);
        $promocode = $this->generatePromocode();
        $promocode->setName('');
    }
}