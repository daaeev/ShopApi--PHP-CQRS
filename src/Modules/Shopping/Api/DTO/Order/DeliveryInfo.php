<?php

namespace Project\Modules\Shopping\Api\DTO\Order;

use Project\Common\Utils\Arrayable;

class DeliveryInfo implements Arrayable
{
    public function __construct(
        public readonly string $service,
        public readonly string $country,
        public readonly string $city,
        public readonly string $street,
        public readonly string $houseNumber,
    ) {}

    public function toArray(): array
    {
        return [
            'service' => $this->service,
            'country' => $this->country,
            'city' => $this->city,
            'street' => $this->street,
            'houseNumber' => $this->houseNumber,
        ];
    }
}