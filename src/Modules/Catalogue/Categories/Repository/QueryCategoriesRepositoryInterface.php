<?php

namespace Project\Modules\Catalogue\Categories\Repository;

use Project\Modules\Catalogue\Api\DTO\Category as DTO;
use Project\Common\Entity\Collections\PaginatedCollection;

interface QueryCategoriesRepositoryInterface
{
    public function get(int $id): DTO\Category;

    public function list(int $page, int $limit, array $options = []): PaginatedCollection;
}