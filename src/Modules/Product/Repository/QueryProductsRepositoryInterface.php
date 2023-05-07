<?php

namespace Project\Modules\Product\Repository;

interface QueryProductsRepositoryInterface
{
    public function get(int $id): array;

    public function list(int $page, int $limit, array $params): array;
}