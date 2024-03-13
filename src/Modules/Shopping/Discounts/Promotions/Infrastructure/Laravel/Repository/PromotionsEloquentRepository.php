<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Infrastructure\Laravel\Repository;

use Project\Common\Entity\Duration;
use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Modules\Shopping\Discounts\Promotions\Entity;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\MechanicFactoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Infrastructure\Laravel\Eloquent;

class PromotionsEloquentRepository implements PromotionsRepositoryInterface
{
    public function __construct(
        private Hydrator $hydrator,
		private IdentityMap $identityMap,
        private MechanicFactoryInterface $discountsFactory
    ) {}

    public function add(Entity\Promotion $promotion): void
    {
        $id = $promotion->getId()->getId();
		if (!empty($id) && $this->identityMap->has($id)) {
			throw new DuplicateKeyException('Promotion with same id already exists');
		}

        if (Eloquent\Promotion::find($id)) {
            throw new DuplicateKeyException('Promotion with same id already exists');
        }

        $this->persist($promotion, new Eloquent\Promotion);
		$this->identityMap->add($promotion->getId()->getId(), $promotion);
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
        $record->status = $entity->getStatus();
        $record->start_date = $entity->getDuration()->getStartDate()?->getTimestamp();
        $record->end_date = $entity->getDuration()->getEndDate()?->getTimestamp();
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
		$id = $promotion->getId()->getId();
		if (empty($id) || !$this->identityMap->has($id)) {
			throw new NotFoundException('Promotion not found');
		}

        if (!$record = Eloquent\Promotion::find($id)) {
            throw new NotFoundException('Promotion not found');
        }

        $this->persist($promotion, $record);
    }

    public function delete(Entity\Promotion $promotion): void
    {
		$id = $promotion->getId()->getId();
		if (empty($id) || !$this->identityMap->has($id)) {
			throw new NotFoundException('Promotion not found');
		}

        if (!$record = Eloquent\Promotion::find($id)) {
            throw new NotFoundException('Promotion not found');
        }

        $record->delete();
		$this->identityMap->remove($id);
	}

    public function get(Entity\PromotionId $id): Entity\Promotion
    {
		if (empty($id->getId())) {
			throw new NotFoundException('Promotion not found');
		}

		if ($this->identityMap->has($id->getId())) {
			return $this->identityMap->get($id->getId());
		}

        $record = Eloquent\Promotion::query()
            ->where('id', $id->getId())
            ->with('discounts')
            ->first();

        if (empty($record)) {
            throw new NotFoundException('Promotion not found');
        }

        $promotion = $this->hydrate($record);
		$this->identityMap->add($id->getId(), $promotion);
		return $promotion;
    }

    private function hydrate(Eloquent\Promotion $record): Entity\Promotion
    {
        return $this->hydrator->hydrate(Entity\Promotion::class, [
            'id' => Entity\PromotionId::make($record->id),
            'name' => $record->name,
            'duration' => new Duration(
                $record->start_date
                    ? new \DateTimeImmutable($record->start_date)
                    : null,
                $record->end_date
                    ? new \DateTimeImmutable($record->end_date)
                    : null,
            ),
            'status' => $record->status,
            'disabled' => $record->disabled,
            'discounts' => array_map(function (Eloquent\PromotionDiscount $discountRecord) {
                return $this->discountsFactory->make(
                    $discountRecord->type,
                    $discountRecord->data,
                    Entity\DiscountMechanics\DiscountMechanicId::make($discountRecord->id)
                );
            }, $record->discounts->all()),
            'createdAt' => new \DateTimeImmutable($record->created_at),
            'updatedAt' => $record->updated_at
                ? new \DateTimeImmutable($record->updated_at)
                : null,
        ]);
    }
}