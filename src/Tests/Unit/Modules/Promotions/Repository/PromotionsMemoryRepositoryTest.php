<?php

namespace Project\Tests\Unit\Modules\Promotions\Repository;

use PHPUnit\Framework\TestCase;
use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsMemoryRepository;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Factory\MechanicFactory;

class PromotionsMemoryRepositoryTest extends TestCase
{
    use PromotionsRepositoryTestTrait;

    protected function setUp(): void
    {
        $this->promotions = new PromotionsMemoryRepository(new Hydrator, new IdentityMap);
        $this->discountFactory = new MechanicFactory;
        parent::setUp();
    }
}