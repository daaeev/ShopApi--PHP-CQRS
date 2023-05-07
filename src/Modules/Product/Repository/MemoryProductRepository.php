<?php

namespace Project\Modules\Product\Repository;

use DomainException;
use Project\Modules\Product\Entity;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;

class MemoryProductRepository implements ProductRepositoryInterface
{
    private array $items = [];
    private int $increment = 0;

    public function __construct(
        private Hydrator $hydrator
    ) {}

    public function add(Entity\Product $entity): void
    {
        $this->guardCodeUnique($entity);

        if (null === $entity->getId()->getId()) {
            $this->hydrator->hydrate($entity->getId(), ['id' => ++$this->increment]);
        }

        if (isset($this->items[$entity->getId()->getId()])) {
            throw new DuplicateKeyException('Product with same id already exists');
        }

        $this->items[$entity->getId()->getId()] = clone $entity;
    }

    private function guardCodeUnique(Entity\Product $entity): void
    {
        foreach ($this->items as $item) {
            if ($item->getId()->equalsTo($entity->getId())) {
                continue;
            }

            if ($item->getCode() === $entity->getCode()) {
                throw new DuplicateKeyException('Product code must be unique');
            }
        }
    }

    public function update(Entity\Product $entity): void
    {
        $this->guardCodeUnique($entity);

        if (empty($this->items[$entity->getId()->getId()])) {
            throw new NotFoundException('Product does not exists');
        }

        $this->items[$entity->getId()->getId()] = clone $entity;
    }

    public function delete(Entity\Product $entity): void
    {
        if (empty($this->items[$entity->getId()->getId()])) {
            throw new NotFoundException('Product does not exists');
        }

        unset($this->items[$entity->getId()->getId()]);
    }

    public function get(Entity\ProductId $id): Entity\Product
    {
        if (empty($this->items[$id->getId()])) {
            throw new NotFoundException('Product does not exists');
        }

        return clone $this->items[$id->getId()];
    }
}