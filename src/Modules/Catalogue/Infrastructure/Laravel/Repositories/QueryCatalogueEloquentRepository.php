<?php

namespace Project\Modules\Catalogue\Infrastructure\Laravel\Repositories;

use Project\Modules\Catalogue\Api\DTO;
use Project\Common\Repository\NotFoundException;
use Project\Common\Entity\Collections\Pagination;
use Project\Common\Environment\EnvironmentInterface;
use Project\Common\Entity\Collections\PaginatedCollection;
use Project\Modules\Catalogue\Repositories\QueryCatalogueRepositoryInterface;
use Project\Modules\Catalogue\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Catalogue\Infrastructure\Laravel\Converters\CatalogueEloquent2DTOConverter;

class QueryCatalogueEloquentRepository implements QueryCatalogueRepositoryInterface
{
    public function __construct(
        private CatalogueEloquent2DTOConverter $dtoConverter,
        private EnvironmentInterface $environment
    ) {}

    public function get(int $id, array $options = []): DTO\CatalogueProduct
    {
        $record = Eloquent\CatalogueProduct::query()
            ->where('id', $id)
            ->options($options)
            ->includeContent($this->environment->getLanguage())
            ->first();

        if (empty($record)) {
            throw new NotFoundException('Catalogue product does not exists');
        }

        return $this->dtoConverter->convert($record);
    }

    public function getByCode(string $code, array $options = []): DTO\CatalogueProduct
    {
        $record = Eloquent\CatalogueProduct::query()
            ->where('code', $code)
            ->options($options)
            ->includeContent($this->environment->getLanguage())
            ->first();

        if (empty($record)) {
            throw new NotFoundException('Catalogue product does not exists');
        }

        return $this->dtoConverter->convert($record);
    }

    public function list(int $page, int $limit, array $options = []): PaginatedCollection
    {
        $query = Eloquent\CatalogueProduct::query()
            ->options($options)
            ->includeContent($this->environment->getLanguage())
            ->paginate(
                perPage: $limit,
                page: $page
            );

        $items = array_map(function (Eloquent\CatalogueProduct $record) {
            return $this->dtoConverter->convert($record);
        }, $query->items());

        return new PaginatedCollection($items, new Pagination(
            $query->currentPage(),
            $query->perPage(),
            $query->total()
        ));
    }
}