<?php

namespace Project\Tests\Laravel\Modules\Promocodes\Repository;

use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Project\Tests\Unit\Modules\Promocodes\Repository\PromocodesRepositoryTestTrait;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Repository\PromocodesEloquentRepository;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Utils\PromocodeEloquentToEntityConverter;

class PromocodesEloquentRepositoryTest extends \Tests\TestCase
{
    use PromocodesRepositoryTestTrait, RefreshDatabase;

    protected function setUp(): void
    {
        $this->promocodes = new PromocodesEloquentRepository(
            new Hydrator,
			new IdentityMap,
            new PromocodeEloquentToEntityConverter(new Hydrator)
        );
        parent::setUp();
    }
}