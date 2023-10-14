<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Repository;

use Project\Modules\Shopping\Api\DTO\Promocodes as DTO;
use Project\Common\Entity\Collections\PaginatedCollection;

interface QueryPromocodesRepositoryInterface
{
    public function get(int $id, array $options = []): DTO\Promocode;

    public function list(int $page, int $limit, array $options = []): PaginatedCollection;
}