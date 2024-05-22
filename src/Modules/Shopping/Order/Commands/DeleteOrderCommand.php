<?php

namespace Project\Modules\Shopping\Order\Commands;

class DeleteOrderCommand
{
    public function __construct(
        public readonly int|string $id,
    ) {}
}