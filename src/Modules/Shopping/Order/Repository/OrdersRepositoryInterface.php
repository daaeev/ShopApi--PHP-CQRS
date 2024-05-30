<?php

namespace Project\Modules\Shopping\Order\Repository;

use Project\Modules\Shopping\Order\Entity;

interface OrdersRepositoryInterface
{
    public function add(Entity\Order $order): void;

    public function update(Entity\Order $order): void;

    public function delete(Entity\Order $order): void;

    public function get(Entity\OrderId $id): Entity\Order;
}