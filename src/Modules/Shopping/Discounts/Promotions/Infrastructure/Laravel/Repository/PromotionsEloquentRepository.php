<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Infrastructure\Laravel\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Modules\Shopping\Discounts\Promotions\Entity;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountMechanicFactoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Infrastructure\Laravel\Eloquent;

class PromotionsEloquentRepository implements PromotionsRepositoryInterface
{
    public function __construct(
        private Hydrator $hydrator,
        private DiscountMechanicFactoryInterface $discountsFactory
    ) {}

    public function add(Entity\Promotion $promotion): void
    {
        $id = $promotion->getId()->getId();
        if (Eloquent\Promotion::find($id)) {
            throw new DuplicateKeyException('Promotion with same id already exists');
        }

        $this->persist($promotion, new Eloquent\Promotion);
    }

    private function persist(Entity\Promotion $entity, Eloquent\Promotion $record): void
    {
        $this->persistPromotion($entity, $record);
        $this->persistDiscounts($entity, $record);
    }

    private function persistPromotion(Entity\Promotion $entity, Eloquent\Promotion $record): void
    {
        $record->id = $entity->getId()->getId();
        $record->name = $entity->getName();
        $record->status = $entity->getActualStatus();
        $record->start_date = $entity->getStartDate()->getTimestamp();
        $record->end_date = $entity->getEndDate()->getTimestamp();
        $record->disabled = $entity->disabled();
        $record->created_at = $entity->getCreatedAt()->getTimestamp();
        $record->updated_at = $entity->getUpdatedAt()?->getTimestamp();
        $record->save();
        $this->hydrator->hydrate($entity->getId(), ['id' => $record->id]);
    }

    private function persistDiscounts(Entity\Promotion $entity, Eloquent\Promotion $record): void
    {
        $record->discounts()->delete();
        foreach ($entity->getDiscounts() as $discount) {
            $discountRecord = new Eloquent\PromotionDiscount;
            $discountRecord->id = $discount->getId()->getId();
            $discountRecord->promotion_id = $entity->getId()->getId();
            $discountRecord->type = $discount->getType();
            $discountRecord->data = $discount->getData();
            $discountRecord->save();
            $this->hydrator->hydrate($discount->getId(), ['id' => $discountRecord->id]);
        }
    }

    public function update(Entity\Promotion $promotion): void
    {
        $record = Eloquent\Promotion::find($promotion->getId()->getId());
        if (empty($record)) {
            throw new NotFoundException('Promotion not found');
        }

        $this->persist($promotion, $record);
    }

    public function delete(Entity\Promotion $promotion): void
    {
        $record = Eloquent\Promotion::find($promotion->getId()->getId());
        if (empty($record)) {
            throw new NotFoundException('Promotion not found');
        }

        $record->delete();
    }

    public function get(Entity\PromotionId $id): Entity\Promotion
    {
        $record = Eloquent\Promotion::query()
            ->where('id', $id->getId())
            ->with('discounts')
            ->first();

        if (empty($record)) {
            throw new NotFoundException('Promotion not found');
        }

        return $this->hydrate($record);
    }

    private function hydrate(Eloquent\Promotion $record): Entity\Promotion
    {
        return $this->hydrator->hydrate(Entity\Promotion::class, [
            'id' => Entity\PromotionId::make($record->id),
            'name' => $record->name,
            'startDate' => new \DateTimeImmutable($record->start_date),
            'endDate' => new \DateTimeImmutable($record->end_date),
            'disabled' => $record->disabled,
            'discounts' => array_map(function (Eloquent\PromotionDiscount $discountRecord) {
                return $this->discountsFactory->make($discountRecord->type, $discountRecord->data);
            }, $record->discounts->all()),
            'createdAt' => new \DateTimeImmutable($record->created_at),
            'updatedAt' => $record->updated_at
                ? new \DateTimeImmutable($record->updated_at)
                : null,
        ]);
    }
}