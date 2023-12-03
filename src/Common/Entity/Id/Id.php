<?php

namespace Project\Common\Entity\Id;

abstract class Id
{
    public function __construct(
        protected mixed $id
    ) {}

    public static function make($id): static
    {
        return new static($id);
    }

    public function equalsTo(self $other): bool
    {
        if ($other->id === null) {
            return false;
        }

        if (static::class !== $other::class) {
            return false;
        }

        return $other->id === $this->id;
    }

    public function getId(): mixed
    {
        return $this->id;
    }

    abstract public static function random(): static;
}