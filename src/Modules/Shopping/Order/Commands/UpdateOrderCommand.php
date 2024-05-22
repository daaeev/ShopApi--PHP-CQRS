<?php

namespace Project\Modules\Shopping\Order\Commands;

use Project\Modules\Shopping\Api\DTO\Order\DeliveryInfo;

class UpdateOrderCommand
{
    public function __construct(
        public readonly int|string $id,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $phone,
        public readonly ?string $email,
        public readonly string $status,
        public readonly string $paymentStatus,
        public readonly DeliveryInfo $delivery,
        public readonly ?string $customerComment,
        public readonly ?string $managerComment,
    ) {}
}