<?php

namespace Project\Tests\Laravel\Modules\Category\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Project\Tests\Unit\Modules\Categories\Repository\CategoryRepositoryTestTrait;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\Repository\ProductRepository;
use Project\Modules\Catalogue\Categories\Infrastructure\Laravel\Repository\CategoryRepository;

class CategoryRepositoryTest extends \Tests\TestCase
{
    use CategoryRepositoryTestTrait, RefreshDatabase;

    protected function setUp(): void
    {
        $this->products = new ProductRepository(new Hydrator());
        $this->categories = new CategoryRepository(new Hydrator());
        parent::setUp();
    }
}