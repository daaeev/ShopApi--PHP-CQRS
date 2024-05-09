<?php

namespace Project\Tests\Unit\Modules\Categories\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\IdentityMap;
use Project\Modules\Catalogue\Product\Repository\ProductsMemoryRepository;
use Project\Modules\Catalogue\Categories\Repository\CategoriesMemoryRepository;

class CategoriesMemoryRepositoryTest extends \PHPUnit\Framework\TestCase
{
    use CategoriesRepositoryTestTrait;

    protected function setUp(): void
    {
        $this->categories = new CategoriesMemoryRepository(new Hydrator, new IdentityMap);
        $this->products = new ProductsMemoryRepository(new Hydrator, new IdentityMap);
    }
}
