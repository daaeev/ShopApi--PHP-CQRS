<?php

namespace Project\Tests\Laravel\Modules\Category\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Project\Common\Repository\IdentityMap;
use Project\Tests\Unit\Modules\Categories\Repository\CategoriesRepositoryTestTrait;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\Repository\ProductsEloquentRepository;
use Project\Modules\Catalogue\Categories\Infrastructure\Laravel\Repository\CategoriesEloquentRepository;

class CategoriesEloquentRepositoryTest extends \Tests\TestCase
{
    use CategoriesRepositoryTestTrait, RefreshDatabase;

    protected function setUp(): void
    {
        $this->products = new ProductsEloquentRepository(new Hydrator, new IdentityMap);
        $this->categories = new CategoriesEloquentRepository(new Hydrator, new IdentityMap);
        parent::setUp();
    }
}
