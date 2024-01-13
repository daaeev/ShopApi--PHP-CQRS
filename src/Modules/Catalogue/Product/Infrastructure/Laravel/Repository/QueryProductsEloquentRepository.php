<?php

namespace Project\Modules\Catalogue\Product\Infrastructure\Laravel\Repository;

use Project\Modules\Catalogue\Api\DTO\Product as DTO;
use Project\Common\Repository\NotFoundException;
use Project\Common\Entity\Collections\Pagination;
use Project\Common\Entity\Collections\PaginatedCollection;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Catalogue\Product\Repository\QueryProductsRepositoryInterface;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\Converters\ProductEloquent2DTOConverter;

class QueryProductsEloquentRepository implements QueryProductsRepositoryInterface
{
    public function get(int $id, array $options = []): DTO\Product
    {
        $record = Eloquent\Product::query()
            ->with('prices', 'sizes', 'colors')
            ->where('id', $id)
            ->first();

        if (!$record) {
            throw new NotFoundException('Product does not exists');
        }

        return ProductEloquent2DTOConverter::convert($record);
    }

    public function list(int $page, int $limit, array $options = []): PaginatedCollection
    {
        $query = Eloquent\Product::query()
            ->with('prices', 'sizes', 'colors')
            ->paginate(
                $limit,
                ['*'],
                'page',
                $page
            );

        $items = array_map(function (Eloquent\Product $record) {
            return ProductEloquent2DTOConverter::convert($record);
        }, $query->items());

        return new PaginatedCollection($items, new Pagination(
            $query->currentPage(),
            $query->perPage(),
            $query->total()
        ));
    }
}