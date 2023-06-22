<?php

namespace Project\Modules\Cart\Entity;

class ItemColor
{
    public function __construct(
        private string $name,
        private string $color,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return $this->color;
    }
}