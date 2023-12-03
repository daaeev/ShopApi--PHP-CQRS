<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Queries\Handlers;

use Project\Modules\Shopping\Discounts\Promotions\Queries\GetPromotionQuery;
use Project\Modules\Shopping\Discounts\Promotions\Repository\QueryPromotionsRepositoryInterface;

class GetPromotionHandler
{
    public function __construct(
        private QueryPromotionsRepositoryInterface $promotions,
    ) {}

    public function __invoke(GetPromotionQuery $query): array
    {
        return $this->promotions->get($query->id, $query->options)->toArray();
    }
}