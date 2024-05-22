<?php

namespace Project\Modules\Shopping\Api\DTO\Order;

use Project\Common\Client\Client;
use Project\Common\Utils\Arrayable;

class ClientInfo implements Arrayable
{
    public function __construct(
        public readonly Client $client,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $phone,
        public readonly ?string $email,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->client->getId(),
            'hash' => $this->client->getHash(),
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'phone' => $this->phone,
            'email' => $this->email,
        ];
    }
}