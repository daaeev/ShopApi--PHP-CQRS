<?php

namespace Project\Modules\Catalogue\Product\Commands;

class DeleteProductCommand
{
    public function __construct(
        public readonly int $id,
    ) {}
}