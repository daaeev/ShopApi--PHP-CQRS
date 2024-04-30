<?php

namespace Project\Modules\Shopping\Order\Entity;

use Project\Common\Entity\Aggregate;
use Project\Modules\Shopping\Order\Entity\Delivery\DeliveryInfo;

class Order extends Aggregate
{
    private OrderId $id;
    private ClientInfo $client;
    private OrderStatus $status;
    private PaymentStatus $paymentStatus;
    private DeliveryInfo $delivery;
    private array $offers;
    private ?string $customerComment;
    private ?string $managerComment;
}