<?php

namespace Project\Modules\Catalogue\Categories\Infrastructure\Laravel\Repository;

use Project\Common\Repository\NotFoundException;
use Project\Common\Entity\Collections\Pagination;
use Project\Modules\Catalogue\Api\DTO\Category as DTO;
use Project\Common\Entity\Collections\PaginatedCollection;
use Project\Modules\Catalogue\Categories\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Catalogue\Categories\Repository\QueryCategoriesRepositoryInterface;

class QueryCategoriesEloquentRepository implements QueryCategoriesRepositoryInterface
{
    public function get(int $id): DTO\Category
    {
        $record = Eloquent\Category::query()
            ->with('productsRef')
            ->where('id', $id)
            ->first();

        if (empty($record)) {
            throw new NotFoundException('Category does not exists');
        }

        return $this->hydrate($record);
    }

    public function hydrate(Eloquent\Category $record): DTO\Category
    {
        return new DTO\Category(
            $record->id,
            $record->name,
            $record->slug,
            array_column($record->productsRef->all(), 'product_id'),
            $record->parent_id,
            new \DateTimeImmutable($record->created_at),
            $record->updated_at
                ? new \DateTimeImmutable($record->updated_at)
                : null
        );
    }

    public function list(int $page, int $limit, array $options = []): PaginatedCollection
    {
        $query = Eloquent\Category::query()
            ->with('productsRef')
            ->paginate(
                $limit,
                ['*'],
                'page',
                $page
            );

        $items = array_map([$this, 'hydrate'], $query->items());
        return new PaginatedCollection($items, new Pagination(
            $query->currentPage(),
            $query->perPage(),
            $query->total()
        ));
    }
}