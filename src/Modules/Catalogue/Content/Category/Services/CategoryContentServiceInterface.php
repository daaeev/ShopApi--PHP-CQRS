<?php

namespace Project\Modules\Catalogue\Content\Category\Services;

use Project\Modules\Catalogue\Content\Category\Commands;

interface CategoryContentServiceInterface
{
    public function updateContent(Commands\UpdateCategoryContentCommand $command): void;
}