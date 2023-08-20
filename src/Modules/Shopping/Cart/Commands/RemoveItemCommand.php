<?php

namespace Project\Modules\Shopping\Cart\Commands;

class RemoveItemCommand
{
    public function __construct(
        public readonly int $item,
    ) {}
}