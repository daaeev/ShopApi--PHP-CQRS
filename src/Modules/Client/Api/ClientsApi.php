<?php

namespace Project\Modules\Client\Api;

use Project\Modules\Client\Api\DTO;
use Project\Modules\Client\Entity;
use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Client\Utils\ClientEntity2DTOConverter;
use Project\Modules\Client\Repository\ClientsRepositoryInterface;

class ClientsApi implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private ClientsRepositoryInterface $clients
    ) {}

    public function get(int|string $id): DTO\Client
    {
        $id = is_int($id) ? new Entity\ClientId($id) : new Entity\ClientHash($id);
        $client = $this->clients->get($id);
        return ClientEntity2DTOConverter::convert($client);
    }

    public function create(
        string $hash,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $phone = null,
        ?string $email = null,
    ): DTO\Client {
        $client = new Entity\Client(
            Entity\ClientId::next(),
            new Entity\ClientHash($hash)
        );
        $client->updateName(new Entity\Name(
            $firstName,
            $lastName
        ));
        $client->updatePhone($phone);
        $client->updateEmail($email);
        $this->clients->add($client);
        $this->dispatchEvents($client->flushEvents());
        return ClientEntity2DTOConverter::convert($client);
    }
}