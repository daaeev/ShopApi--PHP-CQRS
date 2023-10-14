<?php

namespace Project\Tests\Unit\Modules\Product\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Catalogue\Product\Repository\ProductsMemoryRepository;

class ProductsMemoryRepositoryTest extends \PHPUnit\Framework\TestCase
{
    use ProductsRepositoryTestTrait;

    protected function setUp(): void
    {
        $this->products = new ProductsMemoryRepository(new Hydrator);
        parent::setUp();
    }
}