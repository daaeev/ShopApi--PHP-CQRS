<?php

namespace Project\Tests\Laravel\Modules\Promotions\Repository;

use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Project\Tests\Unit\Modules\Promotions\Repository\PromotionsRepositoryTestTrait;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountMechanicFactory;
use Project\Modules\Shopping\Discounts\Promotions\Infrastructure\Laravel\Repository\PromotionsEloquentRepository;

class PromotionsEloquentRepositoryTest extends \Tests\TestCase
{
    use PromotionsRepositoryTestTrait, RefreshDatabase;

    protected function setUp(): void
    {
        $this->discountFactory = new DiscountMechanicFactory;
        $this->promotions = new PromotionsEloquentRepository(
            new Hydrator,
			new IdentityMap,
            new DiscountMechanicFactory
        );

        parent::setUp();
    }
}