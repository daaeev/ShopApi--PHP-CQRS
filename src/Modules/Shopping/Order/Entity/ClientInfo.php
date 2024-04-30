<?php

namespace Project\Modules\Shopping\Order\Entity;

use Project\Modules\Client\Entity\ClientId;

class ClientInfo
{
    public function __construct(
        private ClientId $id,
        private string $firstName,
        private string $lastName,
        private string $phone,
        private ?string $email,
    ) {}

    public function getId(): ClientId
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}