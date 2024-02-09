<?php

namespace Project\Modules\Client\Api;

use Project\Modules\Client\Entity;
use Project\Modules\Client\Api\DTO;
use Project\Modules\Client\Utils\ClientEntity2DTOConverter;
use Project\Modules\Client\Repository\ClientsRepositoryInterface;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Client\Repository\QueryClientsRepositoryInterface;

class ClientsApi implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private ClientsRepositoryInterface $clients,
        private QueryClientsRepositoryInterface $queryClients,
    ) {}

    public function get(int|string $id): DTO\Client
    {
        return is_int($id)
            ? $this->queryClients->getById($id)
            : $this->queryClients->getByHash($id);
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

        $client->updateName(new Entity\Name($firstName, $lastName));
        $client->updatePhone($phone);
        $client->updateEmail($email);
        $this->clients->add($client);
        $this->dispatchEvents($client->flushEvents());
        return ClientEntity2DTOConverter::convert($client);
    }
}