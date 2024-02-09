<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Queries\Handlers;

use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Queries\GetPromocodesListQuery;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\QueryPromocodesRepositoryInterface;

class GetPromocodeListHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private QueryPromocodesRepositoryInterface $promocodes
    ) {}

    public function __invoke(GetPromocodesListQuery $query): array
    {
        return $this->promocodes->list(
            $query->page,
            $query->limit,
            $query->options
        )->toArray();
    }
}