<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Repository;

use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Modules\Shopping\Discounts\Promotions\Entity;

class PromotionsMemoryRepository implements PromotionsRepositoryInterface
{
    private array $items = [];
    private int $increment = 0;

    public function __construct(
        private Hydrator $hydrator,
		private IdentityMap $identityMap,
    ) {}

    public function add(Entity\Promotion $promotion): void
    {
        if (null === $promotion->getId()->getId()) {
            $this->hydrator->hydrate($promotion->getId(), ['id' => ++$this->increment]);
        }

        if (isset($this->items[$promotion->getId()->getId()])) {
            throw new DuplicateKeyException('Promotion with same id already exists');
        }

        foreach ($promotion->getDiscounts() as $discount) {
            if (null === $discount->getId()->getId()) {
                $this->hydrator->hydrate($discount->getId(), ['id' => ++$this->increment]);
            }
        }

		$this->identityMap->add($promotion->getId()->getId(), $promotion);
        $this->items[$promotion->getId()->getId()] = clone $promotion;
    }

    public function update(Entity\Promotion $promotion): void
    {
        if (!isset($this->items[$promotion->getId()->getId()])) {
            throw new NotFoundException('Promotion does not exists');
        }

        foreach ($promotion->getDiscounts() as $discount) {
            if (null === $discount->getId()->getId()) {
                $this->hydrator->hydrate($discount->getId(), ['id' => ++$this->increment]);
            }
        }

        $this->items[$promotion->getId()->getId()] = clone $promotion;
    }

    public function delete(Entity\Promotion $promotion): void
    {
        if (!isset($this->items[$promotion->getId()->getId()])) {
            throw new NotFoundException('Promotion does not exists');
        }

		$this->identityMap->remove($promotion->getId()->getId());
        unset($this->items[$promotion->getId()->getId()]);
    }

    public function get(Entity\PromotionId $id): Entity\Promotion
    {
        if (!isset($this->items[$id->getId()])) {
            throw new NotFoundException('Promotion does not exists');
        }

        return $this->identityMap->get($id->getId());
    }

	public function getActivePromotions(): array
	{
		$promotions = [];
		foreach ($this->items as $promotion) {
			if ($promotion->disabled()) {
				continue;
			}

			if ($promotion->getDuration()->started()) {
				$promotions[] = $this->identityMap->get($promotion->getId()->getId());
			}
		}

		return $promotions;
	}
}