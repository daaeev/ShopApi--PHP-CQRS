<?php

namespace Project\Modules\Catalogue\Content\Product\Commands;

class DeleteProductImageCommand
{
    public function __construct(
        public readonly int $id
    ) {}
}