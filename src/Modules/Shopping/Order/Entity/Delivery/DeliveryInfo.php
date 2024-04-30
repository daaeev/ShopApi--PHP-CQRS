<?php

namespace Project\Modules\Shopping\Order\Entity\Delivery;

class DeliveryInfo
{
    public function __construct(
        private DeliveryService $service,
        private string $country,
        private string $city,
        private string $street,
        private string $houseNumber,
    ) {}

    public function getService(): DeliveryService
    {
        return $this->service;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }
}