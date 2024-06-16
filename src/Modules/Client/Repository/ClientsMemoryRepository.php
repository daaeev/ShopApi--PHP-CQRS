<?php

namespace Project\Modules\Client\Repository;

use Project\Modules\Client\Entity;
use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;

class ClientsMemoryRepository implements ClientsRepositoryInterface
{
    private array $items = [];
    private int $increment = 0;

    public function __construct(
        private Hydrator $hydrator,
        private IdentityMap $identityMap
    ) {}

    public function add(Entity\Client $client): void
    {
        $this->guardContactsUnique($client);

        if (null === $client->getId()->getId()) {
            $this->hydrator->hydrate($client->getId(), ['id' => ++$this->increment]);
        }

        if (isset($this->items[$client->getId()->getId()])) {
            throw new DuplicateKeyException('Client with same id already exists');
        }

        $this->identityMap->add($client->getId()->getId(), $client);
        $this->items[$client->getId()->getId()] = clone $client;
    }

    private function guardContactsUnique(Entity\Client $client): void
    {
        foreach ($this->items as $item) {
            if ($client->getId()->equalsTo($item->getId())) {
                continue;
            }

            if (!empty($client->getContacts()->getPhone())) {
                if ($client->getContacts()->getPhone() === $item->getContacts()->getPhone()) {
                    throw new DuplicateKeyException('Client with same phone already exists');
                }
            }

            if (!empty($client->getContacts()->getEmail())) {
                if ($client->getContacts()->getEmail() === $item->getContacts()->getEmail()) {
                    throw new DuplicateKeyException('Client with same phone already exists');
                }
            }
        }
    }

    public function update(Entity\Client $client): void
    {
        $this->guardContactsUnique($client);

        if (empty($this->items[$client->getId()->getId()])) {
            throw new NotFoundException('Client does not exists');
        }

        $this->items[$client->getId()->getId()] = clone $client;
    }

    public function delete(Entity\Client $client): void
    {
        if (empty($this->items[$client->getId()->getId()])) {
            throw new NotFoundException('Client does not exists');
        }

        $this->identityMap->remove($client->getId()->getId());
        unset($this->items[$client->getId()->getId()]);
    }

    public function get(Entity\ClientId $id): Entity\Client
    {
        if (empty($id->getId())) {
            throw new NotFoundException('Client does not exists');
        }

        if ($this->identityMap->has($id->getId())) {
            return $this->identityMap->get($id->getId());
        }

        throw new NotFoundException('Client does not exists');
    }
}
