<?php

namespace Project\Tests\Laravel\Modules\Cart\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Project\Tests\Unit\Modules\Cart\Repository\CartsRepositoryTestTrait;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Repository\CartsEloquentRepository;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Utils\CartEloquent2EntityConverter;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Repository\PromocodesEloquentRepository;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Utils\PromocodeEloquent2EntityConverter as PromocodeEloquentConverter;

class CartsEloquentRepositoryTest extends \Tests\TestCase
{
    use CartsRepositoryTestTrait, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->carts = new CartsEloquentRepository(
            new Hydrator,
            new CartEloquent2EntityConverter(
                new Hydrator,
                new PromocodeEloquentConverter(new Hydrator)
            )
        );
        $this->promocodes = new PromocodesEloquentRepository(
            new Hydrator,
            new PromocodeEloquentConverter(new Hydrator)
        );
    }
}