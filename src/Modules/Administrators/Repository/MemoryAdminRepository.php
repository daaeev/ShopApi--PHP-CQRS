<?php

namespace Project\Modules\Administrators\Repository;

use Project\Modules\Administrators\Entity;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;

class MemoryAdminRepository implements AdminRepositoryInterface
{
    private array $items = [];
    private int $increment = 0;

    public function __construct(
        private Hydrator $hydrator
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

        $this->items[$entity->getId()->getId()] = clone $entity;
    }

    private function guardLoginUnique(Entity\Admin $entity): void
    {
        foreach ($this->items as $item) {
            if ($entity->getLogin() === $item->getLogin()) {
                throw new \DomainException('Admin with same login already exists');
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

        unset($this->items[$entity->getId()->getId()]);
    }

    public function get(Entity\AdminId $id): Entity\Admin
    {
        if (empty($this->items[$id->getId()])) {
            throw new NotFoundException('Product does not exists');
        }

        $entity = clone $this->items[$id->getId()];
        // Passwords saves in db using hashed value.
        // And repository does not decode value and does not retrieve password
        $this->hydrator->hydrate($entity, [
            'password' => null
        ]);
        return $entity;
    }
}