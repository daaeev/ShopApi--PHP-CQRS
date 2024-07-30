<?php

namespace Project\Tests\Laravel\Modules\Cart\Repository;

use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Project\Tests\Unit\Modules\Cart\Repository\CartsRepositoryTestTrait;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Repository\CartsEloquentRepository;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Utils\CartEloquentToEntityConverter;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Repository\PromocodesEloquentRepository;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Utils\PromocodeEloquentToEntityConverter as PromocodeEloquentConverter;

class CartsEloquentRepositoryTest extends \Project\Tests\Laravel\TestCase
{
    use CartsRepositoryTestTrait, RefreshDatabase;

    protected function setUp(): void
    {
        $this->carts = new CartsEloquentRepository(
            new Hydrator,
			new IdentityMap,
            new CartEloquentToEntityConverter(new Hydrator)
        );

        $this->promocodes = new PromocodesEloquentRepository(
            new Hydrator,
			new IdentityMap,
            new PromocodeEloquentConverter(new Hydrator)
        );

        parent::setUp();
    }
}