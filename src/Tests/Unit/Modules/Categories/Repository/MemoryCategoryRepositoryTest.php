<?php

namespace Project\Tests\Unit\Modules\Categories\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Catalogue\Product\Repository\MemoryProductRepository;
use Project\Modules\Catalogue\Categories\Repository\MemoryCategoryRepository;

class MemoryCategoryRepositoryTest extends \PHPUnit\Framework\TestCase
{
    use CategoryRepositoryTestTrait;

    protected function setUp(): void
    {
        $this->categories = new MemoryCategoryRepository(new Hydrator);
        $this->products = new MemoryProductRepository(new Hydrator);
        parent::setUp();
    }
}