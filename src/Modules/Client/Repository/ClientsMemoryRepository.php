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
        $this->guardHashUnique($client);
        $this->guardContactsUnique($client);

        if (null === $client->getId()->getId()) {
            $this->hydrator->hydrate($client->getId(), ['id' => ++$this->increment]);
        }

        if (isset($this->items[$client->getId()->getId()])) {
            throw new DuplicateKeyException('Client with same id already exists');
        }

        $this->identityMap->add($client->getId()->getId(), $client);
        $this->identityMap->add($client->getHash()->getId(), $client);
        $this->items[$client->getId()->getId()] = clone $client;
    }

    private function guardHashUnique(Entity\Client $client): void
    {
        foreach ($this->items as $item) {
            if ($client->getId()->equalsTo($item->getId())) {
                continue;
            }

            if ($client->getHash() === $item->getHash()) {
                throw new DuplicateKeyException('Client with same hash already exists');
            }
        }
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
        $this->guardHashUnique($client);
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
        $this->identityMap->remove($client->getHash()->getId());
        unset($this->items[$client->getId()->getId()]);
    }

    public function get(Entity\ClientHash|Entity\ClientId $id): Entity\Client
    {
        if (empty($id->getId())) {
            throw new NotFoundException('Client does not exists');
        }

        if ($this->identityMap->has($id->getId())) {
            return $this->identityMap->get($id->getId());
        }

        $client = ($id instanceof Entity\ClientId) ? $this->getById($id) : $this->getByHash($id);
        $this->identityMap->add($client->getId()->getId(), $client);
        $this->identityMap->add($client->getHash()->getId(), $client);
        return $client;
    }

    private function getById(Entity\ClientId $id): Entity\Client
    {
        if (empty($this->items[$id->getId()])) {
            throw new NotFoundException('Client does not exists');
        }

        return clone $this->items[$id->getId()];
    }

    private function getByHash(Entity\ClientHash $hash): Entity\Client
    {
        foreach ($this->items as $item) {
            if ($item->getHash()->equalsTo($hash)) {
                return clone $item;
            }
        }

        throw new NotFoundException('Client does not exists');
    }
}
