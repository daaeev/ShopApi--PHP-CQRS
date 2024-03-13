<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Commands\Handlers;

use Webmozart\Assert\Assert;
use Project\Modules\Shopping\Discounts\Promotions\Entity;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics;
use Project\Modules\Shopping\Discounts\Promotions\Commands\AddPromotionDiscountCommand;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsRepositoryInterface;

class AddPromotionDiscountHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private DiscountMechanics\MechanicFactoryInterface $discountFactory,
        private PromotionsRepositoryInterface $promotions
    ) {}

    public function __invoke(AddPromotionDiscountCommand $command): void
    {
        $promotion = $this->promotions->get(Entity\PromotionId::make($command->id));
        $discount = $this->makeDiscount($command);
        $promotion->addDiscount($discount);
        $this->promotions->update($promotion);
        $this->dispatchEvents($promotion->flushEvents());
    }

    private function makeDiscount(
        AddPromotionDiscountCommand $command
    ): DiscountMechanics\AbstractDiscountMechanic {
        Assert::inArray(
            $command->discountType,
            DiscountMechanics\DiscountType::values(),
            'Unexpected discount type'
        );

        return $this->discountFactory->make(
            DiscountMechanics\DiscountType::from($command->discountType),
            $command->discountData
        );
    }
}