<?php

namespace Project\Tests\Laravel\Modules\Cart\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Project\Tests\Unit\Modules\Cart\Repository\CartRepositoryTestTrait;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Repository\CartRepository;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Utils\Eloquent2EntityConverter;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Repository\PromocodeRepository;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Utils\Eloquent2EntityConverter as PromocodeEloquentConverter;

class CartRepositoryTest extends \Tests\TestCase
{
    use CartRepositoryTestTrait, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->carts = new CartRepository(
            new Hydrator,
            new Eloquent2EntityConverter(
                new Hydrator,
                new PromocodeEloquentConverter(new Hydrator)
            )
        );
        $this->promocodes = new PromocodeRepository(
            new Hydrator,
            new PromocodeEloquentConverter(new Hydrator)
        );
    }
}