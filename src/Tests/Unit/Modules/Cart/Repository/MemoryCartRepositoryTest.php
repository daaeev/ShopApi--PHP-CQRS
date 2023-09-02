<?php

namespace Project\Tests\Unit\Modules\Cart\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Shopping\Cart\Repository\MemoryCartRepository;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\MemoryPromocodesRepository;

class MemoryCartRepositoryTest extends \PHPUnit\Framework\TestCase
{
    use CartRepositoryTestTrait;

    protected function setUp(): void
    {
        $this->carts = new MemoryCartRepository(new Hydrator);
        $this->promocodes = new MemoryPromocodesRepository(new Hydrator());
        parent::setUp();
    }
}