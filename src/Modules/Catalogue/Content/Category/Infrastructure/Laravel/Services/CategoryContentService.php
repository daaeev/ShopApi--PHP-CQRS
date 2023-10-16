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
        $this->guardCategoryExists($command->category);
        $this->clearLocalization($command->category, $command->language);
        Eloquent\Content::create([
            'category' => $command->category,
            'language' => $command->language,
            ...$command->fields
        ]);
    }

    private function guardCategoryExists(int $category): void
    {
        $categoryExists = EloquentCategory::query()
            ->where('id', $category)
            ->exists();

        if (!$categoryExists) {
            throw new NotFoundException('Category does not exists');
        }
    }

    private function clearLocalization(int $category, string $language): void
    {
        Eloquent\Content::query()
            ->where('category', $category)
            ->where('language', $language)
            ->delete();
    }
}