<?php

namespace Project\Modules\Cart\Commands;

class RemoveItemCommand
{
    public function __construct(
        public readonly int $item,
    ) {}
}