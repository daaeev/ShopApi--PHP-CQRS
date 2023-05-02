<?php

namespace Project\Tests\Unit\Modules\Product\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Product\Repository\MemoryProductRepository;

class MemoryProductRepositoryTest extends \PHPUnit\Framework\TestCase
{
    use ProductRepositoryTestTrait;

    protected function setUp(): void
    {
        $this->products = new MemoryProductRepository(new Hydrator);
        parent::setUp();
    }
}