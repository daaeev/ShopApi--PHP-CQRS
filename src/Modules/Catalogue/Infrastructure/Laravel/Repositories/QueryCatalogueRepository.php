<?php

namespace Project\Modules\Catalogue\Infrastructure\Laravel\Repositories;

use Project\Modules\Catalogue\Api\DTO;
use Project\Common\Repository\NotFoundException;
use Project\Common\Entity\Collections\Pagination;
use Project\Common\Environment\EnvironmentInterface;
use Project\Common\Entity\Collections\PaginatedCollection;
use Project\Modules\Catalogue\Repositories\QueryCatalogueRepositoryInterface;
use Project\Modules\Catalogue\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Catalogue\Infrastructure\Laravel\Converters\Eloquent2DTOConverter;
use Project\Modules\Catalogue\Infrastructure\Laravel\Converters\Eloquent2AllContentArrayConverter;

class QueryCatalogueRepository implements QueryCatalogueRepositoryInterface
{
    public function __construct(
        private Eloquent2DTOConverter $dtoConverter,
        private Eloquent2AllContentArrayConverter $allContentArrayConverter,
        private EnvironmentInterface $environment
    ) {}

    public function allContent(int $id, array $options = []): array
    {
        $record = Eloquent\CatalogueProduct::query()
            ->where('id', $id)
            ->options($options)
            ->includeAllContents($this->environment->getLanguage())
            ->first();

        if (empty($record)) {
            throw new NotFoundException('Catalogue product does not exists');
        }

        return $this->allContentArrayConverter->convert($record);
    }

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