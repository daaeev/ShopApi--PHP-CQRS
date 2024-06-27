<?php

namespace Project\Modules\Shopping\Order\Commands;

use Project\Modules\Shopping\Api\DTO\Order\DeliveryInfo;
use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class CreateOrderCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $phone,
        public readonly ?string $email,
        public readonly DeliveryInfo $delivery,
        public readonly ?string $customerComment,
    ) {}
}