<?php

namespace Project\Tests\Unit\Modules\Promocodes\Entity;

use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;
use Project\Modules\Shopping\Api\Events\Promocodes\PromocodeDeleted;

class DeletePromocodeTest extends \PHPUnit\Framework\TestCase
{
    use PromocodeFactory, AssertEvents;

    public function testDelete()
    {
        $promocode = $this->generatePromocode();
        $promocode->deactivate();
        $promocode->flushEvents();
        $promocode->delete();
        $this->assertEvents($promocode, [new PromocodeDeleted($promocode)]);
    }

    public function testDeleteActivePromocode()
    {
        $this->expectException(\DomainException::class);
        $promocode = $this->generatePromocode();
        $promocode->delete();
    }
}