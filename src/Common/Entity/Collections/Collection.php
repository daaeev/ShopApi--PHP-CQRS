<?php

namespace Project\Common\Entity\Collections;

use Project\Common\Utils\Arrayable;

class Collection implements Arrayable, \Iterator
{
    private int $position = 0;

    public function __construct(
        private array $entities
    ) {
        $this->entities = array_values($this->entities);
    }

    public function toArray(): array
    {
        $items = [];

        foreach ($this->entities as $position => $value) {
            $items[] = $this->hydrateValue($value, $position);
        }

        return $items;
    }

    protected function hydrateValue(mixed $value, int $position): string|array
    {
        if (is_scalar($value) || method_exists($value, '__toString')) {
            return (string) $value;
        }

        if ($value instanceof Arrayable) {
            return $value->toArray();
        }

        if (is_array($value)) {
            $items = [];

            foreach ($value as $position => $item) {
                $items[] = $this->hydrateValue($item, $position);
            }

            return $items;
        }

        return 'Item #' . $position;
    }

    public function current(): mixed
    {
        return $this->entities[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key(): mixed
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
}