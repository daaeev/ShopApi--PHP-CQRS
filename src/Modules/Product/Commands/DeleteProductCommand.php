<?php

namespace Project\Modules\Product\Commands;

class DeleteProductCommand
{
    public function __construct(
        public readonly int $id,
    ) {}
}