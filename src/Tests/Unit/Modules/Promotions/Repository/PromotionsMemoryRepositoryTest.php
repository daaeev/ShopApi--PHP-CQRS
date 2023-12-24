<?php

namespace Project\Tests\Unit\Modules\Promotions\Repository;

use PHPUnit\Framework\TestCase;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsMemoryRepository;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountMechanicFactory;

class PromotionsMemoryRepositoryTest extends TestCase
{
    use PromotionsRepositoryTestTrait;

    protected function setUp(): void
    {
        $this->promotions = new PromotionsMemoryRepository(new Hydrator);
        $this->discountFactory = new DiscountMechanicFactory;
        parent::setUp();
    }
}