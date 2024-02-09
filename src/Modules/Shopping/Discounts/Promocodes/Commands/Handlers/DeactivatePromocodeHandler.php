<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Commands\Handlers;

use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\PromocodeId;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\DeactivatePromocodeCommand;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesRepositoryInterface;

class DeactivatePromocodeHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private PromocodesRepositoryInterface $promocodes
    ) {}

    public function __invoke(DeactivatePromocodeCommand $command): void
    {
        $promocode = $this->promocodes->get(new PromocodeId($command->id));
        $promocode->deactivate();
        $this->promocodes->update($promocode);
        $this->dispatchEvents($promocode->flushEvents());
    }
}