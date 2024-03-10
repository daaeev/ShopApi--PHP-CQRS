<?php

namespace Project\Tests\Unit\Modules\Promocodes\Entity;

use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;

class ClonePromocodeTest extends \PHPUnit\Framework\TestCase
{
    use PromocodeFactory;

    public function testClone()
    {
        $promocode = $this->generatePromocode();
		$promocode->deactivate(); // Init updatedAt
		$cloned = clone $promocode;
		$this->assertNotSame($promocode->getId(), $cloned->getId());
		$this->assertNotSame($promocode->getStartDate(), $cloned->getStartDate());
		$this->assertNotSame($promocode->getEndDate(), $cloned->getEndDate());
		$this->assertNotSame($promocode->getCreatedAt(), $cloned->getCreatedAt());
		$this->assertNotSame($promocode->getUpdatedAt(), $cloned->getUpdatedAt());
    }
}