<?php

namespace Project\Modules\Administrators\Infrastructure\Laravel\Repository;

use Project\Common\Administrators\Role;
use Illuminate\Contracts\Hashing\Hasher;
use Project\Modules\Administrators\Entity;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Modules\Administrators\Repository\AdminsRepositoryInterface;
use Project\Modules\Administrators\Infrastructure\Laravel\Models as Eloquent;

class AdminsEloquentRepository implements AdminsRepositoryInterface
{
    public function __construct(
        private Hydrator $hydrator,
        private Hasher $hasher,
    ) {}

    public function add(Entity\Admin $entity): void
    {
        $id = $entity->getId()->getId();

        if (Eloquent\Administrator::find($id)) {
            throw new DuplicateKeyException('Admin with same id already exists');
        }

        $this->persist($entity, new Eloquent\Administrator);
    }

    private function persist(Entity\Admin $entity, Eloquent\Administrator $record): void
    {
        $this->guardLoginUnique($entity);

        if (!$record->exists) {
            $record->id = $entity->getId()->getId();
        }

        if (!empty($entity->getPassword())) {
            $record->hashPassword($this->hasher, $entity->getPassword());
        }

        $record->name = $entity->getName();
        $record->login = $entity->getLogin();
        $record->roles = array_column($entity->getRoles(), 'value');
        $record->save();
        $this->hydrator->hydrate($entity->getId(), ['id' => $record->id]);
    }

    private function guardLoginUnique(Entity\Admin $entity): void
    {
        $notUnique = Eloquent\Administrator::query()
            ->where('id', '!=', $entity->getId()->getId())
            ->where('login', $entity->getLogin())
            ->exists();

        if ($notUnique) {
            throw new DuplicateKeyException('Admin with same login already exists');
        }
    }

    public function update(Entity\Admin $entity): void
    {
        $id = $entity->getId()->getId();

        if (!$record = Eloquent\Administrator::find($id)) {
            throw new NotFoundException('Admin does not exists');
        }

        $this->persist($entity, $record);
    }

    public function delete(Entity\Admin $entity): void
    {
        $id = $entity->getId()->getId();

        if (!$record = Eloquent\Administrator::find($id)) {
            throw new NotFoundException('Admin does not exists');
        }

        $record->delete();
    }

    public function get(Entity\AdminId $id): Entity\Admin
    {
        if (!$record = Eloquent\Administrator::find($id->getId())) {
            throw new NotFoundException('Admin does not exists');
        }

        return $this->hydrate($record);
    }

    private function hydrate(Eloquent\Administrator $record): Entity\Admin
    {
        return $this->hydrator->hydrate(Entity\Admin::class, [
            'id' => new Entity\AdminId($record->id),
            'name' => $record->name,
            'login' => $record->login,
            'roles' => array_map(function (string $role) {
                return Role::from($role);
            }, $record->roles),
        ]);
    }
}