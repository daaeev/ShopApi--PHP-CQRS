<?php

namespace Project\Modules\Product\Entity\Color;

abstract class Color
{
    public function equalsTo(self $other): bool
    {
        return (
            (static::class === $other::class)
            && ($this->getColor() === $other->getColor())
        );
    }

    abstract public function getColor(): string;
}