<?php

namespace Project\Modules\Catalogue\Product\Repository;

use Project\Modules\Catalogue\Product\Api\DTO;
use Project\Common\Entity\Collections\PaginatedCollection;

interface QueryProductRepositoryInterface
{
    public function get(int $id): DTO\Product;

    public function list(int $page, int $limit, array $params = []): PaginatedCollection;
}