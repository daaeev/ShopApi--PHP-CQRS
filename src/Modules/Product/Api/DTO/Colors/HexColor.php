<?php

namespace Project\Modules\Product\Api\DTO\Colors;

class HexColor
{
    public function __construct(
        public readonly string $color
    ) {}
}