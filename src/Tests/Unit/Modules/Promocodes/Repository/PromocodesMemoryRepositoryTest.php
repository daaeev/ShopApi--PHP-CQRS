<?php

namespace Project\Tests\Unit\Modules\Promocodes\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesMemoryRepository;

class PromocodesMemoryRepositoryTest extends \PHPUnit\Framework\TestCase
{
    use PromocodesRepositoryTestTrait;

    protected function setUp(): void
    {
        $this->promocodes = new PromocodesMemoryRepository(new Hydrator);
        parent::setUp();
    }
}