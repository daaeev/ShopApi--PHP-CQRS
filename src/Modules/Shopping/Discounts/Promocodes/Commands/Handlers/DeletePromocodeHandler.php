<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Commands\Handlers;

use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\PromocodeId;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\DeletePromocodeCommand;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodeRepositoryInterface;

class DeletePromocodeHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private PromocodeRepositoryInterface $promocodes
    ) {}

    public function __invoke(DeletePromocodeCommand $command): void
    {
        $promocode = $this->promocodes->get(new PromocodeId($command->id));
        $promocode->delete();
        $this->promocodes->delete($promocode);
        $this->dispatchEvents($promocode->flushEvents());
    }
}