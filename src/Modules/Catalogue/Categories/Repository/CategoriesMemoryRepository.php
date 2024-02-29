<?php

namespace Project\Modules\Catalogue\Categories\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\IdentityMap;
use Project\Modules\Catalogue\Categories\Entity;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;

class CategoriesMemoryRepository implements CategoriesRepositoryInterface
{
    private array $items = [];
    private int $increment = 0;

    public function __construct(
        private readonly Hydrator $hydrator,
        private readonly IdentityMap $identityMap
    ) {}

    public function add(Entity\Category $entity): void
    {
        $this->guardSlugUnique($entity);

        if (null === $entity->getId()->getId()) {
            $this->hydrator->hydrate($entity->getId(), ['id' => ++$this->increment]);
        }

        if (isset($this->items[$entity->getId()->getId()])) {
            throw new DuplicateKeyException('Category with same id already exists');
        }

        $this->identityMap->add($entity->getId()->getId(), $entity);
        $this->items[$entity->getId()->getId()] = clone $entity;
    }

    private function guardSlugUnique(Entity\Category $entity): void
    {
        foreach ($this->items as $item) {
            if ($item->getId()->equalsTo($entity->getId())) {
                continue;
            }

            if ($item->getSlug() === $entity->getSlug()) {
                throw new DuplicateKeyException('Category with same slug already exists');
            }
        }
    }

    public function update(Entity\Category $entity): void
    {
        $this->guardSlugUnique($entity);

        if (empty($this->items[$entity->getId()->getId()])) {
            throw new NotFoundException('Category does not exists');
        }

        $this->items[$entity->getId()->getId()] = clone $entity;
    }

    public function delete(Entity\Category $entity): void
    {
        if (empty($this->items[$entity->getId()->getId()])) {
            throw new NotFoundException('Category does not exists');
        }

        $this->identityMap->remove($entity->getId()->getId());
        unset($this->items[$entity->getId()->getId()]);
    }

    public function get(Entity\CategoryId $id): Entity\Category
    {
        if (empty($this->items[$id->getId()])) {
            throw new NotFoundException('Category does not exists');
        }

        if ($this->identityMap->has($id->getId())) {
            return $this->identityMap->get($id->getId());
        }

        $entity = clone $this->items[$id->getId()];
        $this->identityMap->add($id->getId(), $entity);
        return $entity;
    }
}
