<?php

namespace Project\Tests\Unit\Modules\Cart\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Cart\Repository\MemoryCartRepository;

class MemoryCartRepositoryTest extends \PHPUnit\Framework\TestCase
{
    use CartRepositoryTestTrait;

    protected function setUp(): void
    {
        $this->carts = new MemoryCartRepository(new Hydrator);
        parent::setUp();
    }
}