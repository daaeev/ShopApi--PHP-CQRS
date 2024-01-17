<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Queries\Handlers;

use Project\Modules\Shopping\Discounts\Promotions\Queries\GetPromotionsQuery;
use Project\Modules\Shopping\Discounts\Promotions\Repository\QueryPromotionsRepositoryInterface;

class GetPromotionsHandler
{
    public function __construct(
        private QueryPromotionsRepositoryInterface $promotions,
    ) {}

    public function __invoke(GetPromotionsQuery $query): array
    {
        return $this->promotions->list(
            $query->page,
            $query->limit,
            $query->options
        )->toArray();
    }
}