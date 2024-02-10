<?php

namespace Project\Common\Repository;

use Project\Common\Entity\Aggregate;

class IdentityMap
{
    private array $objects = [];

    public function add(int|string $id, Aggregate $object): void
    {
        if ($this->has($id)) {
            throw new \DomainException("Object #$id already added to identity map");
        }

        $this->objects[$id] = $object;
    }

    public function has(int|string $id): bool
    {
        return array_key_exists($id, $this->objects);
    }

    public function get(int|string $id): Aggregate
    {
        if (!$this->has($id)) {
            throw new \DomainException("Object #$id does not exists in identity map");
        }

        return $this->objects[$id];
    }

    public function remove(int|string $id): void
    {
        if (!$this->has($id)) {
            throw new \DomainException("Object #$id does not exists in identity map");
        }

        unset($this->objects[$id]);
    }
}