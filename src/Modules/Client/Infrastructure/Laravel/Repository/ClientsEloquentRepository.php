<?php

namespace Project\Modules\Client\Infrastructure\Laravel\Repository;

use Project\Modules\Client\Entity;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Modules\Client\Repository\ClientsRepositoryInterface;
use Project\Modules\Client\Infrastructure\Laravel\Models as Eloquent;

class ClientsEloquentRepository implements ClientsRepositoryInterface
{
    public function __construct(
        private Hydrator $hydrator,
    ) {}

    public function add(Entity\Client $client): void
    {
        $id = $client->getId()->getId();
        if (Eloquent\Client::find($id)) {
            throw new DuplicateKeyException('Client with same id already exists');
        }

        $this->persist($client, new Eloquent\Client);
    }

    private function persist(Entity\Client $entity, Eloquent\Client $record): void
    {
        $this->guardHashUnique($entity);
        $this->guardContactsUnique($entity);

        $record->id = $entity->getId()->getId();
        $record->hash = $entity->getHash()->getId();
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

    private function guardHashUnique(Entity\Client $client): void
    {
        $notUnique = Eloquent\Client::query()
            ->where('hash', $client->getHash()->getId())
            ->where('id', '!=', $client->getId()->getId())
            ->exists();

        if ($notUnique) {
            throw new DuplicateKeyException('Client with same hash already exists');
        }
    }

    private function guardContactsUnique(Entity\Client $client): void
    {
        if (!empty($client->getContacts()->getPhone())) {
            $phoneNotUnique = Eloquent\Client::query()
                ->where('phone', $client->getContacts()->getPhone())
                ->where('id', '!=', $client->getId()->getId())
                ->exists();

            if ($phoneNotUnique) {
                throw new DuplicateKeyException('Client with same phone already exists');
            }
        }

        if (!empty($client->getContacts()->getEmail())) {
            $emailNotUnique = Eloquent\Client::query()
                ->where('email', $client->getContacts()->getEmail())
                ->where('id', '!=', $client->getId()->getId())
                ->exists();

            if ($emailNotUnique) {
                throw new DuplicateKeyException('Client with same email already exists');
            }
        }
    }

    public function update(Entity\Client $client): void
    {
        $id = $client->getId()->getId();
        if (!$record = Eloquent\Client::find($id)) {
            throw new NotFoundException('Client does not exists');
        }

        $this->persist($client, $record);
    }

    public function delete(Entity\Client $client): void
    {
        $id = $client->getId()->getId();
        if (!$record = Eloquent\Client::find($id)) {
            throw new NotFoundException('Client does not exists');
        }

        $record->delete();
    }

    public function get(Entity\ClientHash|Entity\ClientId $id): Entity\Client
    {
        return $id instanceof Entity\ClientId
            ? $this->getById($id)
            : $this->getByHash($id);
    }

    private function getById(Entity\ClientId $id): Entity\Client
    {
        $record = Eloquent\Client::find($id->getId());
        if (empty($record)) {
            throw new NotFoundException('Client does not exists');
        }

        return $this->hydrate($record);
    }

    private function getByHash(Entity\ClientHash $id): Entity\Client
    {
        $record = Eloquent\Client::query()
            ->where('hash', $id->getId())
            ->first();

        if (empty($record)) {
            throw new NotFoundException('Client does not exists');
        }

        return $this->hydrate($record);
    }

    private function hydrate(Eloquent\Client $record): Entity\Client
    {
        return $this->hydrator->hydrate(Entity\Client::class, [
            'id' => new Entity\ClientId($record->id),
            'hash' => new Entity\ClientHash($record->hash),
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