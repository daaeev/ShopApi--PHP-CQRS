<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Commands;

class DeactivatePromocodeCommand
{
    public function __construct(
        public readonly int $id,
    ) {}
}