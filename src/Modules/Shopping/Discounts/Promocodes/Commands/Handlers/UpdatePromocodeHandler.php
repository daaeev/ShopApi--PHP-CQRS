<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Commands\Handlers;

use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\PromocodeId;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\UpdatePromocodeCommand;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesRepositoryInterface;

class UpdatePromocodeHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private PromocodesRepositoryInterface $promocodes
    ) {}

    public function __invoke(UpdatePromocodeCommand $command): void
    {
        $promocode = $this->promocodes->get(new PromocodeId($command->id));
        $promocode->setName($command->name);
        $promocode->setStartDate($command->startDate);
        $promocode->setEndDate($command->endDate);
        $this->promocodes->update($promocode);
        $this->dispatchEvents($promocode->flushEvents());
    }
}