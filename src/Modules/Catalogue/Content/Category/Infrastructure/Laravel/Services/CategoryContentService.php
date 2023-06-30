<?php

namespace Project\Modules\Catalogue\Content\Category\Infrastructure\Laravel\Services;

use Project\Common\Repository\NotFoundException;
use Project\Modules\Catalogue\Content\Category\Commands;
use Project\Modules\Catalogue\Content\Category\Services\CategoryContentServiceInterface;
use Project\Modules\Catalogue\Content\Category\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Catalogue\Categories\Infrastructure\Laravel\Models\Category as EloquentCategory;

class CategoryContentService implements CategoryContentServiceInterface
{
    public function updateContent(Commands\UpdateCategoryContentCommand $command): void
    {
        $categoryExists = EloquentCategory::query()
            ->where('id', $command->category)
            ->exists();

        if (!$categoryExists) {
            throw new NotFoundException('Category does not exists');
        }

        Eloquent\Content::updateOrCreate(
            [
                'category' => $command->category,
                'language' => $command->language
            ],
            $command->fields
        );
    }
}