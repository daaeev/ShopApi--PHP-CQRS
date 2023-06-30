<?php

namespace Project\Modules\Catalogue\Categories\Commands;

class DeleteCategoryCommand
{
    public function __construct(
        public readonly int $id,
    ) {}
}