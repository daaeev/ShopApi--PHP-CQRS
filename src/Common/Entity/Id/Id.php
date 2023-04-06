<?php

namespace Project\Common\Entity\Id;

abstract class Id
{
    public function __construct(
        public readonly mixed $id
    ) {}

    public function equalsTo(self $other): bool
    {
        if ($other->id === null) {
            return false;
        }

        return $other->id === $this->id;
    }

    abstract public static function random(): self;
    abstract public static function next(): self;
}