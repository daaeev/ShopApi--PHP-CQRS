<?php

namespace Project\Tests\Laravel\Modules\Product\Repository;

use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Project\Tests\Unit\Modules\Product\Repository\ProductsRepositoryTestTrait;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\Repository\ProductsEloquentRepository;

class ProductsEloquentRepositoryTest extends \Project\Tests\Laravel\TestCase
{
    use ProductsRepositoryTestTrait, RefreshDatabase;

    protected function setUp(): void
    {
        $this->products = new ProductsEloquentRepository(new Hydrator, new IdentityMap);
        parent::setUp();
    }
}
