<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Commands\Handlers;

use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Discounts\Promotions\Entity;
use Project\Modules\Shopping\Discounts\Promotions\Commands\UpdatePromotionCommand;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsRepositoryInterface;

class UpdatePromotionHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private PromotionsRepositoryInterface $promotions
    ) {}

    public function __invoke(UpdatePromotionCommand $command): void
    {
        $promotion = $this->promotions->get(Entity\PromotionId::make($command->id));
        $promotion->updateName($command->name);
        $promotion->updateStartDate($command->startDate);
        $promotion->updateEndDate($command->endDate);
        $this->promotions->update($promotion);
        $this->dispatchEvents($promotion->flushEvents());
    }
}