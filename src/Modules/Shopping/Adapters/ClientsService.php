<?php

namespace Project\Modules\Shopping\Adapters;

use Project\Common\Client\Client;
use Project\Modules\Client\Api\ClientsApi;
use Project\Common\Environment\EnvironmentInterface;

class ClientsService
{
    public function __construct(
        private readonly EnvironmentInterface $environment,
        private readonly ClientsApi $clients,
    ) {}

    public function findClient(
        string $firstName,
        string $lastName,
        string $phone,
        ?string $email
    ): Client {
        $environmentClient = $this->environment->getClient();
        if (null !== $environmentClient->getId()) {
            return $environmentClient;
        }

        $client = $this->clients->getByPhone($phone)
            ?? $this->clients->create($firstName, $lastName, $phone, $email);

        return new Client($environmentClient->getHash(), $client->id);
    }
}