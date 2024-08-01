<?php

namespace Project\Modules\Shopping\Cart\Repository;

use Project\Modules\Shopping\Cart\Entity;
use Project\Common\Services\Environment\Client;

interface CartsRepositoryInterface
{
    public function get(Entity\CartId $id): Entity\Cart;

    public function getByClient(Client $client): Entity\Cart;

    /**
     * @return Entity\Cart[]
     */
    public function getCartsWithProduct(int $product): array;

    public function save(Entity\Cart $cart): void;

    public function delete(Entity\Cart $cart): void;
}