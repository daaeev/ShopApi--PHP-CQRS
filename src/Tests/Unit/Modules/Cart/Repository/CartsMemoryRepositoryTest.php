<?php

namespace Project\Tests\Unit\Modules\Cart\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Shopping\Cart\Repository\CartsMemoryRepository;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesMemoryRepository;

class CartsMemoryRepositoryTest extends \PHPUnit\Framework\TestCase
{
    use CartsRepositoryTestTrait;

    protected function setUp(): void
    {
        $this->carts = new CartsMemoryRepository(new Hydrator);
        $this->promocodes = new PromocodesMemoryRepository(new Hydrator());
        parent::setUp();
    }
}