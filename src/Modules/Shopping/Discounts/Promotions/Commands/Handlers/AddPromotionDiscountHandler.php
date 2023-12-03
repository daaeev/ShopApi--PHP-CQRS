<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Commands\Handlers;

use Webmozart\Assert\Assert;
use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Discounts\Promotions\Entity;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics;
use Project\Modules\Shopping\Discounts\Promotions\Commands\AddPromotionDiscountCommand;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsRepositoryInterface;

class AddPromotionDiscountHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private DiscountMechanics\DiscountMechanicFactory $discountFactory,
        private PromotionsRepositoryInterface $promotions
    ) {}

    public function __invoke(AddPromotionDiscountCommand $command): void
    {
        $promotion = $this->promotions->get(Entity\PromotionId::make($command->id));
        $promotion->addDiscount($this->makeDiscount(
            $command->discountType,
            $command->discountData
        ));
        $this->promotions->update($promotion);
        $this->dispatchEvents($promotion->flushEvents());
    }

    private function makeDiscount(string $discountType, array $discountData): DiscountMechanics\AbstractDiscountMechanic
    {
        Assert::inArray(
            $discountType,
            DiscountMechanics\DiscountType::values(),
            'Unexpected discount type'
        );

        return $this->discountFactory->make(
            DiscountMechanics\DiscountType::from($discountType),
            $discountData
        );
    }
}