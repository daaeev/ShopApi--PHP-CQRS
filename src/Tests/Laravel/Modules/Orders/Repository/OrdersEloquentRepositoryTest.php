<?php

namespace Project\Tests\Laravel\Modules\Orders\Repository;

use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Project\Tests\Unit\Modules\Orders\Repository\OrdersRepositoryTestTrait;
use Project\Modules\Shopping\Order\Infrastructure\Laravel\Repository\OrdersEloquentRepository;

class OrdersEloquentRepositoryTest extends \Project\Tests\Laravel\TestCase
{
    use OrdersRepositoryTestTrait, RefreshDatabase;

    protected function setUp(): void
    {
        $this->orders = new OrdersEloquentRepository(new IdentityMap, new Hydrator);
        parent::setUp();
    }
}
