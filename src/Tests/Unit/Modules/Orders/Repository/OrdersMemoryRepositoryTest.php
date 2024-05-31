<?php

namespace Project\Tests\Unit\Modules\Orders\Repository;

use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Shopping\Order\Repository\OrdersMemoryRepository;

class OrdersMemoryRepositoryTest extends \PHPUnit\Framework\TestCase
{
    use OrdersRepositoryTestTrait;

    protected function setUp(): void
    {
        $this->orders = new OrdersMemoryRepository(new Hydrator, new IdentityMap);
        parent::setUp();
    }
}