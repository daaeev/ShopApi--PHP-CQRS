<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Repository;

use Project\Modules\Shopping\Api\DTO\Promotions as DTO;
use Project\Common\Entity\Collections\PaginatedCollection;

interface QueryPromotionsRepositoryInterface
{
    public function get(int $id, array $options = []): DTO\Promotion;

    public function list(int $page, int $limit, array $options = []): PaginatedCollection;
}