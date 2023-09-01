<?php

namespace Project\Tests\Unit\Modules\Promocodes\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\MemoryPromocodesRepository;

class MemoryPromocodeRepositoryTest extends \PHPUnit\Framework\TestCase
{
    use PromocodesRepositoryTestTrait;

    protected function setUp(): void
    {
        $this->promocodes = new MemoryPromocodesRepository(new Hydrator);
        parent::setUp();
    }
}