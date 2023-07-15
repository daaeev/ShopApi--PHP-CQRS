<?php

namespace Project\Modules\Catalogue\Infrastructure\Laravel\Repositories;

use Project\Modules\Catalogue\Api\DTO;
use Project\Common\Repository\NotFoundException;
use Project\Common\Entity\Collections\Pagination;
use Project\Common\Entity\Collections\PaginatedCollection;
use Project\Modules\Catalogue\Repositories\QueryCatalogueRepositoryInterface;
use Project\Modules\Catalogue\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Catalogue\Infrastructure\Laravel\Converters\Eloquent2DTOConverter;

class QueryCatalogueRepository implements QueryCatalogueRepositoryInterface
{
    public function get(int $id, array $options = []): DTO\CatalogueProduct
    {
        $record = Eloquent\CatalogueProduct::query()
            ->where('id', $id)
            ->options($options)
            ->first();

        if (empty($record)) {
            throw new NotFoundException('Catalogue product does not exists');
        }

        return Eloquent2DTOConverter::convert($record);
    }

    public function getByCode(string $code, array $options = []): DTO\CatalogueProduct
    {
        $record = Eloquent\CatalogueProduct::query()
            ->where('code', $code)
            ->options($options)
            ->first();

        if (empty($record)) {
            throw new NotFoundException('Catalogue product does not exists');
        }

        return Eloquent2DTOConverter::convert($record);
    }

    public function list(int $page, int $limit, array $options = []): PaginatedCollection
    {
        $query = Eloquent\CatalogueProduct::query()
            ->options($options)
            ->paginate(
                perPage: $limit,
                page: $page
            );

        $items = array_map(function (Eloquent\CatalogueProduct $product) {
            return Eloquent2DTOConverter::convert($product);
        }, $query->items());
        return new PaginatedCollection($items, new Pagination(
            $query->currentPage(),
            $query->perPage(),
            $query->total()
        ));
    }
}