<?php

namespace Project\Modules\Shopping\Order\Repository;

use Project\Modules\Shopping\Order\Entity\Order;
use Project\Modules\Shopping\Order\Entity\OrderId;

interface OrdersRepositoryInterface
{
    public function add(Order $order): void;

    public function update(Order $order): void;

    public function delete(Order $order): void;

    public function get(OrderId $id): Order;
}