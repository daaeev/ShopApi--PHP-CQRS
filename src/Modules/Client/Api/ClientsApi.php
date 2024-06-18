<?php

namespace Project\Modules\Client\Api;

use Project\Modules\Client\Entity;
use Project\Modules\Client\Api\DTO;
use Project\Common\Repository\NotFoundException;
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
        return $this->queryClients->get($id);
    }

    public function getByPhone(string $phone): ?DTO\Client
    {
        try {
            return $this->queryClients->getByPhone($phone);
        } catch (NotFoundException) {
            return null;
        }
    }

    public function create(
        ?string $firstName,
        ?string $lastName,
        string $phone,
        ?string $email
    ): DTO\Client {
        $client = new Entity\Client(Entity\ClientId::next(), $phone);
        if ($email) {
            $client->updateEmail($email);
        }

        if ($firstName) {
            $client->updateName(new Entity\Name($firstName, $lastName));
        }

        $this->clients->add($client);
        $this->dispatchEvents($client->flushEvents());
        return ClientEntity2DTOConverter::convert($client);
    }
}