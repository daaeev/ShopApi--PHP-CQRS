<?php

namespace Project\Modules\Catalogue\Infrastructure\Laravel\Presenters;

use Project\Common\Repository\NotFoundException;
use Project\Modules\Catalogue\Api\DTO\Category as DTO;
use Project\Modules\Catalogue\Presenters\CategoryPresenterInterface;
use Project\Modules\Catalogue\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Catalogue\Content\Category\Infrastructure\Laravel\Models\Content as EloquentCategoryContent;

class CategoryAllContentEloquentPresenter implements CategoryPresenterInterface
{
    public function present(DTO\Category $category): array
    {
        $record = Eloquent\CatalogueCategory::query()
            ->where('id', $category->id)
            ->with('contents')
            ->first();

        if (empty($record)) {
            throw new NotFoundException('Catalogue category does not exists');
        }

        return [
            ...$category->toArray(),
            'content' => array_map(function (EloquentCategoryContent $content) {
                return [
                    'language' => $content->language,
                    'name' => $content->name ?? '',
                ];
            }, $record->contents->all()),
        ];
    }
}