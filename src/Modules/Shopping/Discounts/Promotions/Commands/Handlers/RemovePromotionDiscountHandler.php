<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Commands\Handlers;

use Project\Modules\Shopping\Discounts\Promotions\Entity;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics;
use Project\Modules\Shopping\Discounts\Promotions\Commands\RemovePromotionDiscountCommand;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsRepositoryInterface;

class RemovePromotionDiscountHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private PromotionsRepositoryInterface $promotions
    ) {}

    public function __invoke(RemovePromotionDiscountCommand $command): void
    {
        $promotion = $this->promotions->get(Entity\PromotionId::make($command->promotionId));
        $promotion->removeDiscount(DiscountMechanics\DiscountMechanicId::make($command->discountId));
        $this->promotions->update($promotion);
        $this->dispatchEvents($promotion->flushEvents());
    }
}