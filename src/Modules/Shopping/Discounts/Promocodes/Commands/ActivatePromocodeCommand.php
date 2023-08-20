<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Commands;

class ActivatePromocodeCommand
{
    public function __construct(
        public readonly int $id,
    ) {}
}