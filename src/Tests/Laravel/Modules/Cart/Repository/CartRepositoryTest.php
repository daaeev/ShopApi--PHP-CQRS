<?php

namespace Project\Tests\Laravel\Modules\Cart\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Project\Tests\Unit\Modules\Cart\Repository\CartRepositoryTestTrait;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Repository\CartRepository;

class CartRepositoryTest extends \Tests\TestCase
{
    use CartRepositoryTestTrait, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->carts = new CartRepository(new Hydrator);
    }
}