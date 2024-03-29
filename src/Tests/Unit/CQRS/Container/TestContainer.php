<?php

namespace Project\Tests\Unit\CQRS\Container;

use Psr\Container\ContainerInterface;

class TestContainer implements ContainerInterface
{
    private array $bindings = [];

    public function __construct(array $bindings)
    {
        $this->bindings = $bindings;
    }

    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException;
        }

        return $this->bindings[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->bindings[$id]);
    }
}