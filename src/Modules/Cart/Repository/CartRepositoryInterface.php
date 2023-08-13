<?php

namespace Project\Modules\Cart\Repository;

use Project\Modules\Cart\Entity;
use Project\Common\Environment\Client\Client;

interface CartRepositoryInterface
{
    public function get(Entity\CartId $id): Entity\Cart;

    public function getActiveCart(Client $client): Entity\Cart;

    /**
     * @return Entity\Cart[]
     */
    public function getActiveCartsWithProduct(int $product): array;

    public function save(Entity\Cart $cart): void;
}