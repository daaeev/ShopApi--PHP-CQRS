<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Commands\Handlers;

use Webmozart\Assert\Assert;
use Project\Common\Entity\Duration;
use Project\Modules\Shopping\Discounts\Promotions\Entity;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics;
use Project\Modules\Shopping\Discounts\Promotions\Commands\CreatePromotionCommand;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsRepositoryInterface;

class CreatePromotionHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private DiscountMechanics\DiscountMechanicFactoryInterface $discountFactory,
        private PromotionsRepositoryInterface $promotions
    ) {}

    public function __invoke(CreatePromotionCommand $command): int
    {
        $promotionDiscounts = array_map([$this, 'makeDiscount'], $command->discounts);
        $promotion = new Entity\Promotion(
            Entity\PromotionId::next(),
            $command->name,
            new Duration(
                $command->startDate,
                $command->endDate,
            ),
            $command->disabled,
            $promotionDiscounts
        );

        $this->promotions->add($promotion);
        $this->dispatchEvents($promotion->flushEvents());
        return $promotion->getId()->getId();
    }

    private function makeDiscount(array $discount): DiscountMechanics\AbstractDiscountMechanic
    {
        Assert::keyExists($discount, 'type', 'Discount type does not provided');
        Assert::keyExists($discount, 'data', 'Discount data does not provided');
        Assert::inArray(
            $discount['type'],
            DiscountMechanics\DiscountType::values(),
            'Unexpected discount type'
        );

        return $this->discountFactory->make(
            DiscountMechanics\DiscountType::from($discount['type']),
            $discount['data']
        );
    }
}