<?php

namespace Project\Modules\Shopping\Order\Entity\Delivery;

use Webmozart\Assert\Assert;

class DeliveryInfo
{
    public function __construct(
        private DeliveryService $service,
        private string $country,
        private string $city,
        private string $street,
        private string $houseNumber,
    ) {
        Assert::notEmpty($this->country);
        Assert::notEmpty($this->city);
        Assert::notEmpty($this->street);
        Assert::notEmpty($this->houseNumber);
    }

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

    public function equalsTo(self $other): bool
    {
        return (
            ($this->service === $other->service)
            && ($this->country === $other->country)
            && ($this->city === $other->city)
            && ($this->street === $other->street)
            && ($this->houseNumber === $other->houseNumber)
        );
    }
}