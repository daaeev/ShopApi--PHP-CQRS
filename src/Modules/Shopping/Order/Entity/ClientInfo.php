<?php

namespace Project\Modules\Shopping\Order\Entity;

use Webmozart\Assert\Assert;
use Project\Common\Client\Client;

class ClientInfo
{
    public function __construct(
        private Client $client,
        private string $firstName,
        private string $lastName,
        private string $phone,
        private ?string $email,
    ) {
        Assert::notEmpty($this->firstName);
        Assert::notEmpty($this->lastName);
        Assert::notEmpty($this->phone);
    }

    public function __clone(): void
    {
        $this->client = clone $this->client;
    }

    public function getClient(): Client
    {
        return $this->client;
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