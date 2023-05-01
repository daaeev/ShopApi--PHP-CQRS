<?php

namespace Project\Tests\Unit\Modules\Product\Entity\Color;

class TestColor extends \Project\Modules\Product\Entity\Color\Color
{
    public function __construct(
        private string $color
    ) {}

    public function getColor(): string
    {
        return $this->color;
    }
}