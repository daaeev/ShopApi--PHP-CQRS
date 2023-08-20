<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Queries\Handlers;

use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Queries\GetPromocodeQuery;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\QueryPromocodeRepositoryInterface;

class GetPromocodeHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private QueryPromocodeRepositoryInterface $promocodes
    ) {}

    public function __invoke(GetPromocodeQuery $query): array
    {
        return $this->promocodes->get($query->id, $query->options)->toArray();
    }
}