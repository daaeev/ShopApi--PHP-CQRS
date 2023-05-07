<?php

namespace Project\Tests\Laravel\Modules\Product\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Project\Tests\Unit\Modules\Product\Repository\ProductRepositoryTestTrait;
use Project\Modules\Product\Infrastructure\Laravel\Repository\ProductRepository;

class ProductRepositoryTest extends \Tests\TestCase
{
    use ProductRepositoryTestTrait, RefreshDatabase;

    protected function setUp(): void
    {
        $this->products = new ProductRepository(new Hydrator());
        parent::setUp();
    }
}