<?php

namespace Project\Modules\Administrators\Repository;

use Project\Common\Repository\IdentityMap;
use Project\Modules\Administrators\Entity;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;

class AdminsMemoryRepository implements AdminsRepositoryInterface
{
    private array $items = [];
    private int $increment = 0;

    public function __construct(
        private Hydrator $hydrator,
        private IdentityMap $identityMap
    ) {}

    public function add(Entity\Admin $entity): void
    {
        $this->guardLoginUnique($entity);

        if (null === $entity->getId()->getId()) {
            $this->hydrator->hydrate($entity->getId(), ['id' => ++$this->increment]);
        }

        if (isset($this->items[$entity->getId()->getId()])) {
            throw new DuplicateKeyException('Admin with same id already exists');
        }

        $this->identityMap->add($entity->getId()->getId(), $entity);
        $this->items[$entity->getId()->getId()] = clone $entity;
    }

    private function guardLoginUnique(Entity\Admin $entity): void
    {
        foreach ($this->items as $item) {
            if ($entity->getId()->equalsTo($item->getId())) {
                continue;
            }

            if ($entity->getLogin() === $item->getLogin()) {
                throw new DuplicateKeyException('Admin with same login already exists');
            }
        }
    }

    public function update(Entity\Admin $entity): void
    {
        $this->guardLoginUnique($entity);

        if (empty($this->items[$entity->getId()->getId()])) {
            throw new NotFoundException('Admin does not exists');
        }

        $this->items[$entity->getId()->getId()] = clone $entity;
    }

    public function delete(Entity\Admin $entity): void
    {
        if (empty($this->items[$entity->getId()->getId()])) {
            throw new NotFoundException('Admin does not exists');
        }

        $this->identityMap->remove($entity->getId()->getId());
        unset($this->items[$entity->getId()->getId()]);
    }

    public function get(Entity\AdminId $id): Entity\Admin
    {
        if (empty($this->items[$id->getId()])) {
            throw new NotFoundException('Admin does not exists');
        }

        if ($this->identityMap->has($id->getId())) {
            return $this->identityMap->get($id->getId());
        }

        $entity = clone $this->items[$id->getId()];
        $this->identityMap->add($id->getId(), $entity);
        // Passwords saves in db using hashed value.
        // And repository does not decode value and does not retrieve password
        $this->hydrator->hydrate($entity, ['password' => null]);
        return $entity;
    }
}
