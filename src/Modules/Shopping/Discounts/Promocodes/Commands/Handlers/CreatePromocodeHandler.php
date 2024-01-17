<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Commands\Handlers;

use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\Promocode;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\PromocodeId;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\CreatePromocodeCommand;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesRepositoryInterface;

class CreatePromocodeHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private PromocodesRepositoryInterface $promocodes
    ) {}

    public function __invoke(CreatePromocodeCommand $command): int
    {
        $promocode = new Promocode(
            PromocodeId::next(),
            $command->name,
            $command->code,
            $command->discountPercent,
            $command->startDate,
            $command->endDate,
        );

        $this->promocodes->add($promocode);
        $this->dispatchEvents($promocode->flushEvents());
        return $promocode->getId()->getId();
    }
}