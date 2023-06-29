<?php

namespace Project\Modules\Catalogue\Content\Commands;

class DeleteProductImageCommand
{
    public function __construct(
        public readonly int $id
    ) {}
}