<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Commands;

class DeletePromocodeCommand
{
    public function __construct(
        public readonly int $id,
    ) {}
}