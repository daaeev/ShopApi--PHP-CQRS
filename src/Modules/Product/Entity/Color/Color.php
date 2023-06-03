<?php

namespace Project\Modules\Product\Entity\Color;

abstract class Color
{
    final public function __construct(
        protected string $color
    ) {}

    public function equalsTo(self $other): bool
    {
        return (
            (static::class === $other::class)
            && ($this->getColor() === $other->getColor())
        );
    }

    public function getColor(): string
    {
        return $this->color;
    }
}