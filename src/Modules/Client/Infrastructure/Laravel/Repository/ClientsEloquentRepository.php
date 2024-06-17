<?php

namespace Project\Modules\Client\Infrastructure\Laravel\Repository;

use Project\Modules\Client\Entity;
use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Modules\Client\Repository\ClientsRepositoryInterface;
use Project\Modules\Client\Infrastructure\Laravel\Models as Eloquent;

class ClientsEloquentRepository implements ClientsRepositoryInterface
{
    public function __construct(
        private Hydrator $hydrator,
        private IdentityMap $identityMap,
    ) {}

    public function add(Entity\Client $client): void
    {
        $id = $client->getId()->getId();
        if (!empty($id) && $this->identityMap->has($id)) {
            throw new DuplicateKeyException('Client with same id already exists');
        }

        if (Eloquent\Client::find($id)) {
            throw new DuplicateKeyException('Client with same id already exists');
        }

        $this->persist($client, new Eloquent\Client);
        $this->identityMap->add($client->getId()->getId(), $client);
    }

    private function persist(Entity\Client $entity, Eloquent\Client $record): void
    {
        $this->guardContactsUnique($entity);

        $record->id = $entity->getId()->getId();
        $record->firstname = $entity->getName()->getFirstName();
        $record->lastname = $entity->getName()->getLastName();
        $record->phone = $entity->getContacts()->getPhone();
        $record->email = $entity->getContacts()->getEmail();
        $record->phone_confirmed = $entity->getContacts()->isPhoneConfirmed();
        $record->email_confirmed = $entity->getContacts()->isEmailConfirmed();
        $record->created_at = $entity->getCreatedAt()->getTimestamp();
        $record->updated_at = $entity->getUpdatedAt()?->getTimestamp();
        $record->save();

        $this->hydrator->hydrate($entity->getId(), ['id' => $record->id]);
    }

    private function guardContactsUnique(Entity\Client $client): void
    {
        if (empty($client->getContacts()->getPhone())) {
            return;
        }

        $phoneNotUnique = Eloquent\Client::query()
            ->where('phone', $client->getContacts()->getPhone())
            ->where('id', '!=', $client->getId()->getId())
            ->exists();

        if ($phoneNotUnique) {
            throw new DuplicateKeyException('Client with same phone already exists');
        }
    }

    public function update(Entity\Client $client): void
    {
        $id = $client->getId()->getId();
        if (empty($id) || !$this->identityMap->has($id)) {
            throw new NotFoundException('Client does not exists');
        }

        if (!$record = Eloquent\Client::find($id)) {
            throw new NotFoundException('Client does not exists');
        }

        $this->persist($client, $record);
    }

    public function delete(Entity\Client $client): void
    {
        $id = $client->getId()->getId();
        if (empty($id) || !$this->identityMap->has($id)) {
            throw new NotFoundException('Client does not exists');
        }

        if (!$record = Eloquent\Client::find($id)) {
            throw new NotFoundException('Client does not exists');
        }

        $this->identityMap->remove($id);
        $record->delete();
    }

    public function get(Entity\ClientId $id): Entity\Client
    {
        if (empty($id->getId())) {
            throw new NotFoundException('Client does not exists');
        }

        if ($this->identityMap->has($id->getId())) {
            return $this->identityMap->get($id->getId());
        }

        if (empty($record = Eloquent\Client::find($id->getId()))) {
            throw new NotFoundException('Client does not exists');
        }

        $client = $this->hydrate($record);
        $this->identityMap->add($client->getId()->getId(), $client);
        return $client;
    }

    private function hydrate(Eloquent\Client $record): Entity\Client
    {
        return $this->hydrator->hydrate(Entity\Client::class, [
            'id' => new Entity\ClientId($record->id),
            'name' => new Entity\Name(
                $record->firstname,
                $record->lastname,
            ),
            'contacts' => new Entity\Contacts(
                $record->phone,
                $record->email,
                $record->phone_confirmed,
                $record->email_confirmed,
            ),
            'createdAt' => new \DateTimeImmutable($record->created_at),
            'updatedAt' => $record->updated_at
                ? new \DateTimeImmutable($record->updated_at)
                : null
        ]);
    }
}
