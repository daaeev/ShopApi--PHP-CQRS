<?php

namespace Project\Modules\Product\Entity\Size;

class Size
{
    public function __construct(
        private string $size
    ) {}

    public function equalsTo(self $other): bool
    {
        return $this->getSize() === $other->getSize();
    }

    public function getSize(): string
    {
        return $this->size;
    }
}