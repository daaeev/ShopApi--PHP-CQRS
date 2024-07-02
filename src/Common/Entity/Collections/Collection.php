<?php

namespace Project\Common\Entity\Collections;

use Project\Common\Utils\Arrayable;

class Collection implements Arrayable, \Iterator
{
    private int $position = 0;
    protected array $entities;

    public function __construct(array $entities)
    {
        $this->entities = array_values($entities);
    }

    public function __clone(): void
    {
        foreach ($this->entities as $index => $value) {
            if (is_object($value)) {
                $this->entities[$index] = clone $value;
            }
        }
    }

    public function toArray(): array
    {
        $items = [];
        foreach ($this->entities as $position => $value) {
            $items[] = $this->convertValueToArray($value, $position);
        }

        return $items;
    }

    protected function convertValueToArray(mixed $value, int|string $index)
    {
        if (is_array($value)) {
            $items = [];
            foreach ($value as $key => $item) {
                $items[$key] = $this->convertValueToArray($item, $key);
            }

            return $items;
        }

        if (is_scalar($value) || is_null($value)) {
            return $value;
        }

        if (method_exists($value, '__toString')) {
            return (string) $value;
        }

        if ($value instanceof Arrayable) {
            return $value->toArray();
        }

        return 'Item #' . $index;
    }

    public function current(): mixed
    {
        return $this->entities[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->entities[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function all(): array
    {
        return $this->entities;
    }
}