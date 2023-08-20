<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Commands\Handlers;

use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\PromocodeId;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\DeactivatePromocodeCommand;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodeRepositoryInterface;

class DeactivatePromocodeHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private PromocodeRepositoryInterface $promocodes
    ) {}

    public function __invoke(DeactivatePromocodeCommand $command): int
    {
        $promocode = $this->promocodes->get(new PromocodeId($command->id));
        $promocode->deactivate();
        $this->promocodes->update($promocode);
        $this->dispatchEvents($promocode->flushEvents());
    }
}