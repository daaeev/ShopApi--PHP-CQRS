<?php

namespace Project\Modules\Catalogue\Repositories;

use Project\Modules\Catalogue\Api\DTO;
use Project\Common\Entity\Collections\PaginatedCollection;

interface QueryCatalogueRepositoryInterface
{
    public function allContent(int $id, array $options = []): array;

    public function get(int $id, array $options = []): DTO\CatalogueProduct;

    public function getByCode(string $code, array $options = []): DTO\CatalogueProduct;

    public function list(int $page, int $limit, array $options = []): PaginatedCollection;
}