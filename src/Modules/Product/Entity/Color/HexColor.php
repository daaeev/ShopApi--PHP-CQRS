<?php

namespace Project\Modules\Product\Entity\Color;

class HexColor extends Color
{
    public function __construct(
        private string $hex
    ) {}

    public function getColor(): string
    {
        return $this->hex;
    }
}